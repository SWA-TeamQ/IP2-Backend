<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\AdminRepository;

class AdminStatsController extends Controller {
    private AdminRepository $adminRepo;

    public function __construct() {
        $this->adminRepo = new AdminRepository();
    }

    public function index(Request $request, Response $response) {
        $stats = [
            'summary' => $this->adminRepo->getSummaryStats(),
            'recentSales' => $this->adminRepo->getRecentSales(),
            'topProducts' => $this->adminRepo->getTopSellingProducts(),
            'stockAlerts' => $this->adminRepo->getStockAlerts()
        ];

        return $this->success($response, $stats);
    }
}