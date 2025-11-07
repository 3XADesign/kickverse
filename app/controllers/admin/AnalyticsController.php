<?php
/**
 * Analytics Controller - Admin CRM
 * Estadísticas y análisis de ventas, productos y clientes
 */

class AnalyticsController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verificar autenticación de admin
     */
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_user'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Renderizar vista
     */
    private function renderView($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }

    /**
     * Renderizar layout
     */
    private function renderLayout($layout, $data = []) {
        extract($data);
        include __DIR__ . '/../../views/layouts/' . $layout . '.php';
    }

    /**
     * Vista principal de analytics
     */
    public function index() {
        $this->checkAdminAuth();

        // Period filter
        $period = isset($_GET['period']) ? $_GET['period'] : '30days';
        $dateFrom = $this->getDateFromPeriod($period);

        // General stats
        $statsStmt = $this->db->prepare("
            SELECT
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(o.total_amount) as total_revenue,
                AVG(o.total_amount) as avg_order_value,
                COUNT(DISTINCT o.customer_id) as unique_customers,
                COUNT(DISTINCT CASE WHEN o.order_status = 'delivered' THEN o.order_id END) as delivered_orders,
                COUNT(DISTINCT CASE WHEN o.order_status = 'pending_payment' THEN o.order_id END) as pending_orders
            FROM orders o
            WHERE o.order_date >= ?
        ");
        $statsStmt->execute([$dateFrom]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Top products
        $topProductsStmt = $this->db->prepare("
            SELECT
                p.product_id,
                p.name,
                p.sku,
                pi.image_url,
                COUNT(oi.item_id) as order_count,
                SUM(oi.quantity) as total_sold,
                SUM(oi.quantity * oi.unit_price) as total_revenue
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_date >= ? AND o.order_status != 'cancelled'
            GROUP BY p.product_id
            ORDER BY total_sold DESC
            LIMIT 10
        ");
        $topProductsStmt->execute([$dateFrom]);
        $topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Revenue by day (last 30 days)
        $revenueTrendStmt = $this->db->prepare("
            SELECT
                DATE(o.order_date) as date,
                COUNT(DISTINCT o.order_id) as order_count,
                SUM(o.total_amount) as revenue
            FROM orders o
            WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                AND o.order_status != 'cancelled'
            GROUP BY DATE(o.order_date)
            ORDER BY date ASC
        ");
        $revenueTrendStmt->execute();
        $revenueTrend = $revenueTrendStmt->fetchAll(PDO::FETCH_ASSOC);

        // Orders by status
        $ordersByStatusStmt = $this->db->prepare("
            SELECT
                o.order_status,
                COUNT(*) as count,
                SUM(o.total_amount) as revenue
            FROM orders o
            WHERE o.order_date >= ?
            GROUP BY o.order_status
        ");
        $ordersByStatusStmt->execute([$dateFrom]);
        $ordersByStatus = $ordersByStatusStmt->fetchAll(PDO::FETCH_ASSOC);

        // Payment methods
        $paymentMethodsStmt = $this->db->prepare("
            SELECT
                o.payment_method,
                COUNT(*) as count,
                SUM(o.total_amount) as revenue
            FROM orders o
            WHERE o.order_date >= ? AND o.payment_status = 'completed'
            GROUP BY o.payment_method
        ");
        $paymentMethodsStmt->execute([$dateFrom]);
        $paymentMethods = $paymentMethodsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Customer segments
        $customerSegmentsStmt = $this->db->query("
            SELECT
                CASE
                    WHEN c.total_orders_count = 0 THEN 'Sin Pedidos'
                    WHEN c.total_orders_count = 1 THEN 'Nuevo (1 pedido)'
                    WHEN c.total_orders_count BETWEEN 2 AND 5 THEN 'Regular (2-5 pedidos)'
                    WHEN c.total_orders_count > 5 THEN 'VIP (6+ pedidos)'
                END as segment,
                COUNT(*) as customer_count,
                SUM(c.total_spent) as total_revenue,
                AVG(c.total_spent) as avg_spent
            FROM customers c
            GROUP BY segment
            ORDER BY customer_count DESC
        ");
        $customerSegments = $customerSegmentsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Render view
        $content = $this->renderView('admin/analytics/index', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'revenueTrend' => $revenueTrend,
            'ordersByStatus' => $ordersByStatus,
            'paymentMethods' => $paymentMethods,
            'customerSegments' => $customerSegments,
            'period' => $period
        ]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Analytics',
            'active_page' => 'analytics',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * API: Datos de ingresos (JSON)
     */
    public function revenue() {
        $this->checkAdminAuth();

        $period = isset($_GET['period']) ? $_GET['period'] : '30days';
        $dateFrom = $this->getDateFromPeriod($period);
        $groupBy = isset($_GET['group_by']) ? $_GET['group_by'] : 'day';

        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';
        $groupByClause = $groupBy === 'month' ? 'DATE_FORMAT(o.order_date, "%Y-%m")' : 'DATE(o.order_date)';

        $sql = "SELECT
                    $groupByClause as period,
                    COUNT(DISTINCT o.order_id) as order_count,
                    SUM(o.total_amount) as revenue,
                    AVG(o.total_amount) as avg_order_value,
                    COUNT(DISTINCT o.customer_id) as unique_customers
                FROM orders o
                WHERE o.order_date >= ?
                    AND o.order_status != 'cancelled'
                GROUP BY period
                ORDER BY period ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dateFrom]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'revenue' => $data,
                'period' => $period,
                'group_by' => $groupBy
            ]
        ]);
    }

    /**
     * API: Productos más vendidos (JSON)
     */
    public function products() {
        $this->checkAdminAuth();

        $period = isset($_GET['period']) ? $_GET['period'] : '30days';
        $dateFrom = $this->getDateFromPeriod($period);
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        $sql = "SELECT
                    p.product_id,
                    p.name,
                    p.sku,
                    p.base_price,
                    pi.image_url,
                    COUNT(DISTINCT oi.order_id) as order_count,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.quantity * oi.unit_price) as total_revenue,
                    AVG(oi.unit_price) as avg_price,
                    l.name as league_name,
                    t.name as team_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                LEFT JOIN leagues l ON p.league_id = l.league_id
                LEFT JOIN teams t ON p.team_id = t.team_id
                JOIN orders o ON oi.order_id = o.order_id
                WHERE o.order_date >= ?
                    AND o.order_status != 'cancelled'
                GROUP BY p.product_id
                ORDER BY total_sold DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dateFrom, $limit]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'products' => $products,
                'period' => $period,
                'total' => count($products)
            ]
        ]);
    }

    /**
     * API: Datos de clientes (JSON)
     */
    public function customers() {
        $this->checkAdminAuth();

        $period = isset($_GET['period']) ? $_GET['period'] : '30days';
        $dateFrom = $this->getDateFromPeriod($period);

        // New customers
        $newCustomersStmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM customers
            WHERE created_at >= ?
        ");
        $newCustomersStmt->execute([$dateFrom]);
        $newCustomers = $newCustomersStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Active customers (with orders in period)
        $activeCustomersStmt = $this->db->prepare("
            SELECT COUNT(DISTINCT customer_id) as count
            FROM orders
            WHERE order_date >= ?
        ");
        $activeCustomersStmt->execute([$dateFrom]);
        $activeCustomers = $activeCustomersStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Customer lifetime value
        $clvStmt = $this->db->query("
            SELECT
                AVG(total_spent) as avg_lifetime_value,
                MAX(total_spent) as max_lifetime_value,
                MIN(total_spent) as min_lifetime_value
            FROM customers
            WHERE total_orders_count > 0
        ");
        $clv = $clvStmt->fetch(PDO::FETCH_ASSOC);

        // Repeat customer rate
        $repeatRateStmt = $this->db->query("
            SELECT
                COUNT(CASE WHEN total_orders_count > 1 THEN 1 END) as repeat_customers,
                COUNT(*) as total_customers
            FROM customers
            WHERE total_orders_count > 0
        ");
        $repeatRate = $repeatRateStmt->fetch(PDO::FETCH_ASSOC);
        $repeatPercentage = $repeatRate['total_customers'] > 0
            ? ($repeatRate['repeat_customers'] / $repeatRate['total_customers']) * 100
            : 0;

        // Customer acquisition trend
        $acquisitionTrendStmt = $this->db->prepare("
            SELECT
                DATE(created_at) as date,
                COUNT(*) as new_customers
            FROM customers
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $acquisitionTrendStmt->execute([$dateFrom]);
        $acquisitionTrend = $acquisitionTrendStmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'new_customers' => $newCustomers,
                'active_customers' => $activeCustomers,
                'lifetime_value' => $clv,
                'repeat_rate' => [
                    'percentage' => round($repeatPercentage, 2),
                    'repeat_customers' => $repeatRate['repeat_customers'],
                    'total_customers' => $repeatRate['total_customers']
                ],
                'acquisition_trend' => $acquisitionTrend,
                'period' => $period
            ]
        ]);
    }

    /**
     * Helper: Obtener fecha desde periodo
     */
    private function getDateFromPeriod($period) {
        switch ($period) {
            case '7days':
                return date('Y-m-d', strtotime('-7 days'));
            case '30days':
                return date('Y-m-d', strtotime('-30 days'));
            case '90days':
                return date('Y-m-d', strtotime('-90 days'));
            case '12months':
                return date('Y-m-d', strtotime('-12 months'));
            case 'year':
                return date('Y-01-01');
            case 'all':
                return '2020-01-01';
            default:
                return date('Y-m-d', strtotime('-30 days'));
        }
    }
}
