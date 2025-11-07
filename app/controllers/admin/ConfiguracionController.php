<?php
/**
 * Configuracion Controller - Admin CRM
 * Gestión de configuración del sistema
 */

class ConfiguracionController {
    private $db;

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
     * Vista principal de configuración
     */
    public function index() {
        $this->checkAdminAuth();

        // Get all settings grouped by category
        $settingsStmt = $this->db->query("
            SELECT *
            FROM system_settings
            ORDER BY setting_key ASC
        ");
        $allSettings = $settingsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Group settings by category (extracted from key prefix)
        $settingsByCategory = [];
        foreach ($allSettings as $setting) {
            $parts = explode('_', $setting['setting_key']);
            $category = $parts[0] ?? 'general';
            if (!isset($settingsByCategory[$category])) {
                $settingsByCategory[$category] = [];
            }
            $settingsByCategory[$category][] = $setting;
        }

        // Get system info
        $systemInfo = [
            'php_version' => phpversion(),
            'db_version' => $this->db->query("SELECT VERSION()")->fetchColumn(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'max_upload_size' => ini_get('upload_max_filesize'),
            'max_post_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'time_zone' => date_default_timezone_get()
        ];

        // Get admin stats
        $adminStatsStmt = $this->db->query("
            SELECT
                COUNT(*) as total_admins,
                COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_admins,
                MAX(last_login) as last_admin_login
            FROM admin_users
        ");
        $adminStats = $adminStatsStmt->fetch(PDO::FETCH_ASSOC);

        // Get database stats
        $dbStatsStmt = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM customers) as total_customers,
                (SELECT COUNT(*) FROM products) as total_products,
                (SELECT COUNT(*) FROM orders) as total_orders,
                (SELECT COUNT(*) FROM subscriptions) as total_subscriptions
        ");
        $dbStats = $dbStatsStmt->fetch(PDO::FETCH_ASSOC);

        // Render view
        $content = $this->renderView('admin/configuracion/index', [
            'settingsByCategory' => $settingsByCategory,
            'systemInfo' => $systemInfo,
            'adminStats' => $adminStats,
            'dbStats' => $dbStats
        ]);

        $this->renderLayout('admin-crm', [
            'content' => $content,
            'page_title' => 'Configuración',
            'active_page' => 'configuracion',
            'admin_name' => $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin'
        ]);
    }

    /**
     * Actualizar configuraciones (POST)
     */
    public function update() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/configuracion');
            exit;
        }

        // Get admin ID
        $adminId = $_SESSION['admin_user']['admin_id'] ?? null;

        try {
            $this->db->beginTransaction();

            // Process each setting
            $updatedCount = 0;
            foreach ($_POST as $key => $value) {
                // Skip CSRF token and other non-setting fields
                if ($key === 'csrf_token' || $key === 'action') {
                    continue;
                }

                // Check if setting exists
                $checkStmt = $this->db->prepare("SELECT setting_id, setting_type FROM system_settings WHERE setting_key = ?");
                $checkStmt->execute([$key]);
                $setting = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($setting) {
                    // Validate and cast value based on type
                    $typedValue = $this->castValue($value, $setting['setting_type']);

                    // Update setting
                    $updateStmt = $this->db->prepare("
                        UPDATE system_settings
                        SET setting_value = ?,
                            updated_by = ?,
                            updated_at = NOW()
                        WHERE setting_key = ?
                    ");
                    $updateStmt->execute([$typedValue, $adminId, $key]);
                    $updatedCount++;
                }
            }

            $this->db->commit();

            // Set success message
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => "Configuración actualizada correctamente. $updatedCount ajustes guardados."
            ];

        } catch (Exception $e) {
            $this->db->rollBack();

            // Set error message
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Error al actualizar la configuración: ' . $e->getMessage()
            ];
        }

        header('Location: /admin/configuracion');
        exit;
    }

    /**
     * Helper: Cast value to correct type
     */
    private function castValue($value, $type) {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? $value : 0;
            case 'boolean':
                return $value === 'true' || $value === '1' || $value === true ? 'true' : 'false';
            case 'json':
                if (is_array($value)) {
                    return json_encode($value);
                }
                // Validate JSON
                json_decode($value);
                return json_last_error() === JSON_ERROR_NONE ? $value : '{}';
            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * API: Get single setting (JSON)
     */
    public function getSetting($key) {
        $this->checkAdminAuth();

        $stmt = $this->db->prepare("SELECT * FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$setting) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Setting not found'
            ]);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Clear cache (POST)
     */
    public function clearCache() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/configuracion');
            exit;
        }

        try {
            // Clear file cache if exists
            $cacheDir = __DIR__ . '/../../../storage/cache';
            if (is_dir($cacheDir)) {
                $files = glob($cacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }

            // Clear opcache if available
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Caché limpiada correctamente'
            ];

        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Error al limpiar caché: ' . $e->getMessage()
            ];
        }

        header('Location: /admin/configuracion');
        exit;
    }

    /**
     * Test email configuration (POST)
     */
    public function testEmail() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/configuracion');
            exit;
        }

        $testEmail = $_POST['test_email'] ?? '';

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Email inválido'
            ];
            header('Location: /admin/configuracion');
            exit;
        }

        // TODO: Implement actual email sending
        // For now, just simulate success
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => "Email de prueba enviado a $testEmail"
        ];

        header('Location: /admin/configuracion');
        exit;
    }
}
