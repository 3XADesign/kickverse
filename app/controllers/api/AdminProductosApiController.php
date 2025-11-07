<?php
/**
 * Admin Productos API Controller
 * API endpoints para gestión de productos en el admin
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Product.php';

class AdminProductosApiController extends Controller {
    private $productModel;
    private $perPage = 50;

    public function __construct() {
        parent::__construct();
        $this->checkAdminAuth();
        $this->productModel = new Product();
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
     * Get all products with filters
     */
    public function getAll() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->perPage;

            // Build WHERE clause
            $where = [];
            $params = [];

            if (isset($_GET['product_type']) && $_GET['product_type'] !== '') {
                $where[] = "p.product_type = ?";
                $params[] = $_GET['product_type'];
            }

            if (isset($_GET['league_id']) && $_GET['league_id'] !== '') {
                $where[] = "p.league_id = ?";
                $params[] = $_GET['league_id'];
            }

            if (isset($_GET['team_id']) && $_GET['team_id'] !== '') {
                $where[] = "p.team_id = ?";
                $params[] = $_GET['team_id'];
            }

            if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
                $where[] = "p.is_active = ?";
                $params[] = $_GET['is_active'];
            }

            if (isset($_GET['search']) && trim($_GET['search']) !== '') {
                $search = trim($_GET['search']);
                $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.product_id LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Count total
            $countSql = "SELECT COUNT(*) as total
                        FROM products p
                        {$whereClause}";

            $countResult = $this->productModel->fetchAll($countSql, $params);
            $total = $countResult[0]['total'] ?? 0;

            // Get products with JOIN to leagues, teams, product_images
            $sql = "SELECT
                        p.*,
                        l.name as league_name,
                        l.logo_path as league_logo,
                        t.name as team_name,
                        t.logo_path as team_logo,
                        pi.image_path as main_image,
                        COUNT(DISTINCT pv.variant_id) as variants_count,
                        COALESCE(SUM(pv.stock_quantity), 0) as total_stock
                    FROM products p
                    LEFT JOIN leagues l ON p.league_id = l.league_id
                    LEFT JOIN teams t ON p.team_id = t.team_id
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.image_type = 'main'
                    LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                    {$whereClause}
                    GROUP BY p.product_id
                    ORDER BY p.created_at DESC
                    LIMIT ? OFFSET ?";

            $params[] = $this->perPage;
            $params[] = $offset;

            $products = $this->productModel->fetchAll($sql, $params);

            // Calculate pagination
            $totalPages = ceil($total / $this->perPage);

            $this->json([
                'success' => true,
                'products' => $products,
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
            error_log("Error getting products: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar productos'
            ], 500);
        }
    }

    /**
     * Get single product details with all related data
     */
    public function getOne($productId) {
        try {
            // Get base product with league and team info
            $sql = "SELECT
                        p.*,
                        l.name as league_name,
                        l.logo_path as league_logo,
                        l.slug as league_slug,
                        t.name as team_name,
                        t.logo_path as team_logo,
                        t.slug as team_slug
                    FROM products p
                    LEFT JOIN leagues l ON p.league_id = l.league_id
                    LEFT JOIN teams t ON p.team_id = t.team_id
                    WHERE p.product_id = ?";

            $product = $this->productModel->fetchOne($sql, [$productId]);

            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
                return;
            }

            // Get all product images (main + gallery)
            $imagesSql = "SELECT *
                         FROM product_images
                         WHERE product_id = ?
                         ORDER BY
                            CASE image_type
                                WHEN 'main' THEN 1
                                WHEN 'hover' THEN 2
                                WHEN 'detail' THEN 3
                                WHEN 'gallery' THEN 4
                            END,
                            display_order ASC";
            $product['images'] = $this->productModel->fetchAll($imagesSql, [$productId]);

            // Get all product variants with stock
            $variantsSql = "SELECT *
                           FROM product_variants
                           WHERE product_id = ?
                           ORDER BY
                               CASE size
                                   WHEN 'S' THEN 1
                                   WHEN 'M' THEN 2
                                   WHEN 'L' THEN 3
                                   WHEN 'XL' THEN 4
                                   WHEN '2XL' THEN 5
                                   WHEN '3XL' THEN 6
                                   WHEN '4XL' THEN 7
                                   ELSE 8
                               END";
            $variants = $this->productModel->fetchAll($variantsSql, [$productId]);
            $product['variants'] = $variants;

            // Calculate total stock from all variants
            $totalStock = 0;
            $lowStockVariants = 0;
            foreach ($variants as $variant) {
                $totalStock += $variant['stock_quantity'];
                if ($variant['stock_quantity'] <= $variant['low_stock_threshold']) {
                    $lowStockVariants++;
                }
            }
            $product['total_stock'] = $totalStock;
            $product['low_stock_variants'] = $lowStockVariants;

            // Get price history
            $priceHistorySql = "SELECT *
                               FROM product_price_history
                               WHERE product_id = ?
                               ORDER BY changed_at DESC
                               LIMIT 10";
            $product['price_history'] = $this->productModel->fetchAll($priceHistorySql, [$productId]);

            // Get product stats
            $statsSql = "SELECT
                            COUNT(DISTINCT oi.order_id) as total_orders,
                            COALESCE(SUM(oi.quantity), 0) as total_units_sold,
                            COALESCE(SUM(oi.subtotal), 0) as total_revenue
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.order_id
                        WHERE oi.product_id = ?
                        AND o.order_status NOT IN ('cancelled', 'refunded')";
            $stats = $this->productModel->fetchOne($statsSql, [$productId]);
            $product['stats'] = $stats;

            $this->json([
                'success' => true,
                'product' => $product
            ]);

        } catch (Exception $e) {
            error_log("Error getting product: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Error al cargar producto'
            ], 500);
        }
    }
}
