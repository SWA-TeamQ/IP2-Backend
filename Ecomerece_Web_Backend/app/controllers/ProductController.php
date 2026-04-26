<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\ProductService;
use App\Services\FileService;
use App\Repositories\ReviewRepository;
use App\Entities\Review;
use App\Services\OrderService;
use App\Helpers\Validator;

class ProductController extends Controller {
    private ProductService $productService;
    private OrderService $orderService;
    private ReviewRepository $reviewRepo;
    private FileService $fileService;

    public function __construct() {
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
        $this->reviewRepo = new ReviewRepository();
        $this->fileService = new FileService();
    }

    public function index(Request $request, Response $response) {
        $filters = $request->getBody();
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

        return $this->success($response, $product);
    }

    public function addReview(Request $request, Response $response, $productId) {
        $userId = $request->userId; // Set by IsAuthenticated
        $data = $request->getBody();

        $errors = Validator::validate($data, [
            'rating' => 'required',
            'comment' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        $hasPurchased = $this->orderService->hasUserPurchasedProduct($userId, $productId);
        
        if (!$hasPurchased) {
            return $this->error($response, 'You can only review products you have purchased.', 403);
        }

        $review = new Review([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $data['rating'],
            'comment' => $data['comment']
        ]);

        $reviewId = $this->reviewRepo->create($review);
        return $this->success($response, ['id' => $reviewId], 'Review submitted successfully!', 201);
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();
        $files = $request->getFiles();
        
        $errors = Validator::validate($data, [
            'name' => 'required',
            'description' => 'required',
            'price_cents' => 'required',
            'category' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        $imageUrls = [];
        if (!empty($files['images'])) {
            $imageUrls = $this->fileService->uploadMultiple($files['images']);
        } elseif (!empty($data['images'])) {
            $imageUrls = is_array($data['images']) ? $data['images'] : [$data['images']];
        }

        if (empty($imageUrls)) {
            return $this->error($response, 'At least one product image is required', 400);
        }

        $data['images'] = $imageUrls;

        $id = $this->productService->createProduct($data);
        return $this->success($response, ['id' => $id], 'Product created successfully', 201);
    }

    public function update(Request $request, Response $response, $id) {
        $data = $request->getBody();
        $files = $request->getFiles();

        if (!empty($files['images'])) {
            $data['images'] = $this->fileService->uploadMultiple($files['images']);
        }
        
        $this->productService->updateProduct($id, $data);
        return $this->success($response, null, 'Product updated successfully');
    }

    public function delete(Request $request, Response $response, $id) {
        $this->productService->deleteProduct($id);
        return $this->success($response, null, 'Product deleted successfully');
    }
}