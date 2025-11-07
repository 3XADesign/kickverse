<?php
/**
 * Clientes Controller - Admin CRM
 * Gestión de clientes desde el panel de administración
 */

require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Order.php';

class ClientesController {
    private $customerModel;
    private $orderModel;
    private $perPage = 50;

    public function __construct() {
        $this->customerModel = new Customer();
        $this->orderModel = new Order();
    }

    /**
     * Lista de todos los clientes
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->perPage;

        // Filters
        $filters = [];
        if (isset($_GET['tier']) && $_GET['tier'] !== '') {
            $filters['loyalty_tier'] = $_GET['tier'];
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $filters['customer_status'] = $_GET['status'];
        }
        $filters['deleted_at'] = null;

        // Get total count
        $totalClientes = $this->customerModel->count($filters);
        $totalPages = ceil($totalClientes / $this->perPage);

        // Get clientes
        $clientes = $this->customerModel->all([
            'where' => $filters,
            'order_by' => 'registration_date DESC',
            'limit' => $this->perPage,
            'offset' => $offset
        ]);

        // Render view
        $content = $this->renderView('admin/clientes/index', [
            'clientes' => $clientes,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_clientes' => $totalClientes
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Clientes',
            'current_page' => 'clientes',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de un cliente (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $cliente = $this->customerModel->find($id);

        if (!$cliente) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Cliente no encontrado']);
            return;
        }

        // Get customer statistics
        $stats = $this->getCustomerStats($id);

        // Get shipping addresses
        $addresses = $this->customerModel->getAddresses($id);

        // Get recent orders (últimos 10)
        $recentOrders = $this->getRecentOrders($id, 10);

        // Get active subscriptions
        $subscriptions = $this->getActiveSubscriptions($id);

        // Remove sensitive data
        unset($cliente['password_hash']);
        unset($cliente['password_reset_token']);
        unset($cliente['email_verification_token']);

        // Combine all data
        $response = array_merge($cliente, [
            'statistics' => $stats,
            'shipping_addresses' => $addresses,
            'recent_orders' => $recentOrders,
            'subscriptions' => $subscriptions
        ]);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Procesar creación de cliente (API JSON)
     */
    public function store() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($input['full_name'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Nombre completo es requerido']);
            return;
        }

        // Validate at least one contact method
        if (empty($input['email']) && empty($input['telegram_username']) && empty($input['whatsapp_number'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Debe proporcionar al menos un método de contacto']);
            return;
        }

        // Check if email already exists
        if (!empty($input['email'])) {
            $existing = $this->customerModel->findByEmail($input['email']);
            if ($existing) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'El email ya está registrado']);
                return;
            }
        }

        try {
            $data = [
                'email' => $input['email'] ?? null,
                'full_name' => $input['full_name'],
                'phone' => $input['phone'] ?? null,
                'telegram_username' => $input['telegram_username'] ?? null,
                'whatsapp_number' => $input['whatsapp_number'] ?? null,
                'preferred_language' => $input['preferred_language'] ?? 'es',
                'customer_status' => $input['customer_status'] ?? 'active',
                'email_verified' => 1, // Admin created customers are pre-verified
                'loyalty_tier' => $input['loyalty_tier'] ?? 'standard',
                'loyalty_points' => $input['loyalty_points'] ?? 0,
                'marketing_consent' => $input['marketing_consent'] ?? 0,
                'newsletter_subscribed' => $input['newsletter_subscribed'] ?? 0
            ];

            // If password provided, hash it
            if (isset($input['password']) && !empty($input['password'])) {
                $data['password_hash'] = password_hash($input['password'], PASSWORD_BCRYPT);
            }

            $customerId = $this->customerModel->create($data);

            // Create audit log
            $this->createAuditLog('create', 'customer', $customerId, $data);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cliente creado correctamente',
                'customer_id' => $customerId
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Formulario de creación
     */
    public function create() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate input
            $errors = $this->validateClienteData($_POST);

            if (empty($errors)) {
                try {
                    $data = [
                        'email' => $_POST['email'] ?? null,
                        'full_name' => $_POST['full_name'],
                        'phone' => $_POST['phone'] ?? null,
                        'whatsapp_number' => $_POST['whatsapp_number'] ?? null,
                        'telegram_username' => $_POST['telegram_username'] ?? null,
                        'preferred_language' => $_POST['preferred_language'] ?? 'es',
                        'customer_status' => $_POST['customer_status'] ?? 'active',
                        'loyalty_tier' => $_POST['loyalty_tier'] ?? 'standard',
                        'loyalty_points' => $_POST['loyalty_points'] ?? 0,
                        'newsletter_subscribed' => isset($_POST['newsletter_subscribed']) ? 1 : 0,
                        'marketing_consent' => isset($_POST['marketing_consent']) ? 1 : 0
                    ];

                    // If email is provided and password too, hash it
                    if (!empty($_POST['password'])) {
                        $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    }

                    $clienteId = $this->customerModel->create($data);

                    header('Location: /admin/clientes?success=created');
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'Error al crear el cliente: ' . $e->getMessage();
                }
            }
        }

        // Render form
        $content = $this->renderView('admin/clientes/form', [
            'errors' => $errors ?? [],
            'cliente' => $_POST ?? [],
            'action' => 'create'
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Nuevo Cliente',
            'current_page' => 'clientes',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Procesar actualización de cliente (API JSON)
     */
    public function update($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $cliente = $this->customerModel->find($id);
        if (!$cliente) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Cliente no encontrado']);
            return;
        }

        try {
            $data = [];

            // Allowed fields to update
            $allowedFields = [
                'email', 'full_name', 'phone', 'telegram_username', 'whatsapp_number',
                'preferred_language', 'customer_status', 'loyalty_tier', 'loyalty_points',
                'marketing_consent', 'newsletter_subscribed'
            ];

            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $data[$field] = $input[$field];
                }
            }

            // If password provided, hash it
            if (isset($input['password']) && !empty($input['password'])) {
                $data['password_hash'] = password_hash($input['password'], PASSWORD_BCRYPT);
            }

            if (!empty($data)) {
                $this->customerModel->update($id, $data);

                // Create audit log
                $this->createAuditLog('update', 'customer', $id, $data);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cliente actualizado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->checkAdminAuth();

        $cliente = $this->customerModel->find($id);

        if (!$cliente) {
            header('Location: /admin/clientes?error=not_found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateClienteData($_POST, $id);

            if (empty($errors)) {
                try {
                    $data = [
                        'email' => $_POST['email'] ?? null,
                        'full_name' => $_POST['full_name'],
                        'phone' => $_POST['phone'] ?? null,
                        'whatsapp_number' => $_POST['whatsapp_number'] ?? null,
                        'telegram_username' => $_POST['telegram_username'] ?? null,
                        'preferred_language' => $_POST['preferred_language'] ?? 'es',
                        'customer_status' => $_POST['customer_status'] ?? 'active',
                        'loyalty_tier' => $_POST['loyalty_tier'] ?? 'standard',
                        'loyalty_points' => $_POST['loyalty_points'] ?? 0,
                        'newsletter_subscribed' => isset($_POST['newsletter_subscribed']) ? 1 : 0,
                        'marketing_consent' => isset($_POST['marketing_consent']) ? 1 : 0
                    ];

                    // Update password only if provided
                    if (!empty($_POST['password'])) {
                        $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    }

                    $this->customerModel->update($id, $data);

                    header('Location: /admin/clientes?success=updated');
                    exit;
                } catch (Exception $e) {
                    $errors[] = 'Error al actualizar el cliente: ' . $e->getMessage();
                }
            }
        }

        // Render form
        $content = $this->renderView('admin/clientes/form', [
            'errors' => $errors ?? [],
            'cliente' => $cliente,
            'action' => 'edit'
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Editar Cliente',
            'current_page' => 'clientes',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function delete($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $cliente = $this->customerModel->find($id);
        if (!$cliente) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Cliente no encontrado']);
            return;
        }

        try {
            $this->customerModel->deleteCustomer($id);

            // Create audit log
            $this->createAuditLog('delete', 'customer', $id, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cliente eliminado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get customer statistics
     */
    private function getCustomerStats($customerId) {
        $sql = "SELECT
                    COUNT(o.order_id) as total_orders_count,
                    COALESCE(SUM(o.total_amount), 0) as total_spent,
                    c.loyalty_points
                FROM customers c
                LEFT JOIN orders o ON c.customer_id = o.customer_id
                    AND o.order_status IN ('processing', 'shipped', 'delivered')
                WHERE c.customer_id = ?
                GROUP BY c.customer_id";

        return $this->customerModel->fetchOne($sql, [$customerId]);
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($customerId, $limit = 10) {
        $sql = "SELECT
                    order_id,
                    order_type,
                    order_status,
                    payment_status,
                    total_amount,
                    order_date,
                    tracking_number
                FROM orders
                WHERE customer_id = ?
                ORDER BY order_date DESC
                LIMIT ?";

        return $this->orderModel->fetchAll($sql, [$customerId, $limit]);
    }

    /**
     * Get active subscriptions
     */
    private function getActiveSubscriptions($customerId) {
        $sql = "SELECT
                    s.subscription_id,
                    s.status,
                    s.start_date,
                    s.next_billing_date,
                    sp.plan_name,
                    sp.monthly_price
                FROM subscriptions s
                JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE s.customer_id = ?
                    AND s.status IN ('active', 'pending')
                ORDER BY s.start_date DESC";

        return $this->customerModel->fetchAll($sql, [$customerId]);
    }

    /**
     * Create audit log entry
     */
    private function createAuditLog($action, $entityType, $entityId, $newValues) {
        $sql = "INSERT INTO audit_log (admin_id, action_type, entity_type, entity_id, new_values, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->customerModel->query($sql, [
            $_SESSION['admin_id'] ?? null,
            $action,
            $entityType,
            $entityId,
            json_encode($newValues),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    /**
     * Validate cliente data
     */
    private function validateClienteData($data, $excludeId = null) {
        $errors = [];

        // Full name is required
        if (empty($data['full_name'])) {
            $errors[] = 'El nombre completo es obligatorio';
        }

        // Email validation (if provided)
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El email no es válido';
            }

            // Check if email already exists
            $existing = $this->customerModel->findByEmail($data['email']);
            if ($existing && (!$excludeId || $existing['customer_id'] != $excludeId)) {
                $errors[] = 'Este email ya está registrado';
            }
        }

        // At least one contact method (email, telegram, whatsapp)
        if (empty($data['email']) && empty($data['telegram_username']) && empty($data['whatsapp_number'])) {
            $errors[] = 'Debe proporcionar al menos un método de contacto (email, telegram o whatsapp)';
        }

        return $errors;
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
