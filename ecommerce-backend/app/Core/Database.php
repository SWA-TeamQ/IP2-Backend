<?php

namespace App\Core;

use PDO;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Fail fast: Check for required URL
            $dbUrl = $_ENV['DATABASE_URL'] ?? null;
            if (!$dbUrl) {
                throw new Exception("DATABASE_URL environment variable is missing.");
            }

            $parsed = parse_url($dbUrl);
            if (!$parsed || !isset($parsed['host'], $parsed['path'], $parsed['user'], $parsed['pass'])) {
                throw new Exception("DATABASE_URL is malformed.");
            }

            $host     = $parsed['host'];
            $port     = $parsed['port'] ?? 5432;
            $dbName   = ltrim($parsed['path'], '/');
            $username = $parsed['user'];
            $password = $parsed['pass'];

            // Construct DSN
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbName";
            
            // Collect options
            $options = [];
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $queryParts);
                
                // Handle Neon endpoint correctly
                if (isset($queryParts['options'])) {
                    $options[] = $queryParts['options'];
                }
                
                // Keep sslmode as a DSN parameter
                if (isset($queryParts['sslmode'])) {
                    $dsn .= ";sslmode=" . $queryParts['sslmode'];
                }
            }

            if (!empty($options)) {
                $dsn .= ";options=" . implode(' ', $options);
            }

            // Important: SSL options might need to be passed in driver_options if DSN fails
            $driverOptions = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$instance = new PDO($dsn, $username, $password, $driverOptions);
        }

        return self::$instance;
    }
}
