<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;

class UserController extends Controller {
    private UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function profile(Request $request, Response $response) {
        $user = $this->userRepo->find($request->userId);

        if (!$user) {
            return $this->error($response, 'User not found', 404);
        }

        return $this->success($response, $user->toArray());
    }
}