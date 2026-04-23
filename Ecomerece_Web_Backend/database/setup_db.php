<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;

Env::load(__DIR__ . '/../.env');

try {
    $db = Database::getConnection();
    
    // 1. Run the main schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $db->exec($sql);
        echo "Executed schema.sql\n";
    }

    // 2. Run migrations if any
    $migrations = glob(__DIR__ . '/migrations/*.sql');
    foreach ($migrations as $file) {
        $sql = file_get_contents($file);
        $db->exec($sql);
        echo "Executed migration: " . basename($file) . "\n";
    }

    echo "Database setup complete!\n";
} catch (Exception $e) {
    echo "Error during setup: " . $e->getMessage() . "\n";
}