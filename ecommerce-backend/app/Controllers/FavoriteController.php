<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\BaseRepository;
use PDO;

class FavoriteController extends Controller {
    private $db;

    public function __construct() {
        $this->db = \App\Core\Database::getConnection();
    }

    public function index(Request $request, Response $response) {
        $userId = $request->userId;
        $sql = "SELECT p.* FROM products p 
                JOIN favorites f ON p.id = f.product_id 
                WHERE f.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->success($response, $favorites);
    }

    public function toggle(Request $request, Response $response, $productId) {
        $userId = $request->userId;
        
        // Check if exists
        $sqlCheck = "SELECT 1 FROM favorites WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        
        if ($stmt->fetch()) {
            // Remove
            $sqlDelete = "DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->db->prepare($sqlDelete);
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            return $this->success($response, ['favorited' => false], 'Removed from favorites');
        } else {
            // Add
            $sqlInsert = "INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)";
            $stmt = $this->db->prepare($sqlInsert);
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            return $this->success($response, ['favorited' => true], 'Added to favorites');
        }
    }
}
