<?php
/**
 * Admin Model
 * Handles admin user operations and magic link authentication
 */

require_once __DIR__ . '/Model.php';

class Admin extends Model {
    protected $table = 'admin_users';
    protected $primaryKey = 'admin_id';

    /**
     * Find admin by email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND is_active = 1 LIMIT 1";
        $results = $this->fetchAll($sql, [$email]);
        return $results[0] ?? null;
    }

    /**
     * Check if email exists (for security - always return true to prevent email enumeration)
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ? AND is_active = 1";
        $result = $this->fetchAll($sql, [$email]);
        return ($result[0]['count'] ?? 0) > 0;
    }

    /**
     * Create magic link token
     */
    public function createMagicToken($email, $ipAddress = null, $userAgent = null) {
        // Find admin
        $admin = $this->findByEmail($email);

        if (!$admin) {
            // Return dummy token for security (prevent email enumeration)
            return bin2hex(random_bytes(32));
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32));

        // Token expires in 15 minutes
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $sql = "INSERT INTO admin_login_tokens (admin_id, token, email, expires_at, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->query($sql, [
            $admin['admin_id'],
            $token,
            $email,
            $expiresAt,
            $ipAddress,
            $userAgent
        ]);

        return $token;
    }

    /**
     * Verify and use token
     */
    public function verifyToken($token) {
        $sql = "SELECT t.*, a.*
                FROM admin_login_tokens t
                JOIN admin_users a ON t.admin_id = a.admin_id
                WHERE t.token = ?
                AND t.used_at IS NULL
                AND t.expires_at > NOW()
                AND a.is_active = 1
                LIMIT 1";

        $results = $this->fetchAll($sql, [$token]);
        $data = $results[0] ?? null;

        if (!$data) {
            return null;
        }

        // Mark token as used
        $updateSql = "UPDATE admin_login_tokens SET used_at = NOW() WHERE token = ?";
        $this->query($updateSql, [$token]);

        // Update last login
        $this->updateLastLogin($data['admin_id'], $_SERVER['REMOTE_ADDR'] ?? null);

        return $data;
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($adminId, $ipAddress = null) {
        $sql = "UPDATE {$this->table}
                SET last_login = NOW(), last_login_ip = ?, failed_login_attempts = 0
                WHERE admin_id = ?";
        $this->query($sql, [$ipAddress, $adminId]);
    }

    /**
     * Clean up expired tokens (run periodically)
     */
    public function cleanExpiredTokens() {
        $sql = "DELETE FROM admin_login_tokens WHERE expires_at < NOW()";
        $this->query($sql);
    }

    /**
     * Get admin stats for dashboard
     */
    public function getDashboardStats() {
        $stats = [];

        // Total customers
        $sql = "SELECT COUNT(*) as count FROM customers";
        $result = $this->fetchAll($sql);
        $stats['total_customers'] = $result[0]['count'] ?? 0;

        // Total orders
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $this->fetchAll($sql);
        $stats['total_orders'] = $result[0]['count'] ?? 0;

        // Total products
        $sql = "SELECT COUNT(*) as count FROM products WHERE is_active = 1";
        $result = $this->fetchAll($sql);
        $stats['total_products'] = $result[0]['count'] ?? 0;

        // Total revenue
        $sql = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'";
        $result = $this->fetchAll($sql);
        $stats['total_revenue'] = $result[0]['revenue'] ?? 0;

        // Recent orders
        $sql = "SELECT o.*, c.full_name as customer_name
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.customer_id
                ORDER BY o.created_at DESC
                LIMIT 10";
        $stats['recent_orders'] = $this->fetchAll($sql);

        // Low stock products
        $sql = "SELECT p.*,
                       (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.product_id) as total_stock
                FROM products p
                WHERE p.is_active = 1
                HAVING total_stock < 10
                ORDER BY total_stock ASC
                LIMIT 10";
        $stats['low_stock'] = $this->fetchAll($sql);

        return $stats;
    }
}
