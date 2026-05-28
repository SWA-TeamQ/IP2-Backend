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

        $this->cartService->addToCart($request->userId, $productId, $quantity);
        return $this->success($response, null, 'Item added to cart');
    }

public function update(Request $request, Response $response, $id) {
        $data = $request->getBody();
        $quantity = $data['quantity'] ?? null;

        if ($quantity === null || $quantity < 1) {
            return $this->error($response, 'Valid quantity is required', 400);
        }

        // Clean: Middleware handles validation safely now!
        $this->cartService->updateItemQuantity($request->userId, (int)$id, (int)$quantity); 
        return $this->success($response, null, 'Cart updated successfully');
    }

public function remove(Request $request, Response $response, $id) {
        if (!$id) {
            return $this->error($response, 'Cart item ID is required', 400);
        }
        
        $userId = $request->userId;
        
        // Fallback safety layer
        if (!$userId) {
            $authHeader = $request->getHeader('Authorization');
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $decoded = \App\Helpers\JWTHelper::decodeToken($matches[1]);
                $userId = $decoded ? $decoded->sub : null;
            }
        }

        if (!$userId) {
            return $this->error($response, 'Unauthorized: Access denied', 401);
        }

        $this->cartService->removeItem((int)$id, $userId);
        return $this->success($response, null, 'Item removed from cart');
    }
    
}