
<?php
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle pre-flight 'OPTIONS' requests (Required for browsers)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load the Database Connection
require_once __DIR__ . '/../database/db.php';

// Load Models and Repositories
require_once __DIR__ . '/../models/User.model.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

// Includes  your Route Files
require_once __DIR__ . '/../routes/auth.php';
require_once __DIR__ . '/../routes/api.php';


// Default 404 Response
// If none of the routes above 'exit' the script, it means the URL was wrong.
http_response_code(404);
echo json_encode([
    "error" => [
        "code" => "NOT_FOUND",
        "message" => "The requested endpoint does not exist."
    ]
]);