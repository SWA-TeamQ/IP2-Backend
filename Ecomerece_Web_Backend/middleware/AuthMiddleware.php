<?php

class AuthMiddleware {
    /**
     * Prevents access if the user is not logged in.
     */
    public static function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => ["message" => "Unauthorized. Please log in first."]]);
            exit;
        }
    }

    /**
     * Prevents access if the user is not an Admin.
     */
    public static function isAdmin() {
        self::isAuthenticated(); 
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => ["message" => "Forbidden. Admin privileges required."]]);
            exit;
        }
    }
}