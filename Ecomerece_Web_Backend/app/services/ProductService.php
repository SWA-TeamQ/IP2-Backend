<?php
namespace App\Services;

use App\Models\Product;

class ProductService {
    private Product $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function getAllProducts($filters = []) {
        return $this->productModel->getAll($filters);
    }

    public function getProductByIdOrSlug($idOrSlug) {
        return $this->productModel->findByIdOrSlug($idOrSlug);
    }

    public function createProduct($data) {
        return $this->productModel->create($data);
    }

    public function updateProduct($id, $data) {
        return $this->productModel->update($id, $data);
    }

    public function deleteProduct($id) {
        return $this->productModel->delete($id);
    }
}