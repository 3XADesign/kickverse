<?php
/**
 * Order API Controller
 * Handles order creation and management
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Cart.php';
require_once __DIR__ . '/../../models/Customer.php';

class OrderController extends Controller {
    private $orderModel;
    private $cartModel;
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->customerModel = new Customer();
    }

    /**
     * POST /api/orders/create
     * Create order from cart
     */
    public function create() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        // Validate
        $errors = $this->validate($data, [
            'shipping_address_id' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            // Get cart items
            $cartId = $this->cartModel->getOrCreate($user['customer_id'], session_id())['cart_id'];
            $cartItems = $this->cartModel->getItems($cartId);

            if (empty($cartItems)) {
                $this->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
                ], 400);
            }

            // Validate shipping address belongs to customer
            $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ? AND is_active = 1";
            $address = $this->orderModel->fetchOne($sql, [$data['shipping_address_id'], $user['customer_id']]);

            if (!$address) {
                $this->json([
                    'success' => false,
                    'message' => 'Dirección de envío no válida'
                ], 400);
            }

            // Validate coupon if provided
            $couponId = null;
            if (!empty($data['coupon_code'])) {
                $sql = "SELECT * FROM coupons WHERE code = ? AND is_active = 1
                        AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                        AND (usage_limit IS NULL OR times_used < usage_limit)";
                $coupon = $this->orderModel->fetchOne($sql, [$data['coupon_code']]);

                if (!$coupon) {
                    $this->json([
                        'success' => false,
                        'message' => 'Cupón inválido o expirado'
                    ], 400);
                }

                // Check if customer can use this coupon
                if ($coupon['customer_id'] && $coupon['customer_id'] != $user['customer_id']) {
                    $this->json([
                        'success' => false,
                        'message' => 'Este cupón no es válido para tu cuenta'
                    ], 400);
                }

                $couponId = $coupon['coupon_id'];
            }

            // Create order
            $orderId = $this->orderModel->createOrder(
                $user['customer_id'],
                $cartItems,
                $data['shipping_address_id'],
                $couponId
            );

            // Convert cart to order
            $this->cartModel->convertToOrder($cartId, $orderId);

            // Get complete order
            $order = $this->orderModel->getOrderWithItems($orderId);

            $this->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => $order
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/orders
     * Get customer orders
     */
    public function index() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $orders = $this->orderModel->getCustomerOrders($user['customer_id']);

            $this->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar los pedidos'
            ], 500);
        }
    }

    /**
     * GET /api/orders/:id
     * Get order details
     */
    public function show($orderId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $order = $this->orderModel->getOrderWithItems($orderId);

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

            $this->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el pedido'
            ], 500);
        }
    }

    /**
     * POST /api/orders/:id/cancel
     * Cancel order
     */
    public function cancel($orderId) {
        $this->requireAuth();

        $data = $this->input();

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

            // Can only cancel if not shipped
            if (in_array($order['order_status'], ['shipped', 'delivered', 'cancelled'])) {
                $this->json([
                    'success' => false,
                    'message' => 'No se puede cancelar este pedido'
                ], 400);
            }

            $this->orderModel->cancelOrder($orderId, $data['reason'] ?? 'Cancelado por el cliente');

            $this->json([
                'success' => true,
                'message' => 'Pedido cancelado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cancelar el pedido'
            ], 500);
        }
    }

    /**
     * POST /api/orders/validate-coupon
     * Validate coupon code
     */
    public function validateCoupon() {
        $this->requireAuth();

        $data = $this->input();

        if (empty($data['code'])) {
            $this->json([
                'success' => false,
                'message' => 'Código requerido'
            ], 400);
        }

        try {
            $user = $this->getUser();

            $sql = "SELECT * FROM coupons WHERE code = ? AND is_active = 1
                    AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                    AND (usage_limit IS NULL OR times_used < usage_limit)";
            $coupon = $this->orderModel->fetchOne($sql, [$data['code']]);

            if (!$coupon) {
                $this->json([
                    'success' => false,
                    'message' => 'Cupón inválido o expirado'
                ], 400);
            }

            // Check if customer-specific
            if ($coupon['customer_id'] && $coupon['customer_id'] != $user['customer_id']) {
                $this->json([
                    'success' => false,
                    'message' => 'Este cupón no es válido para tu cuenta'
                ], 400);
            }

            $this->json([
                'success' => true,
                'data' => [
                    'coupon_id' => $coupon['coupon_id'],
                    'code' => $coupon['code'],
                    'discount_type' => $coupon['discount_type'],
                    'discount_value' => $coupon['discount_value'],
                    'max_discount_amount' => $coupon['max_discount_amount']
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al validar el cupón'
            ], 500);
        }
    }
}
