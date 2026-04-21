<?php
use app\controllers\ProductController;
use app\Controllers\AuthController;
use App\Controllers\CartController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Controllers\OrderController;
use App\Controllers\UserController;
use App\Controllers\Admin\AdminProductController;

// $router is available here from index.php
//product handler
$router->post('/api/login', [AuthController::class, 'login']);
$router->post('/api/register', [AuthController::class, 'register']);
$router->get('/api/products', [App\Controllers\ProductController::class, 'index']);
$router->get('/api/product', [App\Controllers\ProductController::class, 'show']);
// cart handler
$router->middleware('/api/cart', AuthMiddleware::class);
$router->get('/api/cart', [CartController::class, 'index']);
$router->post('/api/cart', [CartController::class, 'store']);
// order handler
$router->middleware('/api/checkout', AuthMiddleware::class);
$router->post('/api/checkout', [OrderController::class, 'checkout']);
//admin
$router->middleware('/api/admin/product', AuthMiddleware::class);
$router->middleware('/api/admin/product', AdminMiddleware::class);
$router->post('/api/admin/product', [AdminProductController::class, 'addProduct']);
$router->put('/api/admin/product', [AdminProductController::class, 'update']);
$router->delete('/api/admin/product', [AdminProductController::class, 'delete']);
//user handler
$router->middleware('/api/profile', AuthMiddleware::class);
$router->get('/api/profile', [UserController::class, 'profile']);