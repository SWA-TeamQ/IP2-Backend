<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\Validator;

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login(Request $request, Response $response) {
        $data = $request->getBody();
        
        $errors = Validator::validate($data, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            return $response->json(['errors' => $errors], 400);
        }

        $result = $this->authService->login($data['email'], $data['password']);
        
        if (isset($result['error'])) {
            return $response->json($result, 401);
        }

        return $response->json($result);
    }

    public function register(Request $request, Response $response) {
        $data = $request->getBody();
        // Add more validation rules as needed
        $result = $this->authService->register($data);
        return $response->json($result);
    }
}