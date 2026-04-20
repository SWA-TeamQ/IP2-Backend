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

require_once __DIR__ . '/../../utils/responses.php';

try {
    // 2. Load the Database Connection
    require_once __DIR__ . '/../database/db.php';

    // 3. Load your Models and Repositories (So your routes can use them)
    require_once __DIR__ . '/../models/User.model.php';
    require_once __DIR__ . '/../repositories/UserRepository.php';

    // 4. Include route files.
    require_once __DIR__ . '/../routes/auth.php';
    require_once __DIR__ . '/../routes/api.php';

    // 5. Default 404 Response when no route matched.
    http_response_code(404);
    echo json_encode(app_error_response('NOT_FOUND', 'The requested endpoint does not exist.'));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(app_error_response('INTERNAL_SERVER_ERROR', 'Server error', array('hint' => $e->getMessage())));
}
