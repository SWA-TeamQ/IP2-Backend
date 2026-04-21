<?php
// Always return JSON
header("Content-Type: application/json");

// Define a Base Path to avoid repeating __DIR__ . '/..'
$basePath = realpath(__DIR__ . '/../');

// Load CORS first
require_once $basePath . '/middleware/cors.php';

// Load DB & env
require_once $basePath . '/config/database.php';

// Load Router
require_once $basePath . '/core/Router.php';

// Initialize router
$router = new Router();

// Load routes
require_once $basePath . '/routes/api.php';

// Handle request
$router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
