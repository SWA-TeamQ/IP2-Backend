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
        
        $errors = Validator::validate($data, [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            return $response->json(['errors' => $errors], 400);
        }

        $result = $this->authService->register($data);
        
        if (isset($result['error'])) {
            return $response->json($result, 400);
        }

        return $response->json($result, 201);
    }

    public function me(Request $request, Response $response) {
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return $response->json(['error' => 'No authorization token provided'], 401);
        }

        $result = $this->authService->getCurrentUser($token);

        if (isset($result['error'])) {
            return $response->json($result, 401);
        }

        return $response->json($result);
    }
}