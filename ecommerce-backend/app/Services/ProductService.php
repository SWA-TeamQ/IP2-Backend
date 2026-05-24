<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Entities\Product;

class ProductService {
    private ProductRepository $productRepo;
    private ReviewRepository $reviewRepo;

    public function __construct() {
        $this->productRepo = new ProductRepository();
        $this->reviewRepo = new ReviewRepository();
    }

    public function getAllProducts(array $filters = []) {
        $products = $this->productRepo->getAll($filters);
        return array_map(fn($p) => $p->toArray(), $products);
    }

    public function getProductByIdOrSlug(string $idOrSlug) {
        $product = $this->productRepo->findByIdOrSlug($idOrSlug);
        if (!$product) return null;

        $data = $product->toArray();
        $reviews = $this->reviewRepo->findByProduct($product->getId());
        $data['reviews'] = array_map(fn($r) => [
            'id' => $r['id'] ?? null,
            'productId' => $r['product_id'] ?? null,
            'userId' => $r['user_id'] ?? null,
            'rating' => (int)($r['rating'] ?? 0),
            'comment' => $r['comment'] ?? '',
            'createdAt' => $r['created_at'] ?? null,
            'user' => [
                'firstName' => $r['first_name'] ?? null,
                'lastName' => $r['last_name'] ?? null,
                'avatarUrl' => $r['avatar_url'] ?? null
            ]
        ], $reviews);

        return $data;
    }

    public function createProduct(array $data) {
        $product = new Product($data);
        if (empty($data['slug'])) {
            // Re-generate slug if missing to ensure entity consistency
            $data['slug'] = $this->generateSlug($data['name']);
            $product = new Product($data);
        }
        return $this->productRepo->create($product);
    }

    public function updateProduct(string $id, array $data) {
        return $this->productRepo->update($id, $data);
    }

    public function deleteProduct(string $id) {
        return $this->productRepo->delete($id);
    }

    private function generateSlug($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}