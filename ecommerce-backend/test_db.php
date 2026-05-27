<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;
Env::load(__DIR__ . '/.env');

use App\Core\Database;

try {
    echo "Connecting to database...\n";
    $db = Database::getConnection();
    $stmt = $db->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "Connected successfully!\n";
    echo "PostgreSQL version: $version\n";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
