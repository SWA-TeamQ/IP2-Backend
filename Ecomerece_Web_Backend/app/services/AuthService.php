<?php
namespace App\Services;

use App\Models\User;
use App\Helpers\JWTHelper;

class AuthService {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register($data) {
        // Check if user exists
        if ($this->userModel->findByEmail($data['email'])) {
            return ['error' => 'User already exists'];
        }

        $this->userModel->create($data);
        return ['message' => 'Registration successful'];
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['error' => 'Invalid credentials'];
        }

        $token = JWTHelper::generateToken($user['id']);
        
        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ];
    }
}