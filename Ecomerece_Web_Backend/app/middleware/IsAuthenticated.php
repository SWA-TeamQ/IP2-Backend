<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\JWTHelper;

class IsAuthenticated {
    public function handle(Request $request, Response $response) {
        $headers = getallheaders();
        // Handle both cases for key
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $response->error('Unauthorized: No token provided', 401);
        }

        $token = $matches[1];
        $decoded = JWTHelper::decodeToken($token);

        if (!$decoded) {
            return $response->error('Unauthorized: Invalid or expired token', 401);
        }

        // Attach the user ID to the request so controllers can use it
        $request->userId = $decoded->sub;
        return true;
    }
}