<?php
/**
 * Checkout API Controller
 * Handles checkout operations (coupons, validation)
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Model.php';
require_once __DIR__ . '/../../helpers/i18n.php';

class CheckoutAPIController extends Controller {
    private $db;

    public function __construct() {
        parent::__construct();

        // Initialize i18n
        i18n::init();

        // Get database instance
        require_once __DIR__ . '/../../Database.php';
        $this->db = Database::getInstance();
    }

    /**
     * POST /api/checkout/apply-coupon
     * Apply a coupon code to the checkout
     */
    public function applyCoupon() {
        // Require authentication
        if (!$this->isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => __('auth.not_authenticated', 'Not authenticated')
            ], 401);
        }

        // Get input data
        $data = $this->input();
        $couponCode = trim($data['coupon_code'] ?? '');
        $subtotal = floatval($data['subtotal'] ?? 0);

        // Validate input
        if (empty($couponCode)) {
            $this->json([
                'success' => false,
                'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
            ], 400);
        }

        if ($subtotal <= 0) {
            $this->json([
                'success' => false,
                'message' => __('checkout.error_no_items', 'Your cart is empty')
            ], 400);
        }

        try {
            // Find coupon in database
            $sql = "SELECT * FROM coupons WHERE code = ? LIMIT 1";
            $coupon = $this->db->fetchOne($sql, [$couponCode]);

            if (!$coupon) {
                $this->json([
                    'success' => false,
                    'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
                ], 404);
            }

            // Validate coupon status
            if (!$coupon['is_active']) {
                $this->json([
                    'success' => false,
                    'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
                ], 400);
            }

            // Validate dates
            $now = time();

            if ($coupon['valid_from']) {
                $validFrom = strtotime($coupon['valid_from']);
                if ($validFrom && $now < $validFrom) {
                    $this->json([
                        'success' => false,
                        'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
                    ], 400);
                }
            }

            if ($coupon['valid_until']) {
                $validUntil = strtotime($coupon['valid_until']);
                if ($validUntil && $now > $validUntil) {
                    $this->json([
                        'success' => false,
                        'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
                    ], 400);
                }
            }

            // Validate minimum purchase amount
            $minPurchase = floatval($coupon['min_purchase_amount'] ?? 0);
            if ($subtotal < $minPurchase) {
                $this->json([
                    'success' => false,
                    'message' => sprintf(
                        __('checkout.coupon_min_purchase', 'Minimum purchase of €%.2f required'),
                        $minPurchase
                    )
                ], 400);
            }

            // Validate usage limits
            $maxUses = $coupon['max_uses'] ?? null;
            $timesUsed = $coupon['times_used'] ?? 0;

            if ($maxUses !== null && $timesUsed >= $maxUses) {
                $this->json([
                    'success' => false,
                    'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
                ], 400);
            }

            // Check per-customer usage limit
            $maxUsesPerCustomer = $coupon['max_uses_per_customer'] ?? null;
            if ($maxUsesPerCustomer !== null) {
                $user = $this->getUser();
                $customerId = $user['customer_id'] ?? null;

                if ($customerId) {
                    $usageQuery = "SELECT COUNT(*) as usage_count
                                   FROM orders
                                   WHERE customer_id = ?
                                   AND coupon_code = ?";
                    $usageResult = $this->db->fetchOne($usageQuery, [$customerId, $couponCode]);
                    $customerUsage = $usageResult['usage_count'] ?? 0;

                    if ($customerUsage >= $maxUsesPerCustomer) {
                        $this->json([
                            'success' => false,
                            'message' => __('checkout.coupon_limit_reached', 'Coupon usage limit reached')
                        ], 400);
                    }
                }
            }

            // Check first order only restriction
            if ($coupon['applies_to_first_order_only']) {
                $user = $this->getUser();
                $customerId = $user['customer_id'] ?? null;

                if ($customerId) {
                    $orderCountQuery = "SELECT COUNT(*) as order_count
                                        FROM orders
                                        WHERE customer_id = ?
                                        AND order_status != 'cancelled'";
                    $orderCountResult = $this->db->fetchOne($orderCountQuery, [$customerId]);
                    $orderCount = $orderCountResult['order_count'] ?? 0;

                    if ($orderCount > 0) {
                        $this->json([
                            'success' => false,
                            'message' => __('checkout.coupon_first_order_only', 'This coupon is only valid for first orders')
                        ], 400);
                    }
                }
            }

            // Calculate discount
            $discount = 0;
            $discountType = $coupon['discount_type'];
            $discountValue = floatval($coupon['discount_value']);

            if ($discountType === 'fixed') {
                // Fixed amount discount
                $discount = min($discountValue, $subtotal);
            } elseif ($discountType === 'percentage') {
                // Percentage discount
                $discount = ($subtotal * $discountValue) / 100;

                // Apply max discount limit if set
                $maxDiscount = $coupon['max_discount_amount'] ?? null;
                if ($maxDiscount !== null && $discount > $maxDiscount) {
                    $discount = floatval($maxDiscount);
                }

                // Ensure discount doesn't exceed subtotal
                $discount = min($discount, $subtotal);
            }

            // Round to 2 decimal places
            $discount = round($discount, 2);

            // Store coupon in session
            if (!isset($_SESSION['checkout'])) {
                $_SESSION['checkout'] = [];
            }

            $_SESSION['checkout']['coupon_code'] = $couponCode;
            $_SESSION['checkout']['coupon_discount'] = $discount;
            $_SESSION['checkout']['coupon_id'] = $coupon['coupon_id'];

            // Return success response
            $this->json([
                'success' => true,
                'message' => __('checkout.coupon_applied', 'Coupon applied successfully'),
                'discount' => $discount,
                'coupon_code' => $couponCode,
                'discount_type' => $discountType,
                'discount_value' => $discountValue
            ], 200);

        } catch (Exception $e) {
            error_log("Error applying coupon: " . $e->getMessage());

            $this->json([
                'success' => false,
                'message' => __('common.unexpected_error', 'An unexpected error occurred')
            ], 500);
        }
    }

    /**
     * POST /api/checkout/remove-coupon
     * Remove applied coupon from checkout
     */
    public function removeCoupon() {
        // Require authentication
        if (!$this->isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => __('auth.not_authenticated', 'Not authenticated')
            ], 401);
        }

        try {
            // Remove coupon from session
            if (isset($_SESSION['checkout'])) {
                unset($_SESSION['checkout']['coupon_code']);
                unset($_SESSION['checkout']['coupon_discount']);
                unset($_SESSION['checkout']['coupon_id']);
            }

            $this->json([
                'success' => true,
                'message' => __('checkout.coupon_removed', 'Coupon removed')
            ], 200);

        } catch (Exception $e) {
            error_log("Error removing coupon: " . $e->getMessage());

            $this->json([
                'success' => false,
                'message' => __('common.unexpected_error', 'An unexpected error occurred')
            ], 500);
        }
    }

    /**
     * GET /api/checkout/validate-coupon
     * Validate a coupon code without applying it
     */
    public function validateCoupon() {
        // Require authentication
        if (!$this->isLoggedIn()) {
            $this->json([
                'success' => false,
                'message' => __('auth.not_authenticated', 'Not authenticated')
            ], 401);
        }

        // Get input data
        $couponCode = trim($this->get('coupon_code', ''));
        $subtotal = floatval($this->get('subtotal', 0));

        // Validate input
        if (empty($couponCode)) {
            $this->json([
                'success' => false,
                'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon')
            ], 400);
        }

        try {
            // Find coupon in database
            $sql = "SELECT * FROM coupons WHERE code = ? LIMIT 1";
            $coupon = $this->db->fetchOne($sql, [$couponCode]);

            if (!$coupon) {
                $this->json([
                    'success' => false,
                    'message' => __('checkout.coupon_invalid', 'Invalid or expired coupon'),
                    'valid' => false
                ], 200);
            }

            // Check if coupon is valid
            $isValid = true;
            $reason = '';

            // Check active status
            if (!$coupon['is_active']) {
                $isValid = false;
                $reason = 'inactive';
            }

            // Check dates
            $now = time();

            if ($coupon['valid_from']) {
                $validFrom = strtotime($coupon['valid_from']);
                if ($validFrom && $now < $validFrom) {
                    $isValid = false;
                    $reason = 'not_started';
                }
            }

            if ($coupon['valid_until']) {
                $validUntil = strtotime($coupon['valid_until']);
                if ($validUntil && $now > $validUntil) {
                    $isValid = false;
                    $reason = 'expired';
                }
            }

            // Check minimum purchase
            $minPurchase = floatval($coupon['min_purchase_amount'] ?? 0);
            if ($subtotal < $minPurchase) {
                $isValid = false;
                $reason = 'min_purchase';
            }

            // Check usage limits
            $maxUses = $coupon['max_uses'] ?? null;
            $timesUsed = $coupon['times_used'] ?? 0;

            if ($maxUses !== null && $timesUsed >= $maxUses) {
                $isValid = false;
                $reason = 'max_uses';
            }

            $this->json([
                'success' => true,
                'valid' => $isValid,
                'reason' => $reason,
                'coupon' => [
                    'code' => $coupon['code'],
                    'discount_type' => $coupon['discount_type'],
                    'discount_value' => $coupon['discount_value'],
                    'min_purchase_amount' => $coupon['min_purchase_amount']
                ]
            ], 200);

        } catch (Exception $e) {
            error_log("Error validating coupon: " . $e->getMessage());

            $this->json([
                'success' => false,
                'message' => __('common.unexpected_error', 'An unexpected error occurred')
            ], 500);
        }
    }
}
