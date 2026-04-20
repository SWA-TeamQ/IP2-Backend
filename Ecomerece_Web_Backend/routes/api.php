<?php

require_once __DIR__ . '/../controllers/ProductionController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/OrderController.php';

$productionController = new ProductionController();
$authController = new AuthController();
$cartController = new CartController();
$orderController = new OrderController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$normalizedUri = rtrim($uri, '/');
if ($normalizedUri === '') {
	$normalizedUri = '/';
}

function api_route_ends_with($uri, $suffix)
{
	if (function_exists('str_ends_with')) {
		return str_ends_with($uri, $suffix);
	}

	$len = strlen($suffix);
	return $len === 0 || substr($uri, -$len) === $suffix;
}

if (api_route_ends_with($normalizedUri, '/api/products') && $method === 'GET') {
	$productionController->listProducts();
	exit;
}

if (preg_match('#/api/products/(\d+)$#', $normalizedUri, $matches) && $method === 'GET') {
	$productionController->getProduct($matches[1]);
	exit;
}

if (api_route_ends_with($normalizedUri, '/api/cart') && $method === 'GET') {
	$cartController->getCart();
	exit;
}

if (api_route_ends_with($normalizedUri, '/api/cart/items') && $method === 'POST') {
	$cartController->addOrUpdateItem();
	exit;
}

if (preg_match('#/api/cart/items/(\d+)$#', $normalizedUri, $matches) && $method === 'DELETE') {
	$cartController->removeItem((int) $matches[1]);
	exit;
}

if (api_route_ends_with($normalizedUri, '/api/orders') && $method === 'GET') {
	$orderController->listOrders();
	exit;
}

if (api_route_ends_with($normalizedUri, '/api/orders') && $method === 'POST') {
	$orderController->createOrder();
	exit;
}

if (preg_match('#/api/orders/(\d+)$#', $normalizedUri, $matches) && $method === 'GET') {
	$orderController->getOrder((int) $matches[1]);
	exit;
}

// Alias to match the API contract path.
if (api_route_ends_with($normalizedUri, '/api/me') && $method === 'GET') {
	$authController->me();
	exit;
}

if (api_route_ends_with($normalizedUri, '/api/auth/me') && $method === 'GET') {
	$authController->me();
	exit;
}
