<?php
// Simple test script for cart endpoints (run via CLI or browser)

$baseUrl = 'http://localhost/E_commerce_app/IP2-Backend/Ecomerece_Web_Backend/public';

function callApi($method, $url, $data = null, $cookie = null) {
    $opts = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json" . ($cookie ? "\r\nCookie: $cookie" : ''),
            'ignore_errors' => true
        ]
    ];
    if ($data) {
        $opts['http']['content'] = json_encode($data);
    }
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    return [$http_response_header, $result];
}

// Example usage:
// 1. Login to get session cookie (replace with real credentials)
list($headers, $body) = callApi('POST', "$baseUrl/api/auth/login", ["email" => "test@example.com", "password" => "password"]);
$cookie = '';
foreach ($headers as $h) {
    if (stripos($h, 'Set-Cookie:') !== false) {
        $cookie = trim(explode(':', $h, 2)[1]);
        break;
    }
}

echo "Login response: $body\n";

// 2. Add item to cart
list($headers, $body) = callApi('POST', "$baseUrl/api/cart/items", ["productId" => 1, "quantity" => 2], $cookie);
echo "Add item response: $body\n";

// 3. Get cart
list($headers, $body) = callApi('GET', "$baseUrl/api/cart", null, $cookie);
echo "Get cart response: $body\n";

// 4. Remove item from cart
list($headers, $body) = callApi('DELETE', "$baseUrl/api/cart/items/1", null, $cookie);
echo "Remove item response: $body\n";
