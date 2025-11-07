<?php
/**
 * Admin Equipos API Controller
 * API endpoints para gestión de equipos en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Team.php';
require_once __DIR__ . '/../../models/League.php';

class AdminEquiposApiController extends Controller {
    private $teamModel;
    private $leagueModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->teamModel = new Team();
        $this->leagueModel = new League();
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
     * Get all teams with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = [];
            $params = [];

            if (isset($_GET['league_id']) && $_GET['league_id'] !== '') {
                $where[] = "t.league_id = ?";
                $params[] = $_GET['league_id'];
            }

            if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
                $where[] = "t.is_active = ?";
                $params[] = $_GET['is_active'];
            }

            if (isset($_GET['is_top_team']) && $_GET['is_top_team'] !== '') {
                $where[] = "t.is_top_team = ?";
                $params[] = $_GET['is_top_team'];
            }

            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(t.name LIKE ? OR l.name LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM teams t
                        LEFT JOIN leagues l ON t.league_id = l.league_id
                        {$whereClause}";

            $countResult = $this->teamModel->fetchAll($countSql, $params);
            $total = $countResult[0]['total'] ?? 0;

            // Get teams with league info and product count
            $sql = "SELECT
                        t.team_id,
                        t.league_id,
                        t.name,
                        t.slug,
                        t.logo_path,
                        t.is_top_team,
                        t.display_order,
                        t.is_active,
                        t.created_at,
                        l.name as league_name,
                        l.slug as league_slug,
                        (SELECT COUNT(*) FROM products WHERE team_id = t.team_id) as products_count
                    FROM teams t
                    LEFT JOIN leagues l ON t.league_id = l.league_id
                    {$whereClause}
                    ORDER BY t.display_order ASC, t.name ASC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $teams = $this->teamModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'teams' => $teams,
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
            error_log("Error getting teams: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar equipos'
            ], 500);
        }
    }

    /**
     * Get single team details
     */
    public function getOne($teamId) {
        try {
            // Get team with league info
            $sql = "SELECT
                        t.*,
                        l.name as league_name,
                        l.slug as league_slug,
                        l.country as league_country,
                        (SELECT COUNT(*) FROM products WHERE team_id = t.team_id) as products_count
                    FROM teams t
                    LEFT JOIN leagues l ON t.league_id = l.league_id
                    WHERE t.team_id = ?";

            $team = $this->teamModel->fetchOne($sql, [$teamId]);

            if (!$team) {
                $this->json([
                    'success' => false,
                    'message' => 'Equipo no encontrado'
                ], 404);
                return;
            }

            // Get associated products
            $productsSql = "SELECT
                                p.product_id,
                                p.name,
                                p.slug,
                                p.base_price,
                                p.stock_quantity,
                                p.is_active,
                                p.jersey_type,
                                p.season,
                                (SELECT image_path FROM product_images WHERE product_id = p.product_id AND image_type = 'main' LIMIT 1) as main_image
                            FROM products p
                            WHERE p.team_id = ?
                            ORDER BY p.created_at DESC";

            $team['products'] = $this->teamModel->fetchAll($productsSql, [$teamId]);

            $this->json([
                'success' => true,
                'team' => $team
            ]);

        } catch (Exception $e) {
            error_log("Error getting team: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar equipo'
            ], 500);
        }
    }
}
