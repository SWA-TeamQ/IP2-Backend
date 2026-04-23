<?php
namespace App\Models;

class Review extends Model {
    public function create($productId, $userId, $data) {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) 
                VALUES (:product_id, :user_id, :rating, :comment)
                RETURNING id";
        
        return $this->query($sql, [
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $data['rating'],
            'comment' => $data['comment']
        ])->fetchColumn();
    }

    public function delete($id) {
        return $this->query("DELETE FROM reviews WHERE id = :id", ['id' => $id]);
    }

    public function getByProduct($productId) {
        return $this->query("SELECT r.*, u.first_name, u.last_name, u.avatar_url 
                             FROM reviews r 
                             LEFT JOIN users u ON r.user_id = u.id 
                             WHERE r.product_id = :product_id 
                             ORDER BY r.created_at DESC", 
                             ['product_id' => $productId])->fetchAll();
    }
}