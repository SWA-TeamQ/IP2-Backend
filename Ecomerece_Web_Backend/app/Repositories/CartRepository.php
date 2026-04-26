<?php
namespace App\Repositories;

use App\Entities\CartItem;
use PDO;

class CartRepository extends BaseRepository {
    public function findInCart(string $userId, string $productId): ?CartItem {
        $sql = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->query($sql, ['user_id' => $userId, 'product_id' => $productId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new CartItem($data) : null;
    }

    public function add(string $userId, string $productId, int $quantity = 1): void {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $this->query($sql, ['user_id' => $userId, 'product_id' => $productId, 'quantity' => $quantity]);
    }

    public function updateQuantity(int $cartId, int $quantity): void {
        $sql = "UPDATE cart SET quantity = :quantity WHERE id = :id";
        $this->query($sql, ['quantity' => $quantity, 'id' => $cartId]);
    }

    public function getByUser(string $userId): array {
        $sql = "SELECT c.*, p.name, p.price_cents, p.images 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id";
        $stmt = $this->query($sql, ['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove(int $cartId, string $userId): void {
        $sql = "DELETE FROM cart WHERE id = :id AND user_id = :user_id";
        $this->query($sql, ['id' => $cartId, 'user_id' => $userId]);
    }
}