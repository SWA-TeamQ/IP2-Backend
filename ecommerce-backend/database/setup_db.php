<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;

Env::load(__DIR__ . '/../.env');

try {
    $db = Database::getConnection();
    
    // 1. Run all migration files
    $migrationsPath = __DIR__ . '/migrations';
    $migrationFiles = glob($migrationsPath . '/*.sql');
    sort($migrationFiles); // Ensure migrations run in order

    foreach ($migrationFiles as $migrationFile) {
        $sql = file_get_contents($migrationFile);
        $db->exec($sql);
        echo "Executed migration: " . basename($migrationFile) . "\n";
    }

    echo "Database setup complete!\n";
} catch (Exception $e) {
    echo "Error during setup: " . $e->getMessage() . "\n";
}