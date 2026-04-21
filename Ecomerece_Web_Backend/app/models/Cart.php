<?php
namespace App\Models;

class Cart extends Model {
    public function getByUser($userId) {
        $sql = "SELECT c.*, p.name, p.price FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        return $this->query($sql, [$userId])->fetchAll();
    }

    public function updateQuantity($cartId, $quantity) {
        return $this->query("UPDATE cart SET quantity = ? WHERE id = ?", [$quantity, $cartId]);
    }
    public function findInCart($userId, $productId) {
        return $this->query("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $productId])->fetch();
    }

    public function add($userId, $productId, $quantity) {
        return $this->query("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", [$userId, $productId, $quantity]);
    }

    public function remove($cartId, $userId) {
        return $this->query("DELETE FROM cart WHERE id = ? AND user_id = ?", [$cartId, $userId]);
    }
    public function clear($userId) {
    return $this->query("DELETE FROM cart WHERE user_id = ?", [$userId]);
    }
}