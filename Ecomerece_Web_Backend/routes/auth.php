<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();

// Get the current URI and Method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route: POST /api/auth/register
if ($uri === '/api/auth/register' && $method === 'POST') {
    $authController->register();
    exit;
}

// Route: POST /api/auth/login
if ($uri === '/api/auth/login' && $method === 'POST') {
    $authController->login();
    exit;
}

// Route: POST /api/auth/logout
if ($uri === '/api/auth/logout' && $method === 'POST') {
    $authController->logout();
    exit;
}