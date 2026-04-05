<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/Product.model.php';

class ProductRepository
{
	// Main list used on shop page.
	public function getAllProducts()
	{
		$rows = db_fetch_all("SELECT id, name, description, price, stock, image, category, created_at AS createdAt FROM products ORDER BY created_at DESC");
		$products = array();

		foreach ($rows as $row) {
			$products[] = new Product($row);
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

		return $row ? new Product($row) : null;
	}

	public function createProduct($data)
	{
		db_execute(
			"INSERT INTO products (name, description, price, stock, image, category, created_at)
			 VALUES (:name, :description, :price, :stock, :image, :category, NOW())",
			array(
				':name' => $data['name'],
				':description' => isset($data['description']) ? $data['description'] : '',
				':price' => isset($data['price']) ? $data['price'] : 0,
				':stock' => isset($data['stock']) ? $data['stock'] : 0,
				':image' => isset($data['image']) ? $data['image'] : null,
				':category' => isset($data['category']) ? $data['category'] : null
			)
		);

		return db_last_insert_id();
	}

	// Returns true only when a row actually changed.
	public function updateProduct($id, $data)
	{
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
				':price' => isset($data['price']) ? $data['price'] : 0,
				':stock' => isset($data['stock']) ? $data['stock'] : 0,
				':image' => isset($data['image']) ? $data['image'] : null,
				':category' => isset($data['category']) ? $data['category'] : null,
				':id' => $id
			)
		) > 0;
	}

	public function deleteProduct($id)
	{
		return db_execute("DELETE FROM products WHERE id = :id", array(':id' => $id)) > 0;
	}
}

