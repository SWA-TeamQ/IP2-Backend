<?php
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/Product.php';

class ProductService{
    private $repo;

    public function __construct($db)
    {
        $this->repo = new ProductRepository($db);
    }

    public function getAllProducts(){
        $rows = $this->repo->getAll();
        return array_map(function($row){
            return new Product($row);},$rows);
        }

    public function getProductById($id){
        $data = $this->repo->getProductsById($id);
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function getProductByCategory($category){
        $rows = $this->repo->getProductsByCategory($category);
        return array_map(function($row){
            return new Product($row);
        },$rows);
    }
    public function createProduct($data) {
    return $this->repo->create($data);
}

public function updateProduct($id, $data) {
    return $this->repo->update($id, $data);
}

public function deleteProduct($id) {
    return $this->repo->delete($id);
}

    }
?>




