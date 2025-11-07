<?php
/**
 * Ligas Controller - Admin CRM
 * Gestión de ligas desde el panel de administración
 */

require_once __DIR__ . '/../../models/League.php';
require_once __DIR__ . '/../../models/Team.php';

class LigasController {
    private $leagueModel;
    private $teamModel;

    public function __construct() {
        $this->leagueModel = new League();
        $this->teamModel = new Team();
    }

    /**
     * Lista de todas las ligas
     */
    public function index() {
        // Check admin session
        $this->checkAdminAuth();

        // Filters
        $filters = [];
        if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
            $filters['is_active'] = $_GET['is_active'];
        }
        if (isset($_GET['country']) && $_GET['country'] !== '') {
            $filters['country'] = $_GET['country'];
        }

        // Get ligas with team count
        $sql = "SELECT l.*,
                (SELECT COUNT(*) FROM teams WHERE league_id = l.league_id) as team_count
                FROM leagues l
                WHERE 1=1";

        $params = [];

        if (isset($filters['is_active'])) {
            $sql .= " AND l.is_active = ?";
            $params[] = $filters['is_active'];
        }

        if (isset($filters['country'])) {
            $sql .= " AND l.country = ?";
            $params[] = $filters['country'];
        }

        $sql .= " ORDER BY l.display_order ASC, l.name ASC";

        $ligas = $this->leagueModel->fetchAll($sql, $params);

        // Get all countries for filter
        $countriesSql = "SELECT DISTINCT country FROM leagues WHERE country IS NOT NULL ORDER BY country";
        $countries = $this->leagueModel->fetchAll($countriesSql);

        // Render view
        $content = $this->renderView('admin/ligas/index', [
            'ligas' => $ligas,
            'countries' => $countries,
            'filters' => $filters
        ]);

        $this->renderLayout('admin', [
            'content' => $content,
            'page_title' => 'Gestión de Ligas',
            'current_page' => 'ligas',
            'admin_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }

    /**
     * Ver detalles de una liga (API JSON)
     */
    public function show($id) {
        $this->checkAdminAuth();

        $sql = "SELECT l.*,
                (SELECT COUNT(*) FROM teams WHERE league_id = l.league_id) as team_count,
                (SELECT COUNT(*) FROM products WHERE league_id = l.league_id) as product_count
                FROM leagues l
                WHERE l.league_id = ?";

        $liga = $this->leagueModel->fetchOne($sql, [$id]);

        if (!$liga) {
            http_response_code(404);
            echo json_encode(['error' => 'Liga no encontrada']);
            return;
        }

        // Get teams
        $teamsSql = "SELECT t.*,
                     (SELECT COUNT(*) FROM products WHERE team_id = t.team_id) as product_count
                     FROM teams t
                     WHERE t.league_id = ?
                     ORDER BY t.display_order ASC, t.name ASC";
        $liga['teams'] = $this->teamModel->fetchAll($teamsSql, [$id]);

        header('Content-Type: application/json');
        echo json_encode($liga);
    }

    /**
     * Crear nueva liga
     */
    public function create() {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateLigaData($_POST);

            if (empty($errors)) {
                try {
                    $slug = $this->createSlug($_POST['name']);

                    $data = [
                        'name' => $_POST['name'],
                        'slug' => $slug,
                        'country' => $_POST['country'] ?? null,
                        'logo_path' => $_POST['logo_path'] ?? null,
                        'display_order' => $_POST['display_order'] ?? 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0
                    ];

                    $ligaId = $this->leagueModel->create($data);

                    echo json_encode(['success' => true, 'league_id' => $ligaId]);
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
     * Actualizar liga
     */
    public function update($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $liga = $this->leagueModel->find($id);

            if (!$liga) {
                echo json_encode(['success' => false, 'error' => 'Liga no encontrada']);
                exit;
            }

            $errors = $this->validateLigaData($_POST, $id);

            if (empty($errors)) {
                try {
                    $data = [
                        'name' => $_POST['name'],
                        'country' => $_POST['country'] ?? null,
                        'logo_path' => $_POST['logo_path'] ?? null,
                        'display_order' => $_POST['display_order'] ?? 0,
                        'is_active' => isset($_POST['is_active']) ? 1 : 0
                    ];

                    // Update slug only if name changed
                    if ($_POST['name'] !== $liga['name']) {
                        $data['slug'] = $this->createSlug($_POST['name']);
                    }

                    $this->leagueModel->update($id, $data);

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
     * Eliminar liga (soft delete)
     */
    public function delete($id) {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Check if liga has teams
                $teamCount = $this->teamModel->fetchOne(
                    "SELECT COUNT(*) as count FROM teams WHERE league_id = ?",
                    [$id]
                )['count'];

                if ($teamCount > 0) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'No se puede eliminar una liga que tiene equipos asociados'
                    ]);
                    exit;
                }

                // Soft delete
                $this->leagueModel->update($id, ['is_active' => 0]);

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Validate liga data
     */
    private function validateLigaData($data, $excludeId = null) {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'El nombre de la liga es obligatorio';
        }

        // Check if slug already exists
        if (!empty($data['name'])) {
            $slug = $this->createSlug($data['name']);
            $existing = $this->leagueModel->getBySlug($slug);
            if ($existing && (!$excludeId || $existing['league_id'] != $excludeId)) {
                $errors[] = 'Ya existe una liga con este nombre';
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
