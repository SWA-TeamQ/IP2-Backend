<?php

require_once __DIR__ . '/../controllers/ProductionController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$productionController = new ProductionController();
$authController = new AuthController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (str_ends_with($uri, '/api/products') && $method === 'GET') {
	$productionController->listProducts();
	exit;
}

if (preg_match('#/api/products/(\d+)$#', $uri, $matches) && $method === 'GET') {
	$productionController->getProduct($matches[1]);
	exit;
}

// Alias to match the API contract path.
if (str_ends_with($uri, '/api/me') && $method === 'GET') {
	$authController->me();
	exit;
}
