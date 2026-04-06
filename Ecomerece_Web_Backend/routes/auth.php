<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();

// Get the current URI and Method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Normalize (trim trailing slash) and allow apps hosted in subfolders.
$normalizedUri = rtrim($uri, '/');
if ($normalizedUri === '') {
    $normalizedUri = '/';
}

function route_ends_with($uri, $suffix) {
    if (function_exists('str_ends_with')) {
        return str_ends_with($uri, $suffix);
    }
    $len = strlen($suffix);
    return $len === 0 || (substr($uri, -$len) === $suffix);
}

// Route: POST /api/auth/register
if (str_ends_with($uri, '/api/auth/register') && $method === 'POST') {
if (route_ends_with($normalizedUri, '/api/auth/register') && $method === 'POST') {
    $authController->register();
    exit;
}

// Route: POST /api/auth/login
if (str_ends_with($uri, '/api/auth/login') && $method === 'POST') {
if (route_ends_with($normalizedUri, '/api/auth/login') && $method === 'POST') {
    $authController->login();
    exit;
}

// Route: POST /api/auth/logout
if (str_ends_with($uri, '/api/auth/logout') && $method === 'POST') {
if (route_ends_with($normalizedUri, '/api/auth/logout') && $method === 'POST') {
    $authController->logout();
    exit;
}

// Route: GET /api/auth/me (To check if user is logged in)
if (str_ends_with($uri, '/api/auth/me') && $method === 'GET') {
    $authController->me();
    exit;
}