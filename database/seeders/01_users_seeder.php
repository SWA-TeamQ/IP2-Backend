<?php
// Uses $pdo from the parent scope (seed.php)

$password = password_hash('password123', PASSWORD_BCRYPT);
$sql = "INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$password', 'admin'),
('John Doe', 'john@example.com', '$password', 'customer')";

$pdo->exec($sql);
