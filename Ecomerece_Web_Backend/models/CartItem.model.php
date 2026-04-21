<?php

class CartItem
{
	public $id;
	public $cartId;
	public $productId;
	public $quantity;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->cartId = isset($data['cartId']) ? $data['cartId'] : null;
		$this->productId = isset($data['productId']) ? $data['productId'] : null;
		$this->quantity = isset($data['quantity']) ? $data['quantity'] : 1;
	}

	public function toArray()
	{
		return array(
			'id' => $this->id,
			'productId' => $this->productId,
			'quantity' => $this->quantity
		);
	}
}

