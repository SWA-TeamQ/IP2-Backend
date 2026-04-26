<?php
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Middleware\IsAuthenticated;
use App\Middleware\IsAdminUser;
use App\Middleware\IsAuthenticatedOrReadOnly;
use App\Middleware\IsAdminOrReadOnly;
use App\Middleware\AllowAny;

// 1. Authentication
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/login', [AuthController::class, 'login']);

$router->middleware('/auth/me', IsAuthenticated::class);
$router->get('/auth/me', [AuthController::class, 'me']);

// 2. Products
// Public Read, Admin Write
$router->middleware('/products', IsAdminOrReadOnly::class);
$router->get('/products', [ProductController::class, 'index']);
$router->post('/products', [ProductController::class, 'store']);

$router->middleware('/products/([a-zA-Z0-9-]+)', IsAdminOrReadOnly::class);
$router->get('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'show']);
$router->patch('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'update']);
$router->delete('/products/([a-zA-Z0-9-]+)', [ProductController::class, 'delete']);

// 3. Reviews
// Must be authenticated to post a review
$router->middleware('/products/([a-zA-Z0-9-]+)/reviews', IsAuthenticated::class);
$router->post('/products/([a-zA-Z0-9-]+)/reviews', [ProductController::class, 'addReview']);

// 4. Orders
// Must be authenticated to view or create orders
$router->middleware('/orders', IsAuthenticated::class);
$router->post('/orders', [OrderController::class, 'create']);
$router->get('/orders', [OrderController::class, 'index']);

// 5. Admin Specific Routes
$router->middleware('/admin/stats', IsAuthenticated::class);
$router->middleware('/admin/stats', IsAdminUser::class);
$router->get('/admin/stats', [App\Controllers\Admin\AdminStatsController::class, 'index']);

$router->middleware('/orders/all', IsAuthenticated::class);
$router->middleware('/orders/all', IsAdminUser::class);
$router->get('/orders/all', [OrderController::class, 'all']);