<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class AdminMiddleware {
    public function handle(Request $request, Response $response) {
        // AuthMiddleware must run before this, so userId should be set
        if (!$request->userId) {
            $response->setStatusCode(401);
            return $response->json(['error' => 'Unauthorized']);
        }

        $userModel = new User();
        $user = $userModel->find($request->userId);

        if (!$user || $user['role'] !== 'admin') {
            $response->setStatusCode(403);
            return $response->json(['error' => 'Forbidden: Admin access required']);
        }

        return true;
    }
}