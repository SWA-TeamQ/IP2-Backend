<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHelper {
    // Make sure this matches your config/app.php secret!
    private static $secret = 'your-super-secret-key-here'; 

    public static function generateToken($userId) {
        $payload = [
            'iss' => "ShopLight",
            'iat' => time(),
            'exp' => time() + (3600 * 24),
            'sub' => $userId
        ];

        // The library expects the string secret here
        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decodeToken($token) {
        try {
            // The Key class is used here to define the algorithm
            return JWT::decode($token, new Key(self::$secret, 'HS256'));
        } catch (Exception $e) {
            return null; 
        }
    }
}