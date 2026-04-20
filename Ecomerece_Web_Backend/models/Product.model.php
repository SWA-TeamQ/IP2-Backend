<?php

class Product
{
	public $id;
	public $name;
	public $description;
	public $images;
	public $details;
	public $createdAt;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->name = isset($data['name']) ? $data['name'] : '';
		$this->description = isset($data['description']) ? $data['description'] : '';

		$rawImages = isset($data['images']) ? $data['images'] : null;
		if (is_string($rawImages)) {
			$decoded = json_decode($rawImages, true);
			if (is_array($decoded)) {
				$this->images = $decoded;
			} else {
				$this->images = $rawImages !== '' ? array($rawImages) : array();
			}
		} elseif (is_array($rawImages)) {
			$this->images = $rawImages;
		} else {
			$this->images = array();
		}

		$this->details = array(
			'category' => isset($data['category']) ? $data['category'] : null,
			'brand' => isset($data['brand']) ? $data['brand'] : null,
			'price' => isset($data['price']) ? $data['price'] : 0,
			'salePrice' => isset($data['salePrice']) ? $data['salePrice'] : null,
			'rating' => isset($data['rating']) ? $data['rating'] : null,
			'stock' => isset($data['stock']) ? $data['stock'] : 0
		);

		$this->createdAt = isset($data['createdAt']) ? $data['createdAt'] : null;
	}

	public function toArray()
	{
		// Match frontend contract: images + nested details.
		return array(
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
			'images' => $this->images,
			'details' => $this->details,
			'createdAt' => $this->createdAt
		);
	}
}

