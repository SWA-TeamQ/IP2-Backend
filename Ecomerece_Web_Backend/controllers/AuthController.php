<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../../utils/responses.php';
require_once __DIR__ . '/../../utils/request.php';

class AuthController
{
    private $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register()
    {
        // 1. Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        // 2. Simple Validation
        if (empty($data['email']) || empty($data['password']) || empty($data['fullName'])) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Full Name, Email, and Password are required"]]);
            return;
        }

        if ($this->userRepo->getUserByEmail($data['email'])) {
            $this->jsonResponse(app_error_response('CONFLICT', 'Email already registered'), 409);
            return;
        }

        // 4. Hash the Password and Save
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        try {
            $userId = $this->userRepo->createUser([
                'full_name' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $hashedPassword,
                'role' => 'customer'
            ]);

            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $userId, 
                    "email" => $data['email']
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => ["message" => "Registration failed: " . $e->getMessage()]]);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Email and password required"]]);
            return;
        }

        // Use the Repository to find the user
        $userRow = $this->userRepo->getUserByEmail($data['email']);

        if ($userRow && password_verify($data['password'], $userRow['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $userRow['id'];
            $_SESSION['user_role'] = $userRow['role'];

            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $userRow['id'],
                    // Use 'full_name' for consistency with DB field
                    "fullName" => isset($userRow['fullName']) ? $userRow['fullName'] : (isset($userRow['full_name']) ? $userRow['full_name'] : null),
                    "email" => $userRow['email'],
                    "role" => $userRow['role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => ["message" => "Invalid email or password"]]);
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        echo json_encode(["status" => "success", "message" => "Logged out successfully"]);
    }

    public function me() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $user = $this->userRepo->getUserById($_SESSION['user_id']);
            echo json_encode(["loggedIn" => true, "user" => $user]);
        } else {
            echo json_encode(["loggedIn" => false]);
        }
    }
}