<?php

require_once __DIR__ . '/OrderItem.model.php';

class Order
{
	public $id;
	public $userId;
	public $items;
	public $createdAt;
	public $updatedAt;
	public $status;
	public $subtotal;
	public $tax;
	public $shipping;
	public $total;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->userId = isset($data['userId']) ? $data['userId'] : null;
		$this->status = isset($data['status']) ? $data['status'] : 'pending';
		$this->subtotal = isset($data['subtotal']) ? $data['subtotal'] : 0;
		$this->tax = isset($data['tax']) ? $data['tax'] : 0;
		$this->shipping = isset($data['shipping']) ? $data['shipping'] : 0;
		$this->total = isset($data['total']) ? $data['total'] : 0;

		$this->items = array();
		$rawItems = isset($data['items']) ? $data['items'] : array();
		foreach ($rawItems as $item) {
			$this->items[] = ($item instanceof OrderItem) ? $item : new OrderItem($item);
		}

		$this->createdAt = isset($data['createdAt']) ? $data['createdAt'] : null;
		$this->updatedAt = isset($data['updatedAt']) ? $data['updatedAt'] : null;
	}

	public function toArray()
	{
		$itemList = array();
		foreach ($this->items as $item) {
			$itemList[] = ($item instanceof OrderItem) ? $item->toArray() : $item;
		}

		return array(
			'id' => $this->id,
			'userId' => $this->userId,
			'items' => $itemList,
			'status' => $this->status,
			'subtotal' => $this->subtotal,
			'tax' => $this->tax,
			'shipping' => $this->shipping,
			'total' => $this->total,
			'createdAt' => $this->createdAt,
			'updatedAt' => $this->updatedAt
		);
	}
}

