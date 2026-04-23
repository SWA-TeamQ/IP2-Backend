<?php
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

// 1. Authentication
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->get('/auth/me', [AuthController::class, 'me']);

// 2. Products
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'show']);

// 3. Reviews (Auth Required + Verified Purchase Check in Controller)
$router->middleware('/products/([a-zA-Z0-9-]+)/reviews', AuthMiddleware::class);
$router->post('/products/([a-zA-Z0-9-]+)/reviews', [ProductController::class, 'addReview']);

// 4. Orders (Auth Required)
$router->middleware('/orders', AuthMiddleware::class);
$router->post('/orders', [OrderController::class, 'create']);
$router->get('/orders', [OrderController::class, 'index']);

// 5. Admin Routes
$router->middleware('/orders/all', AuthMiddleware::class);
$router->middleware('/orders/all', AdminMiddleware::class);
$router->get('/orders/all', [OrderController::class, 'all']);

// Admin Product Management
$router->middleware('/products', AuthMiddleware::class); // For POST
$router->middleware('/products', AdminMiddleware::class);
$router->post('/products', [ProductController::class, 'store']);

$router->middleware('/products/([a-zA-Z0-9-]+)', AuthMiddleware::class); // For PATCH/DELETE
$router->middleware('/products/([a-zA-Z0-9-]+)', AdminMiddleware::class);
$router->patch('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'update']);
$router->delete('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'delete']);