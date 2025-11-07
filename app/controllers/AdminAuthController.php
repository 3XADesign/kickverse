<?php
/**
 * Admin Authentication Controller
 * Handles magic link authentication for admin users
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminAuthController extends Controller {
    private $adminModel;

    public function __construct() {
        parent::__construct();
        $this->adminModel = new Admin();
    }

    /**
     * Show admin login page
     */
    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['admin_id'])) {
            header('Location: /admin/dashboard');
            exit;
        }

        $this->view('admin/login', [
            'page_title' => 'Admin Login - Kickverse'
        ]);
    }

    /**
     * Send magic link to admin email
     */
    public function sendMagicLink() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json([
                'success' => false,
                'message' => 'Por favor, introduce un email válido.'
            ], 400);
            return;
        }

        // Generate token (returns dummy token if email doesn't exist - security)
        $token = $this->adminModel->createMagicToken(
            $email,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // Check if email actually exists (for email sending logic only)
        $adminExists = $this->adminModel->emailExists($email);

        if ($adminExists) {
            // TODO: In production, send actual email
            // For development, log the magic link
            $magicLink = "http://" . $_SERVER['HTTP_HOST'] . "/admin/verify/" . $token;
            error_log("Magic Link for {$email}: {$magicLink}");

            // Optionally write to file for easy access during development
            file_put_contents(
                __DIR__ . '/../../storage/logs/magic_links.log',
                date('Y-m-d H:i:s') . " - {$email}: {$magicLink}\n",
                FILE_APPEND
            );
        }

        // Always return success (prevent email enumeration)
        $this->json([
            'success' => true,
            'message' => 'Si el email está registrado, recibirás un enlace de acceso en tu bandeja de entrada.'
        ]);
    }

    /**
     * Verify magic link token and log in admin
     */
    public function verifyMagicLink($token) {
        if (empty($token)) {
            header('Location: /admin/login?error=invalid_token');
            exit;
        }

        // Verify token
        $adminData = $this->adminModel->verifyToken($token);

        if (!$adminData) {
            header('Location: /admin/login?error=expired_or_invalid');
            exit;
        }

        // Create admin session
        $_SESSION['admin_id'] = $adminData['admin_id'];
        $_SESSION['admin_email'] = $adminData['email'];
        $_SESSION['admin_name'] = $adminData['name'];
        $_SESSION['admin_role'] = $adminData['role'] ?? 'admin';
        $_SESSION['admin_logged_in'] = true;

        // Redirect to dashboard
        header('Location: /admin/dashboard');
        exit;
    }

    /**
     * Logout admin
     */
    public function logout() {
        // Clear admin session
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_logged_in']);

        // Redirect to login
        header('Location: /admin/login?message=logged_out');
        exit;
    }

    /**
     * Show admin dashboard
     */
    public function dashboard() {
        // Check if admin is logged in
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin/login');
            exit;
        }

        // Get dashboard statistics
        $stats = $this->adminModel->getDashboardStats();

        $this->view('admin/dashboard', [
            'page_title' => 'Dashboard - Admin Kickverse',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin',
            'stats' => $stats
        ]);
    }
}
