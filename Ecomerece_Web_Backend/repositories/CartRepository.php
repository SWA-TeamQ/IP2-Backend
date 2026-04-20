<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/Cart.model.php';
require_once __DIR__ . '/../models/CartItem.model.php';


class CartRepository
{
	// Get one cart by user and attach items.
	public function getCartByUserId($userId)
	{
		$row = db_fetch_one(
			"SELECT id, user_id AS userId, created_at AS createdAt
			 FROM carts
			 WHERE user_id = :userId
			 LIMIT 1",
			array(':userId' => $userId)
		);

		if (!$row) {
			return null;
		}

		$cart = new Cart($row);
		$cart->items = $this->getItemsByCartId($cart->id);

		return $cart;
	}

	// Create cart for a user.
	public function createCart($userId)
	{
		db_execute(
			"INSERT INTO carts (user_id, created_at)
			 VALUES (:userId, NOW())",
			array(':userId' => $userId)
		);

		return db_last_insert_id();
	}

	// Return existing cart or create one.
	public function getOrCreateCartByUserId($userId)
	{
		$cart = $this->getCartByUserId($userId);
		if ($cart) {
			return $cart;
		}

		$cartId = $this->createCart($userId);
		return $this->getCartById($cartId);
	}

	public function getCartById($cartId)
	{
		$row = db_fetch_one(
			"SELECT id, user_id AS userId, created_at AS createdAt
			 FROM carts
			 WHERE id = :cartId
			 LIMIT 1",
			array(':cartId' => $cartId)
		);

		if (!$row) {
			return null;
		}

		$cart = new Cart($row);
		$cart->items = $this->getItemsByCartId($cart->id);

		return $cart;
	}

	public function getItemsByCartId($cartId)
	{
		$rows = db_fetch_all(
			"SELECT ci.id, ci.cart_id AS cartId, ci.product_id AS productId, ci.quantity
			 FROM cart_items ci
			 WHERE ci.cart_id = :cartId
			 ORDER BY ci.id ASC",
			array(':cartId' => $cartId)
		);

		$items = array();
		foreach ($rows as $row) {
			$items[] = new CartItem($row);
		}

		return $items;
	}

	// Add item if missing, otherwise replace quantity.
	public function addItem($cartId, $productId, $quantity)
	{
		$existingItem = db_fetch_one(
			"SELECT id
			 FROM cart_items
			 WHERE cart_id = :cartId AND product_id = :productId
			 LIMIT 1",
			array(
				':cartId' => $cartId,
				':productId' => $productId
			)
		);

		if ($existingItem) {
			return $this->updateItemQuantity($cartId, $productId, $quantity);
		}

		db_execute(
			"INSERT INTO cart_items (cart_id, product_id, quantity)
			 VALUES (:cartId, :productId, :quantity)",
			array(
				':cartId' => $cartId,
				':productId' => $productId,
				':quantity' => $quantity
			)
		);

		return db_last_insert_id();
	}

	public function updateItemQuantity($cartId, $productId, $quantity)
	{
		if ((int) $quantity <= 0) {
			return $this->removeItem($cartId, $productId);
		}

		return db_execute(
			"UPDATE cart_items
			 SET quantity = :quantity
			 WHERE cart_id = :cartId AND product_id = :productId",
			array(
				':quantity' => $quantity,
				':cartId' => $cartId,
				':productId' => $productId
			)
		) > 0;
	}

	public function removeItem($cartId, $productId)
	{
		return db_execute(
			"DELETE FROM cart_items
			 WHERE cart_id = :cartId AND product_id = :productId",
			array(
				':cartId' => $cartId,
				':productId' => $productId
			)
		) > 0;
	}

	public function clearCart($cartId)
	{
		return db_execute("DELETE FROM cart_items WHERE cart_id = :cartId", array(':cartId' => $cartId)) > 0;
	}

	// Remove carts (and their items) that have not been updated for a given number of days (default: 30)
	public function clearOldCarts($days = 30)
	{
		$threshold = date('Y-m-d H:i:s', strtotime("-$days days"));
		// Delete cart items for old carts
		db_execute(
			"DELETE ci FROM cart_items ci
			INNER JOIN carts c ON ci.cart_id = c.id
			WHERE c.created_at < :threshold",
			array(':threshold' => $threshold)
		);
		// Delete old carts
		return db_execute(
			"DELETE FROM carts WHERE created_at < :threshold",
			array(':threshold' => $threshold)
		);
	}
}
