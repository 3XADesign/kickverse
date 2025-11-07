<?php
/**
 * Admin Authentication Controller
 * Handles magic link authentication for admin users
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Admin.php';
require_once __DIR__ . '/../../helpers/Mailer.php';

class AdminAuthController extends Controller {
    private $adminModel;
    private $mailer;

    public function __construct() {
        parent::__construct();
        $this->adminModel = new Admin();
        $this->mailer = new Mailer();
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
        ], null); // Sin layout
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
        $admin = $this->adminModel->findByEmail($email);

        if ($admin) {
            // Generate magic link
            $magicLink = "https://" . $_SERVER['HTTP_HOST'] . "/admin/verify/" . $token;

            // Send professional email
            $emailSent = $this->mailer->sendAdminMagicLink(
                $email,
                $admin['full_name'] ?? $admin['username'],
                $magicLink
            );

            // Log for development
            if (!$emailSent) {
                error_log("Failed to send email to {$email}. Magic Link: {$magicLink}");
            } else {
                error_log("Magic link sent to {$email}");
            }

            // Also log to file for easy access during development
            $logDir = __DIR__ . '/../../../storage/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            file_put_contents(
                $logDir . '/magic_links.log',
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
        $_SESSION['admin_name'] = $adminData['full_name'] ?? $adminData['username'];
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
     * Show admin dashboard (redirects to DashboardController)
     */
    public function dashboard() {
        // Check if admin is logged in
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin/login');
            exit;
        }

        // Redirect to DashboardController
        require_once __DIR__ . '/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
    }
}
