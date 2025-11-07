<?php
/**
 * Admin Middleware
 * Protects admin routes by checking if user is logged in as admin
 */

class AdminMiddleware {
    /**
     * Check if user is authenticated as admin
     * Redirect to login if not authenticated
     */
    public static function handle() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if admin is logged in
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            // Store intended URL for redirect after login
            $_SESSION['admin_intended_url'] = $_SERVER['REQUEST_URI'];

            // Redirect to admin login
            header('Location: /admin/login');
            exit;
        }

        // Check if admin session is still valid (optional timeout check)
        if (isset($_SESSION['admin_last_activity'])) {
            $inactiveTime = time() - $_SESSION['admin_last_activity'];
            $sessionTimeout = 3600; // 1 hour

            if ($inactiveTime > $sessionTimeout) {
                // Session expired
                self::destroyAdminSession();
                header('Location: /admin/login?error=session_expired');
                exit;
            }
        }

        // Update last activity time
        $_SESSION['admin_last_activity'] = time();

        return true;
    }

    /**
     * Destroy admin session
     */
    private static function destroyAdminSession() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_last_activity']);
        unset($_SESSION['admin_intended_url']);
    }

    /**
     * Check if user is admin (without redirecting)
     * Useful for checking permissions in views
     */
    public static function isAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
    }

    /**
     * Get admin data from session
     */
    public static function getAdmin() {
        if (!self::isAdmin()) {
            return null;
        }

        return [
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null,
            'name' => $_SESSION['admin_name'] ?? null,
            'role' => $_SESSION['admin_role'] ?? 'admin'
        ];
    }
}
