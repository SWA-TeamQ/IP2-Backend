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

$router->group('/api', function($router) {
    require_once __DIR__ . '/../routes/api.php';
});

require_once __DIR__ . '/../routes/web.php';

$router->resolve();