<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;

Env::load(__DIR__ . '/../.env');
// Debug
if (!isset($_ENV['DATABASE_URL'])) {
    echo "DEBUG: _ENV content: " . print_r($_ENV, true) . "\n";
}

try {
    $db = Database::getConnection();

    // 1. Run the main schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $db->exec($sql);
        echo "Executed schema.sql\n";
    }

    // 2. Run Seeders
    require_once __DIR__ . '/seeds/UserSeeder.php';
    \App\Database\Seeds\UserSeeder::run();

    echo "Database setup complete!\n";
} catch (Exception $e) {
    echo "Error during setup: " . $e->getMessage() . "\n";
}

