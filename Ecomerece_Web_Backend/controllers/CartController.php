<?php

require_once __DIR__ . '/../repositories/CartRepository.php';
require_once __DIR__ . '/../utils/responses.php';
require_once __DIR__ . '/../utils/request.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';

class CartController
{
    private $cartRepo;
    private $productRepo;
    private $logFile;

    public function __construct()
    {
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
        $this->logFile = __DIR__ . '/../../logs/cart.log';
    }

    private function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // GET /api/cart
    public function getCart()
    {
        $this->ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(app_error_response('UNAUTHORIZED', 'Unauthorized'));
            return;
        }

        $cart = $this->cartRepo->getOrCreateCartByUserId($_SESSION['user_id']);
        http_response_code(200);
        echo json_encode(app_success_response($cart->toArray()));
        $this->logAction('getCart', $_SESSION['user_id']);
    }

    // POST /api/cart/items
    public function addOrUpdateItem()
    {
        $this->ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(app_error_response('UNAUTHORIZED', 'Unauthorized'));
            return;
        }

        $data = app_get_request_body();

        if (empty($data['productId']) || !isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(app_error_response('VALIDATION_ERROR', 'productId and quantity are required'));
            return;
        }

        // Validate quantity
        if (!is_numeric($data['quantity']) || (int)$data['quantity'] < 1) {
            http_response_code(400);
            echo json_encode(app_error_response('VALIDATION_ERROR', 'Quantity must be a positive integer'));
            return;
        }

        // Validate product existence
        $product = $this->productRepo->getProductById($data['productId']);
        if (!$product) {
            http_response_code(404);
            echo json_encode(app_error_response('NOT_FOUND', 'Product not found'));
            return;
        }

        // Concurrency: lock session for cart update
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        session_start();
        $cart = $this->cartRepo->getOrCreateCartByUserId($_SESSION['user_id']);
        $this->cartRepo->addItem($cart->id, $data['productId'], (int)$data['quantity']);
        $cart = $this->cartRepo->getCartById($cart->id);
        http_response_code(200);
        echo json_encode(app_success_response($cart->toArray()));
        $this->logAction('addOrUpdateItem', $_SESSION['user_id'], $data);
    }

    // DELETE /api/cart/items/:productId
    public function removeItem($productId)
    {
        $this->ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(app_error_response('UNAUTHORIZED', 'Unauthorized'));
            return;
        }

        // Validate product existence
        $product = $this->productRepo->getProductById($productId);
        if (!$product) {
            http_response_code(404);
            echo json_encode(app_error_response('NOT_FOUND', 'Product not found'));
            return;
        }

        // Concurrency: lock session for cart update
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        session_start();
        $cart = $this->cartRepo->getOrCreateCartByUserId($_SESSION['user_id']);
        $this->cartRepo->removeItem($cart->id, $productId);
        $cart = $this->cartRepo->getCartById($cart->id);
        http_response_code(200);
        echo json_encode(app_success_response($cart->toArray()));
        $this->logAction('removeItem', $_SESSION['user_id'], ['productId' => $productId]);
    }

    // Log cart actions to a file
    private function logAction($action, $userId, $data = array())
    {
        $entry = date('Y-m-d H:i:s') . " | user: $userId | action: $action | data: " . json_encode($data) . "\n";
        file_put_contents($this->logFile, $entry, FILE_APPEND);
    }
}
