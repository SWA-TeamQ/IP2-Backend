<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../models/Product.model.php';

class ProductRepository
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    private function connection()
    {
        if ($this->db === null) {
            $this->db = db();
        }

        return $this->db;
    }

    public function getDB(){
        return $this->db;
    }

    public function getProductsById($id){

        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=?");
    }
    private function mapProductRow($row)
    {
        if (!isset($row['salePrice']) && isset($row['sell_price'])) {
            $row['salePrice'] = $row['sell_price'];
        }

        if (!isset($row['createdAt']) && isset($row['created_at'])) {
            $row['createdAt'] = $row['created_at'];
        }

        return new Product($row);
    }

    public function getAllProducts()
    {
        $stmt = $this->connection()->prepare(
            'SELECT id, name, description, category, price, stock, images, sell_price AS salePrice, created_at AS createdAt
             FROM products
             ORDER BY id DESC'
        );
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = array();
        foreach ($rows as $row) {
            $products[] = $this->mapProductRow($row);
        }

        return $products;
    }

    // Compatibility helper for older code paths.
    public function getAll()
    {
        $items = array();
        foreach ($this->getAllProducts() as $product) {
            $items[] = $product->toArray();
        }
        return $items;
    }

    // Returns a Product or null.
    public function getProductById($id)
    {
        $stmt = $this->connection()->prepare(
            'SELECT id, name, description, category, price, stock, images, sell_price AS salePrice, created_at AS createdAt
             FROM products
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(array(':id' => $id));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->mapProductRow($result) : null;
    }

    public function getProductsByName($name)
    {
        $stmt = $this->connection()->prepare(
            'SELECT id, name, description, category, price, stock, images, sell_price AS salePrice, created_at AS createdAt
             FROM products
             WHERE name = :name'
        );
        $stmt->execute(array(':name' => $name));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = array();
        foreach ($rows as $row) {
            $products[] = $this->mapProductRow($row);
        }

        return $products;
    }

    public function getProductsByCategory($category)
    {
        $stmt = $this->connection()->prepare(
            'SELECT id, name, description, category, price, stock, images, sell_price AS salePrice, created_at AS createdAt
             FROM products
             WHERE category = :category'
        );
        $stmt->execute(array(':category' => $category));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = array();
        foreach ($rows as $row) {
            $products[] = $this->mapProductRow($row);
        }

        return $products;
    }

    public function update($id, $data)
    {
        $stmt = $this->connection()->prepare(
            'UPDATE products
             SET name = :name,
                 description = :description,
                 category = :category,
                 price = :price,
                 sell_price = :sell_price,
                 images = :images,
                 stock = :stock
             WHERE id = :id'
        );

        return $stmt->execute(array(
            ':name' => $data['name'],
            ':description' => isset($data['description']) ? $data['description'] : '',
            ':category' => isset($data['category']) ? $data['category'] : null,
            ':price' => isset($data['price']) ? $data['price'] : 0,
            ':sell_price' => isset($data['sell_price']) ? $data['sell_price'] : null,
            ':images' => isset($data['images']) ? $data['images'] : null,
            ':stock' => isset($data['stock']) ? $data['stock'] : 0,
            ':id' => $id
        ));
    }

    public function delete($id)
    {
        $stmt = $this->connection()->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(array(':id' => $id));
    }

    public function create($data)
    {
        $stmt = $this->connection()->prepare(
            'INSERT INTO products (name, description, category, price, sell_price, images, stock, created_at)
             VALUES (:name, :description, :category, :price, :sell_price, :images, :stock, NOW())'
        );

        return $stmt->execute(array(
            ':name' => $data['name'],
            ':description' => isset($data['description']) ? $data['description'] : '',
            ':category' => isset($data['category']) ? $data['category'] : null,
            ':price' => isset($data['price']) ? $data['price'] : 0,
            ':sell_price' => isset($data['sell_price']) ? $data['sell_price'] : null,
            ':images' => isset($data['images']) ? $data['images'] : null,
            ':stock' => isset($data['stock']) ? $data['stock'] : 0
        ));
    }
}
