<?php
/**
 * Cupones Controller - Admin CRM
 * Gestión de cupones de descuento desde el panel de administración
 */

class CuponesController {
    private $db;
    private $perPage = 50;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verificar autenticación de admin
     */
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_user'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Renderizar vista
     */
    private function renderView($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }

    /**
     * Renderizar layout
     */
    private function renderLayout($layout, $data = []) {
        extract($data);
        include __DIR__ . '/../../views/layouts/' . $layout . '.php';
    }

    /**
     * Lista de todos los cupones
     */
    public function index() {
        $this->checkAdminAuth();

        // Simply render the view - data will be loaded via API
        ob_start();
        include __DIR__ . '/../../views/admin/cupones/index.php';
        echo ob_get_clean();
    }

    /**
     * Crear cupón
     */
    public function create() {
        $this->checkAdminAuth();

        $content = $this->renderView('admin/cupones/create', []);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Crear Cupón',
            'active_page' => 'cupones',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Guardar nuevo cupón
     */
    public function store() {
        $this->checkAdminAuth();

        $data = $_POST;

        // Validación
        $errors = $this->validateCouponData($data);
        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode(', ', $errors)];
            header('Location: /admin/cupones/crear');
            exit;
        }

        try {
            // Check if code already exists
            $checkStmt = $this->db->prepare("SELECT coupon_id FROM coupons WHERE code = ?");
            $checkStmt->execute([strtoupper($data['code'])]);
            if ($checkStmt->fetch()) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'El código de cupón ya existe'];
                header('Location: /admin/cupones/crear');
                exit;
            }

            // Insert coupon
            $stmt = $this->db->prepare("
                INSERT INTO coupons (
                    code, description, discount_type, discount_value,
                    max_discount_amount, min_purchase_amount,
                    applies_to_product_type, applies_to_first_order_only,
                    usage_limit_total, usage_limit_per_customer,
                    valid_from, valid_until, is_active, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                strtoupper($data['code']),
                $data['description'] ?? null,
                $data['discount_type'],
                $data['discount_value'],
                $data['max_discount_amount'] ?? null,
                $data['min_purchase_amount'] ?? 0.00,
                $data['applies_to_product_type'] ?? 'all',
                isset($data['applies_to_first_order_only']) ? 1 : 0,
                $data['usage_limit_total'] ?? null,
                $data['usage_limit_per_customer'] ?? 1,
                $data['valid_from'] ?? null,
                $data['valid_until'] ?? null,
                isset($data['is_active']) ? 1 : 0,
                $_SESSION['admin_id'] ?? null
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cupón creado correctamente'];
            header('Location: /admin/cupones');
            exit;
        } catch (PDOException $e) {
            error_log("Error creating coupon: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al crear el cupón'];
            header('Location: /admin/cupones/crear');
            exit;
        }
    }

    /**
     * Editar cupón
     */
    public function edit($id) {
        $this->checkAdminAuth();

        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Cupón no encontrado'];
            header('Location: /admin/cupones');
            exit;
        }

        $content = $this->renderView('admin/cupones/edit', ['coupon' => $coupon]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Editar Cupón',
            'active_page' => 'cupones',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Actualizar cupón
     */
    public function update($id) {
        $this->checkAdminAuth();

        $data = $_POST;

        // Validación
        $errors = $this->validateCouponData($data, $id);
        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode(', ', $errors)];
            header('Location: /admin/cupones/editar/' . $id);
            exit;
        }

        try {
            // Check if code already exists (excluding current coupon)
            $checkStmt = $this->db->prepare("SELECT coupon_id FROM coupons WHERE code = ? AND coupon_id != ?");
            $checkStmt->execute([strtoupper($data['code']), $id]);
            if ($checkStmt->fetch()) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'El código de cupón ya existe'];
                header('Location: /admin/cupones/editar/' . $id);
                exit;
            }

            $stmt = $this->db->prepare("
                UPDATE coupons SET
                    code = ?,
                    description = ?,
                    discount_type = ?,
                    discount_value = ?,
                    max_discount_amount = ?,
                    min_purchase_amount = ?,
                    applies_to_product_type = ?,
                    applies_to_first_order_only = ?,
                    usage_limit_total = ?,
                    usage_limit_per_customer = ?,
                    valid_from = ?,
                    valid_until = ?,
                    is_active = ?
                WHERE coupon_id = ?
            ");

            $stmt->execute([
                strtoupper($data['code']),
                $data['description'] ?? null,
                $data['discount_type'],
                $data['discount_value'],
                $data['max_discount_amount'] ?? null,
                $data['min_purchase_amount'] ?? 0.00,
                $data['applies_to_product_type'] ?? 'all',
                isset($data['applies_to_first_order_only']) ? 1 : 0,
                $data['usage_limit_total'] ?? null,
                $data['usage_limit_per_customer'] ?? 1,
                $data['valid_from'] ?? null,
                $data['valid_until'] ?? null,
                isset($data['is_active']) ? 1 : 0,
                $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cupón actualizado correctamente'];
            header('Location: /admin/cupones');
            exit;
        } catch (PDOException $e) {
            error_log("Error updating coupon: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al actualizar el cupón'];
            header('Location: /admin/cupones/editar/' . $id);
            exit;
        }
    }

    /**
     * Validar datos del cupón
     */
    private function validateCouponData($data, $couponId = null) {
        $errors = [];

        // Code required and alphanumeric
        if (empty($data['code'])) {
            $errors[] = 'El código es requerido';
        } elseif (!preg_match('/^[A-Z0-9_-]+$/i', $data['code'])) {
            $errors[] = 'El código solo puede contener letras, números, guiones y guiones bajos';
        }

        // Discount type
        if (empty($data['discount_type']) || !in_array($data['discount_type'], ['fixed', 'percentage'])) {
            $errors[] = 'Tipo de descuento inválido';
        }

        // Discount value
        if (!isset($data['discount_value']) || $data['discount_value'] <= 0) {
            $errors[] = 'El valor del descuento debe ser mayor a 0';
        }

        // Valid dates
        if (!empty($data['valid_from']) && !empty($data['valid_until'])) {
            if (strtotime($data['valid_from']) > strtotime($data['valid_until'])) {
                $errors[] = 'La fecha de inicio debe ser anterior a la fecha de fin';
            }
        }

        // Usage limits
        if (!empty($data['usage_limit_per_customer']) && !empty($data['usage_limit_total'])) {
            if ($data['usage_limit_per_customer'] > $data['usage_limit_total']) {
                $errors[] = 'El límite por cliente no puede ser mayor al límite total';
            }
        }

        return $errors;
    }

    /**
     * Eliminar cupón
     */
    public function delete($id) {
        $this->checkAdminAuth();

        $stmt = $this->db->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Cupón eliminado correctamente'
        ]);
    }
}
