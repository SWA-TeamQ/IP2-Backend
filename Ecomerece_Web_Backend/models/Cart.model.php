<?php

require_once __DIR__ . '/CartItem.model.php';

class Cart
{
	public $id;
	public $userId;
	public $items;
	public $createdAt;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->userId = isset($data['userId']) ? $data['userId'] : null;
		$this->createdAt = isset($data['createdAt']) ? $data['createdAt'] : null;

		$this->items = array();
		$rawItems = isset($data['items']) ? $data['items'] : array();
		foreach ($rawItems as $item) {
			$this->items[] = ($item instanceof CartItem) ? $item : new CartItem($item);
		}
	}

	public function toArray()
	{
		$itemList = array();
		foreach ($this->items as $item) {
			$itemList[] = ($item instanceof CartItem) ? $item->toArray() : $item;
		}

		return array(
			'id' => $this->id,
			'userId' => $this->userId,
			'items' => $itemList,
			'createdAt' => $this->createdAt
		);
	}
}

