<?php
return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '5432',
    'db_name' => $_ENV['DB_NAME'] ?? 'shoplight_db',
    'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
];