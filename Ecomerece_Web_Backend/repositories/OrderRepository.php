<?php
// Define a Base Path to avoid repeating __DIR__ . '/..'
$basePath = realpath(__DIR__ . '/../');

require_once $basePath . '/database/db.php';
require_once $basePath . '/models/Order.model.php';
require_once $basePath . '/models/OrderItem.model.php';

class OrderRepository
{
	// Get all orders for one user with items attached.
	public function getOrdersByUserId($userId)
	{
		$rows = db_fetch_all(
			"SELECT id, user_id AS userId, status, subtotal, tax, shipping, total, created_at AS createdAt, updated_at AS updatedAt
			 FROM orders
			 WHERE user_id = :userId
			 ORDER BY id DESC",
			array(':userId' => $userId)
		);

		$orders = array();
		foreach ($rows as $row) {
			$order = new Order($row);
			$order->items = $this->getItemsByOrderId($order->id);
			$orders[] = $order;
		}

		return $orders;
	}

	public function getOrderById($orderId)
	{
		$row = db_fetch_one(
			"SELECT id, user_id AS userId, status, subtotal, tax, shipping, total, created_at AS createdAt, updated_at AS updatedAt
			 FROM orders
			 WHERE id = :orderId
			 LIMIT 1",
			array(':orderId' => $orderId)
		);

		if (!$row) {
			return null;
		}

		$order = new Order($row);
		$order->items = $this->getItemsByOrderId($order->id);

		return $order;
	}

	// Create the main order row first, then add the items.
	public function createOrder($data)
	{
		db_execute(
			"INSERT INTO orders (user_id, status, subtotal, tax, shipping, total, created_at, updated_at)
			 VALUES (:userId, :status, :subtotal, :tax, :shipping, :total, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
			array(
				':userId' => $data['userId'],
				':status' => isset($data['status']) ? $data['status'] : 'pending',
				':subtotal' => isset($data['subtotal']) ? $data['subtotal'] : 0,
				':tax' => isset($data['tax']) ? $data['tax'] : 0,
				':shipping' => isset($data['shipping']) ? $data['shipping'] : 0,
				':total' => isset($data['total']) ? $data['total'] : 0
			)
		);

		return db_last_insert_id();
	}

	public function addOrderItem($orderId, $item)
	{
		db_execute(
			"INSERT INTO order_items (order_id, product_id, quantity, price)
			 VALUES (:orderId, :productId, :quantity, :price)",
			array(
				':orderId' => $orderId,
				':productId' => $item['productId'],
				':quantity' => isset($item['quantity']) ? $item['quantity'] : 1,
				':price' => isset($item['unitPrice']) ? $item['unitPrice'] : 0
			)
		);

		return db_last_insert_id();
	}

	public function getItemsByOrderId($orderId)
	{
		$rows = db_fetch_all(
			"SELECT oi.id, oi.order_id AS orderId, oi.product_id AS productId, p.name, oi.price AS unitPrice, oi.quantity
			 FROM order_items oi
			 LEFT JOIN products p ON p.id = oi.product_id
			 WHERE oi.order_id = :orderId
			 ORDER BY oi.id ASC",
			array(':orderId' => $orderId)
		);

		$items = array();
		foreach ($rows as $row) {
			$items[] = new OrderItem($row);
		}

		return $items;
	}

	// Simple helper for a full checkout flow.
	public function createOrderWithItems($data, $items)
	{
		$orderId = $this->createOrder($data);
		foreach ($items as $item) {
			$this->addOrderItem($orderId, $item);
		}

		return $this->getOrderById($orderId);
	}

	public function updateOrderStatus($orderId, $status)
	{
		return db_execute(
			"UPDATE orders
			 SET status = :status, updated_at = CURRENT_TIMESTAMP
			 WHERE id = :orderId",
			array(
				':status' => $status,
				':orderId' => $orderId
			)
		) > 0;
	}

	public function deleteOrder($orderId)
	{
		db_execute("DELETE FROM order_items WHERE order_id = :orderId", array(':orderId' => $orderId));
		return db_execute("DELETE FROM orders WHERE id = :orderId", array(':orderId' => $orderId)) > 0;
	}
}
