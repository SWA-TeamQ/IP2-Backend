<?php
// 1. Common Headers (simple local setup)
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// App code lives under Ecomerece_Web_Backend, shared helpers live at the repo root.
$appBasePath = dirname(__DIR__);
$rootBasePath = realpath(__DIR__ . '/../../');

require_once $rootBasePath . '/utils/responses.php';

try {
    // 2. Load the Database Connection
    require_once $appBasePath . '/database/db.php';

    // 3. Load your Models and Repositories (So your routes can use them)
    require_once $appBasePath . '/models/User.model.php';
    require_once $appBasePath . '/repositories/UserRepository.php';

    // 4. Include route files.
    require_once $appBasePath . '/routes/auth.php';
    require_once $appBasePath . '/routes/api.php';

    // 5. Default 404 Response when no route matched.
    http_response_code(404);
    echo json_encode(app_error_response('NOT_FOUND', 'The requested endpoint does not exist.'));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(app_error_response('INTERNAL_SERVER_ERROR', 'Server error', array('hint' => $e->getMessage())));
}
