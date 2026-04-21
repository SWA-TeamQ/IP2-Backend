<?php

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

$appBasePath = dirname(__DIR__);

require_once $appBasePath . '/utils/responses.php';

$status = 'ok';
$checks = array(
    'app' => true,
    'time' => date('c')
);

try {
    require_once __DIR__ . '/../database/db.php';
    $db = db();
    $db->query('SELECT 1');
    $checks['database'] = true;
    $checks['database_type'] = 'sqlite';
} catch (Throwable $e) {
    $status = 'degraded';
    $checks['database'] = false;
    $checks['database_error'] = $e->getMessage();
}

http_response_code($status === 'ok' ? 200 : 503);
echo json_encode(app_success_response(array(
    'status' => $status,
    'checks' => $checks
)));
