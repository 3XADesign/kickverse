<?php
/**
 * Cart API Controller
 * Handles shopping cart operations
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Cart.php';
require_once __DIR__ . '/../../models/Product.php';

class CartController extends Controller {
    private $cartModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    /**
     * Get cart ID (or create if doesn't exist)
     */
    private function getCartId() {
        $customerId = $this->getUser()['customer_id'] ?? null;
        $sessionId = session_id();

        $cart = $this->cartModel->getOrCreate($customerId, $sessionId);
        return $cart['cart_id'];
    }

    /**
     * GET /api/cart
     * Get cart items
     */
    public function index() {
        try {
            $cartId = $this->getCartId();
            $items = $this->cartModel->getItems($cartId);

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

            $this->json([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'free_shipping_threshold' => 50
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el carrito'
            ], 500);
        }
    }

    /**
     * POST /api/cart/add
     * Add item to cart
     */
    public function add() {
        $data = $this->input();

        // Validate input
        $errors = $this->validate($data, [
            'product_id' => 'required',
            'variant_id' => 'required',
            'quantity' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            $productId = $data['product_id'];
            $variantId = $data['variant_id'];
            $quantity = (int) $data['quantity'];

            // Verify product exists and is active
            $product = $this->productModel->find($productId);
            if (!$product || !$product['is_active']) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no disponible'
                ], 404);
            }

            // Verify variant exists and has stock
            $sql = "SELECT * FROM product_variants WHERE variant_id = ? AND product_id = ? AND is_active = 1";
            $variant = $this->productModel->fetchOne($sql, [$variantId, $productId]);

            if (!$variant) {
                $this->json([
                    'success' => false,
                    'message' => 'Talla no disponible'
                ], 404);
            }

            if ($variant['stock_quantity'] < $quantity) {
                $this->json([
                    'success' => false,
                    'message' => 'Stock insuficiente'
                ], 400);
            }

            // Prepare customizations
            $customizations = [
                'has_patches' => isset($data['has_patches']) ? 1 : 0,
                'has_personalization' => isset($data['has_personalization']) ? 1 : 0,
                'personalization_name' => $data['personalization_name'] ?? null,
                'personalization_number' => $data['personalization_number'] ?? null
            ];

            $cartId = $this->getCartId();
            $this->cartModel->addItem($cartId, $productId, $variantId, $quantity, $customizations);

            // Get updated cart count
            $cartItems = $this->cartModel->getItems($cartId);
            $totalItems = 0;
            foreach ($cartItems as $item) {
                $totalItems += $item['quantity'];
            }

            $this->json([
                'success' => true,
                'message' => 'Producto añadido al carrito',
                'cart_count' => $totalItems
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al añadir al carrito'
            ], 500);
        }
    }

    /**
     * PUT /api/cart/update/:itemId
     * Update cart item quantity
     */
    public function update($itemId) {
        $data = $this->input();
        $quantity = (int) ($data['quantity'] ?? 0);

        if ($quantity < 1) {
            $this->json([
                'success' => false,
                'message' => 'Cantidad inválida'
            ], 400);
        }

        try {
            // Verify item exists and belongs to user's cart
            $cartId = $this->getCartId();
            $sql = "SELECT ci.*, pv.stock_quantity
                    FROM cart_items ci
                    JOIN product_variants pv ON ci.variant_id = pv.variant_id
                    WHERE ci.cart_item_id = ? AND ci.cart_id = ?";
            $item = $this->cartModel->fetchOne($sql, [$itemId, $cartId]);

            if (!$item) {
                $this->json([
                    'success' => false,
                    'message' => 'Artículo no encontrado'
                ], 404);
            }

            if ($item['stock_quantity'] < $quantity) {
                $this->json([
                    'success' => false,
                    'message' => 'Stock insuficiente'
                ], 400);
            }

            $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
            $this->cartModel->query($sql, [$quantity, $itemId]);

            $this->json([
                'success' => true,
                'message' => 'Carrito actualizado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el carrito'
            ], 500);
        }
    }

    /**
     * DELETE /api/cart/remove/:itemId
     * Remove item from cart
     */
    public function remove($itemId) {
        try {
            // Verify item belongs to user's cart
            $cartId = $this->getCartId();
            $sql = "SELECT * FROM cart_items WHERE cart_item_id = ? AND cart_id = ?";
            $item = $this->cartModel->fetchOne($sql, [$itemId, $cartId]);

            if (!$item) {
                $this->json([
                    'success' => false,
                    'message' => 'Artículo no encontrado'
                ], 404);
            }

            $this->cartModel->removeItem($itemId);

            $this->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al eliminar del carrito'
            ], 500);
        }
    }

    /**
     * DELETE /api/cart/clear
     * Clear cart
     */
    public function clear() {
        try {
            $cartId = $this->getCartId();
            $this->cartModel->clear($cartId);

            $this->json([
                'success' => true,
                'message' => 'Carrito vaciado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al vaciar el carrito'
            ], 500);
        }
    }
}
