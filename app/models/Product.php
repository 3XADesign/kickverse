<?php
/**
 * Product Model
 * Manages products (jerseys, accessories, etc.)
 */

require_once __DIR__ . '/Model.php';

class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    /**
     * Get products by league
     */
    public function getByLeague($leagueSlug, $active = true) {
        $sql = "SELECT p.*,
                       p.base_price as price,
                       t.name as team_name,
                       t.slug as team_slug,
                       l.name as league_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.product_id AND image_type = 'main' LIMIT 1) as image_url,
                       CASE
                           WHEN p.stock_quantity = 0 THEN 'out'
                           WHEN p.stock_quantity <= 5 THEN 'low'
                           ELSE 'available'
                       END as stock_status
                FROM {$this->table} p
                JOIN teams t ON p.team_id = t.team_id
                JOIN leagues l ON p.league_id = l.league_id
                WHERE l.slug = ?";

        if ($active) {
            $sql .= " AND p.is_active = 1";
        }

        $sql .= " ORDER BY p.is_featured DESC, t.display_order, p.name";

        return $this->fetchAll($sql, [$leagueSlug]);
    }

    /**
     * Get products by team
     */
    public function getByTeam($teamSlug, $active = true) {
        $sql = "SELECT p.*, t.name as team_name, l.name as league_name
                FROM {$this->table} p
                JOIN teams t ON p.team_id = t.team_id
                JOIN leagues l ON p.league_id = l.league_id
                WHERE t.slug = ?";

        if ($active) {
            $sql .= " AND p.is_active = 1";
        }

        $sql .= " ORDER BY p.jersey_type";

        return $this->fetchAll($sql, [$teamSlug]);
    }

    /**
     * Get active products
     */
    public function getActive($limit = 20) {
        $sql = "SELECT p.*, t.name as team_name, t.slug as team_slug,
                       l.name as league_name, l.slug as league_slug
                FROM {$this->table} p
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE p.is_active = 1
                ORDER BY p.is_featured DESC, p.created_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Get featured products
     */
    public function getFeatured($limit = 10) {
        $sql = "SELECT p.*, t.name as team_name, t.slug as team_slug,
                       l.name as league_name, l.slug as league_slug
                FROM {$this->table} p
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE p.is_active = 1 AND p.is_featured = 1
                ORDER BY RAND()
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Get random products (for "best sellers" when no sales data)
     */
    public function getRandom($limit = 3) {
        $sql = "SELECT p.*, t.name as team_name, t.slug as team_slug,
                       l.name as league_name, l.slug as league_slug
                FROM {$this->table} p
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE p.is_active = 1
                ORDER BY RAND()
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Get products by type
     */
    public function getByType($type, $limit = 20) {
        $sql = "SELECT p.*, t.name as team_name, t.slug as team_slug,
                       l.name as league_name, l.slug as league_slug
                FROM {$this->table} p
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE p.is_active = 1 AND p.product_type = ?
                ORDER BY p.is_featured DESC, p.created_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$type, $limit]);
    }

    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        return $this->whereOne(['slug' => $slug, 'is_active' => 1]);
    }

    /**
     * Get product with full details (images, variants)
     */
    public function getFullDetails($productId) {
        // Get product data
        $product = $this->find($productId);

        if (!$product) {
            return null;
        }

        // Get team and league info
        $sql = "SELECT p.*, t.name as team_name, t.slug as team_slug,
                       l.name as league_name, l.slug as league_slug
                FROM {$this->table} p
                LEFT JOIN teams t ON p.team_id = t.team_id
                LEFT JOIN leagues l ON p.league_id = l.league_id
                WHERE p.product_id = ?";

        $product = $this->fetchOne($sql, [$productId]);

        // Get images
        $product['images'] = $this->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order",
            [$productId]
        );

        // Get available variants (sizes with stock)
        $product['variants'] = $this->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1 AND stock_quantity > 0 ORDER BY size",
            [$productId]
        );

        return $product;
    }

    /**
     * Search products
     */
    public function search($query, $limit = 20) {
        $searchTerm = "%{$query}%";

        $sql = "SELECT p.*, t.name as team_name, l.name as league_name
                FROM {$this->table} p
                JOIN teams t ON p.team_id = t.team_id
                JOIN leagues l ON p.league_id = l.league_id
                WHERE p.is_active = 1
                  AND (p.name LIKE ? OR t.name LIKE ? OR l.name LIKE ?)
                ORDER BY p.is_featured DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
    }

    /**
     * Check if product has stock for specific size
     */
    public function hasStock($productId, $size) {
        $sql = "SELECT stock_quantity FROM product_variants
                WHERE product_id = ? AND size = ? AND is_active = 1";

        $result = $this->fetchOne($sql, [$productId, $size]);
        return $result && $result['stock_quantity'] > 0;
    }

    /**
     * Get variant by product and size
     */
    public function getVariant($productId, $size) {
        $sql = "SELECT * FROM product_variants
                WHERE product_id = ? AND size = ? AND is_active = 1
                LIMIT 1";

        return $this->fetchOne($sql, [$productId, $size]);
    }

    /**
     * Update stock after sale
     */
    public function decreaseStock($variantId, $quantity) {
        $sql = "UPDATE product_variants
                SET stock_quantity = stock_quantity - ?
                WHERE variant_id = ? AND stock_quantity >= ?";

        return $this->query($sql, [$quantity, $variantId, $quantity]);
    }

    /**
     * Get price history
     */
    public function getPriceHistory($productId, $limit = 10) {
        $sql = "SELECT * FROM product_price_history
                WHERE product_id = ?
                ORDER BY changed_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$productId, $limit]);
    }

    /**
     * Record price change
     */
    public function recordPriceChange($productId, $oldPrice, $newPrice, $reason, $adminId = null) {
        $sql = "INSERT INTO product_price_history
                (product_id, old_price, new_price, change_reason, changed_by)
                VALUES (?, ?, ?, ?, ?)";

        return $this->query($sql, [$productId, $oldPrice, $newPrice, $reason, $adminId]);
    }
}
