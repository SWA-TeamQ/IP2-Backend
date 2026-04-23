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
        if ($this->userModel->findByEmail($data['email'])) {
            return ['error' => 'User with this email already exists'];
        }

        $userId = $this->userModel->create($data);
        
        return [
            'message' => 'Registration successful',
            'user' => [
                'id' => $userId,
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'role' => $data['role'] ?? 'user'
            ]
        ];
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['error' => 'Invalid email or password'];
        }

        $token = JWTHelper::generateToken($user['id']);
        
        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatarUrl' => $user['avatar_url']
            ]
        ];
    }

    public function getCurrentUser($token) {
        $decoded = JWTHelper::decodeToken($token);
        if (!$decoded) {
            return ['error' => 'Invalid or expired token'];
        }

        $user = $this->userModel->find($decoded->sub);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        return [
            'id' => $user['id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatarUrl' => $user['avatar_url']
        ];
    }
}