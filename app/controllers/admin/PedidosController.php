<?php
/**
 * Pedidos Controller - Admin CRM
 * Gestión de pedidos desde el panel de administración
 */

require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Customer.php';

class PedidosController {
    private $orderModel;
    private $customerModel;
    private $perPage = 50;

    public function __construct() {
        $this->orderModel = new Order();
        $this->customerModel = new Customer();
    }

    /**
     * Lista de todos los pedidos
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
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

        if (isset($_GET['payment_method']) && $_GET['payment_method'] !== '') {
            $where[] = "o.payment_method = ?";
            $params[] = $_GET['payment_method'];
        }

        // Search by order ID, customer name, or tracking number
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(o.order_id LIKE ? OR c.full_name LIKE ? OR o.tracking_number LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM orders o
                     JOIN customers c ON o.customer_id = c.customer_id
                     $whereClause";
        $countResult = $this->orderModel->fetchOne($countSql, $params);
        $totalPedidos = $countResult['total'];
        $totalPages = ceil($totalPedidos / $this->perPage);

        // Get pedidos with customer info and items
        $sql = "SELECT o.*,
                       c.full_name as customer_name,
                       c.email as customer_email,
                       c.telegram_username,
                       c.whatsapp_number,
                       COUNT(DISTINCT oi.order_item_id) as items_count,
                       GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') as product_names
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.product_id
                $whereClause
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT ? OFFSET ?";

        $params[] = $this->perPage;
        $params[] = $offset;

        $pedidos = $this->orderModel->fetchAll($sql, $params);

        // Render view
        $content = $this->renderView('admin/pedidos/index', [
            'pedidos' => $pedidos,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_pedidos' => $totalPedidos
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Pedidos',
            'current_page' => 'pedidos',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de un pedido (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $pedido = $this->orderModel->getOrderWithItems($id);

        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['error' => 'Pedido no encontrado']);
            return;
        }

        // Add status timeline
        $pedido['timeline'] = $this->getOrderTimeline($id);

        header('Content-Type: application/json');
        echo json_encode($pedido);
    }

    /**
     * Actualizar estado del pedido
     */
    public function updateStatus($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['order_status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Estado no proporcionado']);
            return;
        }

        try {
            $trackingNumber = $input['tracking_number'] ?? null;
            $carrier = $input['carrier'] ?? null;

            $this->orderModel->updateStatus($id, $input['order_status'], $trackingNumber);

            // Update carrier if provided
            if ($carrier) {
                $this->orderModel->update($id, ['carrier' => $carrier]);
            }

            // Add admin note if provided
            if (isset($input['admin_notes'])) {
                $this->orderModel->update($id, ['admin_notes' => $input['admin_notes']]);
            }

            // Create audit log
            $this->createAuditLog('status_change', 'order', $id, [
                'status' => $input['order_status'],
                'tracking_number' => $trackingNumber,
                'carrier' => $carrier
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar estado de pago
     */
    public function updatePayment($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['payment_status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Estado de pago no proporcionado']);
            return;
        }

        try {
            $this->orderModel->updatePaymentStatus($id, $input['payment_status']);

            // Create audit log
            $this->createAuditLog('payment_verify', 'order', $id, [
                'payment_status' => $input['payment_status']
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Estado de pago actualizado']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar tracking number
     */
    public function updateTracking($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['tracking_number'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Tracking number no proporcionado']);
            return;
        }

        try {
            $data = [
                'tracking_number' => $input['tracking_number'],
                'carrier' => $input['carrier'] ?? null
            ];

            // If adding tracking, update status to shipped
            if ($input['tracking_number'] && empty($this->orderModel->find($id)['tracking_number'])) {
                $data['order_status'] = 'shipped';
                $data['shipped_date'] = date('Y-m-d');
            }

            $this->orderModel->update($id, $data);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Tracking actualizado correctamente']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancelar pedido
     */
    public function cancel($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $reason = $input['reason'] ?? 'Cancelado por administrador';

        try {
            $this->orderModel->cancelOrder($id, $reason);

            // Create audit log
            $this->createAuditLog('status_change', 'order', $id, [
                'status' => 'cancelled',
                'reason' => $reason
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Pedido cancelado']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get order timeline
     */
    private function getOrderTimeline($orderId) {
        $order = $this->orderModel->find($orderId);

        $timeline = [];

        // Order placed
        $timeline[] = [
            'status' => 'Pedido creado',
            'date' => $order['order_date'],
            'icon' => 'fa-shopping-cart',
            'color' => 'info',
            'completed' => true
        ];

        // Payment
        if ($order['payment_status'] === 'completed') {
            $timeline[] = [
                'status' => 'Pago confirmado',
                'date' => $order['updated_at'],
                'icon' => 'fa-credit-card',
                'color' => 'success',
                'completed' => true
            ];
        }

        // Processing
        if (in_array($order['order_status'], ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'En preparación',
                'date' => $order['updated_at'],
                'icon' => 'fa-box',
                'color' => 'info',
                'completed' => true
            ];
        }

        // Shipped
        if ($order['order_status'] === 'shipped' || $order['order_status'] === 'delivered') {
            $timeline[] = [
                'status' => 'Enviado',
                'date' => $order['shipped_date'],
                'icon' => 'fa-shipping-fast',
                'color' => 'info',
                'completed' => true,
                'tracking' => $order['tracking_number'] ?? null
            ];
        }

        // Delivered
        if ($order['order_status'] === 'delivered') {
            $timeline[] = [
                'status' => 'Entregado',
                'date' => $order['delivered_date'],
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'completed' => true
            ];
        }

        // Cancelled
        if ($order['order_status'] === 'cancelled') {
            $timeline[] = [
                'status' => 'Cancelado',
                'date' => $order['updated_at'],
                'icon' => 'fa-times-circle',
                'color' => 'danger',
                'completed' => true
            ];
        }

        return $timeline;
    }

    /**
     * Create audit log entry
     */
    private function createAuditLog($action, $entityType, $entityId, $newValues) {
        $sql = "INSERT INTO audit_log (admin_id, action_type, entity_type, entity_id, new_values, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->orderModel->query($sql, [
            $_SESSION['admin_id'],
            $action,
            $entityType,
            $entityId,
            json_encode($newValues),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    /**
     * Check admin authentication
     */
    private function checkAdminAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Render view
     */
    private function renderView($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }

    /**
     * Render layout
     */
    private function renderLayout($layout, $data = []) {
        extract($data);
        include __DIR__ . '/../../views/layouts/' . $layout . '.php';
    }
}
