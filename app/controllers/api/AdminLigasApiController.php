<?php
/**
 * Admin Ligas API Controller
 * API endpoints para gestión de ligas en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/League.php';
require_once __DIR__ . '/../../models/Team.php';

class AdminLigasApiController extends Controller {
    private $leagueModel;
    private $teamModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->leagueModel = new League();
        $this->teamModel = new Team();
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
     * Get all leagues with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = [];
            $params = [];

            if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
                $where[] = "l.is_active = ?";
                $params[] = $_GET['is_active'];
            }

            if (isset($_GET['country']) && $_GET['country'] !== '') {
                $where[] = "l.country = ?";
                $params[] = $_GET['country'];
            }

            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(l.name LIKE ? OR l.country LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM leagues l
                        {$whereClause}";

            $countResult = $this->leagueModel->fetchAll($countSql, $params);
            $total = $countResult[0]['total'] ?? 0;

            // Get leagues with team count
            $sql = "SELECT
                        l.league_id,
                        l.name,
                        l.slug,
                        l.country,
                        l.logo_path,
                        l.display_order,
                        l.is_active,
                        l.created_at,
                        (SELECT COUNT(*) FROM teams WHERE league_id = l.league_id) as teams_count
                    FROM leagues l
                    {$whereClause}
                    ORDER BY l.display_order ASC, l.name ASC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $leagues = $this->leagueModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'leagues' => $leagues,
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
            error_log("Error getting leagues: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar ligas'
            ], 500);
        }
    }

    /**
     * Get single league details
     */
    public function getOne($leagueId) {
        try {
            // Get league with counts
            $sql = "SELECT
                        l.*,
                        (SELECT COUNT(*) FROM teams WHERE league_id = l.league_id) as teams_count,
                        (SELECT COUNT(*) FROM products WHERE league_id = l.league_id) as products_count
                    FROM leagues l
                    WHERE l.league_id = ?";

            $league = $this->leagueModel->fetchOne($sql, [$leagueId]);

            if (!$league) {
                $this->json([
                    'success' => false,
                    'message' => 'Liga no encontrada'
                ], 404);
                return;
            }

            // Get associated teams
            $teamsSql = "SELECT
                            t.team_id,
                            t.name,
                            t.slug,
                            t.logo_path,
                            t.is_top_team,
                            t.display_order,
                            t.is_active,
                            (SELECT COUNT(*) FROM products WHERE team_id = t.team_id) as products_count
                        FROM teams t
                        WHERE t.league_id = ?
                        ORDER BY t.display_order ASC, t.name ASC";

            $league['teams'] = $this->teamModel->fetchAll($teamsSql, [$leagueId]);

            $this->json([
                'success' => true,
                'league' => $league
            ]);

        } catch (Exception $e) {
            error_log("Error getting league: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar liga'
            ], 500);
        }
    }
}
