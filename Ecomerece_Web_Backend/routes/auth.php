<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();

// Get the current URI and Method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Auth Routing Logic
 */

// Route: POST /api/auth/register
if (str_ends_with($uri, '/api/auth/register') && $method === 'POST') {
    $authController->register();
    exit;
}

// Route: POST /api/auth/login
if (str_ends_with($uri, '/api/auth/login') && $method === 'POST') {
    $authController->login();
    exit;
}

// Route: POST /api/auth/logout
if (str_ends_with($uri, '/api/auth/logout') && $method === 'POST') {
    $authController->logout();
    exit;
}

// Route: GET /api/auth/me (To check if user is logged in)
if (str_ends_with($uri, '/api/auth/me') && $method === 'GET') {
    $authController->me();
    exit;
}