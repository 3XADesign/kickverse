<?php
/**
 * Clientes Controller - Admin CRM
 * Gestión de clientes desde el panel de administración
 */

require_once __DIR__ . '/../../models/Customer.php';

class ClientesController {
    private $customerModel;
    private $perPage = 50;

    public function __construct() {
        $this->customerModel = new Customer();
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
            echo json_encode(['error' => 'Cliente no encontrado']);
            return;
        }

        // Remove sensitive data
        unset($cliente['password_hash']);
        unset($cliente['password_reset_token']);
        unset($cliente['email_verification_token']);

        header('Content-Type: application/json');
        echo json_encode($cliente);
    }

    /**
     * Crear nuevo cliente
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
     * Editar cliente
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

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                // Soft delete
                $this->customerModel->update($id, [
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'customer_status' => 'inactive'
                ]);

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
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
