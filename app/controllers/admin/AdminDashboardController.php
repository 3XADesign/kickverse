<?php
/**
 * Admin Dashboard Controller
 * Main admin panel dashboard
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Product.php';

class AdminDashboardController extends Controller {
    private $orderModel;
    private $customerModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdminAuth();
        $this->orderModel = new Order();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    /**
     * Check if user is admin
     */
    private function requireAdminAuth() {
        // Check admin session
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * Admin dashboard index
     */
    public function index() {
        try {
            // Get statistics
            $stats = $this->getStatistics();

            // Get recent orders
            $recentOrders = $this->orderModel->getPendingOrders(10);

            // Get low stock products
            $lowStockProducts = $this->getLowStockProducts();

            $this->view('admin/dashboard', [
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'low_stock_products' => $lowStockProducts
            ]);
        } catch (Exception $e) {
            die('Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics() {
        // Today's stats
        $sql = "SELECT
                    COUNT(*) as today_orders,
                    COALESCE(SUM(total_amount), 0) as today_revenue
                FROM orders
                WHERE DATE(order_date) = CURDATE()
                  AND payment_status = 'completed'";
        $todayStats = $this->orderModel->fetchOne($sql);

        // This month's stats
        $sql = "SELECT
                    COUNT(*) as month_orders,
                    COALESCE(SUM(total_amount), 0) as month_revenue
                FROM orders
                WHERE YEAR(order_date) = YEAR(CURDATE())
                  AND MONTH(order_date) = MONTH(CURDATE())
                  AND payment_status = 'completed'";
        $monthStats = $this->orderModel->fetchOne($sql);

        // Total customers
        $sql = "SELECT COUNT(*) as total_customers
                FROM customers
                WHERE customer_status = 'active' AND deleted_at IS NULL";
        $customerStats = $this->orderModel->fetchOne($sql);

        // Pending orders
        $sql = "SELECT COUNT(*) as pending_orders
                FROM orders
                WHERE order_status IN ('pending_payment', 'processing')";
        $pendingStats = $this->orderModel->fetchOne($sql);

        // Total products
        $sql = "SELECT COUNT(*) as total_products
                FROM products
                WHERE is_active = 1";
        $productStats = $this->orderModel->fetchOne($sql);

        // Active subscriptions
        $sql = "SELECT COUNT(*) as active_subscriptions
                FROM subscriptions
                WHERE status = 'active'";
        $subscriptionStats = $this->orderModel->fetchOne($sql);

        return [
            'today' => [
                'orders' => $todayStats['today_orders'],
                'revenue' => $todayStats['today_revenue']
            ],
            'month' => [
                'orders' => $monthStats['month_orders'],
                'revenue' => $monthStats['month_revenue']
            ],
            'customers' => $customerStats['total_customers'],
            'pending_orders' => $pendingStats['pending_orders'],
            'products' => $productStats['total_products'],
            'subscriptions' => $subscriptionStats['active_subscriptions']
        ];
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts() {
        $sql = "SELECT p.product_id, p.name, pv.variant_id, pv.size, pv.stock_quantity, pv.low_stock_threshold
                FROM product_variants pv
                JOIN products p ON pv.product_id = p.product_id
                WHERE pv.stock_quantity <= pv.low_stock_threshold
                  AND pv.is_active = 1
                  AND p.is_active = 1
                ORDER BY pv.stock_quantity ASC
                LIMIT 10";

        return $this->productModel->fetchAll($sql);
    }
}
