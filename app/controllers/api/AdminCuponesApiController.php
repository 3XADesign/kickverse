<?php
/**
 * Admin Cupones API Controller
 * API endpoints para gestión de cupones en el admin
 */

require_once __DIR__ . '/../Controller.php';

class AdminCuponesApiController extends Controller {
    private $db;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->db = Database::getInstance()->getConnection();
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
     * Get all coupons with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = [];
            $params = [];

            // Search filter
            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(c.code LIKE ? OR c.description LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            // Discount type filter
            if (isset($_GET['discount_type']) && $_GET['discount_type'] !== '') {
                $where[] = "c.discount_type = ?";
                $params[] = $_GET['discount_type'];
            }

            // Active status filter
            if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
                $where[] = "c.is_active = ?";
                $params[] = (int)$_GET['is_active'];
            }

            // Valid from filter
            if (isset($_GET['valid_from']) && $_GET['valid_from'] !== '') {
                $where[] = "(c.valid_from IS NULL OR c.valid_from >= ?)";
                $params[] = $_GET['valid_from'];
            }

            // Valid until filter
            if (isset($_GET['valid_until']) && $_GET['valid_until'] !== '') {
                $where[] = "(c.valid_until IS NULL OR c.valid_until <= ?)";
                $params[] = $_GET['valid_until'];
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM coupons c
                        {$whereClause}";

            $stmt = $this->db->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Get coupons with usage statistics
            $sql = "SELECT
                        c.*,
                        COALESCE(cu.usage_count, 0) as times_used,
                        COALESCE(cu.total_discount, 0) as total_discount_given
                    FROM coupons c
                    LEFT JOIN (
                        SELECT
                            coupon_id,
                            COUNT(*) as usage_count,
                            SUM(discount_applied) as total_discount
                        FROM coupon_usage
                        GROUP BY coupon_id
                    ) cu ON c.coupon_id = cu.coupon_id
                    {$whereClause}
                    ORDER BY c.created_at DESC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'coupons' => $coupons,
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
            error_log("Error getting coupons: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar cupones'
            ], 500);
        }
    }

    /**
     * Get single coupon details with usage history
     */
    public function getOne($couponId) {
        try {
            // Get coupon details with statistics
            $stmt = $this->db->prepare("
                SELECT
                    c.*,
                    COALESCE(cu.usage_count, 0) as times_used,
                    COALESCE(cu.total_discount, 0) as total_discount_given
                FROM coupons c
                LEFT JOIN (
                    SELECT
                        coupon_id,
                        COUNT(*) as usage_count,
                        SUM(discount_applied) as total_discount
                    FROM coupon_usage
                    GROUP BY coupon_id
                ) cu ON c.coupon_id = cu.coupon_id
                WHERE c.coupon_id = ?
            ");
            $stmt->execute([$couponId]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$coupon) {
                $this->json([
                    'success' => false,
                    'message' => 'Cupón no encontrado'
                ], 404);
                return;
            }

            // Get usage history with customer and order details
            $usageStmt = $this->db->prepare("
                SELECT
                    cu.*,
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
            $usageStmt->execute([$couponId]);
            $usageHistory = $usageStmt->fetchAll(PDO::FETCH_ASSOC);

            $coupon['usage_history'] = $usageHistory;

            $this->json([
                'success' => true,
                'data' => $coupon
            ]);

        } catch (Exception $e) {
            error_log("Error getting coupon details: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar detalles del cupón'
            ], 500);
        }
    }

    /**
     * Send JSON response
     */
    private function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
