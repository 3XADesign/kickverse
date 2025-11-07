<?php
/**
 * Admin Pedidos API Controller
 * API endpoints para gestión de pedidos en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Order.php';

class AdminPedidosApiController extends Controller {
    private $orderModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
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
     * Get all orders with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = [];
            $params = [];

            if (isset($_GET['order_status']) && $_GET['order_status'] !== '') {
                $where[] = "o.order_status = ?";
                $params[] = $_GET['order_status'];
            }

            if (isset($_GET['payment_status']) && $_GET['payment_status'] !== '') {
                $where[] = "o.payment_status = ?";
                $params[] = $_GET['payment_status'];
            }

            if (isset($_GET['order_type']) && $_GET['order_type'] !== '') {
                $where[] = "o.order_type = ?";
                $params[] = $_GET['order_type'];
            }

            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(o.order_id LIKE ? OR c.full_name LIKE ? OR c.email LIKE ? OR o.tracking_number LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM orders o
                        LEFT JOIN customers c ON o.customer_id = c.customer_id
                        {$whereClause}";

            $countResult = $this->orderModel->fetchAll($countSql, $params);
            $total = $countResult[0]['total'] ?? 0;

            // Get orders
            $sql = "SELECT
                        o.order_id,
                        o.customer_id,
                        o.order_type,
                        o.order_status,
                        o.payment_status,
                        o.total_amount,
                        o.payment_method,
                        o.order_date,
                        o.tracking_number,
                        c.full_name as customer_name,
                        c.email as customer_email
                    FROM orders o
                    LEFT JOIN customers c ON o.customer_id = c.customer_id
                    {$whereClause}
                    ORDER BY o.order_date DESC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $orders = $this->orderModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'orders' => $orders,
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
            error_log("Error getting orders: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar pedidos'
            ], 500);
        }
    }

    /**
     * Get single order details
     */
    public function getOne($orderId) {
        try {
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'order' => $order
            ]);

        } catch (Exception $e) {
            error_log("Error getting order: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar pedido'
            ], 500);
        }
    }
}
