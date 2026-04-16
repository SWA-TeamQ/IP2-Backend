<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$normalizedUri = rtrim($uri, '/');
if ($normalizedUri === '') {
    $normalizedUri = '/';
}

function route_api_ends_with($uri, $suffix) {
    if (function_exists('str_ends_with')) {
        return str_ends_with($uri, $suffix);
    }
    $len = strlen($suffix);
    return $len === 0 || (substr($uri, -$len) === $suffix);
}

// Route: GET /api/health
if (route_api_ends_with($normalizedUri, '/api/health') && $method === 'GET') {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "API working"
    ]);
    exit;
}

// Route: GET /api
if (route_api_ends_with($normalizedUri, '/api') && $method === 'GET') {
    http_response_code(200);
    echo json_encode([
        "status" => "OK",
        "message" => "API is running"
    ]);
    exit;
}

