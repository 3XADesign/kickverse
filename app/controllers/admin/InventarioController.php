<?php
/**
 * Inventario Controller - Admin CRM
 * Gestión de inventario, movimientos de stock y alertas
 */

class InventarioController {
    private $db;
    private $perPage = 50;

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
     * Vista principal de inventario
     */
    public function index() {
        $this->checkAdminAuth();

        // Pagination para movimientos
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $where = [];
        $params = [];

        if (isset($_GET['movement_type']) && $_GET['movement_type'] !== '') {
            $where[] = "sm.movement_type = ?";
            $params[] = $_GET['movement_type'];
        }

        if (isset($_GET['product']) && $_GET['product'] !== '') {
            $where[] = "p.product_id = ?";
            $params[] = $_GET['product'];
        }

        // Date range filter
        if (isset($_GET['date_from']) && $_GET['date_from'] !== '') {
            $where[] = "DATE(sm.created_at) >= ?";
            $params[] = $_GET['date_from'];
        }

        if (isset($_GET['date_to']) && $_GET['date_to'] !== '') {
            $where[] = "DATE(sm.created_at) <= ?";
            $params[] = $_GET['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM stock_movements sm
                     JOIN product_variants pv ON sm.product_variant_id = pv.variant_id
                     JOIN products p ON pv.product_id = p.product_id
                     $whereClause";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $totalMovements = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalMovements / $this->perPage);

        // Get stock movements
        $sql = "SELECT sm.*,
                       p.product_id,
                       p.name as product_name,
                       p.sku,
                       pv.size,
                       pv.stock_quantity as current_stock,
                       pv.low_stock_threshold,
                       pi.image_url,
                       CASE
                           WHEN sm.reference_order_id IS NOT NULL THEN CONCAT('Pedido #', sm.reference_order_id)
                           WHEN sm.reference_subscription_shipment_id IS NOT NULL THEN CONCAT('Suscripción #', sm.reference_subscription_shipment_id)
                           WHEN sm.reference_mystery_box_order_id IS NOT NULL THEN CONCAT('Mystery Box #', sm.reference_mystery_box_order_id)
                           ELSE 'Manual'
                       END as reference_text
                FROM stock_movements sm
                JOIN product_variants pv ON sm.product_variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                $whereClause
                ORDER BY sm.created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $this->perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get low stock alerts
        $alertsSql = "SELECT lsa.*,
                             p.product_id,
                             p.name as product_name,
                             p.sku,
                             pv.size,
                             pv.stock_quantity,
                             pi.image_url
                      FROM low_stock_alerts lsa
                      JOIN product_variants pv ON lsa.product_variant_id = pv.variant_id
                      JOIN products p ON pv.product_id = p.product_id
                      LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                      WHERE lsa.alert_status IN ('pending', 'notified')
                      ORDER BY lsa.created_at DESC
                      LIMIT 10";
        $alertsStmt = $this->db->query($alertsSql);
        $lowStockAlerts = $alertsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get products for filter
        $productsStmt = $this->db->query("SELECT product_id, name, sku FROM products WHERE status = 'active' ORDER BY name");
        $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get inventory stats
        $statsStmt = $this->db->query("
            SELECT
                COUNT(DISTINCT pv.variant_id) as total_variants,
                SUM(pv.stock_quantity) as total_stock,
                COUNT(DISTINCT CASE WHEN pv.stock_quantity <= pv.low_stock_threshold THEN pv.variant_id END) as low_stock_count,
                COUNT(DISTINCT CASE WHEN pv.stock_quantity = 0 THEN pv.variant_id END) as out_of_stock_count,
                SUM(CASE WHEN sm.movement_type = 'sale' AND DATE(sm.created_at) = CURDATE() THEN ABS(sm.quantity) ELSE 0 END) as today_sales
            FROM product_variants pv
            LEFT JOIN stock_movements sm ON pv.variant_id = sm.product_variant_id
            WHERE pv.is_active = TRUE
        ");
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Render view
        $content = $this->renderView('admin/inventario/index', [
            'movements' => $movements,
            'lowStockAlerts' => $lowStockAlerts,
            'products' => $products,
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_movements' => $totalMovements
        ]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Inventario',
            'active_page' => 'inventario',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * API: Movimientos de stock (JSON)
     */
    public function movements() {
        $this->checkAdminAuth();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        $offset = ($page - 1) * $perPage;

        // Filters
        $where = [];
        $params = [];

        if (isset($_GET['movement_type']) && $_GET['movement_type'] !== '') {
            $where[] = "sm.movement_type = ?";
            $params[] = $_GET['movement_type'];
        }

        if (isset($_GET['product_id']) && $_GET['product_id'] !== '') {
            $where[] = "p.product_id = ?";
            $params[] = $_GET['product_id'];
        }

        if (isset($_GET['date_from']) && $_GET['date_from'] !== '') {
            $where[] = "DATE(sm.created_at) >= ?";
            $params[] = $_GET['date_from'];
        }

        if (isset($_GET['date_to']) && $_GET['date_to'] !== '') {
            $where[] = "DATE(sm.created_at) <= ?";
            $params[] = $_GET['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get movements
        $sql = "SELECT sm.*,
                       p.product_id,
                       p.name as product_name,
                       p.sku,
                       pv.size,
                       pv.stock_quantity as current_stock,
                       pi.image_url
                FROM stock_movements sm
                JOIN product_variants pv ON sm.product_variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                $whereClause
                ORDER BY sm.created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM stock_movements sm
                     JOIN product_variants pv ON sm.product_variant_id = pv.variant_id
                     JOIN products p ON pv.product_id = p.product_id
                     $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute(array_slice($params, 0, -2)); // Remove limit/offset params
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'movements' => $movements,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage)
                ]
            ]
        ]);
    }

    /**
     * API: Alertas de stock bajo (JSON)
     */
    public function lowStockAlerts() {
        $this->checkAdminAuth();

        $status = isset($_GET['status']) ? $_GET['status'] : 'pending,notified';
        $statuses = explode(',', $status);
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));

        $sql = "SELECT lsa.*,
                       p.product_id,
                       p.name as product_name,
                       p.sku,
                       pv.variant_id,
                       pv.size,
                       pv.stock_quantity,
                       pv.low_stock_threshold,
                       pi.image_url,
                       (pv.low_stock_threshold - pv.stock_quantity) as shortage_amount
                FROM low_stock_alerts lsa
                JOIN product_variants pv ON lsa.product_variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
                WHERE lsa.alert_status IN ($placeholders)
                ORDER BY shortage_amount DESC, lsa.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($statuses);
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'alerts' => $alerts,
                'total' => count($alerts)
            ]
        ]);
    }
}
