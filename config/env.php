<?php
function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new RuntimeException(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || preg_match('/^\s*#/', $line)) continue;
        if (strpos($line, '=') === false) continue;

        list($key, $value) = explode('=', $line, 2);
        $value = trim($value, " \t\n\r\0\x0B\""); // remove whitespace and quotes
        $_ENV[trim($key)] = $value;
    }
}
