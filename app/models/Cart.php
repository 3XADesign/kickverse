<?php
/**
 * Cart Model
 */

require_once __DIR__ . '/Model.php';

class Cart extends Model {
    protected $table = 'carts';
    protected $primaryKey = 'cart_id';

    public function getOrCreate($customerId = null, $sessionId = null) {
        if ($customerId) {
            $cart = $this->whereOne(['customer_id' => $customerId, 'cart_status' => 'active']);
        } else {
            $cart = $this->whereOne(['session_id' => $sessionId, 'cart_status' => 'active']);
        }

        if (!$cart) {
            $data = [
                'customer_id' => $customerId,
                'session_id' => $sessionId,
                'cart_status' => 'active',
                'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days'))
            ];
            $cartId = $this->create($data);
            return $this->find($cartId);
        }

        return $cart;
    }

    public function getItems($cartId) {
        $sql = "SELECT ci.*, p.name as product_name, p.slug as product_slug, p.base_price,
                       pv.size, pv.stock_quantity,
                       t.name as team_name, l.name as league_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY display_order ASC LIMIT 1) as image_path
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.product_id
                JOIN product_variants pv ON ci.variant_id = pv.variant_id
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE ci.cart_id = ?";

        return $this->fetchAll($sql, [$cartId]);
    }

    public function addItem($cartId, $productId, $variantId, $quantity = 1, $customizations = []) {
        // Check if item already exists
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ? AND variant_id = ?";
        $existing = $this->fetchOne($sql, [$cartId, $productId, $variantId]);

        if ($existing) {
            // Update quantity
            $sql = "UPDATE cart_items SET quantity = quantity + ? WHERE cart_item_id = ?";
            $this->query($sql, [$quantity, $existing['cart_item_id']]);
        } else {
            // Insert new item
            $data = array_merge([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $this->fetchOne("SELECT base_price FROM products WHERE product_id = ?", [$productId])['base_price']
            ], $customizations);

            $fields = implode(',', array_keys($data));
            $placeholders = str_repeat('?,', count($data) - 1) . '?';
            $sql = "INSERT INTO cart_items ({$fields}) VALUES ({$placeholders})";
            $this->query($sql, array_values($data));
        }

        // Update cart timestamp
        $this->query("UPDATE carts SET updated_at = NOW() WHERE cart_id = ?", [$cartId]);
    }

    public function removeItem($cartItemId) {
        $this->query("DELETE FROM cart_items WHERE cart_item_id = ?", [$cartItemId]);
    }

    public function clear($cartId) {
        $this->query("DELETE FROM cart_items WHERE cart_id = ?", [$cartId]);
    }

    public function convertToOrder($cartId, $orderId) {
        $this->update($cartId, ['cart_status' => 'converted', 'converted_to_order_id' => $orderId]);
    }
}
