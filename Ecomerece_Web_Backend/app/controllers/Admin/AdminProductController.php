<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;
use App\Entities\Product;

class AdminProductController extends Controller {
    private ProductRepository $productRepo;

    public function __construct() {
        $this->productRepo = new ProductRepository();
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();
        $product = new Product($data);
        $id = $this->productRepo->create($product);
        return $this->success($response, ['id' => $id], 'Product created successfully', 201);
    }

    public function update(Request $request, Response $response, $id) {
        $data = $request->getBody();
        if (!$id) return $this->error($response, 'Product ID required', 400);

        $this->productRepo->update($id, $data);
        return $this->success($response, null, 'Product updated successfully');
    }

    public function delete(Request $request, Response $response, $id) {
        if (!$id) return $this->error($response, 'Product ID required', 400);

        $this->productRepo->delete($id);
        return $this->success($response, null, 'Product deleted successfully');
    }
}