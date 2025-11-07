<?php
/**
 * Product Page Controller
 * Handles product listing and detail pages
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/League.php';

class ProductPageController extends Controller {
    private $productModel;
    private $leagueModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->leagueModel = new League();
    }

    /**
     * Product listing page
     */
    public function index() {
        $leagueSlug = $this->get('league');
        $teamSlug = $this->get('team');
        $priceRange = $this->get('price_range');
        $page = max(1, intval($this->get('page') ?? 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        try {
            // Build WHERE conditions and params
            $whereConditions = ['p.is_active = 1'];
            $params = [];

            if ($leagueSlug) {
                $whereConditions[] = 'l.slug = ?';
                $params[] = $leagueSlug;
                $pageTitle = 'Productos de ' . ucfirst($leagueSlug);
            } elseif ($teamSlug) {
                $whereConditions[] = 't.slug = ?';
                $params[] = $teamSlug;
                $pageTitle = 'Productos de ' . ucfirst($teamSlug);
            } else {
                $pageTitle = 'Todos los Productos';
            }

            // Add price range filter
            if ($priceRange) {
                if ($priceRange === '100+') {
                    $whereConditions[] = 'p.base_price >= 100';
                } else {
                    list($min, $max) = explode('-', $priceRange);
                    $whereConditions[] = 'p.base_price >= ? AND p.base_price <= ?';
                    $params[] = floatval($min);
                    $params[] = floatval($max);
                }
            }

            $whereClause = implode(' AND ', $whereConditions);

            // Get products based on filters with pagination
            if ($leagueSlug) {
                $sql = "SELECT p.*, t.name as team_name, l.name as league_name
                        FROM products p
                        LEFT JOIN teams t ON p.team_id = t.team_id
                        JOIN leagues l ON p.league_id = l.league_id
                        WHERE $whereClause
                        ORDER BY p.created_at DESC
                        LIMIT ? OFFSET ?";
            } elseif ($teamSlug) {
                $sql = "SELECT p.*, t.name as team_name, l.name as league_name
                        FROM products p
                        JOIN teams t ON p.team_id = t.team_id
                        LEFT JOIN leagues l ON p.league_id = l.league_id
                        WHERE $whereClause
                        ORDER BY p.created_at DESC
                        LIMIT ? OFFSET ?";
            } else {
                $sql = "SELECT p.*, t.name as team_name, l.name as league_name
                        FROM products p
                        LEFT JOIN teams t ON p.team_id = t.team_id
                        LEFT JOIN leagues l ON p.league_id = l.league_id
                        WHERE $whereClause
                        ORDER BY p.created_at DESC
                        LIMIT ? OFFSET ?";
            }

            $params[] = $perPage;
            $params[] = $offset;
            $products = $this->productModel->fetchAll($sql, $params);

            // Get main image for each product
            foreach ($products as &$product) {
                $sql = "SELECT image_path FROM product_images
                        WHERE product_id = ?
                        ORDER BY display_order ASC
                        LIMIT 1";
                $image = $this->productModel->fetchOne($sql, [$product['product_id']]);
                $product['main_image'] = $image['image_path'] ?? '/img/placeholder.png';
            }

            // Get all leagues for filter
            $leagues = $this->leagueModel->getAllActive();

            // Get total count of products with same filters
            $countParams = [];
            foreach ($params as $param) {
                if ($param !== $perPage && $param !== $offset) {
                    $countParams[] = $param;
                }
            }

            if ($leagueSlug) {
                $countSql = "SELECT COUNT(*) as total FROM products p
                            JOIN leagues l ON p.league_id = l.league_id
                            WHERE $whereClause";
            } elseif ($teamSlug) {
                $countSql = "SELECT COUNT(*) as total FROM products p
                            JOIN teams t ON p.team_id = t.team_id
                            WHERE $whereClause";
            } else {
                $countSql = "SELECT COUNT(*) as total FROM products p
                            LEFT JOIN teams t ON p.team_id = t.team_id
                            LEFT JOIN leagues l ON p.league_id = l.league_id
                            WHERE $whereClause";
            }
            $countResult = $this->productModel->fetchOne($countSql, $countParams);
            $totalProducts = $countResult['total'] ?? 0;
            $totalPages = ceil($totalProducts / $perPage);

            $this->view('products/index', [
                'products' => $products,
                'leagues' => $leagues,
                'page_title' => $pageTitle,
                'total_products' => $totalProducts,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'csrf_token' => $this->generateCSRF()
            ]);
        } catch (Exception $e) {
            die('Error loading products: ' . $e->getMessage());
        }
    }

    /**
     * Product detail page
     */
    public function show($slug) {
        try {
            $product = $this->productModel->getBySlug($slug);

            if (!$product || !$product['is_active']) {
                http_response_code(404);
                $this->view('errors/404');
                return;
            }

            // Get full product details with images and variants
            $product = $this->productModel->getFullDetails($product['product_id']);

            // Get related products (same team or league)
            if ($product['team_id']) {
                $sql = "SELECT p.*, t.name as team_name,
                        (SELECT image_path FROM product_images WHERE product_id = p.product_id AND image_type = 'main' LIMIT 1) as image_path
                        FROM products p
                        LEFT JOIN teams t ON p.team_id = t.team_id
                        WHERE p.team_id = ? AND p.product_id != ? AND p.is_active = 1
                        LIMIT 4";
                $relatedProducts = $this->productModel->fetchAll($sql, [$product['team_id'], $product['product_id']]);
            } elseif ($product['league_id']) {
                $sql = "SELECT p.*, t.name as team_name,
                        (SELECT image_path FROM product_images WHERE product_id = p.product_id AND image_type = 'main' LIMIT 1) as image_path
                        FROM products p
                        LEFT JOIN teams t ON p.team_id = t.team_id
                        WHERE p.league_id = ? AND p.product_id != ? AND p.is_active = 1
                        LIMIT 4";
                $relatedProducts = $this->productModel->fetchAll($sql, [$product['league_id'], $product['product_id']]);
            } else {
                $relatedProducts = [];
            }

            $this->view('products/show', [
                'product' => $product,
                'related_products' => $relatedProducts,
                'csrf_token' => $this->generateCSRF(),
                'page_title' => $product['name']
            ]);
        } catch (Exception $e) {
            die('Error loading product: ' . $e->getMessage());
        }
    }
}
