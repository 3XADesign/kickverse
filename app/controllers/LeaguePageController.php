<?php
/**
 * League Page Controller
 * Handles league pages
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/League.php';
require_once __DIR__ . '/../models/Product.php';

class LeaguePageController extends Controller {
    private $leagueModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->leagueModel = new League();
        $this->productModel = new Product();
    }

    /**
     * Leagues listing page
     */
    public function index() {
        try {
            // Get all active leagues with their teams
            $leagues = $this->leagueModel->getAllActive();

            // Get teams for each league
            foreach ($leagues as &$league) {
                $sql = "SELECT team_id, name, slug, logo_path, is_top_team
                        FROM teams
                        WHERE league_id = ? AND is_active = 1
                        ORDER BY display_order ASC";
                $league['teams'] = $this->leagueModel->fetchAll($sql, [$league['league_id']]);

                // Count products for this league
                $sql = "SELECT COUNT(*) as count FROM products WHERE league_id = ? AND is_active = 1";
                $count = $this->leagueModel->fetchOne($sql, [$league['league_id']]);
                $league['product_count'] = $count['count'] ?? 0;
            }

            $this->view('leagues/index', [
                'leagues' => $leagues,
                'page_title' => 'Todas las Ligas'
            ]);
        } catch (Exception $e) {
            die('Error loading leagues: ' . $e->getMessage());
        }
    }

    /**
     * League page with teams and products
     */
    public function show($slug) {
        try {
            $league = $this->leagueModel->getWithTeams($slug);

            if (!$league) {
                http_response_code(404);
                $this->view('errors/404');
                return;
            }

            // Get products from this league
            $products = $this->productModel->getByLeague($slug);

            $this->view('leagues/show', [
                'league' => $league,
                'products' => $products,
                'csrf_token' => $this->generateCSRF()
            ]);
        } catch (Exception $e) {
            die('Error loading league: ' . $e->getMessage());
        }
    }
}
