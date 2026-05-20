<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Helpers\Validator;

class OrderController extends Controller {
    private OrderService $orderService;
    private PaymentService $paymentService;

    public function __construct() {
        $this->orderService = new OrderService();
        $this->paymentService = new PaymentService();
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

        // Process Payment if paymentDetails exist
        $status = 'pending';
        if (!empty($data['paymentDetails'])) {
            $paymentResult = $this->paymentService->process($data['total'], $data['paymentDetails']);
            if ($paymentResult['status'] === 'failed') {
                return $this->error($response, 'Payment Failed: ' . $paymentResult['message'], 400);
            }
            if ($paymentResult['status'] === 'success') {
                $status = 'completed'; // or 'paid'
            }
        }

        $orderData = [
            'user_id' => $userId,
            'items' => $data['items'],
            'shipping_address' => $data['shippingAddress'],
            'subtotal_cents' => (int)($data['subtotal'] * 100),
            'tax_cents' => (int)($data['tax'] * 100),
            'shipping_cents' => (int)($data['shipping'] * 100),
            'total_cents' => (int)($data['total'] * 100),
            'status' => $status
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

    public function updateStatus(Request $request, Response $response, $id) {
        $data = $request->getBody();
        if (empty($data['status'])) {
            return $this->error($response, 'Status is required', 400);
        }

        $success = $this->orderService->updateStatus($id, $data['status']);
        if (!$success) {
            return $this->error($response, 'Failed to update order status', 500);
        }

        return $this->success($response, null, 'Order status updated successfully');
    }
}