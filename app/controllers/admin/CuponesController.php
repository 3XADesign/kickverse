<?php
/**
 * Cupones Controller - Admin CRM
 * Gestión de cupones de descuento desde el panel de administración
 */

class CuponesController {
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
     * Lista de todos los cupones
     */
    public function index() {
        $this->checkAdminAuth();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $where = [];
        $params = [];

        if (isset($_GET['status']) && $_GET['status'] !== '') {
            if ($_GET['status'] === 'active') {
                $where[] = "is_active = TRUE AND (expiry_date IS NULL OR expiry_date >= CURDATE())";
            } else if ($_GET['status'] === 'expired') {
                $where[] = "expiry_date < CURDATE()";
            } else if ($_GET['status'] === 'inactive') {
                $where[] = "is_active = FALSE";
            }
        }

        // Search
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(code LIKE ? OR description LIKE ?)";
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM coupons $whereClause";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $totalCoupons = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalCoupons / $this->perPage);

        // Get cupones
        $sql = "SELECT c.*,
                       COUNT(DISTINCT cu.usage_id) as total_uses,
                       SUM(cu.discount_applied) as total_discount_given
                FROM coupons c
                LEFT JOIN coupon_usage cu ON c.coupon_id = cu.coupon_id
                $whereClause
                GROUP BY c.coupon_id
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $this->perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $cupones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get stats
        $statsStmt = $this->db->query("
            SELECT
                COUNT(*) as total_coupons,
                COUNT(CASE WHEN is_active = TRUE AND (expiry_date IS NULL OR expiry_date >= CURDATE()) THEN 1 END) as active_coupons,
                COUNT(CASE WHEN expiry_date < CURDATE() THEN 1 END) as expired_coupons,
                COALESCE(SUM(cu.total_uses), 0) as total_uses,
                COALESCE(SUM(cu.total_discount), 0) as total_discount_given
            FROM coupons c
            LEFT JOIN (
                SELECT coupon_id, COUNT(*) as total_uses, SUM(discount_applied) as total_discount
                FROM coupon_usage
                GROUP BY coupon_id
            ) cu ON c.coupon_id = cu.coupon_id
        ");
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Render view
        $content = $this->renderView('admin/cupones/index', [
            'cupones' => $cupones,
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_cupones' => $totalCoupons
        ]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Gestión de Cupones',
            'active_page' => 'cupones',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de un cupón (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        // Get coupon details
        $stmt = $this->db->prepare("
            SELECT c.*,
                   COUNT(DISTINCT cu.usage_id) as total_uses,
                   SUM(cu.discount_applied) as total_discount_given
            FROM coupons c
            LEFT JOIN coupon_usage cu ON c.coupon_id = cu.coupon_id
            WHERE c.coupon_id = ?
            GROUP BY c.coupon_id
        ");
        $stmt->execute([$id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!coupon) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Cupón no encontrado',
                'data' => null
            ]);
            return;
        }

        // Get usage history
        $usageStmt = $this->db->prepare("
            SELECT cu.*,
                   c.full_name as customer_name,
                   c.email as customer_email,
                   o.order_id,
                   o.total_amount as order_total
            FROM coupon_usage cu
            JOIN customers c ON cu.customer_id = c.customer_id
            JOIN orders o ON cu.order_id = o.order_id
            WHERE cu.coupon_id = ?
            ORDER BY cu.used_at DESC
            LIMIT 50
        ");
        $usageStmt->execute([$id]);
        $usage_history = $usageStmt->fetchAll(PDO::FETCH_ASSOC);

        $coupon['usage_history'] = $usage_history;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '',
            'data' => $coupon
        ]);
    }

    /**
     * Crear cupón
     */
    public function create() {
        $this->checkAdminAuth();

        $content = $this->renderView('admin/cupones/create', []);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Crear Cupón',
            'active_page' => 'cupones',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Guardar nuevo cupón
     */
    public function store() {
        $this->checkAdminAuth();

        $data = $_POST;

        // Validación básica
        if (empty($data['code']) || empty($data['discount_type'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Campos requeridos faltantes'];
            header('Location: /admin/cupones/crear');
            exit;
        }

        // Insertar cupón
        $stmt = $this->db->prepare("
            INSERT INTO coupons (
                code, description, discount_type, discount_value,
                min_order_amount, max_discount_amount, max_uses_per_customer,
                expiry_date, is_active, applicable_to, applicable_ids
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            strtoupper($data['code']),
            $data['description'] ?? null,
            $data['discount_type'],
            $data['discount_value'],
            $data['min_order_amount'] ?? null,
            $data['max_discount_amount'] ?? null,
            $data['max_uses_per_customer'] ?? null,
            $data['expiry_date'] ?? null,
            isset($data['is_active']) ? 1 : 0,
            $data['applicable_to'] ?? 'all',
            $data['applicable_ids'] ?? null
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cupón creado correctamente'];
        header('Location: /admin/cupones');
        exit;
    }

    /**
     * Editar cupón
     */
    public function edit($id) {
        $this->checkAdminAuth();

        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Cupón no encontrado'];
            header('Location: /admin/cupones');
            exit;
        }

        $content = $this->renderView('admin/cupones/edit', ['coupon' => $coupon]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Editar Cupón',
            'active_page' => 'cupones',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Actualizar cupón
     */
    public function update($id) {
        $this->checkAdminAuth();

        $data = $_POST;

        $stmt = $this->db->prepare("
            UPDATE coupons SET
                code = ?,
                description = ?,
                discount_type = ?,
                discount_value = ?,
                min_order_amount = ?,
                max_discount_amount = ?,
                max_uses_per_customer = ?,
                expiry_date = ?,
                is_active = ?
            WHERE coupon_id = ?
        ");

        $stmt->execute([
            strtoupper($data['code']),
            $data['description'] ?? null,
            $data['discount_type'],
            $data['discount_value'],
            $data['min_order_amount'] ?? null,
            $data['max_discount_amount'] ?? null,
            $data['max_uses_per_customer'] ?? null,
            $data['expiry_date'] ?? null,
            isset($data['is_active']) ? 1 : 0,
            $id
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cupón actualizado correctamente'];
        header('Location: /admin/cupones');
        exit;
    }

    /**
     * Eliminar cupón
     */
    public function delete($id) {
        $this->checkAdminAuth();

        $stmt = $this->db->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Cupón eliminado correctamente'
        ]);
    }
}
