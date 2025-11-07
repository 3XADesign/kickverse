<?php
/**
 * ProductImage Model
 * Manages product images (main, detail, hover, gallery)
 */

require_once __DIR__ . '/Model.php';

class ProductImage extends Model {
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';

    /**
     * Get all images for a product
     */
    public function getByProductId($productId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ?
                ORDER BY display_order ASC";

        return $this->fetchAll($sql, [$productId]);
    }

    /**
     * Get main image for a product
     */
    public function getMainImage($productId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ? AND image_type = 'main'
                ORDER BY display_order ASC
                LIMIT 1";

        return $this->fetchOne($sql, [$productId]);
    }

    /**
     * Get images by type
     */
    public function getByType($productId, $type) {
        $sql = "SELECT * FROM {$this->table}
                WHERE product_id = ? AND image_type = ?
                ORDER BY display_order ASC";

        return $this->fetchAll($sql, [$productId, $type]);
    }

    /**
     * Add image to product
     */
    public function addImage($productId, $imagePath, $imageType = 'gallery', $altText = null) {
        // Get max display order
        $sql = "SELECT MAX(display_order) as max_order FROM {$this->table}
                WHERE product_id = ?";
        $result = $this->fetchOne($sql, [$productId]);
        $displayOrder = ($result['max_order'] ?? -1) + 1;

        $data = [
            'product_id' => $productId,
            'image_path' => $imagePath,
            'image_type' => $imageType,
            'display_order' => $displayOrder,
            'alt_text' => $altText
        ];

        return $this->create($data);
    }

    /**
     * Update image order
     */
    public function updateOrder($imageId, $newOrder) {
        return $this->update($imageId, ['display_order' => $newOrder]);
    }

    /**
     * Set as main image
     */
    public function setAsMain($imageId, $productId) {
        // First, change any existing main images to gallery
        $sql = "UPDATE {$this->table}
                SET image_type = 'gallery'
                WHERE product_id = ? AND image_type = 'main'";
        $this->query($sql, [$productId]);

        // Then set this image as main
        return $this->update($imageId, ['image_type' => 'main', 'display_order' => 0]);
    }

    /**
     * Delete all images for a product
     */
    public function deleteByProductId($productId) {
        $sql = "DELETE FROM {$this->table} WHERE product_id = ?";
        return $this->query($sql, [$productId]);
    }
}
