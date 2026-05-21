<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReviewService;

class AdminReviewController extends Controller {
    private ReviewService $reviewService;

    public function __construct() {
        $this->reviewService = new ReviewService();
    }

    public function index(Request $request, Response $response) {
        $limit = (int)($request->getBody()['limit'] ?? 10);
        $offset = (int)($request->getBody()['offset'] ?? 0);

        $reviews = $this->reviewService->getAllReviews($limit, $offset);
        return $this->success($response, $reviews);
    }

    public function delete(Request $request, Response $response, $id) {
        $deleted = $this->reviewService->deleteReview($id);
        if (!$deleted) {
            return $this->error($response, 'Failed to delete review', 404);
        }

        return $this->success($response, null, 'Review deleted successfully');
    }
}