<?php
/**
 * Suscripciones Controller - Admin CRM
 * Gestión de suscripciones desde el panel de administración
 */

require_once __DIR__ . '/../../models/Subscription.php';
require_once __DIR__ . '/../../models/Customer.php';

class SuscripcionesController {
    private $subscriptionModel;
    private $customerModel;

    public function __construct() {
        $this->subscriptionModel = new Subscription();
        $this->customerModel = new Customer();
    }

    /**
     * Lista de todas las suscripciones
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Render view (uses layout/header.php and layout/footer.php)
        require_once __DIR__ . '/../../views/admin/suscripciones/index.php';
    }


    /**
     * Pausar suscripción
     */
    public function pause($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $reason = $_POST['reason'] ?? null;
                $this->subscriptionModel->pauseSubscription($id, $reason);

                // Create audit log
                $this->createAuditLog('status_change', 'subscription', $id, [
                    'status' => 'paused',
                    'reason' => $reason
                ]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Suscripción pausada correctamente']);
            } catch (Exception $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Cancelar suscripción
     */
    public function cancel($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $reason = $_POST['reason'] ?? null;
                $this->subscriptionModel->cancelSubscription($id, $reason);

                // Create audit log
                $this->createAuditLog('status_change', 'subscription', $id, [
                    'status' => 'cancelled',
                    'reason' => $reason
                ]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Suscripción cancelada correctamente']);
            } catch (Exception $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Reactivar suscripción
     */
    public function reactivate($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->subscriptionModel->reactivateSubscription($id);

                // Create audit log
                $this->createAuditLog('status_change', 'subscription', $id, [
                    'status' => 'active'
                ]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Suscripción reactivada correctamente']);
            } catch (Exception $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Create audit log entry
     */
    private function createAuditLog($action, $entityType, $entityId, $newValues) {
        $sql = "INSERT INTO audit_log (admin_id, action_type, entity_type, entity_id, new_values, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->subscriptionModel->query($sql, [
            $_SESSION['admin_id'] ?? null,
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
}
