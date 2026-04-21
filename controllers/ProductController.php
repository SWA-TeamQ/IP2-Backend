<?php

class ProductController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $products
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id
            ");
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $product
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Product not found"
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
