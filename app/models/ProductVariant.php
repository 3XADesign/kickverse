<?php
/**
 * ProductVariant Model
 * Manages product variants (sizes with stock)
 */

require_once __DIR__ . '/Model.php';

class ProductVariant extends Model {
    protected $table = 'product_variants';
    protected $primaryKey = 'variant_id';

    /**
     * Get all variants for a product
     */
    public function getByProductId($productId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ?
                ORDER BY
                    CASE size_category
                        WHEN 'general' THEN 1
                        WHEN 'player' THEN 2
                        WHEN 'kids' THEN 3
                        WHEN 'tracksuit' THEN 4
                        ELSE 5
                    END,
                    CASE size
                        WHEN 'S' THEN 1
                        WHEN 'M' THEN 2
                        WHEN 'L' THEN 3
                        WHEN 'XL' THEN 4
                        WHEN '2XL' THEN 5
                        WHEN '3XL' THEN 6
                        WHEN '4XL' THEN 7
                        ELSE CAST(size AS UNSIGNED)
                    END";

        return $this->fetchAll($sql, [$productId]);
    }

    /**
     * Get active variants with stock
     */
    public function getAvailableVariants($productId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ?
                  AND is_active = 1
                  AND stock_quantity > 0
                ORDER BY size";

        return $this->fetchAll($sql, [$productId]);
    }

    /**
     * Get variant by product and size
     */
    public function getBySize($productId, $size, $sizeCategory = 'general') {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ? AND size = ? AND size_category = ?
                LIMIT 1";

        return $this->fetchOne($sql, [$productId, $size, $sizeCategory]);
    }

    /**
     * Get variant by SKU
     */
    public function getBySku($sku) {
        return $this->whereOne(['sku' => $sku]);
    }

    /**
     * Check if size is available
     */
    public function isAvailable($productId, $size, $sizeCategory = 'general') {
        $sql = "SELECT stock_quantity FROM {$this->table}
                WHERE product_id = ? AND size = ? AND size_category = ?
                  AND is_active = 1
                LIMIT 1";

        $result = $this->fetchOne($sql, [$productId, $size, $sizeCategory]);
        return $result && $result['stock_quantity'] > 0;
    }

    /**
     * Get variants by category
     */
    public function getByCategory($productId, $category) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ? AND size_category = ?
                ORDER BY size";

        return $this->fetchAll($sql, [$productId, $category]);
    }

    /**
     * Update stock quantity
     */
    public function updateStock($variantId, $quantity) {
        return $this->update($variantId, ['stock_quantity' => $quantity]);
    }

    /**
     * Decrease stock (for sales)
     */
    public function decreaseStock($variantId, $quantity) {
        $sql = "UPDATE {$this->table}
                SET stock_quantity = stock_quantity - ?
                WHERE variant_id = ? AND stock_quantity >= ?";

        return $this->query($sql, [$quantity, $variantId, $quantity]);
    }

    /**
     * Increase stock (for returns/restocks)
     */
    public function increaseStock($variantId, $quantity) {
        $sql = "UPDATE {$this->table}
                SET stock_quantity = stock_quantity + ?
                WHERE variant_id = ?";

        return $this->query($sql, [$quantity, $variantId]);
    }

    /**
     * Get low stock variants
     */
    public function getLowStock($productId = null) {
        $sql = "SELECT v.*, p.name as product_name
                FROM {$this->table} v
                JOIN products p ON v.product_id = p.product_id
                WHERE v.is_active = 1
                  AND v.stock_quantity > 0
                  AND v.stock_quantity <= v.low_stock_threshold";

        if ($productId) {
            $sql .= " AND v.product_id = ?";
            return $this->fetchAll($sql, [$productId]);
        }

        return $this->fetchAll($sql);
    }

    /**
     * Get out of stock variants
     */
    public function getOutOfStock($productId = null) {
        $sql = "SELECT v.*, p.name as product_name
                FROM {$this->table} v
                JOIN products p ON v.product_id = p.product_id
                WHERE v.is_active = 1
                  AND v.stock_quantity = 0";

        if ($productId) {
            $sql .= " AND v.product_id = ?";
            return $this->fetchAll($sql, [$productId]);
        }

        return $this->fetchAll($sql);
    }

    /**
     * Get total stock for a product
     */
    public function getTotalStock($productId) {
        $sql = "SELECT SUM(stock_quantity) as total
                FROM {$this->table}
                WHERE product_id = ? AND is_active = 1";

        $result = $this->fetchOne($sql, [$productId]);
        return $result['total'] ?? 0;
    }

    /**
     * Create variant with auto-generated SKU
     */
    public function createVariant($productId, $size, $sizeCategory = 'general', $stock = 0) {
        // Get product info for SKU generation
        $sql = "SELECT slug FROM products WHERE product_id = ?";
        $product = $this->fetchOne($sql, [$productId]);

        if (!$product) {
            return false;
        }

        // Generate SKU: product-slug-size-category
        $sku = strtoupper($product['slug'] . '-' . $size . '-' . substr($sizeCategory, 0, 3));

        $data = [
            'product_id' => $productId,
            'size' => $size,
            'size_category' => $sizeCategory,
            'sku' => $sku,
            'stock_quantity' => $stock,
            'low_stock_threshold' => 10,
            'is_active' => 1
        ];

        return $this->create($data);
    }

    /**
     * Delete all variants for a product
     */
    public function deleteByProductId($productId) {
        $sql = "DELETE FROM {$this->table} WHERE product_id = ?";
        return $this->query($sql, [$productId]);
    }
}
