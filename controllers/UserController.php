<?php

class UserController
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
            // Exclude password for security
            $stmt = $this->pdo->prepare("SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $users
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
            $stmt = $this->pdo->prepare("SELECT id, name, email, role, created_at, updated_at FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $user
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "User not found"
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
