<?php
namespace App\Models;

class Order extends Model {
    public function create($data) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO orders (user_id, subtotal_cents, tax_cents, shipping_cents, total_cents, status, shipping_address) 
                    VALUES (:user_id, :subtotal_cents, :tax_cents, :shipping_cents, :total_cents, :status, :shipping_address)
                    RETURNING id";
            
            $orderId = $this->query($sql, [
                'user_id' => $data['user_id'],
                'subtotal_cents' => $data['subtotal_cents'],
                'tax_cents' => $data['tax_cents'],
                'shipping_cents' => $data['shipping_cents'],
                'total_cents' => $data['total_cents'],
                'status' => $data['status'] ?? 'pending',
                'shipping_address' => json_encode($data['shipping_address'])
            ])->fetchColumn();

            foreach ($data['items'] as $item) {
                $this->addItem($orderId, $item);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function addItem($orderId, $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_cents) 
                VALUES (:order_id, :product_id, :quantity, :price_cents)";
        $this->query($sql, [
            'order_id' => $orderId,
            'product_id' => $item['productId'],
            'quantity' => $item['quantity'],
            'price_cents' => $item['price'] // price at time of purchase
        ]);
    }

    public function findByUser($userId) {
        return $this->query("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC", ['user_id' => $userId])->fetchAll();
    }

    public function findAll() {
        return $this->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    }
}