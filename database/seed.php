<?php
require_once __DIR__ . '/../config/database.php';

echo "Starting Database Seeding...\n";

// Disable foreign key checks to prevent seeder issues with relations
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

// We specify order explicitly here or ensure naming conventions (01_, 02_) handle it
$seedFiles = glob(__DIR__ . '/seeders/*.php');
foreach ($seedFiles as $file) {
    echo "Running seeder: " . basename($file) . "\n";
    try {
        // We will include the file, and assume the file directly uses the $pdo object
        require $file;
        echo "Successfully seeded " . basename($file) . "\n";
    } catch (PDOException $e) {
        echo "Error in seeder " . basename($file) . ": " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Re-enable foreign key checks
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "All seeders executed successfully!\n";
