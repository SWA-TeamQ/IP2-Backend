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
        // 1. Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        // 2. Simple Validation
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Invalid JSON body"]]);
            return;
        }

        if (empty($data['fullName']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Full Name, Email, and Password are required"]]);
            return;
        }

        // 3. Check if user exists
        if ($this->userRepo->getUserByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(["error" => ["message" => "Email already registered"]]);
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

        echo json_encode(["user" => ["id" => $userId, "email" => $data['email']]]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Invalid JSON body"]]);
            return;
        }

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => ["message" => "Email and password required"]]);
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
            http_response_code(401);
            echo json_encode(["error" => ["message" => "Invalid credentials"]]);
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        http_response_code(200);
        echo json_encode(["message" => "Logged out"]);
    }
}
