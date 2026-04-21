<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserController {
    public function profile(Request $request, Response $response) {
        $userModel = new User();
        $user = $userModel->find($request->userId);
        
        // Don't send the password back to React!
        unset($user['password']);
        
        return $response->json($user);
    }
}