<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/Product.model.php';

class ProductRepository
{
	private function mapRowToProductData($row)
	{
		if (isset($row['image'])) {
			$row['images'] = $row['image'] ? array($row['image']) : array();
		}

		if (!isset($row['createdAt']) && isset($row['created_at'])) {
			$row['createdAt'] = $row['created_at'];
		}

		return $row;
	}

	private function getProductImage($data)
	{
		if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
			return $data['images'][0];
		}

		if (isset($data['images']) && is_string($data['images']) && $data['images'] !== '') {
			return $data['images'];
		}

		if (isset($data['image'])) {
			return $data['image'];
		}

		return null;
	}

	private function getProductDetails($data)
	{
		$details = isset($data['details']) && is_array($data['details']) ? $data['details'] : array();

		return array(
			'category' => isset($details['category']) ? $details['category'] : (isset($data['category']) ? $data['category'] : null),
			'brand' => isset($details['brand']) ? $details['brand'] : (isset($data['brand']) ? $data['brand'] : null),
			'price' => isset($details['price']) ? $details['price'] : (isset($data['price']) ? $data['price'] : 0),
			'salePrice' => isset($details['salePrice']) ? $details['salePrice'] : (isset($data['salePrice']) ? $data['salePrice'] : null),
			'rating' => isset($details['rating']) ? $details['rating'] : (isset($data['rating']) ? $data['rating'] : null),
			'stock' => isset($details['stock']) ? $details['stock'] : (isset($data['stock']) ? $data['stock'] : 0)
		);
	}

	// Main list used on shop page.
	public function getAllProducts()
	{
		$rows = db_fetch_all("SELECT id, name, description, price, stock, image, category, created_at AS createdAt FROM products ORDER BY created_at DESC");
		$products = array();

		foreach ($rows as $row) {
			$products[] = new Product($this->mapRowToProductData($row));
		}

		return $products;
	}

	public function getProductById($id)
	{
		$row = db_fetch_one(
			"SELECT id, name, description, price, stock, image, category, created_at AS createdAt
			 FROM products
			 WHERE id = :id
			 LIMIT 1",
			array(':id' => $id)
		);

		return $row ? new Product($this->mapRowToProductData($row)) : null;
	}

	public function createProduct($data)
	{
		$details = $this->getProductDetails($data);
		$image = $this->getProductImage($data);

		db_execute(
			"INSERT INTO products (name, description, price, stock, image, category, created_at)
			 VALUES (:name, :description, :price, :stock, :image, :category, NOW())",
			array(
				':name' => $data['name'],
				':description' => isset($data['description']) ? $data['description'] : '',
				':price' => $details['price'],
				':stock' => $details['stock'],
				':image' => $image,
				':category' => $details['category']
			)
		);

		return db_last_insert_id();
	}

	// Returns true only when a row actually changed.
	public function updateProduct($id, $data)
	{
		$details = $this->getProductDetails($data);
		$image = $this->getProductImage($data);

		return db_execute(
			"UPDATE products
			 SET name = :name,
				 description = :description,
				 price = :price,
				 stock = :stock,
				 image = :image,
				 category = :category
			 WHERE id = :id",
			array(
				':name' => $data['name'],
				':description' => isset($data['description']) ? $data['description'] : '',
				':price' => $details['price'],
				':stock' => $details['stock'],
				':image' => $image,
				':category' => $details['category'],
				':id' => $id
			)
		) > 0;
	}

	public function deleteProduct($id)
	{
		return db_execute("DELETE FROM products WHERE id = :id", array(':id' => $id)) > 0;
	}
}

