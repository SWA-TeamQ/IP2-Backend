<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserController extends Controller {
    public function profile(Request $request, Response $response) {
        $userModel = new User();
        $user = $userModel->find($request->userId);

        if (!$user) {
            return $this->error($response, 'User not found', 404);
        }

        // Don't send the password back!
        unset($user['password_hash']);

        return $this->success($response, $user);
    }
}