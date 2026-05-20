<?php
namespace App\Services;

use App\Repositories\CartRepository;

class CartService {
    private CartRepository $cartRepo;

    public function __construct() {
        $this->cartRepo = new CartRepository();
    }

    public function addToCart(string $userId, string $productId, int $quantity = 1) {
        $existing = $this->cartRepo->findInCart($userId, $productId);

        if ($existing) {
            $newQuantity = $existing->getQuantity() + $quantity;
            return $this->cartRepo->updateQuantity($existing->getId(), $newQuantity);
        }

        return $this->cartRepo->add($userId, $productId, $quantity);
    }

    public function getItems(string $userId) {
        return $this->cartRepo->getByUser($userId);
    }

    public function removeItem(int $cartId, string $userId) {
        return $this->cartRepo->remove($cartId, $userId);
    }
}