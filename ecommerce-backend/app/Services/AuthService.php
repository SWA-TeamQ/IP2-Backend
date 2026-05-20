<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Entities\User;
use App\Helpers\JWTHelper;

class AuthService {
    private UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function register(array $data) {
        if ($this->userRepo->findByEmail($data['email'])) {
            return ['error' => 'User with this email already exists'];
        }

        $user = new User([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => 'user'
        ]);

        $userId = $this->userRepo->create($user);
        
        return [
            'message' => 'Registration successful',
            'user' => [
                'id' => $userId,
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ]
        ];
    }

    public function login(string $email, string $password) {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            return ['error' => 'Invalid email or password'];
        }

        $token = JWTHelper::generateToken($user->getId());
        
        return [
            'token' => $token,
            'user' => $user->toArray()
        ];
    }

    public function getCurrentUser(string $token) {
        $decoded = JWTHelper::decodeToken($token);
        if (!$decoded) {
            return ['error' => 'Invalid or expired token'];
        }

        $user = $this->userRepo->find($decoded->sub);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        return $user->toArray();
    }
}