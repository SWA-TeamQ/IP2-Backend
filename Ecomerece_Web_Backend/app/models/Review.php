<?php
namespace App\Models;

class Review extends Model {
    public function getByProduct($productId) {
        return $this->query("SELECT r.*, u.username FROM reviews r 
                             JOIN users u ON r.user_id = u.id 
                             WHERE r.product_id = ?", [$productId])->fetchAll();
    }
}