<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;

class AdminProductController {
    public function addProduct(Request $request, Response $response) {
        $data = $request->getBody();
        $productModel = new Product();
        
        $productModel->create($data);

        return $response->json(['message' => 'Product added by Admin']);
    }
    public function update(Request $request, Response $response) {
    $id = $_GET['id'] ?? null;
    $data = $request->getBody();
    
    if (!$id) return $response->json(['error' => 'Product ID required'], 400);

    $productModel = new Product();
    $productModel->update($id, $data);

    return $response->json(['message' => 'Product updated successfully']);
    }

    public function delete(Request $request, Response $response) {
        $id = $_GET['id'] ?? null;
        
        if (!$id) return $response->json(['error' => 'Product ID required'], 400);

        $productModel = new Product();
        $productModel->delete($id);

        return $response->json(['message' => 'Product deleted successfully']);
    }
}