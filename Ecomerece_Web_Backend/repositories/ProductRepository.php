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

    public function getDB()
    {
        return $this->connection();
    }

    private function baseSelectSql()
    {
        return 'SELECT id, name, description, category, brand, price, stock, rating, images, sell_price AS salePrice, created_at AS createdAt
                FROM products';
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

    private function fetchRawProductById($id)
    {
        $stmt = $this->connection()->prepare(
            'SELECT id, name, description, category, brand, price, stock, rating, images, sell_price, created_at
             FROM products
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(array(':id' => (int) $id));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

    private function normalizeImages($images)
    {
        if (is_array($images)) {
            return json_encode(array_values($images));
        }

        if (is_string($images)) {
            $trimmed = trim($images);
            if ($trimmed === '') {
                return json_encode(array());
            }

            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return json_encode(array_values($decoded));
            }

            return json_encode(array($trimmed));
        }

        return json_encode(array());
    }

    private function applyFilters(&$whereParts, &$params, $filters)
    {
        if (!empty($filters['category'])) {
            $whereParts[] = 'category = :category';
            $params[':category'] = trim((string) $filters['category']);
        }

        $query = null;
        if (!empty($filters['q'])) {
            $query = trim((string) $filters['q']);
        } elseif (!empty($filters['search'])) {
            $query = trim((string) $filters['search']);
        }

        if ($query !== null && $query !== '') {
            $whereParts[] = '(name LIKE :search OR description LIKE :search)';
            $params[':search'] = '%' . $query . '%';
        }
    }

    private function normalizeSortColumn($sortBy)
    {
        $allowedSort = array(
            'name' => 'name',
            'price' => 'price',
            'rating' => 'rating',
            'createdAt' => 'created_at'
        );

        return isset($allowedSort[$sortBy]) ? $allowedSort[$sortBy] : 'name';
    }

    public function getProductsById($id)
    {
        return $this->getProductById($id);
    }

    public function getAllProducts()
    {
        $stmt = $this->connection()->prepare(
            $this->baseSelectSql() . '
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

    public function getAll()
    {
        $items = array();
        foreach ($this->getAllProducts() as $product) {
            $items[] = $product->toArray();
        }

        return $items;
    }

    public function getProductById($id)
    {
        $stmt = $this->connection()->prepare(
            $this->baseSelectSql() . '
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(array(':id' => (int) $id));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->mapProductRow($result) : null;
    }

    public function getProductsByName($name)
    {
        $stmt = $this->connection()->prepare(
            $this->baseSelectSql() . '
             WHERE name LIKE :name
             ORDER BY name ASC'
        );
        $stmt->execute(array(':name' => '%' . trim((string) $name) . '%'));

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
            $this->baseSelectSql() . '
             WHERE category = :category
             ORDER BY name ASC'
        );
        $stmt->execute(array(':category' => trim((string) $category)));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = array();
        foreach ($rows as $row) {
            $products[] = $this->mapProductRow($row);
        }

        return $products;
    }

    public function findFiltered($filters)
    {
        $whereParts = array();
        $params = array();
        $this->applyFilters($whereParts, $params, $filters);

        $whereSql = '';
        if (!empty($whereParts)) {
            $whereSql = ' WHERE ' . implode(' AND ', $whereParts);
        }

        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int) $filters['limit'])) : 12;
        $offset = ($page - 1) * $limit;

        $sortBy = $this->normalizeSortColumn(isset($filters['sortBy']) ? $filters['sortBy'] : 'name');
        $order = (isset($filters['order']) && strtoupper((string) $filters['order']) === 'DESC') ? 'DESC' : 'ASC';

        $countStmt = $this->connection()->prepare('SELECT COUNT(*) AS total FROM products' . $whereSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = $this->baseSelectSql() . $whereSql . " ORDER BY {$sortBy} {$order} LIMIT :limit OFFSET :offset";
        $stmt = $this->connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($rows as $row) {
            $items[] = $this->mapProductRow($row);
        }

        return array(
            'items' => $items,
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        );
    }

    public function create($data)
    {
        $stmt = $this->connection()->prepare(
            'INSERT INTO products (name, description, category, brand, price, sell_price, rating, stock, images)
             VALUES (:name, :description, :category, :brand, :price, :sell_price, :rating, :stock, :images)'
        );

        $result = $stmt->execute(array(
            ':name' => trim((string) $data['name']),
            ':description' => isset($data['description']) ? trim((string) $data['description']) : '',
            ':category' => isset($data['category']) ? trim((string) $data['category']) : null,
            ':brand' => isset($data['brand']) ? trim((string) $data['brand']) : null,
            ':price' => isset($data['price']) ? (float) $data['price'] : 0,
            ':sell_price' => isset($data['salePrice']) && $data['salePrice'] !== '' ? (float) $data['salePrice'] : null,
            ':rating' => isset($data['rating']) && $data['rating'] !== '' ? (float) $data['rating'] : null,
            ':stock' => isset($data['stock']) ? max(0, (int) $data['stock']) : 0,
            ':images' => $this->normalizeImages(isset($data['images']) ? $data['images'] : array())
        ));

        return $result ? (int) $this->connection()->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $existing = $this->fetchRawProductById($id);
        if (!$existing) {
            return false;
        }

        $name = array_key_exists('name', $data) ? trim((string) $data['name']) : (string) $existing['name'];
        $description = array_key_exists('description', $data) ? trim((string) $data['description']) : (string) $existing['description'];
        $category = array_key_exists('category', $data) ? trim((string) $data['category']) : $existing['category'];
        $brand = array_key_exists('brand', $data) ? trim((string) $data['brand']) : $existing['brand'];
        $price = array_key_exists('price', $data) ? (float) $data['price'] : (float) $existing['price'];
        $salePrice = array_key_exists('salePrice', $data)
            ? ($data['salePrice'] === null || $data['salePrice'] === '' ? null : (float) $data['salePrice'])
            : $existing['sell_price'];
        $rating = array_key_exists('rating', $data)
            ? ($data['rating'] === null || $data['rating'] === '' ? null : (float) $data['rating'])
            : $existing['rating'];
        $stock = array_key_exists('stock', $data) ? max(0, (int) $data['stock']) : (int) $existing['stock'];
        $images = array_key_exists('images', $data) ? $data['images'] : $existing['images'];

        $stmt = $this->connection()->prepare(
            'UPDATE products
             SET name = :name,
                 description = :description,
                 category = :category,
                 brand = :brand,
                 price = :price,
                 sell_price = :sell_price,
                 rating = :rating,
                 stock = :stock,
                 images = :images
             WHERE id = :id'
        );

        return $stmt->execute(array(
            ':name' => $name,
            ':description' => $description,
            ':category' => $category,
            ':brand' => $brand,
            ':price' => $price,
            ':sell_price' => $salePrice,
            ':rating' => $rating,
            ':stock' => $stock,
            ':images' => $this->normalizeImages($images),
            ':id' => (int) $id
        ));
    }

    public function delete($id)
    {
        $stmt = $this->connection()->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(array(':id' => (int) $id));
    }
}
