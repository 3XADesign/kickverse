<?php
/**
 * Product API Controller
 * Handles product listing, details, and filtering
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/League.php';

class ProductController extends Controller {
    private $productModel;
    private $leagueModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->leagueModel = new League();
    }

    /**
     * GET /api/products
     * List all products with optional filters
     */
    public function index() {
        $leagueSlug = $this->get('league');
        $teamSlug = $this->get('team');
        $featured = $this->get('featured');
        $type = $this->get('type');

        try {
            if ($featured) {
                $products = $this->productModel->getFeatured();
            } elseif ($leagueSlug) {
                $products = $this->productModel->getByLeague($leagueSlug);
            } elseif ($teamSlug) {
                $products = $this->productModel->getByTeam($teamSlug);
            } elseif ($type) {
                $products = $this->productModel->getByType($type);
            } else {
                $products = $this->productModel->getActive();
            }

            $this->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar productos'
            ], 500);
        }
    }

    /**
     * GET /api/products/:id
     * Get product details with variants
     */
    public function show($productId) {
        try {
            $product = $this->productModel->getFullDetails($productId);

            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $this->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el producto'
            ], 500);
        }
    }

    /**
     * GET /api/products/slug/:slug
     * Get product by slug
     */
    public function getBySlug($slug) {
        try {
            $product = $this->productModel->getBySlug($slug);

            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $this->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el producto'
            ], 500);
        }
    }

    /**
     * GET /api/leagues
     * Get all leagues with teams
     */
    public function getLeagues() {
        try {
            $leagues = $this->leagueModel->getAllActive();

            // Get teams for each league
            foreach ($leagues as &$league) {
                $sql = "SELECT team_id, name, slug, logo_path, is_top_team
                        FROM teams
                        WHERE league_id = ? AND is_active = 1
                        ORDER BY display_order ASC";
                $league['teams'] = $this->productModel->fetchAll($sql, [$league['league_id']]);
            }

            $this->json([
                'success' => true,
                'data' => $leagues
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar las ligas'
            ], 500);
        }
    }

    /**
     * GET /api/products/search
     * Search products
     */
    public function search() {
        $query = $this->get('q', '');

        if (strlen($query) < 2) {
            $this->json([
                'success' => false,
                'message' => 'La búsqueda debe tener al menos 2 caracteres'
            ], 400);
        }

        try {
            $products = $this->productModel->search($query);

            $this->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    /**
     * GET /api/products/:id/variant
     * Get variant ID by product and size
     */
    public function getVariant($productId) {
        $size = $this->get('size');

        if (!$size) {
            $this->json([
                'success' => false,
                'message' => 'Talla requerida'
            ], 400);
            return;
        }

        try {
            $variant = $this->productModel->getVariant($productId, $size);

            if (!$variant) {
                $this->json([
                    'success' => false,
                    'message' => 'Talla no disponible'
                ], 404);
                return;
            }

            $this->json([
                'success' => true,
                'data' => $variant
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al obtener la variante'
            ], 500);
        }
    }
}
