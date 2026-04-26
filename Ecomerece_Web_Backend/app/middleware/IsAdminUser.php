<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class IsAdminUser {
    public function handle(Request $request, Response $response) {
        // IsAuthenticated must run before this to set userId
        if (empty($request->userId)) {
            return $response->error('Unauthorized: Authentication required', 401);
        }

        $userModel = new User();
        $user = $userModel->find($request->userId);

        if (!$user || $user['role'] !== 'admin') {
            return $response->error('Forbidden: Admin access required', 403);
        }

        return true;
    }
}