<?php

// Keep DB config in one place so changing environments is easy.
if (!defined('DB_HOST')) {
	define('DB_HOST', getenv("DB_HOST"));
	define('DB_NAME', getenv("DB_NAME"));
	define('DB_USER', getenv("DB_USERNAME"));
	define('DB_PASS', getenv("DB_PASSWORD"));
	define('DB_CHARSET', 'utf8mb4');
}

function db()
{
	static $pdo = null;

	// Reuse one PDO connection per request.
	if ($pdo === null) {
		$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
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

