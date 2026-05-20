<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;

class IsAdminUser {
    private UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function handle(Request $request, Response $response) {
        if (empty($request->userId)) {
            return $response->error('Unauthorized: Authentication required', 401);
        }

        $user = $this->userRepo->find($request->userId);

        if (!$user || $user->getRole() !== 'admin') {
            return $response->error('Forbidden: Admin access required', 403);
        }

        return true;
    }
}