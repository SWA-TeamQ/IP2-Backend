<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ProductService;

class AdminProductController extends Controller {
    private ProductService $productService;

    public function __construct() {
        $this->productService = new ProductService();
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();
        try {
            $id = $this->productService->createProduct($data);
            return $this->success($response, ['id' => $id], 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($response, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Response $response, $id) {
        $data = $request->getBody();
        if (!$id) return $this->error($response, 'Product ID required', 400);

        try {
            $this->productService->updateProduct($id, $data);
            return $this->success($response, null, 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->error($response, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, Response $response, $id) {
        if (!$id) return $this->error($response, 'Product ID required', 400);

        try {
            $this->productService->deleteProduct($id);
            return $this->success($response, null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->error($response, $e->getMessage(), 500);
        }
    }
}