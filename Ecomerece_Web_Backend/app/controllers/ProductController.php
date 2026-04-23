<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ProductService;
use App\Models\Order;
use App\Models\Review;
use App\Helpers\Validator;

class ProductController extends Controller {
    private ProductService $productService;
    private Order $orderModel;
    private Review $reviewModel;

    public function __construct() {
        $this->productService = new ProductService();
        $this->orderModel = new Order();
        $this->reviewModel = new Review();
    }

    public function index(Request $request, Response $response) {
        $filters = $request->getBody(); // category, search, sortBy, etc.
        $products = $this->productService->getAllProducts($filters);
        return $this->success($response, $products);
    }

    public function show(Request $request, Response $response, $idOrSlug) {
        if (!$idOrSlug) {
            return $this->error($response, 'ID or Slug is required', 400);
        }

        $product = $this->productService->getProductByIdOrSlug($idOrSlug);
        
        if (!$product) {
            return $this->error($response, 'Product not found', 404);
        }

        // Fetch reviews for this product
        $product['reviews'] = $this->reviewModel->getByProduct($product['id']);

        return $this->success($response, $product);
    }

    public function addReview(Request $request, Response $response, $productId) {
        $userId = $request->userId; // Set by AuthMiddleware
        $data = $request->getBody();

        // 1. Validation
        $errors = Validator::validate($data, [
            'rating' => 'required',
            'comment' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        // 2. Business Logic: Verified Purchase Check
        $hasPurchased = $this->orderModel->hasUserPurchasedProduct($userId, $productId);
        
        if (!$hasPurchased) {
            return $this->error($response, 'You can only review products you have purchased.', 403);
        }

        // 3. Save Review
        try {
            $reviewId = $this->reviewModel->create($productId, $userId, $data);
            return $this->success($response, ['id' => $reviewId], 'Review submitted successfully!', 201);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to submit review: ' . $e->getMessage(), 500);
        }
    }
}