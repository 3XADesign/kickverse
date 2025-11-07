<?php
/**
 * Checkout Page Controller
 * Handles the 3-step checkout flow:
 * 1. /checkout/datos - Personal data and shipping address
 * 2. /checkout/resumen - Order summary with coupon validation
 * 3. /checkout/pago - Payment methods
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/ShippingAddress.php';
require_once __DIR__ . '/../models/Cart.php';

class CheckoutPageController extends Controller {
    private $customerModel;
    private $addressModel;
    private $cartModel;
    private $currentCart;
    private $currentCartItems;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->addressModel = new ShippingAddress();
        $this->cartModel = new Cart();

        // Require authentication for all checkout pages
        $this->requireAuth();

        // Validate that cart is not empty
        $this->validateCart();
    }

    /**
     * Validate that the cart has items
     */
    private function validateCart() {
        $customerId = $this->getUser()['customer_id'] ?? null;
        $sessionId = session_id();

        $cart = $this->cartModel->getOrCreate($customerId, $sessionId);
        $items = $this->cartModel->getItems($cart['cart_id']);

        if (empty($items)) {
            $this->setFlash('error', __('checkout.error_no_items'));
            $this->redirect('/carrito');
        }

        // Store cart data in property for reuse
        $this->currentCart = $cart;
        $this->currentCartItems = $items;
    }

    /**
     * Step 1: Personal data and shipping address
     * Route: /checkout/datos
     */
    public function datos() {
        $user = $this->getUser();
        $customerId = $user['customer_id'];

        // Get customer data
        $customer = $this->customerModel->find($customerId);

        // Get customer addresses
        $addresses = $this->addressModel->getByCustomer($customerId);

        // Get default address
        $defaultAddress = $this->addressModel->getDefault($customerId);

        // Pre-select address from session or default
        $selectedAddressId = $_SESSION['checkout']['address_id'] ?? ($defaultAddress['address_id'] ?? null);

        // Calculate cart totals for sidebar
        $cart_items = $this->currentCartItems;
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $itemTotal = $item['unit_price'];
            if ($item['has_patches']) $itemTotal += 1.99;
            if ($item['has_personalization']) $itemTotal += 2.99;
            $subtotal += $itemTotal * $item['quantity'];
        }
        $shipping_cost = $subtotal >= 50 ? 0 : 5.99;
        $total = $subtotal + $shipping_cost;

        $this->view('checkout/datos', [
            'customer' => $customer,
            'addresses' => $addresses,
            'selected_address_id' => $selectedAddressId,
            'cart_items' => $cart_items,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'total' => $total,
            'csrf_token' => $this->generateCSRF(),
            'page_title' => __('checkout.step_1_title') . ' - Kickverse',
            'additional_css' => ['/css/checkout.css']
        ]);
    }

    /**
     * Step 2: Order summary with coupon validation
     * Route: /checkout/resumen
     */
    public function resumen() {
        // Validate that address is selected
        if (empty($_SESSION['checkout']['address_id'])) {
            $this->setFlash('error', __('checkout.error_no_address'));
            $this->redirect('/checkout/datos');
            return;
        }

        $user = $this->getUser();
        $customerId = $user['customer_id'];

        // Get customer and address data
        $customer = $this->customerModel->find($customerId);
        $addressId = $_SESSION['checkout']['address_id'];
        $address = $this->addressModel->getAddress($addressId, $customerId);

        // Validate address belongs to customer
        if (!$address) {
            $this->setFlash('error', __('checkout.error_no_address'));
            $this->redirect('/checkout/datos');
            return;
        }

        // Get cart items
        $items = $this->currentCartItems;

        // Calculate subtotal
        $subtotal = 0;
        foreach ($items as &$item) {
            $itemTotal = $item['unit_price'];

            // Add customizations
            if ($item['has_patches']) {
                $itemTotal += 1.99;
            }

            if ($item['has_personalization']) {
                $itemTotal += 2.99;
            }

            $item['item_total'] = $itemTotal * $item['quantity'];
            $subtotal += $item['item_total'];
        }

        // Calculate shipping
        $shippingCost = $subtotal >= 50 ? 0 : 5.99;

        // Apply coupon if exists
        $discount = 0;
        $couponCode = $_SESSION['checkout']['coupon_code'] ?? null;
        $couponData = null;

        if ($couponCode) {
            $couponData = $this->validateCoupon($couponCode, $subtotal);
            if ($couponData) {
                $discount = $couponData['discount_amount'];
            } else {
                // Invalid coupon, remove from session
                unset($_SESSION['checkout']['coupon_code']);
                $couponCode = null;
            }
        }

        // Calculate total
        $total = $subtotal + $shippingCost - $discount;

        $this->view('checkout/resumen', [
            'customer' => $customer,
            'address' => $address,
            'cart_items' => $items,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'coupon_code' => $couponCode,
            'coupon' => $couponData,
            'free_shipping_threshold' => 50,
            'csrf_token' => $this->generateCSRF(),
            'page_title' => __('checkout.step_2_title') . ' - Kickverse',
            'additional_css' => ['/css/checkout.css']
        ]);
    }

    /**
     * Process Step 2 form (address selection and coupon)
     * Route: POST /checkout/procesar-paso-2
     */
    public function processStep2() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout/datos');
            return;
        }

        // Validate CSRF
        $csrfToken = $this->post('csrf_token');
        if (!$this->validateCSRF($csrfToken)) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/checkout/datos');
            return;
        }

        $user = $this->getUser();
        $customerId = $user['customer_id'];

        // Process address selection
        $addressId = $this->post('address_id');

        if ($addressId) {
            // Verify address belongs to customer
            $address = $this->addressModel->getAddress($addressId, $customerId);

            if ($address) {
                $_SESSION['checkout']['address_id'] = $addressId;
            } else {
                $this->setFlash('error', __('checkout.error_no_address'));
                $this->redirect('/checkout/datos');
                return;
            }
        } else {
            $this->setFlash('error', __('checkout.error_no_address'));
            $this->redirect('/checkout/datos');
            return;
        }

        // Process new address if provided
        $addNewAddress = $this->post('add_new_address');
        if ($addNewAddress) {
            $newAddressData = [
                'customer_id' => $customerId,
                'recipient_name' => $this->post('recipient_name'),
                'phone' => $this->post('phone'),
                'email' => $this->post('email', $user['email']),
                'street_address' => $this->post('street_address'),
                'additional_address' => $this->post('additional_address'),
                'city' => $this->post('city'),
                'province' => $this->post('province'),
                'postal_code' => $this->post('postal_code'),
                'country' => $this->post('country', 'España'),
                'additional_notes' => $this->post('additional_notes'),
                'is_default' => 0
            ];

            // Validate required fields
            if (empty($newAddressData['recipient_name']) ||
                empty($newAddressData['phone']) ||
                empty($newAddressData['street_address']) ||
                empty($newAddressData['city']) ||
                empty($newAddressData['postal_code'])) {

                $this->setFlash('error', __('checkout.required_field'));
                $this->redirect('/checkout/datos');
                return;
            }

            // If save address is checked, save to database
            if ($this->post('save_address')) {
                $this->addressModel->createAddress($newAddressData);
            }

            // Store temporary address in session
            $_SESSION['checkout']['temp_address'] = $newAddressData;
        }

        // Process coupon code
        $couponAction = $this->post('coupon_action');

        if ($couponAction === 'apply') {
            $couponCode = $this->post('coupon_code');

            if ($couponCode) {
                // Validate coupon
                $items = $this->currentCartItems;
                $subtotal = 0;
                foreach ($items as $item) {
                    $itemTotal = $item['unit_price'];
                    if ($item['has_patches']) $itemTotal += 1.99;
                    if ($item['has_personalization']) $itemTotal += 2.99;
                    $subtotal += $itemTotal * $item['quantity'];
                }

                $couponData = $this->validateCoupon($couponCode, $subtotal);

                if ($couponData) {
                    $_SESSION['checkout']['coupon_code'] = $couponCode;
                    $this->setFlash('success', __('checkout.coupon_applied'));
                } else {
                    $this->setFlash('error', __('checkout.coupon_invalid'));
                }
            }
        } elseif ($couponAction === 'remove') {
            unset($_SESSION['checkout']['coupon_code']);
            $this->setFlash('success', __('checkout.coupon_removed'));
        }

        // Redirect to summary page
        $this->redirect('/checkout/resumen');
    }

    /**
     * Step 3: Payment methods
     * Route: /checkout/pago
     */
    public function pago() {
        // Validate that all data is complete
        if (empty($_SESSION['checkout']['address_id'])) {
            $this->setFlash('error', __('checkout.error_no_address'));
            $this->redirect('/checkout/datos');
            return;
        }

        $user = $this->getUser();
        $customerId = $user['customer_id'];

        // Get customer and address
        $customer = $this->customerModel->find($customerId);
        $addressId = $_SESSION['checkout']['address_id'];
        $address = $this->addressModel->getAddress($addressId, $customerId);

        // Get cart items
        $items = $this->currentCartItems;

        // Calculate totals
        $subtotal = 0;
        foreach ($items as &$item) {
            $itemTotal = $item['unit_price'];
            if ($item['has_patches']) $itemTotal += 1.99;
            if ($item['has_personalization']) $itemTotal += 2.99;
            $item['item_total'] = $itemTotal * $item['quantity'];
            $subtotal += $item['item_total'];
        }

        $shippingCost = $subtotal >= 50 ? 0 : 5.99;

        // Apply coupon if exists
        $discount = 0;
        $couponCode = $_SESSION['checkout']['coupon_code'] ?? null;
        $couponData = null;

        if ($couponCode) {
            $couponData = $this->validateCoupon($couponCode, $subtotal);
            if ($couponData) {
                $discount = $couponData['discount_amount'];
            } else {
                unset($_SESSION['checkout']['coupon_code']);
                $couponCode = null;
            }
        }

        $total = $subtotal + $shippingCost - $discount;

        // Store final summary in session for payment processing
        $_SESSION['checkout']['summary'] = [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'coupon_code' => $couponCode
        ];

        // Build order summary array for view
        $order_summary = [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'item_count' => count($items)
        ];

        $this->view('checkout/pago', [
            'customer' => $customer,
            'address' => $address,
            'cart_items' => $items,
            'order_summary' => $order_summary,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'coupon_code' => $couponCode,
            'csrf_token' => $this->generateCSRF(),
            'page_title' => __('checkout.step_3_title') . ' - Kickverse',
            'additional_css' => ['/css/checkout.css']
        ]);
    }

    /**
     * GET /checkout/confirmacion
     * Show order confirmation page after payment
     */
    public function confirmacion() {
        $orderId = $_GET['order'] ?? null;
        $method = $_GET['method'] ?? 'oxapay';

        if (!$orderId) {
            header('Location: /');
            exit;
        }

        // Get order
        $order = $this->customerModel->fetchOne(
            "SELECT * FROM orders WHERE order_id = ?",
            [$orderId]
        );

        if (!$order) {
            header('Location: /');
            exit;
        }

        // If user is logged in, verify order belongs to them AND clear cart
        if ($this->isLoggedIn()) {
            $user = $this->getUser();
            if ($order['customer_id'] != $user['customer_id']) {
                header('Location: /');
                exit;
            }

            // Clear cart now that order is confirmed
            require_once __DIR__ . '/../models/Cart.php';
            $cartModel = new Cart();
            $cart = $cartModel->getOrCreate($user['customer_id']);
            if ($cart) {
                $cartModel->clear($cart['cart_id']);
            }
        }

        // Get payment code if Telegram payment
        $paymentCode = null;
        $telegramUrl = 'https://t.me/esKickverse';

        if ($method === 'telegram') {
            $payment = $this->customerModel->fetchOne(
                "SELECT manual_payment_reference FROM payment_transactions
                 WHERE order_id = ? AND payment_method = 'telegram_manual'
                 ORDER BY initiated_at DESC LIMIT 1",
                [$orderId]
            );
            $paymentCode = $payment['manual_payment_reference'] ?? null;
        }

        $this->view('checkout/confirmacion', [
            'order' => $order,
            'payment_method' => $method,
            'payment_code' => $paymentCode,
            'telegram_url' => $telegramUrl,
            'page_title' => __('checkout.order_confirmed') . ' - Kickverse',
            'additional_css' => ['/css/checkout.css']
        ]);
    }

    /**
     * Validate coupon code
     *
     * @param string $code Coupon code
     * @param float $subtotal Order subtotal
     * @return array|null Coupon data if valid, null otherwise
     */
    private function validateCoupon($code, $subtotal) {
        // Query coupon from database
        $sql = "SELECT * FROM coupons
                WHERE code = ?
                AND is_active = 1
                AND (valid_from IS NULL OR valid_from <= NOW())
                AND (valid_until IS NULL OR valid_until >= NOW())
                LIMIT 1";

        $coupon = $this->customerModel->fetchOne($sql, [$code]);

        if (!$coupon) {
            return null;
        }

        // Check minimum purchase requirement
        if ($coupon['min_purchase_amount'] && $subtotal < $coupon['min_purchase_amount']) {
            return null;
        }

        // Calculate discount
        $discountAmount = 0;

        if ($coupon['discount_type'] === 'fixed') {
            $discountAmount = $coupon['discount_value'];
        } elseif ($coupon['discount_type'] === 'percentage') {
            $discountAmount = ($subtotal * $coupon['discount_value']) / 100;

            // Apply max discount if set
            if ($coupon['max_discount_amount'] && $discountAmount > $coupon['max_discount_amount']) {
                $discountAmount = $coupon['max_discount_amount'];
            }
        }

        return [
            'coupon_id' => $coupon['coupon_id'],
            'code' => $coupon['code'],
            'discount_type' => $coupon['discount_type'],
            'discount_value' => $coupon['discount_value'],
            'discount_amount' => $discountAmount
        ];
    }

    /**
     * Clear checkout session data
     */
    public function clearCheckout() {
        unset($_SESSION['checkout']);
        $this->redirect('/');
    }
}
