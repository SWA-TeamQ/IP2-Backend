<?php
require_once __DIR__ . '/../repositories/UserRepository.php';

class AuthController
{
    private $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register()
    {
        try{
        // 1. Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        // 2. STRICT VALIDATION (Added for project finalization)
        if (empty($data['email']) || empty($data['password']) || empty($data['fullName'])) {
            $this->sendError(400, "Full Name, Email, and Password are required.");
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sendError(400, "Please provide a valid email address.");
            return;
        }

        if (strlen($data['password']) < 6) {
            $this->sendError(400, "Password must be at least 6 characters.");
            return;
        }

        // 3. Check if user exists
        if ($this->userRepo->getUserByEmail($data['email'])) {
            $this->sendError(409, "Email already registered.");
            return;
        }

        // 4. Hash the Password and Save
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $userId = $this->userRepo->createUser([
            'full_name' => $data['fullName'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
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
            $this->sendError(500, "Registration failed: " . $e->getMessage());
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['email']) || empty($data['password'])) {
            $this->sendError(400, "Email and password required.");
            return;
        }

        $userRow = db_fetch_one("SELECT * FROM users WHERE email = :email", [':email' => $data['email']]);

        if ($userRow && password_verify($data['password'], $userRow['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $userRow['id'];

            echo json_encode(["user" => [
                "id" => $userRow['id'],
                "fullName" => $userRow['full_name'],
                "email" => $userRow['email']
            ]]);
        } else {
            $this->sendError(401, "Invalid email or password.");
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

    /**
     * Helper method for consistent error responses
     */
    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode(["error" => ["message" => $message]]);
    }
}
