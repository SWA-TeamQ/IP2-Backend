<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dbUrl = parse_url($_ENV['DATABASE_URL']);

                $driver   = "postgresql";
                $host     = $dbUrl['host'];
                $port     = $dbUrl['port'];
                $dbName   = ltrim($dbUrl['path'], '/');
                $username = $dbUrl['user'];
                $password = $dbUrl['pass'];

                $dsn = "$driver:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (\Throwable $e) {
                error_log("Critical Database Error: " . $e->getMessage());
                die("A connection error occurred. Please check system logs.");
            }
        }

        return self::$instance;
    }
}
