<?php

class OrderItem
{
	public $id;
	public $orderId;
	public $productId;
	public $name;
	public $unitPrice;
	public $quantity;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->orderId = isset($data['orderId']) ? $data['orderId'] : null;
		$this->productId = isset($data['productId']) ? $data['productId'] : null;
		$this->name = isset($data['name']) ? $data['name'] : '';
		$this->unitPrice = isset($data['unitPrice']) ? $data['unitPrice'] : 0;
		$this->quantity = isset($data['quantity']) ? $data['quantity'] : 1;
	}

	public function toArray()
	{
		return array(
			'id' => $this->id,
			'productId' => $this->productId,
			'name' => $this->name,
			'unitPrice' => $this->unitPrice,
			'quantity' => $this->quantity
		);
	}
}

