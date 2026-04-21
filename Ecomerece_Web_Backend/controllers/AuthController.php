<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../utils/responses.php';
require_once __DIR__ . '/../utils/request.php';

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
        try {
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
            $userId = $this->userRepo->createUser(array(
                'full_name' => $data['fullName'],
                'email' => $data['email'],
                'phone' => isset($data['phone']) ? $data['phone'] : null,
                'password' => $hashedPassword,
                'role' => 'customer'
            ));

            $this->jsonResponse(
                app_success_response(array(
                    'user' => array(
                        'id' => (int) $userId,
                        'fullName' => $data['fullName'],
                        'email' => $data['email'],
                        'role' => 'customer'
                    )
                )),
                201
            );
        } catch (Throwable $e) {
            $this->jsonResponse(app_error_response('INTERNAL_SERVER_ERROR', 'Registration failed'), 500);
        }
    }

    public function login()
    {
        try {
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

            if (!$userRow || !password_verify($data['password'], $userRow['password'])) {
                $this->jsonResponse(app_error_response('UNAUTHORIZED', 'Invalid email or password'), 401);
                return;
            }

            $this->ensureSessionStarted();
            $_SESSION['user_id'] = (int) $userRow['id'];
            $_SESSION['user_role'] = isset($userRow['role']) ? $userRow['role'] : 'customer';

            $this->jsonResponse(app_success_response(array(
                'user' => array(
                    'id' => (int) $userRow['id'],
                    'fullName' => $userRow['full_name'],
                    'email' => $userRow['email'],
                    'role' => $_SESSION['user_role']
                )
            )));
        } catch (Throwable $e) {
            $this->jsonResponse(app_error_response('INTERNAL_SERVER_ERROR', 'Login failed'), 500);
        }
    }

    public function logout()
    {
        $this->ensureSessionStarted();

        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        $this->jsonResponse(app_success_response(array('message' => 'Logged out successfully')));
    }

    public function me()
    {
        $this->ensureSessionStarted();

        if (empty($_SESSION['user_id'])) {
            $this->jsonResponse(app_error_response('UNAUTHORIZED', 'Not authenticated'), 401);
            return;
        }

        $user = $this->userRepo->getUserById((int) $_SESSION['user_id']);
        if (!$user) {
            $this->jsonResponse(app_error_response('NOT_FOUND', 'User not found'), 404);
            return;
        }

        $this->jsonResponse(app_success_response(array('user' => $user->toArray())));
    }
}
