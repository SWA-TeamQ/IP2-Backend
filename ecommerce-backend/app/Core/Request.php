<?php
namespace App\Core;

class Request {
    public ?string $userId = null;

    public function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        // Handle project subdirectories (XAMPP/WAMP/MAMP or PHP built-in server)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        
        // Normalize slashes for comparison
        $path = str_replace('\\', '/', $path);
        $baseDir = str_replace('\\', '/', $baseDir);

        if ($baseDir !== '/' && $baseDir !== '.' && $baseDir !== '') {
            if (strpos($path, $baseDir) === 0) {
                $path = substr($path, strlen($baseDir));
            }
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }
        
        // Ensure path starts with /
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path;
    }

    public function getQueryParams() {
        $params = [];
        foreach ($_GET as $key => $value) {
            $params[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $params;
    }

    public function getBody() {
        $body = [];
        $method = $this->getMethod();

        if ($method === 'GET') {
            return $this->getQueryParams();
        }

        // Handle JSON Body for POST, PUT, PATCH, DELETE
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $body = $input;
        } else {
            // Fallback for form-data
            $dataSource = ($method === 'POST') ? $_POST : [];
            foreach ($dataSource as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function getHeader($name) {
        $headers = getallheaders();
        return $headers[$name] ?? $headers[strtolower($name)] ?? null;
    }

    public function getFiles() {
        return $_FILES;
    }
}