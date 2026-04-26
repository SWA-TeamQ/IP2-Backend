<?php
namespace App\Repositories;

use App\Entities\Review;
use PDO;

class ReviewRepository extends BaseRepository {
    public function findByProduct(string $productId): array {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.avatar_url 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = :product_id 
                ORDER BY r.created_at DESC";
        $stmt = $this->query($sql, ['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(Review $review): string {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) 
                VALUES (:product_id, :user_id, :rating, :comment)
                RETURNING id";
        
        $stmt = $this->query($sql, [
            'product_id' => $review->getProductId(),
            'user_id' => $review->getUserId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment()
        ]);
        
        return $stmt->fetchColumn();
    }

    public function delete(string $id): void {
        $this->query("DELETE FROM reviews WHERE id = :id", ['id' => $id]);
    }
}