<?php
require_once 'database.php';

echo json_encode([
    "status" => "success",
    "message" => "Database connected successfully"
]);
