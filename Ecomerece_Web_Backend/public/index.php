
<?php
// 1. Common Headers (CORS) - This allows your Frontend to talk to this Backend
header("Access-Control-Allow-Origin: *"); // In production, change * to your frontend URL
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle pre-flight 'OPTIONS' requests (Required for browsers)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Load the Database Connection
// Ensure the path to your db.php is correct!
require_once __DIR__ . '/../database/db.php';

// 3. Load your Models and Repositories (So your routes can use them)
require_once __DIR__ . '/../models/User.model.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

// 4. Include your Route Files
// As you and your team build more, you'll add 'products.php', 'cart.php', etc.
require_once __DIR__ . '/../routes/auth.php';


// 5. Default 404 Response
// If none of the routes above 'exit' the script, it means the URL was wrong.
http_response_code(404);
echo json_encode([
    "error" => [
        "code" => "NOT_FOUND",
        "message" => "The requested endpoint does not exist."
    ]
]);