<?php
/**
 * Admin Suscripciones API Controller
 * API endpoints para gestión de suscripciones en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Subscription.php';

class AdminSuscripcionesApiController extends Controller {
    private $subscriptionModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->subscriptionModel = new Subscription();
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
     * Get all subscriptions with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build filters
            $filters = [];

            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $filters['status'] = $_GET['status'];
            }

            if (isset($_GET['plan_id']) && $_GET['plan_id'] !== '') {
                $filters['plan_id'] = $_GET['plan_id'];
            }

            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $filters['customer_search'] = trim($_GET['search']);
            }

            // Count total
            $total = $this->subscriptionModel->countWithFilters($filters);

            // Get subscriptions with JOIN
            $sql = "SELECT
                        s.*,
                        c.full_name as customer_name,
                        c.email as customer_email,
                        c.telegram_username,
                        c.whatsapp_number,
                        sp.plan_name,
                        sp.monthly_price,
                        sp.plan_type,
                        COUNT(DISTINCT spm.payment_id) as total_payments,
                        COUNT(DISTINCT ssh.shipment_id) as total_shipments,
                        COALESCE(SUM(CASE WHEN spm.status = 'completed' THEN spm.amount ELSE 0 END), 0) as total_paid
                    FROM subscriptions s
                    JOIN customers c ON s.customer_id = c.customer_id
                    JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                    LEFT JOIN subscription_payments spm ON s.subscription_id = spm.subscription_id
                    LEFT JOIN subscription_shipments ssh ON s.subscription_id = ssh.subscription_id";

            // Build WHERE clause
            $where = [];
            $params = [];

            if (isset($filters['status'])) {
                $where[] = "s.status = ?";
                $params[] = $filters['status'];
            }

            if (isset($filters['plan_id'])) {
                $where[] = "s.plan_id = ?";
                $params[] = $filters['plan_id'];
            }

            if (isset($filters['customer_search'])) {
                $where[] = "(c.full_name LIKE ? OR c.email LIKE ? OR c.telegram_username LIKE ?)";
                $searchTerm = '%' . $filters['customer_search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $sql .= " GROUP BY s.subscription_id
                     ORDER BY s.created_at DESC
                     LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $subscriptions = $this->subscriptionModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'subscriptions' => $subscriptions,
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
            error_log("Error getting subscriptions: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar suscripciones'
            ], 500);
        }
    }

    /**
     * Get single subscription details with all related data
     */
    public function getOne($subscriptionId) {
        try {
            // Get subscription with full details
            $subscription = $this->subscriptionModel->getFullDetails($subscriptionId);

            if (!$subscription) {
                $this->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada'
                ], 404);
                return;
            }

            // Get payment history
            $payments = $this->subscriptionModel->getPaymentHistory($subscriptionId);

            // Get shipment history
            $shipments = $this->subscriptionModel->getShipmentHistory($subscriptionId);

            // Decode JSON preferences
            $subscription['league_preferences_decoded'] = json_decode($subscription['league_preferences'] ?? '[]', true);
            $subscription['team_preferences_decoded'] = json_decode($subscription['team_preferences'] ?? '[]', true);
            $subscription['teams_to_exclude_decoded'] = json_decode($subscription['teams_to_exclude'] ?? '[]', true);

            // Get league names
            if (!empty($subscription['league_preferences_decoded'])) {
                $subscription['leagues'] = $this->subscriptionModel->getLeagueNames($subscription['league_preferences_decoded']);
            } else {
                $subscription['leagues'] = [];
            }

            // Get team names
            if (!empty($subscription['team_preferences_decoded'])) {
                $subscription['teams'] = $this->subscriptionModel->getTeamNames($subscription['team_preferences_decoded']);
            } else {
                $subscription['teams'] = [];
            }

            // Get excluded team names
            if (!empty($subscription['teams_to_exclude_decoded'])) {
                $subscription['excluded_teams'] = $this->subscriptionModel->getTeamNames($subscription['teams_to_exclude_decoded']);
            } else {
                $subscription['excluded_teams'] = [];
            }

            // Decode shipment contents
            foreach ($shipments as &$shipment) {
                $shipment['contents_decoded'] = json_decode($shipment['contents'] ?? '[]', true);
            }

            // Decode payment transaction data
            foreach ($payments as &$payment) {
                if (!empty($payment['transaction_data'])) {
                    $payment['transaction_data_decoded'] = json_decode($payment['transaction_data'], true);
                }
            }

            $this->json([
                'success' => true,
                'subscription' => $subscription,
                'payments' => $payments,
                'shipments' => $shipments
            ]);

        } catch (Exception $e) {
            error_log("Error getting subscription: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar suscripción'
            ], 500);
        }
    }
}
