<?php
/**
 * Mystery Boxes Controller - Admin CRM
 * Gestión de Mystery Boxes desde el panel de administración
 */

class MysteryBoxesController {
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
     * Lista de todas las Mystery Boxes
     */
    public function index() {
        $this->checkAdminAuth();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $where = [];
        $params = [];

        if (isset($_GET['type']) && $_GET['type'] !== '') {
            $where[] = "mbo.box_type_id = ?";
            $params[] = $_GET['type'];
        }

        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $where[] = "o.order_status = ?";
            $params[] = $_GET['status'];
        }

        // Search
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(mbo.order_id LIKE ? OR c.full_name LIKE ?)";
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM mystery_box_orders mbo
                     JOIN orders o ON mbo.order_id = o.order_id
                     JOIN customers c ON o.customer_id = c.customer_id
                     $whereClause";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $totalBoxes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalBoxes / $this->perPage);

        // Get mystery boxes
        $sql = "SELECT mbo.*,
                       mbt.name as box_type_name,
                       mbt.base_price,
                       o.order_status,
                       o.payment_status,
                       o.order_date,
                       o.total_amount,
                       c.customer_id,
                       c.full_name as customer_name,
                       c.email as customer_email,
                       l.name as league_name,
                       COUNT(DISTINCT mbc.content_id) as items_count
                FROM mystery_box_orders mbo
                JOIN mystery_box_types mbt ON mbo.box_type_id = mbt.box_type_id
                JOIN orders o ON mbo.order_id = o.order_id
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN leagues l ON mbo.selected_league_id = l.league_id
                LEFT JOIN mystery_box_contents mbc ON mbo.mystery_box_order_id = mbc.mystery_box_order_id
                $whereClause
                GROUP BY mbo.mystery_box_order_id
                ORDER BY o.order_date DESC
                LIMIT ? OFFSET ?";

        $params[] = $this->perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $boxes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get box types for filter
        $typesStmt = $this->db->query("SELECT * FROM mystery_box_types ORDER BY name");
        $boxTypes = $typesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get stats
        $statsStmt = $this->db->query("
            SELECT
                COUNT(DISTINCT mbo.mystery_box_order_id) as total_boxes,
                SUM(o.total_amount) as total_revenue,
                COUNT(DISTINCT CASE WHEN o.order_status = 'delivered' THEN mbo.mystery_box_order_id END) as delivered,
                COUNT(DISTINCT CASE WHEN o.order_status = 'processing' THEN mbo.mystery_box_order_id END) as processing
            FROM mystery_box_orders mbo
            JOIN orders o ON mbo.order_id = o.order_id
        ");
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Render view
        $content = $this->renderView('admin/mystery-boxes/index', [
            'boxes' => $boxes,
            'boxTypes' => $boxTypes,
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_boxes' => $totalBoxes
        ]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Mystery Boxes',
            'active_page' => 'mystery-boxes',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de una Mystery Box (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        // Get mystery box details
        $sql = "SELECT mbo.*,
                       mbt.name as box_type_name,
                       mbt.description as box_description,
                       mbt.base_price,
                       o.order_id,
                       o.order_status,
                       o.payment_status,
                       o.payment_method,
                       o.order_date,
                       o.total_amount,
                       o.tracking_number,
                       c.customer_id,
                       c.full_name as customer_name,
                       c.email as customer_email,
                       c.telegram_username,
                       c.whatsapp_number,
                       l.name as league_name,
                       sa.address_line1,
                       sa.city,
                       sa.postal_code,
                       sa.country
                FROM mystery_box_orders mbo
                JOIN mystery_box_types mbt ON mbo.box_type_id = mbt.box_type_id
                JOIN orders o ON mbo.order_id = o.order_id
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN leagues l ON mbo.selected_league_id = l.league_id
                LEFT JOIN shipping_addresses sa ON o.shipping_address_id = sa.address_id
                WHERE mbo.mystery_box_order_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $box = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$box) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Mystery Box no encontrada',
                'data' => null
            ]);
            return;
        }

        // Get contents
        $contentsStmt = $this->db->prepare("
            SELECT mbc.*,
                   p.name as product_name,
                   p.base_price as product_price,
                   pv.size,
                   pi.image_url
            FROM mystery_box_contents mbc
            JOIN products p ON mbc.product_id = p.product_id
            LEFT JOIN product_variants pv ON mbc.variant_id = pv.variant_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = TRUE
            WHERE mbc.mystery_box_order_id = ?
            ORDER BY mbc.created_at
        ");
        $contentsStmt->execute([$id]);
        $contents = $contentsStmt->fetchAll(PDO::FETCH_ASSOC);

        $box['contents'] = $contents;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '',
            'data' => $box
        ]);
    }
}
