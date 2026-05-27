<?php
namespace App\Core;

class Response {
    public function setStatusCode(int $code) {
        http_response_code($code);
        return $this;
    }

    public function json($data, int $code = 200) {
        $this->setStatusCode($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function error($message, int $code = 400, $errors = []) {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $this->json($response, $code);
    }

    public function success($data, $message = 'Success', int $code = 200) {
        return $this->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}