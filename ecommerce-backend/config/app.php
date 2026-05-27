<?php
return [
    'app_name' => 'ShopLight API',
    'base_url' => $_ENV['BASE_URL'] ?? 'http://localhost:8000/api',
    'jwt_secret' => $_ENV['JWT_SECRET'] ?? 'your-super-secret-key-here',
    'jwt_expiry' => 3600 * 24, // 24 hours
];