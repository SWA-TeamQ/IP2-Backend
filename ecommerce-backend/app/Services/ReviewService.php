<?php
namespace App\Services;

use App\Repositories\ReviewRepository;

class ReviewService {
    private ReviewRepository $reviewRepo;

    public function __construct() {
        $this->reviewRepo = new ReviewRepository();
    }

    public function getAllReviews(int $limit = 10, int $offset = 0): array {
        return $this->reviewRepo->getAllReviewsWithDetails($limit, $offset);
    }

    public function deleteReview(string $id): bool {
        try {
            $this->reviewRepo->delete($id);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}