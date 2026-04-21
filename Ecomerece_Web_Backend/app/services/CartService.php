<?php
namespace App\Services;

use App\Models\Cart;

class CartService {
    private Cart $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if item already exists in user's cart
        $existing = $this->cartModel->findInCart($userId, $productId);

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->cartModel->updateQuantity($existing['id'], $newQuantity);
        }

        return $this->cartModel->add($userId, $productId, $quantity);
    }

    public function getItems($userId) {
        return $this->cartModel->getByUser($userId);
    }

    public function removeItem($cartId, $userId) {
        // We pass userId to ensure people can't delete other people's cart items
        return $this->cartModel->remove($cartId, $userId);
    }
}