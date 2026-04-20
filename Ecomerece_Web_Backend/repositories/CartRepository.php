<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/Cart.model.php';
require_once __DIR__ . '/../models/CartItem.model.php';


class CartRepository
{
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