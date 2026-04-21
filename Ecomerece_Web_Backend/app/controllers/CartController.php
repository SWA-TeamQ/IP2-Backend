<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\CartService;

class CartController {
    private CartService $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }

    public function index(Request $request, Response $response) {
        $items = $this->cartService->getItems($request->userId);
        return $response->json($items);
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $response->json(['error' => 'Product ID is required'], 400);
        }

        $this->cartService->addToCart($request->userId, $productId, $quantity);
        return $response->json(['message' => 'Item added to cart']);
    }
}