<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\OrderService;

class AdminOrderController extends Controller {
    private OrderService $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function index(Request $request, Response $response) {
        $limit = (int)($request->getBody()['limit'] ?? 10);
        $offset = (int)($request->getBody()['offset'] ?? 0);

        $orders = $this->orderService->getAdminOrders($limit, $offset);
        return $this->success($response, $orders);
    }

    public function updateStatus(Request $request, Response $response, $id) {
        $data = $request->getBody();
        if (!isset($data['status'])) {
            return $this->error($response, 'Status is required', 400);
        }

        $updated = $this->orderService->updateOrderStatus($id, $data['status']);
        if (!$updated) {
            return $this->error($response, 'Failed to update order status', 404);
        }

        return $this->success($response, null, 'Order status updated successfully');
    }
}