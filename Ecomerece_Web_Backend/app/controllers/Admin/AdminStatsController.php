<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Order;
use App\Models\Product;

class AdminStatsController extends Controller {
    private Order $orderModel;
    private Product $productModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function index(Request $request, Response $response) {
        try {
            $stats = [
                'summary' => $this->getSummaryStats(),
                'recentSales' => $this->getRecentSales(),
                'topProducts' => $this->getTopSellingProducts(),
                'stockAlerts' => $this->getStockAlerts()
            ];

            return $this->success($response, $stats);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to fetch dashboard stats: ' . $e->getMessage(), 500);
        }
    }

    private function getSummaryStats() {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_cents), 0) as total_revenue,
                    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_customers
                FROM orders";
        return $this->orderModel->query($sql)->fetch();
    }

    private function getRecentSales() {
        $sql = "SELECT o.id, o.total_cents, o.status, o.created_at, u.first_name, u.last_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT 5";
        return $this->orderModel->query($sql)->fetchAll();
    }

    private function getTopSellingProducts() {
        $sql = "SELECT p.name, SUM(oi.quantity) as units_sold, SUM(oi.price_cents * oi.quantity) as revenue
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                GROUP BY p.id, p.name
                ORDER BY units_sold DESC LIMIT 5";
        return $this->productModel->query($sql)->fetchAll();
    }

    private function getStockAlerts() {
        $sql = "SELECT name, stock_quantity 
                FROM products 
                WHERE stock_quantity < 10 
                ORDER BY stock_quantity ASC LIMIT 10";
        return $this->productModel->query($sql)->fetchAll();
    }
}
