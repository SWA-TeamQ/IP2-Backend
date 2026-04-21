<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\OrderService;

class OrderController {
    private OrderService $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function checkout(Request $request, Response $response) {
       try {
        $data = $request->getBody(); // Get card_number, etc. from React
        $result = $this->orderService->placeOrder($request->userId, $data);
        
        return $response->json([
            'message' => 'Order placed successfully',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return $response->json(['error' => $e->getMessage()], 400);
    }
    }
}