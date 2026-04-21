<?php

require_once __DIR__ . '/../repositories/OrderRepository.php';
require_once __DIR__ . '/../repositories/CartRepository.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../../utils/responses.php';
require_once __DIR__ . '/../../utils/request.php';

class OrderController
{
    private $orderRepo;
    private $cartRepo;
    private $productRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
    }

    private function jsonResponse($payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($payload);
    }

    private function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getAuthenticatedUserId()
    {
        $this->ensureSessionStarted();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    }

    public function listOrders()
    {
        $userId = $this->getAuthenticatedUserId();
        if ($userId <= 0) {
            $this->jsonResponse(app_error_response('UNAUTHORIZED', 'Unauthorized'), 401);
            return;
        }

        $orders = $this->orderRepo->getOrdersByUserId($userId);
        $items = array();
        foreach ($orders as $order) {
            $items[] = $order->toArray();
        }

        $this->jsonResponse(app_success_response(array('items' => $items), array('total' => count($items))));
    }

    public function getOrder($orderId)
    {
        $userId = $this->getAuthenticatedUserId();
        if ($userId <= 0) {
            $this->jsonResponse(app_error_response('UNAUTHORIZED', 'Unauthorized'), 401);
            return;
        }

        $order = $this->orderRepo->getOrderById((int) $orderId);
        if (!$order || (int) $order->userId !== $userId) {
            $this->jsonResponse(app_error_response('NOT_FOUND', 'Order not found'), 404);
            return;
        }

        $this->jsonResponse(app_success_response(array('order' => $order->toArray())));
    }

    public function createOrder()
    {
        $userId = $this->getAuthenticatedUserId();
        if ($userId <= 0) {
            $this->jsonResponse(app_error_response('UNAUTHORIZED', 'Unauthorized'), 401);
            return;
        }

        $body = app_get_request_body();

        $items = array();
        if (isset($body['items']) && is_array($body['items'])) {
            $items = $body['items'];
        } else {
            $cart = $this->cartRepo->getOrCreateCartByUserId($userId);
            foreach ($cart->items as $item) {
                $items[] = array(
                    'productId' => $item->productId,
                    'quantity' => $item->quantity
                );
            }
        }

        if (empty($items)) {
            $this->jsonResponse(app_error_response('VALIDATION_ERROR', 'No order items provided'), 400);
            return;
        }

        $orderItems = array();
        $subtotal = 0;

        foreach ($items as $item) {
            $productId = isset($item['productId']) ? (int) $item['productId'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

            if ($productId <= 0 || $quantity <= 0) {
                $this->jsonResponse(app_error_response('VALIDATION_ERROR', 'Each item needs valid productId and quantity'), 400);
                return;
            }

            $product = $this->productRepo->getProductById($productId);
            if (!$product) {
                $this->jsonResponse(app_error_response('NOT_FOUND', 'Product not found: ' . $productId), 404);
                return;
            }

            $unitPrice = isset($product->details['price']) ? (float) $product->details['price'] : 0;
            $subtotal += $unitPrice * $quantity;

            $orderItems[] = array(
                'productId' => $productId,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice
            );
        }

        $shipping = isset($body['shipping']) ? (float) $body['shipping'] : 0;
        $tax = isset($body['tax']) ? (float) $body['tax'] : 0;

        $order = $this->orderRepo->createOrderWithItems(
            array(
                'userId' => $userId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $subtotal + $tax + $shipping
            ),
            $orderItems
        );

        $cart = $this->cartRepo->getCartByUserId($userId);
        if ($cart) {
            $this->cartRepo->clearCart($cart->id);
        }

        $this->jsonResponse(app_success_response(array('order' => $order->toArray())), 201);
    }
}
