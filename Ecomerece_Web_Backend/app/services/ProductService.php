<?php
namespace App\Services;

use App\Models\Product;

class ProductService {
    private Product $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function getAllProducts() {
        // You could add logic here to only show products with stock > 0
        return $this->productModel->getAll();
    }

    public function getProductById($id) {
        $product = $this->productModel->find($id);
        if (!$product) {
            return ['error' => 'Product not found'];
        }
        return $product;
    }

    public function searchProducts($query) {
        // Logic for searching products by name or description
        return $this->productModel->search($query);
    }
}