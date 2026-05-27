<?php
namespace App\Repositories;

use PDO;

class AdminRepository extends BaseRepository {
    public function getSummaryStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_cents), 0) as total_revenue,
                    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_customers
                FROM orders";
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentSales(): array {
        $sql = "SELECT o.id, o.total_cents, o.status, o.created_at, u.first_name, u.last_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT 5";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts(): array {
        $sql = "SELECT p.name, SUM(oi.quantity) as units_sold, SUM(oi.price_cents * oi.quantity) as revenue
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                GROUP BY p.id, p.name
                ORDER BY units_sold DESC LIMIT 5";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockAlerts(): array {
        $sql = "SELECT name, stock_quantity 
                FROM products 
                WHERE stock_quantity < 10 
                ORDER BY stock_quantity ASC LIMIT 10";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}