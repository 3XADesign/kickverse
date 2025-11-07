<?php
/**
 * Email Verification Controller
 * Handles email verification for customer registration
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Customer.php';

class EmailVerificationController extends Controller {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    /**
     * GET /auth/verify-email/:token
     * Verify email with token and create session
     */
    public function verify($token) {
        try {
            // Find customer by verification token
            $customer = $this->customerModel->findByVerificationToken($token);

            if (!$customer) {
                // Invalid or expired token - redirect to home with modal
                $_SESSION['verification_result'] = [
                    'status' => 'invalid',
                    'title' => __('auth.verification_invalid'),
                    'message' => __('auth.verification_invalid_message')
                ];
                header('Location: /');
                exit;
            }

            // Check if already verified
            if ($customer['email_verified'] == 1) {
                // Already verified - create session and redirect
                $this->createSession($customer);
                $_SESSION['verification_result'] = [
                    'status' => 'invalid',
                    'title' => __('auth.verification_invalid'),
                    'message' => __('auth.verification_invalid_message')
                ];
                header('Location: /');
                exit;
            }

            // Mark email as verified
            $this->customerModel->verifyEmail($customer['customer_id']);

            // Create session
            $this->createSession($customer);

            // Redirect to home with success modal
            $_SESSION['verification_result'] = [
                'status' => 'success',
                'title' => __('auth.verification_success'),
                'message' => __('auth.verification_success_message')
            ];
            header('Location: /');
            exit;

        } catch (Exception $e) {
            error_log("Email verification error: " . $e->getMessage());

            $_SESSION['verification_result'] = [
                'status' => 'error',
                'title' => __('auth.verification_error'),
                'message' => __('auth.verification_error_message')
            ];
            header('Location: /');
            exit;
        }
    }

    /**
     * Create user session after verification
     */
    private function createSession($customer) {
        $_SESSION['user'] = [
            'customer_id' => $customer['customer_id'],
            'email' => $customer['email'],
            'full_name' => $customer['full_name'],
            'loyalty_tier' => $customer['loyalty_tier'],
            'loyalty_points' => $customer['loyalty_points']
        ];

        // Update last login
        $this->customerModel->updateLastLogin($customer['customer_id']);
    }
}
