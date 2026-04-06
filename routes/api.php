<?php
$router->get('/', function () {
    http_response_code(200);
    echo json_encode([
        "status" => "OK",
        "message" => "API is running"
    ]);
});

$router->get('/health', function () {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "API working"
    ]);
});
