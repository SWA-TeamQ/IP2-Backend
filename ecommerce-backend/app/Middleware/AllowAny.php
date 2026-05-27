<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AllowAny {
    public function handle(Request $request, Response $response) {
        return true;
    }
}