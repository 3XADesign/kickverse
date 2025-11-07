<?php
/**
 * Admin Customer Controller
 * Manage customers in admin panel
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Customer.php';

class AdminCustomerController extends Controller {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdminAuth();
        $this->customerModel = new Customer();
    }

    private function requireAdminAuth() {
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * List all customers
     */
    public function index() {
        $page = (int) ($this->get('page') ?? 1);
        $perPage = 50;
        $search = $this->get('search');
        $tier = $this->get('tier');

        try {
            $sql = "SELECT * FROM customers WHERE deleted_at IS NULL";
            $params = [];

            if ($search) {
                $sql .= " AND (full_name LIKE ? OR email LIKE ? OR telegram_username LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            if ($tier) {
                $sql .= " AND loyalty_tier = ?";
                $params[] = $tier;
            }

            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = ($page - 1) * $perPage;

            $customers = $this->customerModel->fetchAll($sql, $params);

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM customers WHERE deleted_at IS NULL";
            $countParams = [];

            if ($search) {
                $countSql .= " AND (full_name LIKE ? OR email LIKE ? OR telegram_username LIKE ?)";
                $searchParam = '%' . $search . '%';
                $countParams[] = $searchParam;
                $countParams[] = $searchParam;
                $countParams[] = $searchParam;
            }

            if ($tier) {
                $countSql .= " AND loyalty_tier = ?";
                $countParams[] = $tier;
            }

            $total = $this->customerModel->fetchOne($countSql, $countParams)['total'];

            $this->view('admin/customers/index', [
                'customers' => $customers,
                'page' => $page,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'search' => $search,
                'tier_filter' => $tier
            ]);
        } catch (Exception $e) {
            die('Error loading customers: ' . $e->getMessage());
        }
    }

    /**
     * Show customer details
     */
    public function show($customerId) {
        try {
            $customer = $this->customerModel->find($customerId);

            if (!$customer || $customer['deleted_at']) {
                $this->setFlash('error', 'Cliente no encontrado');
                $this->redirect('/admin/customers');
            }

            // Get customer orders
            $sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC LIMIT 20";
            $orders = $this->customerModel->fetchAll($sql, [$customerId]);

            // Get addresses
            $addresses = $this->customerModel->getAddresses($customerId);

            // Get loyalty points history
            $sql = "SELECT * FROM loyalty_points_history WHERE customer_id = ? ORDER BY transaction_date DESC LIMIT 20";
            $loyaltyHistory = $this->customerModel->fetchAll($sql, [$customerId]);

            $this->view('admin/customers/show', [
                'customer' => $customer,
                'orders' => $orders,
                'addresses' => $addresses,
                'loyalty_history' => $loyaltyHistory
            ]);
        } catch (Exception $e) {
            die('Error loading customer: ' . $e->getMessage());
        }
    }
}
