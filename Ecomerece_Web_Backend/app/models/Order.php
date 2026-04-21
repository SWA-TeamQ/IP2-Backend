<?php
namespace App\Models;

class Order extends Model {
    public function createOrder($userId, $total) {
        $this->query("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')", 
            [$userId, $total]);
        return $this->db->lastInsertId();
    }

    public function addItems($orderId, $items) {
        foreach ($items as $item) {
            $this->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)", 
                [$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }
    }
}