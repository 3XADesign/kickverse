<?php
/**
 * Equipos Controller - Admin CRM
 * Gestión de equipos desde el panel de administración
 */

require_once __DIR__ . '/../../models/Team.php';
require_once __DIR__ . '/../../models/League.php';

class EquiposController {
    private $teamModel;
    private $leagueModel;

    public function __construct() {
        $this->teamModel = new Team();
        $this->leagueModel = new League();
    }

    /**
     * Lista de todos los equipos
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Filters
        $filters = [];
        if (isset($_GET['league_id']) && $_GET['league_id'] !== '') {
            $filters['league_id'] = $_GET['league_id'];
        }
        if (isset($_GET['is_top_team']) && $_GET['is_top_team'] !== '') {
            $filters['is_top_team'] = $_GET['is_top_team'];
        }
        if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
            $filters['is_active'] = $_GET['is_active'];
        }

        // Get equipos with league and product count
        $equipos = $this->teamModel->getAllWithLeague($filters);

        // Get all leagues for filter
        $leagues = $this->leagueModel->fetchAll(
            "SELECT * FROM leagues ORDER BY display_order ASC, name ASC"
        );

        // Render view
        $content = $this->renderView('admin/equipos/index', [
            'equipos' => $equipos,
            'leagues' => $leagues,
            'filters' => $filters
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Equipos',
            'current_page' => 'equipos',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de un equipo (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $equipo = $this->teamModel->getWithProducts($id);

        if (!$equipo) {
            http_response_code(404);
            echo json_encode(['error' => 'Equipo no encontrado']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($equipo);
    }

    /**
     * Crear nuevo equipo
     */
    public function create() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateEquipoData($_POST);

            if (empty($errors)) {
                try {
                    $slug = $this->createSlug($_POST['name']);

                    $data = [
                        'league_id' => $_POST['league_id'],
                        'name' => $_POST['name'],
                        'slug' => $slug,
                        'logo_path' => $_POST['logo_path'] ?? null,
                        'is_top_team' => isset($_POST['is_top_team']) ? 1 : 0,
                        'display_order' => $_POST['display_order'] ?? 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0
                    ];

                    $equipoId = $this->teamModel->create($data);

                    echo json_encode(['success' => true, 'team_id' => $equipoId]);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }
        }
    }

    /**
     * Actualizar equipo
     */
    public function update($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipo = $this->teamModel->find($id);

            if (!$equipo) {
                echo json_encode(['success' => false, 'error' => 'Equipo no encontrado']);
                exit;
            }

            $errors = $this->validateEquipoData($_POST, $id);

            if (empty($errors)) {
                try {
                    $data = [
                        'league_id' => $_POST['league_id'],
                        'name' => $_POST['name'],
                        'logo_path' => $_POST['logo_path'] ?? null,
                        'is_top_team' => isset($_POST['is_top_team']) ? 1 : 0,
                        'display_order' => $_POST['display_order'] ?? 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0
                    ];

                    // Update slug only if name changed
                    if ($_POST['name'] !== $equipo['name']) {
                        $data['slug'] = $this->createSlug($_POST['name']);
                    }

                    $this->teamModel->update($id, $data);

                    echo json_encode(['success' => true]);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }
        }
    }

    /**
     * Eliminar equipo (soft delete)
     */
    public function delete($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Check if equipo has products
                $productCount = $this->teamModel->fetchOne(
                    "SELECT COUNT(*) as count FROM products WHERE team_id = ?",
                    [$id]
                )['count'];

                if ($productCount > 0) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'No se puede eliminar un equipo que tiene productos asociados'
                    ]);
                    exit;
                }

                // Soft delete
                $this->teamModel->update($id, ['is_active' => 0]);

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Validate equipo data
     */
    private function validateEquipoData($data, $excludeId = null) {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'El nombre del equipo es obligatorio';
        }

        if (empty($data['league_id'])) {
            $errors[] = 'La liga es obligatoria';
        } else {
            // Check if league exists
            $league = $this->leagueModel->find($data['league_id']);
            if (!$league) {
                $errors[] = 'La liga seleccionada no existe';
            }
        }

        // Check if slug already exists
        if (!empty($data['name'])) {
            $slug = $this->createSlug($data['name']);
            $existing = $this->teamModel->getBySlug($slug);
            if ($existing && (!$excludeId || $existing['team_id'] != $excludeId)) {
                $errors[] = 'Ya existe un equipo con este nombre';
            }
        }

        return $errors;
    }

    /**
     * Create slug from name
     */
    private function createSlug($name) {
        $slug = strtolower($name);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
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
