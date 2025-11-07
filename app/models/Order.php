<?php
/**
 * Order Model
 * Manages customer orders
 */

require_once __DIR__ . '/Model.php';

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    /**
     * Get last insert ID
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * Create new order
     */
    public function createOrder($customerId, $items, $shippingAddressId, $couponId = null) {
        $this->beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $itemTotal = ($item['unit_price'] +
                             ($item['has_patches'] ? 1.99 : 0) +
                             ($item['has_personalization'] ? 2.99 : 0)) * $item['quantity'];
                $subtotal += $itemTotal;
            }

            // Calculate shipping
            $shippingCost = $subtotal >= 50 ? 0 : 5.99;

            // Calculate discount
            $discountAmount = 0;
            if ($couponId) {
                $coupon = $this->fetchOne("SELECT * FROM coupons WHERE coupon_id = ?", [$couponId]);
                if ($coupon) {
                    if ($coupon['discount_type'] === 'fixed') {
                        $discountAmount = $coupon['discount_value'];
                    } else {
                        $discountAmount = min(
                            ($subtotal * $coupon['discount_value'] / 100),
                            $coupon['max_discount_amount'] ?? PHP_INT_MAX
                        );
                    }
                }
            }

            $totalAmount = $subtotal - $discountAmount + $shippingCost;

            // Create order
            $orderData = [
                'customer_id' => $customerId,
                'order_type' => 'catalog',
                'order_status' => 'pending_payment',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_id' => $couponId,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'shipping_address_id' => $shippingAddressId,
            ];

            $orderId = $this->create($orderData);

            // Create order items
            foreach ($items as $item) {
                $itemSubtotal = ($item['unit_price'] +
                               ($item['has_patches'] ? 1.99 : 0) +
                               ($item['has_personalization'] ? 2.99 : 0)) * $item['quantity'];

                $itemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'has_patches' => $item['has_patches'] ? 1 : 0,
                    'patches_price' => $item['has_patches'] ? 1.99 : 0,
                    'has_personalization' => $item['has_personalization'] ? 1 : 0,
                    'personalization_name' => $item['personalization_name'] ?? null,
                    'personalization_number' => $item['personalization_number'] ?? null,
                    'personalization_price' => $item['has_personalization'] ? 2.99 : 0,
                    'subtotal' => $itemSubtotal,
                    'is_free_item' => $item['is_free_item'] ?? 0,
                ];

                $sql = "INSERT INTO order_items (" . implode(',', array_keys($itemData)) . ")
                        VALUES (" . str_repeat('?,', count($itemData) - 1) . "?)";

                $this->query($sql, array_values($itemData));
            }

            // Record coupon usage
            if ($couponId) {
                $sql = "INSERT INTO coupon_usage (coupon_id, customer_id, order_id, discount_applied)
                        VALUES (?, ?, ?, ?)";
                $this->query($sql, [$couponId, $customerId, $orderId, $discountAmount]);

                // Update coupon times_used
                $this->query("UPDATE coupons SET times_used = times_used + 1 WHERE coupon_id = ?", [$couponId]);
            }

            $this->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get order with items
     */
    public function getOrderWithItems($orderId) {
        $order = $this->find($orderId);

        if (!$order) {
            return null;
        }

        // Get customer info
        $sql = "SELECT c.full_name, c.email, c.telegram_username, c.whatsapp_number
                FROM customers c
                WHERE c.customer_id = ?";
        $order['customer'] = $this->fetchOne($sql, [$order['customer_id']]);

        // Get coupon code if exists
        if ($order['coupon_id']) {
            $sql = "SELECT code FROM coupons WHERE coupon_id = ?";
            $coupon = $this->fetchOne($sql, [$order['coupon_id']]);
            $order['coupon_code'] = $coupon ? $coupon['code'] : null;
        } else {
            $order['coupon_code'] = null;
        }

        // Get items with images
        $sql = "SELECT oi.*, p.name as product_name, p.slug as product_slug,
                       pv.size,
                       t.name as team_name, l.name as league_name,
                       pi.image_path
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                JOIN product_variants pv ON oi.variant_id = pv.variant_id
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                LEFT JOIN (
                    SELECT product_id, image_path
                    FROM product_images
                    WHERE image_type = 'main'
                    GROUP BY product_id
                ) pi ON p.product_id = pi.product_id
                WHERE oi.order_id = ?";
        $order['items'] = $this->fetchAll($sql, [$orderId]);

        // Get shipping address
        $sql = "SELECT * FROM shipping_addresses WHERE address_id = ?";
        $order['shipping_address'] = $this->fetchOne($sql, [$order['shipping_address_id']]);

        return $order;
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders($customerId, $limit = 20) {
        $sql = "SELECT o.*,
                       c.code as coupon_code,
                       COUNT(DISTINCT oi.order_item_id) as item_count
                FROM {$this->table} o
                LEFT JOIN coupons c ON o.coupon_id = c.coupon_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.customer_id = ?
                GROUP BY o.order_id, c.code
                ORDER BY o.order_date DESC
                LIMIT ?";

        $orders = $this->fetchAll($sql, [$customerId, $limit]);

        // Get order items with product details for each order
        foreach ($orders as &$order) {
            $itemSql = "SELECT oi.quantity, oi.unit_price,
                               p.name as product_name,
                               pv.size,
                               pi.image_path
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.product_id
                        JOIN product_variants pv ON oi.variant_id = pv.variant_id
                        LEFT JOIN (
                            SELECT product_id, image_path
                            FROM product_images
                            WHERE image_type = 'main'
                            GROUP BY product_id
                        ) pi ON p.product_id = pi.product_id
                        WHERE oi.order_id = ?
                        LIMIT 3";

            $order['items'] = $this->fetchAll($itemSql, [$order['order_id']]);
        }

        return $orders;
    }

    /**
     * Get customer orders with filtering
     */
    public function getCustomerOrdersFiltered($customerId, $status = null, $search = null, $limit = 50) {
        $params = [$customerId];
        $sql = "SELECT o.*,
                       COUNT(oi.order_item_id) as item_count
                FROM {$this->table} o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.customer_id = ?";

        if ($status) {
            $sql .= " AND o.order_status = ?";
            $params[] = $status;
        }

        if ($search) {
            $sql .= " AND (o.order_id LIKE ? OR o.tracking_number LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $sql .= " GROUP BY o.order_id
                  ORDER BY o.order_date DESC
                  LIMIT ?";
        $params[] = $limit;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status, $trackingNumber = null) {
        $data = ['order_status' => $status];

        if ($trackingNumber) {
            $data['tracking_number'] = $trackingNumber;
        }

        if ($status === 'shipped') {
            $data['shipped_date'] = date('Y-m-d');
        } elseif ($status === 'delivered') {
            $data['delivered_date'] = date('Y-m-d');
        }

        return $this->update($orderId, $data);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderId, $status, $paymentId = null) {
        $data = ['payment_status' => $status];

        if ($paymentId) {
            $data['payment_id'] = $paymentId;
        }

        // If payment completed, update order status
        if ($status === 'completed') {
            $data['order_status'] = 'processing';
        }

        return $this->update($orderId, $data);
    }

    /**
     * Get pending orders
     */
    public function getPendingOrders($limit = 50) {
        $sql = "SELECT o.*, c.full_name as customer_name
                FROM {$this->table} o
                JOIN customers c ON o.customer_id = c.customer_id
                WHERE o.order_status IN ('pending_payment', 'processing')
                ORDER BY o.order_date DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Get revenue stats
     */
    public function getRevenueStats($startDate, $endDate) {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value
                FROM {$this->table}
                WHERE order_status IN ('delivered', 'shipped')
                  AND order_date BETWEEN ? AND ?";

        return $this->fetchOne($sql, [$startDate, $endDate]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $reason = null) {
        $data = [
            'order_status' => 'cancelled',
            'admin_notes' => $reason
        ];

        return $this->update($orderId, $data);
    }
}
