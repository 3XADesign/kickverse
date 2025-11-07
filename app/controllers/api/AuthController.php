<?php
/**
 * Authentication API Controller
 * Handles login, register, and social auth
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../helpers/Mailer.php';

class AuthController extends Controller {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    /**
     * POST /api/auth/register
     * Register new customer (classic auth)
     */
    public function register() {
        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'full_name' => 'required|min:2'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            // Check if email already exists
            $existing = $this->customerModel->findByEmail($data['email']);
            if ($existing) {
                $lang = $_SESSION['lang'] ?? 'es';
                $message = $lang === 'es' ?
                    'Este correo electrónico ya está en uso' :
                    'This email address is already in use';

                $this->json([
                    'success' => false,
                    'message' => $message
                ], 409);
            }

            // Register customer (requires email verification)
            // Save the current language preference
            $currentLang = $_SESSION['lang'] ?? 'es';

            $result = $this->customerModel->register(
                $data['email'],
                $data['password'],
                $data['full_name'],
                $data['phone'] ?? null,
                $currentLang
            );

            $customerId = $result['customer_id'];
            $verificationToken = $result['verification_token'];

            // Send verification email
            $lang = $_SESSION['lang'] ?? 'es';
            $verificationLink = 'https://kickverse.es/auth/verify-email/' . $verificationToken;

            $mailer = new Mailer();
            $emailSent = $mailer->sendVerificationEmail(
                $data['email'],
                $data['full_name'],
                $verificationLink,
                $lang
            );

            if (!$emailSent) {
                error_log("Failed to send verification email to: " . $data['email']);
            }

            // DO NOT create session - user must verify email first
            $message = $lang === 'es' ?
                '¡Registro exitoso! Revisa tu email para verificar tu cuenta.' :
                'Registration successful! Check your email to verify your account.';

            $this->json([
                'success' => true,
                'message' => $message,
                'requires_verification' => true,
                'email' => $data['email']
            ]);
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/auth/login
     * Login with email and password
     */
    public function login() {
        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
            return;
        }

        try {
            // Find customer first
            $customer = $this->customerModel->findByEmail($data['email']);

            // Check if customer exists
            if (!$customer) {
                $this->json([
                    'success' => false,
                    'message' => __('auth.invalid_credentials')
                ], 401);
                return;
            }

            // Verify password
            error_log("Attempting password verification for: " . $data['email']);
            error_log("Password from request: " . $data['password']);
            error_log("Customer hash from DB: " . $customer['password_hash']);

            if (!$this->customerModel->verifyPassword($data['email'], $data['password'])) {
                error_log("Password verification failed for email: " . $data['email']);
                $this->json([
                    'success' => false,
                    'message' => __('auth.invalid_credentials')
                ], 401);
                return;
            }

            error_log("Password verification succeeded for: " . $data['email']);

            // Check email verification
            if ($customer['email_verified'] == 0) {
                $lang = $_SESSION['lang'] ?? 'es';
                $message = $lang === 'es' ?
                    'Tu cuenta aún no ha sido verificada. Revisa tu email.' :
                    'Your account has not been verified yet. Check your email.';

                $this->json([
                    'success' => false,
                    'message' => $message,
                    'email_not_verified' => true,
                    'email' => $customer['email']
                ], 403);
                return;
            }

            // Check if customer is active
            if ($customer['customer_status'] !== 'active') {
                $this->json([
                    'success' => false,
                    'message' => __('auth.inactive_account')
                ], 403);
                return;
            }

            // Create session
            $_SESSION['user'] = [
                'customer_id' => $customer['customer_id'],
                'email' => $customer['email'],
                'full_name' => $customer['full_name'],
                'loyalty_tier' => $customer['loyalty_tier'],
                'loyalty_points' => $customer['loyalty_points']
            ];

            // Load user's preferred language
            if (!empty($customer['preferred_language'])) {
                $_SESSION['lang'] = $customer['preferred_language'];
            }

            // Update last login
            $this->customerModel->updateLastLogin($customer['customer_id']);

            $this->json([
                'success' => true,
                'message' => __('auth.login_successful'),
                'data' => $_SESSION['user']
            ]);
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => __('auth.login_error')
            ], 500);
        }
    }

    /**
     * GET /api/auth/verify-email
     * Verify email with token
     */
    public function verifyEmail() {
        $token = $this->get('token');

        if (!$token) {
            $this->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 400);
        }

        try {
            // Split token and timestamp
            $parts = explode('|', $token);
            if (count($parts) !== 2) {
                $this->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 400);
            }

            list($tokenHash, $expiresAt) = $parts;

            // Check if token expired
            if (time() > intval($expiresAt)) {
                $this->json([
                    'success' => false,
                    'message' => 'El enlace de verificación ha expirado',
                    'expired' => true
                ], 410);
            }

            // Find customer by full token (with timestamp)
            $sql = "SELECT * FROM customers
                    WHERE email_verification_token = ?
                    AND deleted_at IS NULL
                    LIMIT 1";

            $customer = $this->customerModel->fetchOne($sql, [$token]);

            if (!$customer) {
                $this->json([
                    'success' => false,
                    'message' => 'Token inválido o expirado'
                ], 404);
            }

            // Check if already verified
            if ($customer['customer_status'] === 'active' && $customer['email_verified'] == 1) {
                $this->json([
                    'success' => true,
                    'message' => 'Email ya verificado',
                    'already_verified' => true
                ]);
            }

            // Activate customer
            $this->customerModel->update($customer['customer_id'], [
                'customer_status' => 'active',
                'email_verified' => 1,
                'email_verification_token' => null
            ]);

            $this->json([
                'success' => true,
                'message' => 'Email verificado exitosamente'
            ]);
        } catch (Exception $e) {
            error_log("Email verification error: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al verificar el email'
            ], 500);
        }
    }

    /**
     * POST /api/auth/logout
     * Logout current user
     */
    public function logout() {
        // Clear all session data
        $_SESSION = [];

        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Redirect to home page after logout
        header('Location: /', true, 302);
        exit;
    }

    /**
     * GET /api/auth/me
     * Get current user info
     */
    public function me() {
        if (!$this->isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => __('auth.not_authenticated')
            ], 401);
        }

        try {
            $user = $this->getUser();
            $customer = $this->customerModel->find($user['customer_id']);

            // Remove sensitive data
            unset($customer['password_hash']);
            unset($customer['email_verification_token']);

            $this->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al obtener usuario'
            ], 500);
        }
    }

    /**
     * POST /api/auth/social/telegram
     * Login or register via Telegram
     */
    public function loginTelegram() {
        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'telegram_username' => 'required',
            'full_name' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            // Check if customer exists
            $customer = $this->customerModel->findByTelegram($data['telegram_username']);

            if (!$customer) {
                // Register new customer
                $customerId = $this->customerModel->registerSocial(
                    $data['full_name'],
                    $data['telegram_username'],
                    null,
                    $data['phone'] ?? null
                );
                $customer = $this->customerModel->find($customerId);
            }

            // Check if customer is active
            if ($customer['customer_status'] !== 'active') {
                $this->json([
                    'success' => false,
                    'message' => __('auth.inactive_account')
                ], 403);
            }

            // Create session
            $_SESSION['user'] = [
                'customer_id' => $customer['customer_id'],
                'telegram_username' => $customer['telegram_username'],
                'full_name' => $customer['full_name'],
                'loyalty_tier' => $customer['loyalty_tier'],
                'loyalty_points' => $customer['loyalty_points']
            ];

            // Update last login
            $this->customerModel->updateLastLogin($customer['customer_id']);

            $this->json([
                'success' => true,
                'message' => __('auth.login_successful'),
                'data' => $_SESSION['user']
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error en el login de Telegram'
            ], 500);
        }
    }

    /**
     * POST /api/auth/social/whatsapp
     * Login or register via WhatsApp
     */
    public function loginWhatsApp() {
        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'whatsapp_number' => 'required',
            'full_name' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            // Check if customer exists
            $customer = $this->customerModel->findByWhatsApp($data['whatsapp_number']);

            if (!$customer) {
                // Register new customer
                $customerId = $this->customerModel->registerSocial(
                    $data['full_name'],
                    null,
                    $data['whatsapp_number'],
                    $data['phone'] ?? null
                );
                $customer = $this->customerModel->find($customerId);
            }

            // Check if customer is active
            if ($customer['customer_status'] !== 'active') {
                $this->json([
                    'success' => false,
                    'message' => __('auth.inactive_account')
                ], 403);
            }

            // Create session
            $_SESSION['user'] = [
                'customer_id' => $customer['customer_id'],
                'whatsapp_number' => $customer['whatsapp_number'],
                'full_name' => $customer['full_name'],
                'loyalty_tier' => $customer['loyalty_tier'],
                'loyalty_points' => $customer['loyalty_points']
            ];

            // Update last login
            $this->customerModel->updateLastLogin($customer['customer_id']);

            $this->json([
                'success' => true,
                'message' => __('auth.login_successful'),
                'data' => $_SESSION['user']
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error en el login de WhatsApp'
            ], 500);
        }
    }

    /**
     * POST /api/auth/resend-verification
     * Resend verification email
     */
    public function resendVerification() {
        $data = $this->input();

        // Validate
        if (empty($data['email'])) {
            $this->json([
                'success' => false,
                'message' => 'Email es requerido'
            ], 400);
            return;
        }

        try {
            // Find customer
            $customer = $this->customerModel->findByEmail($data['email']);

            if (!$customer) {
                $this->json([
                    'success' => false,
                    'message' => 'Email no encontrado'
                ], 404);
                return;
            }

            // Check if already verified
            if ($customer['email_verified'] == 1) {
                $lang = $_SESSION['lang'] ?? 'es';
                $message = $lang === 'es' ?
                    'Esta cuenta ya está verificada' :
                    'This account is already verified';

                $this->json([
                    'success' => false,
                    'message' => $message
                ], 400);
                return;
            }

            // Generate new verification token
            $verificationToken = bin2hex(random_bytes(32));

            // Update token in database
            $this->customerModel->query(
                "UPDATE customers SET email_verification_token = ? WHERE customer_id = ?",
                [$verificationToken, $customer['customer_id']]
            );

            // Send verification email
            $lang = $_SESSION['lang'] ?? 'es';
            $verificationLink = 'https://kickverse.es/auth/verify-email/' . $verificationToken;

            $mailer = new Mailer();
            $emailSent = $mailer->sendVerificationEmail(
                $customer['email'],
                $customer['full_name'],
                $verificationLink,
                $lang
            );

            if (!$emailSent) {
                error_log("Failed to resend verification email to: " . $customer['email']);
                $message = $lang === 'es' ?
                    'Error al enviar el correo de verificación' :
                    'Error sending verification email';

                $this->json([
                    'success' => false,
                    'message' => $message
                ], 500);
                return;
            }

            $message = $lang === 'es' ?
                'Correo de verificación reenviado correctamente' :
                'Verification email resent successfully';

            $this->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            error_log("Resend verification error: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al reenviar verificación: ' . $e->getMessage()
            ], 500);
        }
    }
}
