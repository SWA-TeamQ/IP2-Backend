<?php

function env_value($key, $default = null)
{
	$value = getenv($key);
	if ($value !== false && $value !== '') {
		return $value;
	}

	static $envFile = null;
	if ($envFile === null) {
		$envPath = __DIR__ . '/../.env';
		$envFile = file_exists($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_RAW) : array();
	}

	if (isset($envFile[$key])) {
		return trim(trim((string) $envFile[$key]), "\"'");
	}

	return $default;
}

// Keep DB config in one place so changing environments is easy.
if (!defined('DB_HOST')) {
	define('DB_DIALECT', env_value('DB_DIALECT', 'mysql'));
	define('DB_HOST', env_value('DB_HOST', '127.0.0.1'));
	define('DB_NAME', env_value('DB_NAME', 'shoplightdb'));
	define('DB_USER', env_value('DB_USERNAME', 'root'));
	define('DB_PASS', env_value('DB_PASSWORD', ''));
	define('DB_PORT', env_value('DB_PORT', '3306'));
	define('DB_CHARSET', env_value('DB_CHARSET', 'utf8mb4'));
}

function db()
{
    static $pdo = null;

	// Reuse one PDO connection per request.
	if ($pdo === null) {
		$dsn = DB_DIALECT . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false
		);

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}

function db_query($sql, $params = array())
{
    // Main helper for prepared queries.
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch_all($sql, $params = array())
{
    return db_query($sql, $params)->fetchAll();
}

function db_fetch_one($sql, $params = array())
{
    $result = db_query($sql, $params)->fetch();
    return $result ? $result : null;
}

function db_execute($sql, $params = array())
{
    return db_query($sql, $params)->rowCount();
}

function db_last_insert_id()
{
    return db()->lastInsertId();
}