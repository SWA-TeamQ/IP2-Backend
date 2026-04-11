<?php
require_once __DIR__ . '/../controllers/ProductionController.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

if (strpos($requestUri, '/api/products') !== false) {
    $controller = new ProductController($db);

    if (preg_match('/\/api\/products\/([a-zA-Z0-9_-]+)/', $requestUri, $matches)) {
        $id = $matches[1];
        $controller->show($id);
    } else {
        $controller->index();
    }
}