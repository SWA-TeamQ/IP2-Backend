<?php
namespace App\Services;

use App\Repositories\OrderRepository;
use App\Entities\Order;

class OrderService {
    private OrderRepository $orderRepo;

    public function __construct() {
        $this->orderRepo = new OrderRepository();
    }

    public function createOrder(array $data) {
        $order = new Order($data);
        return $this->orderRepo->create($order);
    }

    public function getUserOrders(string $userId) {
        $orders = $this->orderRepo->findByUser($userId);
        return array_map(fn($o) => $o->toArray(), $orders);
    }

    public function getAllOrders() {
        $orders = $this->orderRepo->findAll();
        return array_map(fn($o) => $o->toArray(), $orders);
    }

    public function getAdminOrders(int $limit = 10, int $offset = 0) {
        return $this->orderRepo->getAllOrdersWithUsers($limit, $offset);
    }

    public function updateOrderStatus(string $id, string $status) {
        return $this->orderRepo->updateOrderStatus($id, $status);
    }

    public function hasUserPurchasedProduct(string $userId, string $productId) {
        return $this->orderRepo->hasUserPurchasedProduct($userId, $productId);
    }
}