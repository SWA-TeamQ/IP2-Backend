<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\CartService;

class CartController extends Controller {
    private CartService $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }

    public function index(Request $request, Response $response) {
        $items = $this->cartService->getItems($request->userId);
        return $this->success($response, $items);
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->error($response, 'Product ID is required', 400);
        }

        try {
            $this->cartService->addToCart($request->userId, $productId, $quantity);
            return $this->success($response, null, 'Item added to cart');
        } catch (\Exception $e) {
            return $this->error($response, $e->getMessage(), 500);
        }
    }
}