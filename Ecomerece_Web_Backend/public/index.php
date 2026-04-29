<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
Env::load(__DIR__ . '/../.env');

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

// Handle CORS for React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

$router = new Router(new Request(), new Response());

// Global Error Handler (Express-like)
$router->setErrorHandler(function($e, $request, $response) {
    // Debug: Check if DATABASE_URL exists
    if (!isset($_ENV['DATABASE_URL'])) {
        return $response->error("DATABASE_URL not found in _ENV. Current env keys: " . implode(', ', array_keys($_ENV)), 500);
    }
    return $response->error($e->getMessage(), 500);
});

$router->group('/api', function($router) {
    require_once __DIR__ . '/../routes/api.php';
});

require_once __DIR__ . '/../routes/web.php';

$router->resolve();