<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class IsAdminOrReadOnly {
    public function handle(Request $request, Response $response) {
        $method = $request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        // Must be authenticated first to check for admin
        $auth = new IsAuthenticated();
        $authResult = $auth->handle($request, $response);
        if ($authResult !== true) {
            return $authResult;
        }

        $admin = new IsAdminUser();
        return $admin->handle($request, $response);
    }
}