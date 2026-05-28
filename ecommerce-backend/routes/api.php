<?php

use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Middleware\IsAuthenticated;
use App\Middleware\IsAdminUser;
use App\Middleware\IsAuthenticatedOrReadOnly;
use App\Middleware\IsAdminOrReadOnly;
use App\Middleware\AllowAny;
use App\Controllers\CartController;

// 1. Health & Status
$router->get('/health', function ($request, $response) {
    try {
        $db = \App\Core\Database::getConnection();
        $db->query("SELECT 1");
        return $response->success([
            'status' => 'UP',
            'database' => 'connected',
            'timestamp' => date('c'),
            'php_version' => PHP_VERSION,
            'os' => PHP_OS
        ], 'API is healthy');
    } catch (\Exception $e) {
        return $response->error('API is running but database connection failed', 500, ['error' => $e->getMessage()]);
    }
});

// 2. Authentication
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/login', [AuthController::class, 'login']);

$router->middleware('/auth/me', IsAuthenticated::class);
$router->get('/auth/me', [AuthController::class, 'me']);

// Favorites Routes
$router->middleware('/favorites', IsAuthenticated::class);
$router->get('/favorites', [\App\Controllers\FavoriteController::class, 'index']);

$router->middleware('/favorites/([a-zA-Z0-9-]+)', IsAuthenticated::class);
$router->post('/favorites/([a-zA-Z0-9-]+)', [\App\Controllers\FavoriteController::class, 'toggle']);

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

// Cart Routes
$router->middleware('/cart', IsAuthenticated::class);
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart', [CartController::class, 'store']);
$router->patch('/cart/([0-9]+)', [CartController::class, 'update']);
$router->delete('/cart/([0-9]+)', [CartController::class, 'remove']);

// 5. Admin Specific Routes
$router->middleware('/admin/stats', IsAuthenticated::class);
$router->middleware('/admin/stats', IsAdminUser::class);
$router->get('/admin/stats', [App\Controllers\Admin\AdminStatsController::class, 'index']);

$router->middleware('/orders/all', IsAuthenticated::class);
$router->middleware('/orders/all', IsAdminUser::class);
$router->get('/orders/all', [OrderController::class, 'all']);

$router->middleware('/orders/([a-zA-Z0-9-]+)/status', IsAuthenticated::class);
$router->middleware('/orders/([a-zA-Z0-9-]+)/status', IsAdminUser::class);
$router->patch('/orders/([a-zA-Z0-9-]+)/status', [OrderController::class, 'updateStatus']);

