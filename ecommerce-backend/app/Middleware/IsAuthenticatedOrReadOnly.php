<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class IsAuthenticatedOrReadOnly {
    public function handle(Request $request, Response $response) {
        $method = $request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        $auth = new IsAuthenticated();
        return $auth->handle($request, $response);
    }
}