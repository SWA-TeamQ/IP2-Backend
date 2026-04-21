<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;

$db = Database::getConnection();

$products = [
    ['Smartphone X', 'Latest model with 5G', 799.99, 50, 'https://placehold.co/400x400?text=Phone'],
    ['Laptop Pro', '16GB RAM, 512GB SSD', 1200.00, 20, 'https://placehold.co/400x400?text=Laptop'],
    ['Wireless Buds', 'Noise-canceling audio', 150.00, 100, 'https://placehold.co/400x400?text=Buds'],
];

foreach ($products as $p) {
    $stmt = $db->prepare("INSERT INTO products (name, description, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute($p);
}

echo "Database Seeded Successfully!";