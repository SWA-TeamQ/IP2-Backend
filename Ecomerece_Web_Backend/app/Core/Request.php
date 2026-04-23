<?php
namespace App\Core;

class Request {
    public ?int $userId = null;

    public function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove project subdirectory if present (common in XAMPP)
        // Adjust this if your project is in a different subfolder
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $path = str_replace($scriptName, '', $path);

        $position = strpos($path, '?');
        return $position === false ? $path : substr($path, 0, $position);
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
}