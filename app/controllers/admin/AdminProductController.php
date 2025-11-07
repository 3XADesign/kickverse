<?php
/**
 * Admin Product Controller
 * Manage products in admin panel
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/League.php';

class AdminProductController extends Controller {
    private $productModel;
    private $leagueModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdminAuth();
        $this->productModel = new Product();
        $this->leagueModel = new League();
    }

    private function requireAdminAuth() {
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * List all products
     */
    public function index() {
        $page = (int) ($this->get('page') ?? 1);
        $perPage = 50;
        $type = $this->get('type');
        $league = $this->get('league');

        try {
            $sql = "SELECT p.*, l.name as league_name, t.name as team_name
                    FROM products p
                    LEFT JOIN leagues l ON p.league_id = l.league_id
                    LEFT JOIN teams t ON p.team_id = t.team_id
                    WHERE 1=1";

            $params = [];

            if ($type) {
                $sql .= " AND p.product_type = ?";
                $params[] = $type;
            }

            if ($league) {
                $sql .= " AND p.league_id = ?";
                $params[] = $league;
            }

            $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = ($page - 1) * $perPage;

            $products = $this->productModel->fetchAll($sql, $params);

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
            $countParams = [];

            if ($type) {
                $countSql .= " AND p.product_type = ?";
                $countParams[] = $type;
            }

            if ($league) {
                $countSql .= " AND p.league_id = ?";
                $countParams[] = $league;
            }

            $total = $this->productModel->fetchOne($countSql, $countParams)['total'];

            // Get leagues for filter
            $leagues = $this->leagueModel->getAllActive();

            $this->view('admin/products/index', [
                'products' => $products,
                'leagues' => $leagues,
                'page' => $page,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'type_filter' => $type,
                'league_filter' => $league
            ]);
        } catch (Exception $e) {
            die('Error loading products: ' . $e->getMessage());
        }
    }

    /**
     * Show create product form
     */
    public function create() {
        try {
            // Get leagues with teams
            $leagues = $this->leagueModel->getAllActive();
            foreach ($leagues as &$league) {
                $sql = "SELECT * FROM teams WHERE league_id = ? AND is_active = 1 ORDER BY name";
                $league['teams'] = $this->productModel->fetchAll($sql, [$league['league_id']]);
            }

            $this->view('admin/products/create', [
                'leagues' => $leagues
            ]);
        } catch (Exception $e) {
            die('Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store new product
     */
    public function store() {
        $data = $this->input();

        // Validate
        $errors = $this->validate($data, [
            'name' => 'required',
            'product_type' => 'required',
            'base_price' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            // Create slug
            $slug = $this->createSlug($data['name']);

            // Prepare data
            $productData = [
                'product_type' => $data['product_type'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'original_price' => $data['original_price'] ?? null,
                'league_id' => $data['league_id'] ?? null,
                'team_id' => $data['team_id'] ?? null,
                'jersey_type' => $data['jersey_type'] ?? null,
                'season' => $data['season'] ?? null,
                'version' => $data['version'] ?? null,
                'is_featured' => isset($data['is_featured']) ? 1 : 0,
                'is_active' => isset($data['is_active']) ? 1 : 0
            ];

            $productId = $this->productModel->create($productData);

            // Create variants (sizes)
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    if (!empty($variant['size'])) {
                        $variantData = [
                            'product_id' => $productId,
                            'size' => $variant['size'],
                            'stock_quantity' => $variant['stock_quantity'] ?? 0,
                            'sku' => $slug . '-' . $variant['size'],
                            'is_active' => 1
                        ];

                        $fields = implode(',', array_keys($variantData));
                        $placeholders = str_repeat('?,', count($variantData) - 1) . '?';
                        $sql = "INSERT INTO product_variants ({$fields}) VALUES ({$placeholders})";
                        $this->productModel->query($sql, array_values($variantData));
                    }
                }
            }

            $this->json([
                'success' => true,
                'message' => 'Producto creado',
                'product_id' => $productId
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit product form
     */
    public function edit($productId) {
        try {
            $product = $this->productModel->getFullDetails($productId);

            if (!$product) {
                $this->setFlash('error', 'Producto no encontrado');
                $this->redirect('/admin/products');
            }

            // Get leagues with teams
            $leagues = $this->leagueModel->getAllActive();
            foreach ($leagues as &$league) {
                $sql = "SELECT * FROM teams WHERE league_id = ? AND is_active = 1 ORDER BY name";
                $league['teams'] = $this->productModel->fetchAll($sql, [$league['league_id']]);
            }

            $this->view('admin/products/edit', [
                'product' => $product,
                'leagues' => $leagues
            ]);
        } catch (Exception $e) {
            die('Error loading product: ' . $e->getMessage());
        }
    }

    /**
     * Update product
     */
    public function update($productId) {
        $data = $this->input();

        try {
            $product = $this->productModel->find($productId);

            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Update product
            $updateData = [];
            $allowedFields = ['name', 'description', 'base_price', 'original_price',
                            'league_id', 'team_id', 'jersey_type', 'season', 'version',
                            'is_featured', 'is_active'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if (!empty($updateData)) {
                $this->productModel->update($productId, $updateData);
            }

            $this->json([
                'success' => true,
                'message' => 'Producto actualizado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el producto'
            ], 500);
        }
    }

    /**
     * Delete product
     */
    public function delete($productId) {
        try {
            $product = $this->productModel->find($productId);

            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Soft delete (set is_active = 0)
            $this->productModel->update($productId, ['is_active' => 0]);

            $this->json([
                'success' => true,
                'message' => 'Producto eliminado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al eliminar el producto'
            ], 500);
        }
    }

    /**
     * Create slug from name
     */
    private function createSlug($name) {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check if slug exists
        $existing = $this->productModel->getBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        return $slug;
    }
}
