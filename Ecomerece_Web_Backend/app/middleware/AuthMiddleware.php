<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\JWTHelper;

class AuthMiddleware {
    public function handle(Request $request, Response $response) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->setStatusCode(401);
            return $response->json(['error' => 'Unauthorized: No token provided']);
        }

        $token = $matches[1];
        $decoded = JWTHelper::decodeToken($token);

        if (!$decoded) {
            $response->setStatusCode(401);
            return $response->json(['error' => 'Unauthorized: Invalid or expired token']);
        }

        // Attach the user ID to the request so controllers can use it
        $request->userId = $decoded->sub;
        return true;
    }
}