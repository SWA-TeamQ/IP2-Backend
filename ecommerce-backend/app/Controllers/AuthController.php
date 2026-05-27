<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\Validator;

class AuthController extends Controller {
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
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        $result = $this->authService->login($data['email'], $data['password']);
        
        if (isset($result['error'])) {
            return $this->error($response, $result['error'], 401);
        }

        return $this->success($response, $result, 'Login successful');
    }

    public function register(Request $request, Response $response) {
        $data = $request->getBody();
        
        $errors = Validator::validate($data, [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        $result = $this->authService->register($data);
        
        if (isset($result['error'])) {
            return $this->error($response, $result['error'], 400);
        }

        return $this->success($response, $result['user'], 'Registration successful', 201);
    }

    public function me(Request $request, Response $response) {
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return $this->error($response, 'No authorization token provided', 401);
        }

        $result = $this->authService->getCurrentUser($token);

        if (isset($result['error'])) {
            return $this->error($response, $result['error'], 401);
        }

        return $this->success($response, $result);
    }
}