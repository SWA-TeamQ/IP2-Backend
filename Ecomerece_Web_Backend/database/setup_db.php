<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    $files = glob(__DIR__ . '/migrations/*.sql');
    
    foreach ($files as $file) {
        $sql = file_get_contents($file);
        $db->exec($sql);
        echo "Executed: " . basename($file) . "\n";
    }
    echo "Database setup complete!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}