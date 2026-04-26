<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\OrderService;
use App\Helpers\Validator;

class OrderController extends Controller {
    private OrderService $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function create(Request $request, Response $response) {
        $data = $request->getBody();
        $userId = $request->userId; // From IsAuthenticated

        $errors = Validator::validate($data, [
            'items' => 'required',
            'shippingAddress' => 'required',
            'subtotal' => 'required',
            'tax' => 'required',
            'shipping' => 'required',
            'total' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        $orderData = [
            'user_id' => $userId,
            'items' => $data['items'],
            'shipping_address' => $data['shippingAddress'],
            'subtotal_cents' => (int)($data['subtotal'] * 100),
            'tax_cents' => (int)($data['tax'] * 100),
            'shipping_cents' => (int)($data['shipping'] * 100),
            'total_cents' => (int)($data['total'] * 100),
        ];

        $orderId = $this->orderService->createOrder($orderData);
        return $this->success($response, ['id' => $orderId], 'Order created successfully', 201);
    }

    public function index(Request $request, Response $response) {
        $userId = $request->userId;
        $orders = $this->orderService->getUserOrders($userId);
        return $this->success($response, $orders);
    }

    public function all(Request $request, Response $response) {
        $orders = $this->orderService->getAllOrders();
        return $this->success($response, $orders);
    }
}