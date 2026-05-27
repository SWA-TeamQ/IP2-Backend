<?php
namespace App\Repositories;

use App\Entities\Order;
use PDO;
use Exception;

class OrderRepository extends BaseRepository {
    public function create(Order $order): string {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO orders (user_id, subtotal_cents, tax_cents, shipping_cents, total_cents, status, shipping_address) 
                    VALUES (:user_id, :subtotal, :tax, :shipping, :total, :status, :address)
                    RETURNING id";
            
            $stmt = $this->query($sql, [
                'user_id' => $order->getUserId(),
                'subtotal' => $order->getSubtotalCents(),
                'tax' => $order->getTaxCents(),
                'shipping' => $order->getShippingCents(),
                'total' => $order->getTotalCents(),
                'status' => $order->getStatus(),
                'address' => json_encode($order->getShippingAddress())
            ]);
            
            $orderId = $stmt->fetchColumn();

            // Insert Items
            foreach ($order->getItems() as $item) {
                $this->addItem($orderId, $item);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function addItem(string $orderId, array $item): void {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_cents) 
                VALUES (:order_id, :product_id, :quantity, :price_cents)";
        
        $priceCents = $item['unitPriceCents'] ?? $item['unit_price_cents'] ?? (isset($item['price']) ? (int)($item['price'] * 100) : 0);

        $this->query($sql, [
            'order_id' => $orderId,
            'product_id' => $item['productId'],
            'quantity' => $item['quantity'],
            'price_cents' => $priceCents
        ]);

        // Decrement stock
        $updateStockSql = "UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :product_id";
        $this->query($updateStockSql, [
            'quantity' => $item['quantity'],
            'product_id' => $item['productId']
        ]);
    }

    public function findByUser(string $userId): array {
        $stmt = $this->query("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC", ['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Order($data), $results);
    }

    public function findAll(): array {
        $stmt = $this->query("SELECT * FROM orders ORDER BY created_at DESC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Order($data), $results);
    }

    public function hasUserPurchasedProduct(string $userId, string $productId): bool {
        $sql = "SELECT COUNT(*) FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                WHERE o.user_id = :user_id AND oi.product_id = :product_id AND o.status = 'completed'";
        $stmt = $this->query($sql, ['user_id' => $userId, 'product_id' => $productId]);
        return $stmt->fetchColumn() > 0;
    }

    public function updateStatus(string $id, string $status): bool {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->query($sql, ['status' => $status, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }
}