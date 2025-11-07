<?php
/**
 * Admin Clientes API Controller
 * API endpoints para gestión de clientes en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Order.php';

class AdminClientesApiController extends Controller {
    private $customerModel;
    private $orderModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->customerModel = new Customer();
        $this->orderModel = new Order();
    }

    /**
     * Check admin authentication
     */
    private function checkAdminAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            exit;
        }
    }

    /**
     * Get all customers with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = ['deleted_at IS NULL'];
            $params = [];

            // Filter by status
            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $where[] = "customer_status = ?";
                $params[] = $_GET['status'];
            }

            // Filter by tier
            if (isset($_GET['tier']) && $_GET['tier'] !== '') {
                $where[] = "loyalty_tier = ?";
                $params[] = $_GET['tier'];
            }

            // Filter by language
            if (isset($_GET['language']) && $_GET['language'] !== '') {
                $where[] = "preferred_language = ?";
                $params[] = $_GET['language'];
            }

            // Search by name, email, phone, telegram, whatsapp
            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR telegram_username LIKE ? OR whatsapp_number LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM customers
                        {$whereClause}";

            $countResult = $this->customerModel->fetchAll($countSql, $params);
            $total = $countResult[0]['total'] ?? 0;

            // Get customers with statistics
            $sql = "SELECT
                        c.customer_id,
                        c.email,
                        c.full_name,
                        c.phone,
                        c.telegram_username,
                        c.whatsapp_number,
                        c.customer_status,
                        c.loyalty_tier,
                        c.loyalty_points,
                        c.preferred_language,
                        c.registration_date,
                        c.last_activity_date,
                        COUNT(DISTINCT o.order_id) as total_orders,
                        COALESCE(SUM(CASE WHEN o.order_status IN ('processing', 'shipped', 'delivered') THEN o.total_amount ELSE 0 END), 0) as total_spent
                    FROM customers c
                    LEFT JOIN orders o ON c.customer_id = o.customer_id
                    {$whereClause}
                    GROUP BY c.customer_id
                    ORDER BY c.registration_date DESC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $customers = $this->customerModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'customers' => $customers,
                'pagination' => [
                    'current_page' => $page,
                    'pages' => $totalPages,
                    'per_page' => $this->perPage,
                    'total' => $total,
                    'from' => $offset + 1,
                    'to' => min($offset + $this->perPage, $total)
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error getting customers: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar clientes'
            ], 500);
        }
    }

    /**
     * Get single customer details
     */
    public function getOne($customerId) {
        try {
            $customer = $this->customerModel->find($customerId);

            if (!$customer) {
                $this->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
                return;
            }

            // Get customer statistics
            $stats = $this->getCustomerStats($customerId);

            // Get shipping addresses
            $addresses = $this->customerModel->getAddresses($customerId);

            // Get recent orders (últimos 10)
            $recentOrders = $this->getRecentOrders($customerId, 10);

            // Get active subscriptions
            $subscriptions = $this->getActiveSubscriptions($customerId);

            // Get preferences
            $preferences = $this->customerModel->getPreferences($customerId);

            // Remove sensitive data
            unset($customer['password_hash']);
            unset($customer['password_reset_token']);
            unset($customer['email_verification_token']);

            // Combine all data
            $response = [
                'success' => true,
                'customer' => array_merge($customer, [
                    'statistics' => $stats,
                    'shipping_addresses' => $addresses,
                    'recent_orders' => $recentOrders,
                    'subscriptions' => $subscriptions,
                    'preferences' => $preferences
                ])
            ];

            $this->json($response);

        } catch (Exception $e) {
            error_log("Error getting customer: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar cliente'
            ], 500);
        }
    }

    /**
     * Get customer statistics
     */
    private function getCustomerStats($customerId) {
        $sql = "SELECT
                    COUNT(o.order_id) as total_orders_count,
                    COALESCE(SUM(CASE WHEN o.order_status IN ('processing', 'shipped', 'delivered') THEN o.total_amount ELSE 0 END), 0) as total_spent,
                    c.loyalty_points,
                    COUNT(DISTINCT CASE WHEN o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN o.order_id END) as orders_last_30_days,
                    MAX(o.order_date) as last_order_date
                FROM customers c
                LEFT JOIN orders o ON c.customer_id = o.customer_id
                WHERE c.customer_id = ?
                GROUP BY c.customer_id";

        return $this->customerModel->fetchOne($sql, [$customerId]);
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($customerId, $limit = 10) {
        $sql = "SELECT
                    order_id,
                    order_type,
                    order_status,
                    payment_status,
                    total_amount,
                    order_date,
                    tracking_number
                FROM orders
                WHERE customer_id = ?
                ORDER BY order_date DESC
                LIMIT ?";

        return $this->orderModel->fetchAll($sql, [$customerId, $limit]);
    }

    /**
     * Get active subscriptions
     */
    private function getActiveSubscriptions($customerId) {
        $sql = "SELECT
                    s.subscription_id,
                    s.status,
                    s.start_date,
                    s.next_billing_date,
                    s.current_period_start,
                    s.current_period_end,
                    sp.plan_name,
                    sp.monthly_price
                FROM subscriptions s
                JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE s.customer_id = ?
                    AND s.status IN ('active', 'pending', 'paused')
                ORDER BY s.start_date DESC";

        return $this->customerModel->fetchAll($sql, [$customerId]);
    }
}
