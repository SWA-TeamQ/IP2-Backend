<?php
require_once __DIR__ . '/../config/database.php';

// 3. Load your Models and Repositories (So your routes can use them)
require_once __DIR__ . '/../models/User.model.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

// 4. Include your Route Files
// As you and your team build more, you'll add 'products.php', 'cart.php', etc.
require_once __DIR__ . '/../routes/auth.php';
require_once __DIR__ . '/../routes/api.php';


// 5. Default 404 Response
// If none of the routes above 'exit' the script, it means the URL was wrong.
http_response_code(404);
echo json_encode([
    "error" => [
        "code" => "NOT_FOUND",
        "message" => "The requested endpoint does not exist."
    ]
]);