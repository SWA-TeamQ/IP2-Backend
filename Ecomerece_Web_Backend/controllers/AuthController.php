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

    public function register()
    {
        // 1. Get JSON input from shared utility.
        $data = app_get_request_body();

        // 2. Strict validation.
        if (empty($data['email']) || empty($data['password']) || empty($data['fullName'])) {
            $this->jsonResponse(
                app_error_response('VALIDATION_ERROR', 'Full Name, Email, and Password are required'),
                400
            );
            return;
        }

            // 3. Check if user exists
            if ($this->userRepo->getUserByEmail($data['email'])) {
                $this->jsonResponse(
                    app_error_response('CONFLICT', 'Email already registered'),
                    409
                );
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

            $this->jsonResponse(
                app_success_response(array(
                    'user' => array(
                        'id' => (int) $userId,
                        'fullName' => $data['fullName'],
                        'email' => $data['email'],
                        'role' => 'customer'
                    )
                ))
            );
        } catch (Exception $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'An error occurred during registration', array('hint' => $e->getMessage())),
                500
            );
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

        $userRow = db_fetch_one("SELECT * FROM users WHERE email = :email", [':email' => $data['email']]);

        if ($userRow && password_verify($data['password'], $userRow['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $userRow['id'];

            $this->jsonResponse(
                app_success_response(array(
                    'user' => array(
                        'id' => (int) $userRow['id'],
                        'fullName' => $userRow['full_name'],
                        'email' => $userRow['email'],
                        'role' => isset($userRow['role']) ? $userRow['role'] : 'customer'
                    )
                ))
            );
        } else {
            $this->jsonResponse(
                app_error_response('UNAUTHORIZED', 'Invalid email or password'),
                401
            );
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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