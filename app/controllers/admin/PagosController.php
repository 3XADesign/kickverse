<?php
/**
 * Pagos Controller - Admin CRM
 * Gestión de transacciones de pago desde el panel de administración
 */

require_once __DIR__ . '/../../models/PaymentTransaction.php';
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Subscription.php';

class PagosController {
    private $paymentModel;
    private $customerModel;
    private $orderModel;
    private $subscriptionModel;
    private $perPage = 50;

    public function __construct() {
        $this->paymentModel = new PaymentTransaction();
        $this->customerModel = new Customer();
        $this->orderModel = new Order();
        $this->subscriptionModel = new Subscription();
    }

    /**
     * Lista de todas las transacciones de pago
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $filters = [];
        $whereConditions = [];
        $params = [];

        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $whereConditions[] = "pt.status = ?";
            $params[] = $_GET['status'];
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] !== '') {
            $whereConditions[] = "pt.payment_method = ?";
            $params[] = $_GET['payment_method'];
        }

        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = $_GET['search'];
            $whereConditions[] = "(pt.transaction_id LIKE ? OR c.full_name LIKE ? OR pt.oxapay_transaction_id LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (isset($_GET['date_from']) && $_GET['date_from'] !== '') {
            $whereConditions[] = "DATE(pt.initiated_at) >= ?";
            $params[] = $_GET['date_from'];
        }

        if (isset($_GET['date_to']) && $_GET['date_to'] !== '') {
            $whereConditions[] = "DATE(pt.initiated_at) <= ?";
            $params[] = $_GET['date_to'];
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM payment_transactions pt
                     JOIN customers c ON pt.customer_id = c.customer_id
                     $whereClause";
        $totalPagos = $this->executeQuery($countSql, $params)[0]['total'] ?? 0;
        $totalPages = ceil($totalPagos / $this->perPage);

        // Get pagos with customer info
        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email,
                       c.telegram_username,
                       o.order_id,
                       o.order_type,
                       s.subscription_id,
                       s.plan_id
                FROM payment_transactions pt
                JOIN customers c ON pt.customer_id = c.customer_id
                LEFT JOIN orders o ON pt.order_id = o.order_id
                LEFT JOIN subscriptions s ON pt.subscription_id = s.subscription_id
                $whereClause
                ORDER BY pt.initiated_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $this->perPage;
        $params[] = $offset;

        $pagos = $this->executeQuery($sql, $params);

        // Render view
        $content = $this->renderView('admin/pagos/index', [
            'pagos' => $pagos,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_pagos' => $totalPagos
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Pagos',
            'current_page' => 'pagos',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de una transacción (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $sql = "SELECT pt.*,
                       c.customer_id, c.full_name as customer_name, c.email as customer_email,
                       c.telegram_username, c.whatsapp_number, c.phone,
                       o.order_id, o.order_type, o.order_status, o.total_amount as order_total,
                       o.order_date, o.tracking_number,
                       s.subscription_id, s.plan_id, s.status as subscription_status,
                       sp.plan_name, sp.monthly_price
                FROM payment_transactions pt
                JOIN customers c ON pt.customer_id = c.customer_id
                LEFT JOIN orders o ON pt.order_id = o.order_id
                LEFT JOIN subscriptions s ON pt.subscription_id = s.subscription_id
                LEFT JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE pt.transaction_id = ?";

        $result = $this->executeQuery($sql, [$id]);
        $pago = $result[0] ?? null;

        if (!$pago) {
            http_response_code(404);
            echo json_encode(['error' => 'Transacción no encontrada']);
            return;
        }

        // Parse JSON fields
        if ($pago['oxapay_response']) {
            $pago['oxapay_response'] = json_decode($pago['oxapay_response'], true);
        }

        header('Content-Type: application/json');
        echo json_encode($pago);
    }

    /**
     * Actualizar estado de una transacción
     */
    public function updateStatus($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $newStatus = $_POST['status'] ?? null;
        $notes = $_POST['notes'] ?? null;

        if (!$newStatus || !in_array($newStatus, ['pending', 'processing', 'completed', 'failed', 'expired', 'refunded'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Estado inválido']);
            return;
        }

        try {
            // Get current transaction
            $sql = "SELECT * FROM payment_transactions WHERE transaction_id = ?";
            $result = $this->executeQuery($sql, [$id]);
            $transaction = $result[0] ?? null;

            if (!$transaction) {
                http_response_code(404);
                echo json_encode(['error' => 'Transacción no encontrada']);
                return;
            }

            // Update transaction
            $updateData = [
                'status' => $newStatus,
                'verified_by' => $_SESSION['admin_id'] ?? null,
                'verified_at' => date('Y-m-d H:i:s')
            ];

            if ($newStatus === 'completed') {
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            }

            if ($notes) {
                $updateData['notes'] = $notes;
            }

            $this->updateTransaction($id, $updateData);

            // If completed, update related order or subscription
            if ($newStatus === 'completed') {
                if ($transaction['order_id']) {
                    $this->updateOrderPaymentStatus($transaction['order_id'], 'completed');
                }
                if ($transaction['subscription_id']) {
                    $this->updateSubscriptionPaymentStatus($transaction['subscription_id']);
                }
            }

            // Log audit
            $this->logAudit([
                'admin_id' => $_SESSION['admin_id'] ?? null,
                'action_type' => 'status_change',
                'entity_type' => 'payment_transaction',
                'entity_id' => $id,
                'old_values' => json_encode(['status' => $transaction['status']]),
                'new_values' => json_encode(['status' => $newStatus]),
                'description' => "Estado de pago cambiado de {$transaction['status']} a {$newStatus}"
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el estado: ' . $e->getMessage()]);
        }
    }

    /**
     * Marcar pago como completado
     */
    public function markAsCompleted($id) {
        $_POST['status'] = 'completed';
        $this->updateStatus($id);
    }

    /**
     * Marcar pago como fallido
     */
    public function markAsFailed($id) {
        $_POST['status'] = 'failed';
        $this->updateStatus($id);
    }

    /**
     * Procesar reembolso
     */
    public function processRefund($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        try {
            // Get transaction
            $sql = "SELECT * FROM payment_transactions WHERE transaction_id = ?";
            $result = $this->executeQuery($sql, [$id]);
            $transaction = $result[0] ?? null;

            if (!$transaction) {
                http_response_code(404);
                echo json_encode(['error' => 'Transacción no encontrada']);
                return;
            }

            if ($transaction['status'] !== 'completed') {
                http_response_code(400);
                echo json_encode(['error' => 'Solo se pueden reembolsar pagos completados']);
                return;
            }

            // Update transaction
            $this->updateTransaction($id, [
                'status' => 'refunded',
                'verified_by' => $_SESSION['admin_id'] ?? null,
                'notes' => ($transaction['notes'] ?? '') . "\nReembolso procesado el " . date('Y-m-d H:i:s')
            ]);

            // Update related order or subscription
            if ($transaction['order_id']) {
                $this->updateOrderPaymentStatus($transaction['order_id'], 'refunded');
            }

            // Log audit
            $this->logAudit([
                'admin_id' => $_SESSION['admin_id'] ?? null,
                'action_type' => 'payment_refund',
                'entity_type' => 'payment_transaction',
                'entity_id' => $id,
                'description' => "Reembolso procesado para transacción #{$id} - Monto: €{$transaction['amount']}"
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Reembolso procesado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar el reembolso: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: Update transaction
     */
    private function updateTransaction($id, $data) {
        $setClauses = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "$key = ?";
            $params[] = $value;
        }

        $params[] = $id;
        $sql = "UPDATE payment_transactions SET " . implode(', ', $setClauses) . " WHERE transaction_id = ?";
        $this->executeQuery($sql, $params);
    }

    /**
     * Helper: Update order payment status
     */
    private function updateOrderPaymentStatus($orderId, $status) {
        $sql = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
        $this->executeQuery($sql, [$status, $orderId]);
    }

    /**
     * Helper: Update subscription payment status
     */
    private function updateSubscriptionPaymentStatus($subscriptionId) {
        $sql = "UPDATE subscriptions
                SET last_payment_date = NOW(),
                    total_months_paid = total_months_paid + 1,
                    next_billing_date = DATE_ADD(next_billing_date, INTERVAL 1 MONTH)
                WHERE subscription_id = ?";
        $this->executeQuery($sql, [$subscriptionId]);
    }

    /**
     * Helper: Log audit
     */
    private function logAudit($data) {
        $sql = "INSERT INTO audit_log (admin_id, action_type, entity_type, entity_id, old_values, new_values, description, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $this->executeQuery($sql, [
            $data['admin_id'] ?? null,
            $data['action_type'] ?? null,
            $data['entity_type'] ?? null,
            $data['entity_id'] ?? null,
            $data['old_values'] ?? null,
            $data['new_values'] ?? null,
            $data['description'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    /**
     * Helper: Execute SQL query
     */
    private function executeQuery($sql, $params = []) {
        try {
            require_once __DIR__ . '/../../config/database.php';
            global $pdo;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            if (stripos($sql, 'SELECT') === 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Error en la base de datos");
        }
    }

    /**
     * Check admin authentication
     */
    private function checkAdminAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
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
