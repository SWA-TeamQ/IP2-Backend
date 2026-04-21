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

    private function jsonResponse($payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($payload);
    }

    private function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register()
    {
        $data = app_get_request_body();

        if (empty($data['email']) || empty($data['password']) || empty($data['fullName'])) {
            $this->jsonResponse(
                app_error_response('VALIDATION_ERROR', 'Full Name, Email, and Password are required'),
                400
            );
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(app_error_response('VALIDATION_ERROR', 'Invalid email address'), 400);
            return;
        }

        if ($this->userRepo->getUserByEmail($data['email'])) {
            $this->jsonResponse(app_error_response('CONFLICT', 'Email already registered'), 409);
            return;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $userId = $this->userRepo->createUser([
                'full_name' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $hashedPassword,
                'role' => 'customer'
            ]);

            $this->jsonResponse([
                "status" => "success",
                "user" => [
                    "id" => $userId,
                    "email" => $data['email']
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                "error" => ["message" => "Registration failed: " . $e->getMessage()]
            ], 500);
        }
    }

    public function login()
    {
        $data = app_get_request_body();

        if (empty($data['email']) || empty($data['password'])) {
            $this->jsonResponse(
                app_error_response('VALIDATION_ERROR', 'Email and password are required'),
                400
            );
            return;
        }

        $userRow = db_fetch_one(
            'SELECT id, full_name, email, role, password FROM users WHERE email = :email LIMIT 1',
            array(':email' => $data['email'])
        );

        if ($userRow && password_verify($data['password'], $userRow['password'])) {
            $this->ensureSessionStarted();
            $_SESSION['user_id'] = $userRow['id'];
            $_SESSION['user_role'] = $userRow['role'];

            $this->jsonResponse([
                "status" => "success",
                "user" => [
                    "id" => $userRow['id'],
                    "fullName" => isset($userRow['fullName']) ? $userRow['fullName'] : (isset($userRow['full_name']) ? $userRow['full_name'] : null),
                    "email" => $userRow['email'],
                    "role" => $userRow['role']
                ]
            ]);
        } else {
            $this->jsonResponse(app_error_response('AUTH_ERROR', 'Invalid email or password'), 401);
        }
    }

    public function logout()
    {
        $this->ensureSessionStarted();
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

        $this->jsonResponse(["message" => "Logged out"]);
    }
}