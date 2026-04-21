<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ProductService;

class ProductController {
    private ProductService $productService;

    public function __construct() {
        $this->productService = new ProductService();
    }

    public function index(Request $request, Response $response) {
        $products = $this->productService->getAllProducts();
        return $response->json($products);
    }

    public function show(Request $request, Response $response) {
        // We'll need to grab the ID from the URL (handled by Router)
        $id = $_GET['id'] ?? null; 
        
        if (!$id) {
            return $response->json(['error' => 'ID is required'], 400);
        }

        $product = $this->productService->getProductById($id);
        
        if (isset($product['error'])) {
            return $response->json($product, 404);
        }

        return $response->json($product);
    }
}