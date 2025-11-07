<?php
/**
 * Cart Page Controller
 * Handles cart page
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Cart.php';

class CartPageController extends Controller {
    private $cartModel;

    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
    }

    /**
     * Cart page
     */
    public function index() {
        try {
            $customerId = $this->getUser()['customer_id'] ?? null;
            $sessionId = session_id();

            $cart = $this->cartModel->getOrCreate($customerId, $sessionId);
            $items = $this->cartModel->getItems($cart['cart_id']);

            // Calculate totals
            $subtotal = 0;
            foreach ($items as &$item) {
                $itemTotal = $item['unit_price'];

                if ($item['has_patches']) {
                    $itemTotal += 1.99;
                }

                if ($item['has_personalization']) {
                    $itemTotal += 2.99;
                }

                $item['item_total'] = $itemTotal * $item['quantity'];
                $subtotal += $item['item_total'];
            }

            $shippingCost = $subtotal >= 50 ? 0 : 5.99;
            $total = $subtotal + $shippingCost;

            $this->view('cart/index', [
                'items' => $items,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'free_shipping_threshold' => 50,
                'csrf_token' => $this->generateCSRF()
            ]);
        } catch (Exception $e) {
            die('Error loading cart: ' . $e->getMessage());
        }
    }
}
