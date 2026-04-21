<?php

require 'db.php'; // your file

try {
    $pdo = db();

    echo "✅ Connected successfully<br>";

    $dialect = defined('DB_DIALECT') ? strtolower((string) DB_DIALECT) : 'unknown';

    // Dialect-specific version query.
    if ($dialect === 'sqlite') {
        $stmt = $pdo->query("SELECT sqlite_version() AS version");
    } else {
        $stmt = $pdo->query("SELECT version() AS version");
    }

    $version = $stmt->fetch();

    echo strtoupper($dialect) . " version: " . $version['version'];

} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}