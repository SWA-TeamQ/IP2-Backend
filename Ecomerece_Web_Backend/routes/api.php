<?php
require_once __DIR__ . '/../controllers/CartController.php';

$cartController = new CartController();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

session_start();

// GET /api/cart
if (str_ends_with($uri, '/api/cart') && $method === 'GET') {
    $cartController->getCart();
    exit;
}

// POST /api/cart/items
if (str_ends_with($uri, '/api/cart/items') && $method === 'POST') {
    $cartController->addOrUpdateItem();
    exit;
}

// DELETE /api/cart/items/:productId
if (preg_match('#/api/cart/items/([^/]+)$#', $uri, $matches) && $method === 'DELETE') {
    $cartController->removeItem($matches[1]);
    exit;
}
