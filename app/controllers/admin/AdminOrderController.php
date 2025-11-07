<?php
/**
 * Admin Order Controller
 * Manage orders in admin panel
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Order.php';

class AdminOrderController extends Controller {
    private $orderModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdminAuth();
        $this->orderModel = new Order();
    }

    private function requireAdminAuth() {
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * List all orders
     */
    public function index() {
        $page = (int) ($this->get('page') ?? 1);
        $perPage = 50;
        $status = $this->get('status');

        try {
            // Build query
            $sql = "SELECT o.*, c.full_name as customer_name, c.email as customer_email
                    FROM orders o
                    JOIN customers c ON o.customer_id = c.customer_id";

            $params = [];

            if ($status) {
                $sql .= " WHERE o.order_status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY o.order_date DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = ($page - 1) * $perPage;

            $orders = $this->orderModel->fetchAll($sql, $params);

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM orders o";
            if ($status) {
                $countSql .= " WHERE o.order_status = ?";
                $total = $this->orderModel->fetchOne($countSql, [$status])['total'];
            } else {
                $total = $this->orderModel->fetchOne($countSql)['total'];
            }

            $this->view('admin/orders/index', [
                'orders' => $orders,
                'page' => $page,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'status_filter' => $status
            ]);
        } catch (Exception $e) {
            die('Error loading orders: ' . $e->getMessage());
        }
    }

    /**
     * Show order details
     */
    public function show($orderId) {
        try {
            $order = $this->orderModel->getOrderWithItems($orderId);

            if (!$order) {
                $this->setFlash('error', 'Pedido no encontrado');
                $this->redirect('/admin/orders');
            }

            $this->view('admin/orders/show', [
                'order' => $order
            ]);
        } catch (Exception $e) {
            die('Error loading order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId) {
        $data = $this->input();

        if (empty($data['status'])) {
            $this->json([
                'success' => false,
                'message' => 'Estado requerido'
            ], 400);
        }

        try {
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            $this->orderModel->updateStatus($orderId, $data['status']);

            $this->json([
                'success' => true,
                'message' => 'Estado actualizado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }

    /**
     * Update tracking number
     */
    public function updateTracking($orderId) {
        $data = $this->input();

        if (empty($data['tracking_number'])) {
            $this->json([
                'success' => false,
                'message' => 'Número de seguimiento requerido'
            ], 400);
        }

        try {
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                $this->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            $this->orderModel->updateStatus($orderId, 'shipped', $data['tracking_number']);

            $this->json([
                'success' => true,
                'message' => 'Seguimiento actualizado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el seguimiento'
            ], 500);
        }
    }
}
