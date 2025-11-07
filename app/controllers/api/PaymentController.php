<?php
/**
 * Payment API Controller
 * Handles Oxapay payment integration
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Order.php';

class PaymentController extends Controller {
    private $orderModel;
    private $oxapayApiKey;
    private $oxapayMerchantId;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();

        // Load Oxapay credentials from config
        $this->oxapayApiKey = $this->config['oxapay']['api_key'] ?? '';
        $this->oxapayMerchantId = $this->config['oxapay']['merchant_id'] ?? '';
    }

    /**
     * POST /api/payment/oxapay/create
     * Create OxaPay payment request
     */
    public function createOxaPayPayment() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            // Get checkout data from session
            if (empty($_SESSION['checkout']['summary'])) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay datos de checkout'
                ], 400);
                return;
            }

            $summary = $_SESSION['checkout']['summary'];
            $addressId = $_SESSION['checkout']['address_id'] ?? null;

            if (!$addressId) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay dirección de envío'
                ], 400);
                return;
            }

            // Create order when INITIATING payment
            $orderId = $this->createOrder($user['customer_id'], $addressId, $summary, 'oxapay');

            if (!$orderId) {
                throw new Exception('Error al crear el pedido');
            }

            // Create payment with OxaPay
            require_once __DIR__ . '/../../helpers/OxaPayAPI.php';
            $oxapay = new OxaPayAPI();

            // Generate unique trackId with timestamp to avoid duplicates
            $trackId = $orderId . '-' . time() . '-' . substr(md5(uniqid()), 0, 8);

            $paymentResult = $oxapay->createPayment(
                $trackId,
                $summary['total'],
                'USD',
                $this->config['app_url'] . '/api/payment/oxapay/webhook',
                [
                    'return_url' => $this->config['app_url'] . '/checkout/confirmacion?order=' . $orderId . '&method=oxapay',
                    'description' => 'Pedido #' . $orderId . ' - Kickverse',
                    'email' => $user['email']
                ]
            );

            if (!$paymentResult['success']) {
                // Delete the order if payment creation failed
                $this->orderModel->query("DELETE FROM order_items WHERE order_id = ?", [$orderId]);
                $this->orderModel->query("DELETE FROM orders WHERE order_id = ?", [$orderId]);
                throw new Exception('Error al crear el pago en OxaPay');
            }

            // Save payment transaction
            $sql = "INSERT INTO payment_transactions
                    (customer_id, order_id, payment_method, oxapay_transaction_id, amount, currency,
                     status, oxapay_response, initiated_at)
                    VALUES (?, ?, 'oxapay_btc', ?, ?, 'USD', 'pending', ?, NOW())";

            $this->orderModel->query($sql, [
                $user['customer_id'],
                $orderId,
                $paymentResult['data']['track_id'],
                $summary['total'],
                json_encode($paymentResult)
            ]);

            // Clear checkout session but NOT cart (cart cleared on confirmation page)
            unset($_SESSION['checkout']);

            $this->json([
                'success' => true,
                'payment_url' => $paymentResult['data']['payment_url'],
                'order_id' => $orderId
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/payment/telegram/create
     * Create manual Telegram payment
     */
    public function createTelegramPayment() {
        $this->requireAuth();

        $user = $this->getUser();

        try {
            // Get checkout data from session
            if (empty($_SESSION['checkout']['summary'])) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay datos de checkout'
                ], 400);
                return;
            }

            $summary = $_SESSION['checkout']['summary'];
            $addressId = $_SESSION['checkout']['address_id'] ?? null;

            if (!$addressId) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay dirección de envío'
                ], 400);
                return;
            }

            // Create order when INITIATING payment
            $orderId = $this->createOrder($user['customer_id'], $addressId, $summary, 'telegram');

            if (!$orderId) {
                throw new Exception('Error al crear el pedido');
            }

            // Generate unique payment code
            $paymentCode = 'TG-' . strtoupper(substr(md5($orderId . time()), 0, 8));

            // Save payment transaction
            $sql = "INSERT INTO payment_transactions
                    (customer_id, order_id, payment_method, manual_payment_reference, amount, currency,
                     status, initiated_at)
                    VALUES (?, ?, 'telegram_manual', ?, ?, 'USD', 'pending', NOW())";

            $this->orderModel->query($sql, [
                $user['customer_id'],
                $orderId,
                $paymentCode,
                $summary['total']
            ]);

            // Clear checkout session but NOT cart (cart cleared on confirmation page)
            unset($_SESSION['checkout']);

            $this->json([
                'success' => true,
                'redirect_url' => '/checkout/confirmacion?order=' . $orderId . '&method=telegram',
                'order_id' => $orderId,
                'payment_code' => $paymentCode
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al procesar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/payment/oxapay/webhook
     * OxaPay webhook callback
     */
    public function oxaPayWebhook() {
        try {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);

            // Verify webhook signature
            require_once __DIR__ . '/../../helpers/OxaPayAPI.php';
            $oxapay = new OxaPayAPI();

            $hmacHeader = $_SERVER['HTTP_X_HMAC'] ?? $_SERVER['HTTP_X_SIGNATURE'] ?? '';

            if (!$oxapay->verifyWebhook($rawInput, $hmacHeader)) {
                $this->json([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 401);
                return;
            }

            $trackId = $data['trackId'] ?? null;
            $status = $data['status'] ?? 'pending'; // Paid, Waiting, Expired, Cancelled

            if (!$trackId) {
                $this->json([
                    'success' => false,
                    'message' => 'Missing track ID'
                ], 400);
                return;
            }

            // Get payment transaction by trackId
            $transaction = $this->orderModel->fetchOne(
                "SELECT * FROM payment_transactions WHERE oxapay_transaction_id = ?",
                [$trackId]
            );

            if (!$transaction || empty($transaction['order_id'])) {
                $this->json([
                    'success' => false,
                    'message' => 'Transaction or order not found'
                ], 404);
                return;
            }

            $orderId = $transaction['order_id'];

            // Get order
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
                return;
            }

            // Update transaction status
            $paymentStatus = $this->mapOxapayStatus($status);
            $this->orderModel->query(
                "UPDATE payment_transactions SET status = ?, oxapay_response = ?, completed_at = NOW() WHERE transaction_id = ?",
                [$paymentStatus, json_encode($data), $transaction['transaction_id']]
            );

            // If payment successful, mark order as paid
            if ($status === 'Paid') {
                // Mark order as paid
                $this->orderModel->query(
                    "UPDATE orders SET payment_status = 'completed', order_status = 'processing' WHERE order_id = ?",
                    [$orderId]
                );

                // Award loyalty points (1 point per dollar)
                $points = floor($order['total_amount']);
                require_once __DIR__ . '/../../models/Customer.php';
                $customerModel = new Customer();
                $customerModel->addLoyaltyPoints(
                    $order['customer_id'],
                    $points,
                    'purchase',
                    $orderId,
                    "Compra del pedido #{$orderId}"
                );

            } elseif (in_array($status, ['Expired', 'Cancelled'])) {
                $this->orderModel->query(
                    "UPDATE orders SET payment_status = 'failed', order_status = 'cancelled' WHERE order_id = ?",
                    [$orderId]
                );
            }

            $this->json([
                'success' => true,
                'message' => 'Webhook processed'
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create order in database
     */
    private function createOrder($customerId, $addressId, $summary, $paymentMethod) {
        try {
            // Get address
            require_once __DIR__ . '/../../models/ShippingAddress.php';
            $addressModel = new ShippingAddress();
            $address = $addressModel->getAddress($addressId, $customerId);

            if (!$address) {
                throw new Exception('Dirección no encontrada');
            }

            // Get cart items
            require_once __DIR__ . '/../../models/Cart.php';
            $cartModel = new Cart();
            $cart = $cartModel->getOrCreate($customerId);

            if (!$cart) {
                throw new Exception('Carrito no encontrado');
            }

            $cartItems = $cartModel->getItems($cart['cart_id']);

            if (empty($cartItems)) {
                throw new Exception('Carrito vacío');
            }

            // Get coupon_id if coupon_code exists
            $couponId = null;
            if (!empty($summary['coupon_code'])) {
                $couponSql = "SELECT coupon_id FROM coupons WHERE code = ? AND is_active = 1";
                $coupon = $this->orderModel->fetchOne($couponSql, [$summary['coupon_code']]);
                if ($coupon) {
                    $couponId = $coupon['coupon_id'];
                }
            }

            // Debug log
            error_log('Creating order with summary: ' . json_encode($summary));
            error_log('Discount amount: ' . ($summary['discount'] ?? 0));
            error_log('Coupon ID: ' . ($couponId ?? 'NULL'));

            // Create order
            $sql = "INSERT INTO orders
                    (customer_id, order_date, total_amount, subtotal, shipping_cost,
                     discount_amount, coupon_id, order_status, payment_status,
                     shipping_address_id, payment_method)
                    VALUES (?, NOW(), ?, ?, ?, ?, ?, 'pending_payment', 'pending',
                            ?, ?)";

            $this->orderModel->query($sql, [
                $customerId,
                $summary['total'],
                $summary['subtotal'],
                $summary['shipping_cost'],
                $summary['discount'] ?? 0,
                $couponId,
                $addressId,
                $paymentMethod
            ]);

            $orderId = $this->orderModel->getLastInsertId();

            // Insert order items
            foreach ($cartItems as $item) {
                $patchesPrice = ($item['has_patches'] ?? 0) ? 1.99 : 0;
                $personalizationPrice = ($item['has_personalization'] ?? 0) ? 2.99 : 0;
                $subtotal = ($item['unit_price'] + $patchesPrice + $personalizationPrice) * $item['quantity'];

                $sql = "INSERT INTO order_items
                        (order_id, product_id, variant_id, quantity, unit_price, subtotal,
                         has_patches, patches_price, has_personalization,
                         personalization_name, personalization_number, personalization_price)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $this->orderModel->query($sql, [
                    $orderId,
                    $item['product_id'],
                    $item['variant_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $subtotal,
                    $item['has_patches'] ?? 0,
                    $patchesPrice,
                    $item['has_personalization'] ?? 0,
                    $item['personalization_name'] ?? null,
                    $item['personalization_number'] ?? null,
                    $personalizationPrice
                ]);
            }

            // Record coupon usage if a coupon was used
            if ($couponId && isset($summary['discount']) && $summary['discount'] > 0) {
                $sql = "INSERT INTO coupon_usage (coupon_id, customer_id, order_id, discount_applied, used_at)
                        VALUES (?, ?, ?, ?, NOW())";
                $this->orderModel->query($sql, [$couponId, $customerId, $orderId, $summary['discount']]);

                // Update coupon times_used
                $this->orderModel->query("UPDATE coupons SET times_used = times_used + 1 WHERE coupon_id = ?", [$couponId]);
            }

            // Don't clear cart here - will be cleared in webhook when payment is confirmed
            // $cartModel->clear($cart['cart_id']);

            return $orderId;

        } catch (Exception $e) {
            error_log('Error creating order: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw new Exception('Error al crear el pedido: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/payment/create
     * Create payment request with Oxapay (OLD METHOD - DEPRECATED)
     */
    public function create() {
        $this->requireAuth();

        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'order_id' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            $user = $this->getUser();
            $order = $this->orderModel->find($data['order_id']);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            // Verify order belongs to customer
            if ($order['customer_id'] != $user['customer_id']) {
                $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            // Verify order is pending payment
            if ($order['payment_status'] !== 'pending') {
                $this->json([
                    'success' => false,
                    'message' => 'Este pedido ya fue procesado'
                ], 400);
            }

            // Create payment request with Oxapay
            $paymentData = [
                'merchant' => $this->oxapayMerchantId,
                'amount' => $order['total_amount'],
                'currency' => 'USD',
                'orderId' => $order['order_id'],
                'callbackUrl' => $this->config['app_url'] . '/api/payment/callback',
                'returnUrl' => $this->config['app_url'] . '/order-confirmation?order_id=' . $order['order_id'],
                'description' => 'Pedido #' . $order['order_id'] . ' - Kickverse'
            ];

            $response = $this->callOxapayAPI('https://api.oxapay.com/merchants/request', $paymentData);

            if (!$response || !isset($response['payLink'])) {
                throw new Exception('Error al crear el pago');
            }

            // Store payment info
            $sql = "INSERT INTO payments (order_id, payment_method, amount, currency, payment_status,
                                        transaction_id, payment_data, created_at)
                    VALUES (?, 'oxapay', ?, 'USD', 'pending', ?, ?, NOW())";
            $this->orderModel->query($sql, [
                $order['order_id'],
                $order['total_amount'],
                $response['trackId'],
                json_encode($response)
            ]);

            $this->json([
                'success' => true,
                'data' => [
                    'payment_url' => $response['payLink'],
                    'track_id' => $response['trackId']
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/payment/callback
     * Oxapay webhook callback
     */
    public function callback() {
        $data = $this->input();

        try {
            // Verify callback signature (Oxapay sends hmac signature)
            $signature = $_SERVER['HTTP_X_OXAPAY_SIGNATURE'] ?? '';

            if (!$this->verifyOxapaySignature($data, $signature)) {
                $this->json([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 401);
            }

            $orderId = $data['orderId'];
            $trackId = $data['trackId'];
            $status = $data['status']; // 'Paid', 'Waiting', 'Expired', 'Cancelled'

            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Update payment record
            $sql = "UPDATE payments
                    SET payment_status = ?,
                        transaction_id = ?,
                        payment_data = ?,
                        updated_at = NOW()
                    WHERE order_id = ? AND payment_method = 'oxapay'";

            $paymentStatus = $this->mapOxapayStatus($status);
            $this->orderModel->query($sql, [
                $paymentStatus,
                $trackId,
                json_encode($data),
                $orderId
            ]);

            // Update order if payment is completed
            if ($status === 'Paid') {
                $this->orderModel->updatePaymentStatus($orderId, 'completed', $trackId);

                // Award loyalty points (1 point per dollar spent)
                $points = floor($order['total_amount']);
                $sql = "SELECT * FROM customers WHERE customer_id = ?";
                $customer = $this->orderModel->fetchOne($sql, [$order['customer_id']]);

                require_once __DIR__ . '/../../models/Customer.php';
                $customerModel = new Customer();
                $customerModel->addLoyaltyPoints(
                    $order['customer_id'],
                    $points,
                    'purchase',
                    $orderId,
                    "Compra del pedido #{$orderId}"
                );
            } elseif (in_array($status, ['Expired', 'Cancelled'])) {
                $this->orderModel->updatePaymentStatus($orderId, 'failed');
            }

            $this->json([
                'success' => true,
                'message' => 'Callback processed'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/payment/status/:orderId
     * Check payment status
     */
    public function status($orderId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            // Verify order belongs to customer
            if ($order['customer_id'] != $user['customer_id']) {
                $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $sql = "SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1";
            $payment = $this->orderModel->fetchOne($sql, [$orderId]);

            $this->json([
                'success' => true,
                'data' => [
                    'payment_status' => $order['payment_status'],
                    'order_status' => $order['order_status'],
                    'payment_method' => $payment['payment_method'] ?? null,
                    'transaction_id' => $payment['transaction_id'] ?? null
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al verificar el pago'
            ], 500);
        }
    }

    /**
     * Call Oxapay API
     */
    private function callOxapayAPI($url, $data) {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->oxapayApiKey
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Verify Oxapay callback signature
     */
    private function verifyOxapaySignature($data, $signature) {
        // Oxapay uses HMAC-SHA512 for signature verification
        $payload = json_encode($data);
        $expectedSignature = hash_hmac('sha512', $payload, $this->oxapayApiKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * GET /api/payment/oxapay/status/:orderId
     * Check OxaPay payment status
     */
    public function checkOxaPayStatus($orderId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();

            // Get order
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
                return;
            }

            // Verify order belongs to customer
            if ($order['customer_id'] != $user['customer_id']) {
                $this->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
                return;
            }

            // Get payment transaction
            $sql = "SELECT * FROM payment_transactions
                    WHERE order_id = ? AND payment_method = 'oxapay'
                    ORDER BY initiated_at DESC LIMIT 1";
            $payment = $this->orderModel->fetchOne($sql, [$orderId]);

            $this->json([
                'success' => true,
                'data' => [
                    'order_id' => $order['order_id'],
                    'order_status' => $order['order_status'],
                    'payment_status' => $order['payment_status'],
                    'total_amount' => $order['total_amount'],
                    'payment_method' => $payment['payment_method'] ?? null,
                    'transaction_reference' => $payment['transaction_reference'] ?? null
                ]
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al verificar el pago'
            ], 500);
        }
    }

    /**
     * Map Oxapay status to internal payment status
     */
    private function mapOxapayStatus($oxapayStatus) {
        $statusMap = [
            'Paid' => 'completed',
            'Waiting' => 'pending',
            'Expired' => 'failed',
            'Cancelled' => 'cancelled'
        ];

        return $statusMap[$oxapayStatus] ?? 'pending';
    }
}
