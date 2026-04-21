<?php
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/Product.php';

class ProductService {
    private $repo;

    public function __construct($db) {
        $this->repo = new ProductRepository($db);
    }

    public function getProductById($id) {
        $data = $this->repo->getProductById($id);
        return $data ? new Product($data) : null;
    }

    public function getFilteredProducts($filters) {
        $rows = $this->repo->findFiltered($filters);
        return array_map(fn($row) => new Product($row), $rows);
    }

    public function createProduct($data) { return $this->repo->create($data); }
    public function updateProduct($id, $data) { return $this->repo->update($id, $data); }
    public function deleteProduct($id) { return $this->repo->delete($id); }
}