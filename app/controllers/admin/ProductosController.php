<?php

// Load required models
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/League.php';
require_once __DIR__ . '/../../models/Team.php';
require_once __DIR__ . '/../../models/ProductImage.php';
require_once __DIR__ . '/../../models/ProductVariant.php';

class ProductosController
{
    private $productModel;
    private $leagueModel;
    private $teamModel;
    private $productImageModel;
    private $productVariantModel;

    public function __construct()
    {
        // Initialize models
        $this->productModel = new Product();
        $this->leagueModel = new League();
        $this->teamModel = new Team();
        $this->productImageModel = new ProductImage();
        $this->productVariantModel = new ProductVariant();
    }

    /**
     * Display products listing with pagination and filters
     */
    public function index()
    {
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filters
        $filters = [
            'type' => $_GET['type'] ?? null,
            'league_id' => $_GET['league_id'] ?? null,
            'team_id' => $_GET['team_id'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        // Get productos with relationships
        $productos = $this->getProductosWithDetails($filters, $perPage, $offset);

        // Get total count for pagination
        $totalProductos = $this->countProductosWithFilters($filters);
        $totalPages = ceil($totalProductos / $perPage);

        // Get leagues for filter dropdown
        $leagues = $this->leagueModel->getAll();

        // Prepare view data
        $data = [
            'productos' => $productos,
            'leagues' => $leagues,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_productos' => $totalProductos
        ];

        // Load view
        $this->loadView('admin/productos/index', $data);
    }

    /**
     * Get single product details (for modal)
     */
    public function show($id)
    {
        $producto = $this->getProductoDetails($id);

        if (!$producto) {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }

        // Return JSON for AJAX requests
        header('Content-Type: application/json');
        echo json_encode($producto);
    }

    /**
     * Get productos with all related data
     */
    private function getProductosWithDetails($filters, $limit, $offset)
    {
        $sql = "
            SELECT
                p.*,
                l.name as league_name,
                l.logo_path as league_logo,
                t.name as team_name,
                t.logo_path as team_logo,
                (SELECT image_path FROM product_images
                 WHERE product_id = p.product_id AND image_type = 'main'
                 ORDER BY display_order ASC LIMIT 1) as main_image,
                (SELECT COUNT(*) FROM product_variants
                 WHERE product_id = p.product_id AND is_active = 1) as total_variants
            FROM products p
            LEFT JOIN leagues l ON p.league_id = l.league_id
            LEFT JOIN teams t ON p.team_id = t.team_id
            WHERE 1=1
        ";

        $params = [];

        // Apply filters
        if ($filters['type']) {
            $sql .= " AND p.product_type = ?";
            $params[] = $filters['type'];
        }

        if ($filters['league_id']) {
            $sql .= " AND p.league_id = ?";
            $params[] = $filters['league_id'];
        }

        if ($filters['team_id']) {
            $sql .= " AND p.team_id = ?";
            $params[] = $filters['team_id'];
        }

        if ($filters['status'] === 'active') {
            $sql .= " AND p.is_active = 1";
        } elseif ($filters['status'] === 'inactive') {
            $sql .= " AND p.is_active = 0";
        }

        if ($filters['search']) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR t.name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY p.product_id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->productModel->fetchAll($sql, $params);
    }

    /**
     * Count products with filters
     */
    private function countProductosWithFilters($filters)
    {
        $sql = "
            SELECT COUNT(*) as total
            FROM products p
            LEFT JOIN teams t ON p.team_id = t.team_id
            WHERE 1=1
        ";

        $params = [];

        // Apply same filters as getProductosWithDetails
        if ($filters['type']) {
            $sql .= " AND p.product_type = ?";
            $params[] = $filters['type'];
        }

        if ($filters['league_id']) {
            $sql .= " AND p.league_id = ?";
            $params[] = $filters['league_id'];
        }

        if ($filters['team_id']) {
            $sql .= " AND p.team_id = ?";
            $params[] = $filters['team_id'];
        }

        if ($filters['status'] === 'active') {
            $sql .= " AND p.is_active = 1";
        } elseif ($filters['status'] === 'inactive') {
            $sql .= " AND p.is_active = 0";
        }

        if ($filters['search']) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR t.name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $result = $this->productModel->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Get complete product details for modal
     */
    private function getProductoDetails($id)
    {
        // Get base product data with relationships
        $sql = "
            SELECT
                p.*,
                l.name as league_name,
                l.logo_path as league_logo,
                t.name as team_name,
                t.logo_path as team_logo
            FROM products p
            LEFT JOIN leagues l ON p.league_id = l.league_id
            LEFT JOIN teams t ON p.team_id = t.team_id
            WHERE p.product_id = ?
        ";

        $producto = $this->productModel->queryOne($sql, [$id]);

        if (!$producto) {
            return null;
        }

        // Get all images
        $producto['images'] = $this->productImageModel->getByProductId($id);

        // Get all variants with stock
        $producto['variants'] = $this->productVariantModel->getByProductId($id);

        // Calculate total stock from variants
        $totalStock = 0;
        foreach ($producto['variants'] as $variant) {
            $totalStock += $variant['stock_quantity'];
        }
        $producto['stock_quantity'] = $totalStock;

        return $producto;
    }

    /**
     * Load view with layout
     */
    private function loadView($viewPath, $data = [])
    {
        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        include __DIR__ . '/../../views/' . $viewPath . '.php';

        // Get the buffered content
        $content = ob_get_clean();

        // Include the admin layout
        include __DIR__ . '/../../views/layouts/admin.php';
    }

    /**
     * Create new product (placeholder for future implementation)
     */
    public function create()
    {
        // Get leagues and teams for form
        $leagues = $this->leagueModel->getAll();
        $teams = $this->teamModel->getAll();

        $data = [
            'leagues' => $leagues,
            'teams' => $teams
        ];

        $this->loadView('admin/productos/create', $data);
    }

    /**
     * Edit existing product (placeholder for future implementation)
     */
    public function edit($id)
    {
        $producto = $this->getProductoDetails($id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado';
            header('Location: /admin/productos');
            exit;
        }

        // Get leagues and teams for form
        $leagues = $this->leagueModel->getAll();
        $teams = $this->teamModel->getAll();

        $data = [
            'producto' => $producto,
            'leagues' => $leagues,
            'teams' => $teams
        ];

        $this->loadView('admin/productos/edit', $data);
    }

    /**
     * Store new product (placeholder for future implementation)
     */
    public function store()
    {
        // TODO: Implement product creation
        $_SESSION['success'] = 'Producto creado correctamente';
        header('Location: /admin/productos');
        exit;
    }

    /**
     * Update existing product (placeholder for future implementation)
     */
    public function update($id)
    {
        // TODO: Implement product update
        $_SESSION['success'] = 'Producto actualizado correctamente';
        header('Location: /admin/productos');
        exit;
    }

    /**
     * Delete product (placeholder for future implementation)
     */
    public function delete($id)
    {
        // TODO: Implement product deletion
        $success = $this->productModel->delete($id);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar el producto']);
        }
    }
}
