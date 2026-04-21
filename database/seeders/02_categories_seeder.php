<?php
// Uses $pdo from the parent scope (seed.php)

$sql = "INSERT INTO categories (name, description) VALUES 
('Electronics', 'Gadgets and electronic devices.'),
('Clothing', 'Apparel for men and women.'),
('Books', 'Physical and electronic books.')";

$pdo->exec($sql);
