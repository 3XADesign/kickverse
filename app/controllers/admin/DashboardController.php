<?php
/**
 * Admin Dashboard Controller
 * CRM Dashboard completo con estadísticas y widgets
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../config/database.php';

class DashboardController extends Controller {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->requireAdminAuth();
        $this->db = $this->getDatabase();
    }

    /**
     * Get database connection
     */
    private function getDatabase() {
        $config = require __DIR__ . '/../../../config/database.php';

        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            return $pdo;
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if user is admin
     */
    private function requireAdminAuth() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * Dashboard index
     */
    public function index() {
        try {
            // Obtener todas las estadísticas
            $data = [
                'page_title' => 'Dashboard - Admin Kickverse',
                'admin_name' => $_SESSION['admin_name'] ?? 'Administrador',
                'stats' => $this->getMainStats(),
                'recent_orders' => $this->getRecentOrders(),
                'low_stock_products' => $this->getLowStockProducts(),
                'new_customers_week' => $this->getNewCustomersThisWeek(),
                'pending_payments' => $this->getPendingPayments(),
                'top_products' => $this->getTopProducts(),
                'expiring_subscriptions' => $this->getExpiringSubscriptions(),
                'recent_activities' => $this->getRecentActivities()
            ];

            $this->view('admin/dashboard', $data, null);
        } catch (Exception $e) {
            die('Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Get main statistics (4 cards)
     */
    private function getMainStats() {
        // Total Clientes
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM customers
            WHERE deleted_at IS NULL
        ");
        $totalCustomers = $stmt->fetch()['total'] ?? 0;

        // Total Pedidos
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM orders
        ");
        $totalOrders = $stmt->fetch()['total'] ?? 0;

        // Ingresos del Mes
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0) as total
            FROM orders
            WHERE YEAR(created_at) = YEAR(CURDATE())
              AND MONTH(created_at) = MONTH(CURDATE())
              AND payment_status = 'completed'
        ");
        $monthRevenue = $stmt->fetch()['total'] ?? 0;

        // Suscripciones Activas
        $stmt = $this->db->query("
            SELECT COUNT(*) as total
            FROM subscriptions
            WHERE status = 'active'
        ");
        $activeSubscriptions = $stmt->fetch()['total'] ?? 0;

        return [
            'total_customers' => $totalCustomers,
            'total_orders' => $totalOrders,
            'month_revenue' => $monthRevenue,
            'active_subscriptions' => $activeSubscriptions
        ];
    }

    /**
     * Get recent orders (últimos 10)
     */
    private function getRecentOrders() {
        $stmt = $this->db->query("
            SELECT
                o.order_id,
                o.total_amount,
                o.order_status,
                o.payment_status,
                o.created_at,
                c.full_name as customer_name,
                c.email as customer_email
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            ORDER BY o.created_at DESC
            LIMIT 10
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get low stock products (< 10 units)
     */
    private function getLowStockProducts() {
        $stmt = $this->db->query("
            SELECT
                p.product_id,
                p.name,
                p.slug,
                t.name as team_name,
                pv.size,
                pv.stock_quantity,
                pv.variant_id
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.product_id
            LEFT JOIN teams t ON p.team_id = t.team_id
            WHERE pv.stock_quantity < 10
              AND pv.is_active = 1
              AND p.is_active = 1
            ORDER BY pv.stock_quantity ASC
            LIMIT 10
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get new customers this week
     */
    private function getNewCustomersThisWeek() {
        $stmt = $this->db->query("
            SELECT
                customer_id,
                full_name,
                email,
                phone,
                created_at,
                loyalty_tier
            FROM customers
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              AND deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT 10
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get pending payments
     */
    private function getPendingPayments() {
        $stmt = $this->db->query("
            SELECT
                o.order_id,
                o.total_amount,
                o.payment_method,
                o.created_at,
                c.full_name as customer_name,
                c.email as customer_email,
                c.phone as customer_phone
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.payment_status IN ('pending', 'awaiting_confirmation')
            ORDER BY o.created_at DESC
            LIMIT 10
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get top 5 selling products
     */
    private function getTopProducts() {
        $stmt = $this->db->query("
            SELECT
                p.product_id,
                p.name,
                p.slug,
                t.name as team_name,
                COUNT(oi.order_item_id) as total_sales,
                SUM(oi.quantity) as units_sold,
                SUM(oi.price * oi.quantity) as revenue
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            LEFT JOIN teams t ON p.team_id = t.team_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.payment_status = 'completed'
            GROUP BY p.product_id, p.name, p.slug, t.name
            ORDER BY units_sold DESC
            LIMIT 5
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get subscriptions expiring in next 30 days
     */
    private function getExpiringSubscriptions() {
        $stmt = $this->db->query("
            SELECT
                s.subscription_id,
                s.next_billing_date,
                s.status,
                s.monthly_price,
                c.full_name as customer_name,
                c.email as customer_email,
                c.phone as customer_phone,
                sp.plan_name
            FROM subscriptions s
            JOIN customers c ON s.customer_id = c.customer_id
            JOIN subscription_plans sp ON s.plan_id = sp.plan_id
            WHERE s.status = 'active'
              AND s.next_billing_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY s.next_billing_date ASC
            LIMIT 10
        ");

        return $stmt->fetchAll();
    }

    /**
     * Get recent system activities
     */
    private function getRecentActivities() {
        $activities = [];

        // Recent orders
        $stmt = $this->db->query("
            SELECT
                'order' as type,
                o.order_id as reference_id,
                CONCAT('Nuevo pedido #', o.order_id, ' - ', c.full_name) as description,
                o.created_at
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        $activities = array_merge($activities, $stmt->fetchAll());

        // New customers
        $stmt = $this->db->query("
            SELECT
                'customer' as type,
                customer_id as reference_id,
                CONCAT('Nuevo cliente: ', full_name) as description,
                created_at
            FROM customers
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $activities = array_merge($activities, $stmt->fetchAll());

        // New subscriptions
        $stmt = $this->db->query("
            SELECT
                'subscription' as type,
                s.subscription_id as reference_id,
                CONCAT('Nueva suscripción - ', c.full_name) as description,
                s.created_at
            FROM subscriptions s
            JOIN customers c ON s.customer_id = c.customer_id
            ORDER BY s.created_at DESC
            LIMIT 5
        ");
        $activities = array_merge($activities, $stmt->fetchAll());

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($activities, 0, 15);
    }
}
