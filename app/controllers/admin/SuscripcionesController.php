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
    private $perPage = 50;

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

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $filters = [];
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filters['status'] = $_GET['status'];
        }
        if (isset($_GET['plan_id']) && $_GET['plan_id'] !== '') {
            $filters['plan_id'] = $_GET['plan_id'];
        }
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $filters['customer_search'] = $_GET['search'];
        }

        // Get total count
        $totalSuscripciones = $this->subscriptionModel->countWithFilters($filters);
        $totalPages = ceil($totalSuscripciones / $this->perPage);

        // Get suscripciones
        $suscripciones = $this->subscriptionModel->getAllWithDetails($filters, 's.created_at DESC', $this->perPage, $offset);

        // Get all plans for filter
        $planes = $this->subscriptionModel->getAllPlans();

        // Get stats
        $stats = $this->subscriptionModel->getStats();

        // Render view
        $content = $this->renderView('admin/suscripciones/index', [
            'suscripciones' => $suscripciones,
            'planes' => $planes,
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_suscripciones' => $totalSuscripciones,
            'filters' => $filters
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Suscripciones',
            'current_page' => 'suscripciones',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de una suscripción (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $suscripcion = $this->subscriptionModel->getFullDetails($id);

        if (!$suscripcion) {
            http_response_code(404);
            echo json_encode(['error' => 'Suscripción no encontrada']);
            return;
        }

        // Get payment history
        $payments = $this->subscriptionModel->getPaymentHistory($id);

        // Get shipment history
        $shipments = $this->subscriptionModel->getShipmentHistory($id);

        // Decode JSON preferences
        $suscripcion['league_preferences_decoded'] = json_decode($suscripcion['league_preferences'] ?? '[]', true);
        $suscripcion['team_preferences_decoded'] = json_decode($suscripcion['team_preferences'] ?? '[]', true);
        $suscripcion['teams_to_exclude_decoded'] = json_decode($suscripcion['teams_to_exclude'] ?? '[]', true);

        // Get league names
        if (!empty($suscripcion['league_preferences_decoded'])) {
            $suscripcion['leagues'] = $this->subscriptionModel->getLeagueNames($suscripcion['league_preferences_decoded']);
        } else {
            $suscripcion['leagues'] = [];
        }

        // Get team names
        if (!empty($suscripcion['team_preferences_decoded'])) {
            $suscripcion['teams'] = $this->subscriptionModel->getTeamNames($suscripcion['team_preferences_decoded']);
        } else {
            $suscripcion['teams'] = [];
        }

        // Decode shipment contents
        foreach ($shipments as &$shipment) {
            $shipment['contents_decoded'] = json_decode($shipment['contents'] ?? '[]', true);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'subscription' => $suscripcion,
            'payments' => $payments,
            'shipments' => $shipments
        ]);
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
     * Check admin authentication
     */
    private function checkAdminAuth() {
        session_start();
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
