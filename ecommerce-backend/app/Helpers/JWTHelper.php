<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHelper {
    private static function getSecret() {
        return $_ENV['JWT_SECRET'] ?? 'fallback-secret-if-env-missing';
    }

    public static function generateToken($userId) {
        $payload = [
            'iss' => "ShopLight",
            'iat' => time(),
            'exp' => time() + (3600 * 24), // 24 hours
            'sub' => $userId
        ];

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function decodeToken($token) {
        try {
            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);
            return JWT::decode($token, new Key(self::getSecret(), 'HS256'));
        } catch (Exception $e) {
            return null; 
        }
    }
}