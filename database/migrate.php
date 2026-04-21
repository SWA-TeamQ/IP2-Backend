<?php
require_once __DIR__ . '/../config/database.php';

echo "Starting Database Migrations...\n";

// Disable foreign key checks before dropping tables
try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $migrationFiles = glob(__DIR__ . '/migrations/*.php');
    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        echo "Running migration: $filename\n";

        try {
            $sql = require $file;

            if (is_string($sql) && !empty(trim($sql))) {
                // We use exec here. Note: for multiple statements, it depends on driver config.
                // Most MySQL setups allow multiple statements in one exec() call.
                $pdo->exec($sql);
                echo "Successfully migrated $filename\n";
            } else {
                echo "Warning: Migration $filename did not return a valid SQL string.\n";
            }
        } catch (PDOException $e) {
            echo "Error in migration $filename: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "All migrations applied successfully!\n";
} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
    exit(1);
}
