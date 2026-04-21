<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use Exception;

class OrderService {
    private Order $orderModel;
    private Cart $cartModel;
    private Product $productModel;
    private PaymentService $paymentService;

    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->paymentService = new PaymentService();
    }

    public function placeOrder($userId, $paymentDetails) {
        $cartItems = $this->cartModel->getByUser($userId);
        
        if (empty($cartItems)) {
            throw new Exception("Cart is empty");
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $paymentResult = $this->paymentService->process($total, $paymentDetails);
    
        if ($paymentResult['status'] !== 'success') {
            throw new \Exception("Payment failed: " . $paymentResult['message']);
        }
        // Create the main order record
        $orderId = $this->orderModel->createOrder($userId, $total);

        // Move items from cart to order_items and update stock
        foreach ($cartItems as $item) {
            // 1. Add to order items
            $this->orderModel->addItems($orderId, [$item]);
            
            // 2. Reduce stock in Product table
            $product = $this->productModel->find($item['product_id']);
            $newStock = $product['stock_quantity'] - $item['quantity'];
            $this->productModel->updateStock($item['product_id'], $newStock);
        }

        // 3. Clear the user's cart
        $this->cartModel->clear($userId);

        return ['order_id' => $orderId,'transaction_id' => $paymentResult['transaction_id'] ,'total' => $total];
    }
}