<?php
// Uses $pdo from the parent scope (seed.php)

$sql = "INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES 
(1, 'Smartphone X', 'Latest smartphone with amazing features.', 799.99, 50, 'https://via.placeholder.com/150'),
(1, 'Wireless Headphones', 'Noise-cancelling wireless headphones.', 199.99, 100, 'https://via.placeholder.com/150'),
(2, 'Classic T-Shirt', '100% cotton casual t-shirt.', 19.99, 200, 'https://via.placeholder.com/150'),
(2, 'Denim Jeans', 'Stylish blue denim jeans.', 49.99, 150, 'https://via.placeholder.com/150'),
(3, 'Programming for Beginners', 'Learn the basics of coding.', 29.99, 80, 'https://via.placeholder.com/150')";

$pdo->exec($sql);
