<?php
// Always return JSON
header("Content-Type: application/json");

// Load CORS first
require_once __DIR__ . '/../middleware/cors.php';

// Load DB & env
require_once __DIR__ . '/../config/database.php';

// Load Router
require_once __DIR__ . '/../core/Router.php';

// Initialize router
$router = new Router();

// Load routes
require_once __DIR__ . '/../routes/api.php';

// Handle request
$router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
