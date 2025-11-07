<?php
/**
 * PaymentTransaction Model
 * Manages payment transactions in the database
 */

require_once __DIR__ . '/Model.php';

class PaymentTransaction extends Model {
    protected $table = 'payment_transactions';
    protected $primaryKey = 'transaction_id';
    protected $timestamps = false; // Using custom timestamp fields

    /**
     * Get all transactions with customer and order info
     */
    public function getAllWithDetails($filters = [], $limit = 50, $offset = 0) {
        $whereConditions = [];
        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $whereConditions[] = "pt.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_method'])) {
            $whereConditions[] = "pt.payment_method = ?";
            $params[] = $filters['payment_method'];
        }

        if (!empty($filters['customer_id'])) {
            $whereConditions[] = "pt.customer_id = ?";
            $params[] = $filters['customer_id'];
        }

        if (!empty($filters['order_id'])) {
            $whereConditions[] = "pt.order_id = ?";
            $params[] = $filters['order_id'];
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(pt.initiated_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(pt.initiated_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email,
                       c.telegram_username,
                       o.order_id,
                       o.order_type,
                       o.order_status,
                       s.subscription_id,
                       s.plan_id
                FROM payment_transactions pt
                JOIN customers c ON pt.customer_id = c.customer_id
                LEFT JOIN orders o ON pt.order_id = o.order_id
                LEFT JOIN subscriptions s ON pt.subscription_id = s.subscription_id
                $whereClause
                ORDER BY pt.initiated_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get transaction by ID with full details
     */
    public function getWithDetails($transactionId) {
        $sql = "SELECT pt.*,
                       c.customer_id, c.full_name as customer_name, c.email as customer_email,
                       c.telegram_username, c.whatsapp_number, c.phone,
                       o.order_id, o.order_type, o.order_status, o.total_amount as order_total,
                       o.order_date, o.tracking_number,
                       s.subscription_id, s.plan_id, s.status as subscription_status,
                       sp.plan_name, sp.monthly_price
                FROM payment_transactions pt
                JOIN customers c ON pt.customer_id = c.customer_id
                LEFT JOIN orders o ON pt.order_id = o.order_id
                LEFT JOIN subscriptions s ON pt.subscription_id = s.subscription_id
                LEFT JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE pt.transaction_id = ?";

        return $this->fetchOne($sql, [$transactionId]);
    }

    /**
     * Get transactions by customer
     */
    public function getByCustomer($customerId, $limit = 10) {
        return $this->where(['customer_id' => $customerId], 'initiated_at DESC', $limit);
    }

    /**
     * Get transactions by order
     */
    public function getByOrder($orderId) {
        return $this->where(['order_id' => $orderId], 'initiated_at DESC');
    }

    /**
     * Get transactions by subscription
     */
    public function getBySubscription($subscriptionId) {
        return $this->where(['subscription_id' => $subscriptionId], 'initiated_at DESC');
    }

    /**
     * Get transactions by status
     */
    public function getByStatus($status, $limit = null) {
        return $this->where(['status' => $status], 'initiated_at DESC', $limit);
    }

    /**
     * Create new transaction
     */
    public function createTransaction($data) {
        $defaultData = [
            'status' => 'pending',
            'currency' => 'EUR',
            'initiated_at' => date('Y-m-d H:i:s')
        ];

        $data = array_merge($defaultData, $data);

        return $this->create($data);
    }

    /**
     * Update transaction status
     */
    public function updateStatus($transactionId, $status, $additionalData = []) {
        $updateData = array_merge(['status' => $status], $additionalData);

        if ($status === 'completed' && !isset($additionalData['completed_at'])) {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($transactionId, $updateData);
    }

    /**
     * Mark as verified by admin
     */
    public function markAsVerified($transactionId, $adminId) {
        return $this->update($transactionId, [
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get pending transactions
     */
    public function getPendingTransactions($limit = 50) {
        return $this->getByStatus('pending', $limit);
    }

    /**
     * Get expired transactions (pending for more than X hours)
     */
    public function getExpiredTransactions($hoursOld = 24) {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'pending'
                AND initiated_at < DATE_SUB(NOW(), INTERVAL ? HOUR)
                ORDER BY initiated_at DESC";

        return $this->fetchAll($sql, [$hoursOld]);
    }

    /**
     * Get total transaction amount by customer
     */
    public function getTotalByCustomer($customerId, $status = 'completed') {
        $sql = "SELECT SUM(amount) as total
                FROM {$this->table}
                WHERE customer_id = ? AND status = ?";

        $result = $this->fetchOne($sql, [$customerId, $status]);
        return $result['total'] ?? 0;
    }

    /**
     * Get transaction statistics
     */
    public function getStatistics($dateFrom = null, $dateTo = null) {
        $whereConditions = [];
        $params = [];

        if ($dateFrom) {
            $whereConditions[] = "DATE(initiated_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $whereConditions[] = "DATE(initiated_at) <= ?";
            $params[] = $dateTo;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        $sql = "SELECT
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
                    AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_amount,
                    payment_method,
                    COUNT(*) as method_count
                FROM {$this->table}
                $whereClause
                GROUP BY payment_method";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Count transactions by status
     */
    public function countByStatus() {
        $sql = "SELECT status, COUNT(*) as count
                FROM {$this->table}
                GROUP BY status";

        return $this->fetchAll($sql);
    }

    /**
     * Get recent transactions
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email
                FROM {$this->table} pt
                JOIN customers c ON pt.customer_id = c.customer_id
                ORDER BY pt.initiated_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Search transactions
     */
    public function search($query, $limit = 50) {
        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email
                FROM {$this->table} pt
                JOIN customers c ON pt.customer_id = c.customer_id
                WHERE pt.transaction_id LIKE ?
                   OR pt.oxapay_transaction_id LIKE ?
                   OR c.full_name LIKE ?
                   OR c.email LIKE ?
                ORDER BY pt.initiated_at DESC
                LIMIT ?";

        $searchTerm = "%$query%";
        return $this->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
    }

    /**
     * Get transactions by payment method
     */
    public function getByPaymentMethod($method, $limit = 50) {
        return $this->where(['payment_method' => $method], 'initiated_at DESC', $limit);
    }

    /**
     * Get crypto transactions with details
     */
    public function getCryptoTransactions($limit = 50) {
        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email
                FROM {$this->table} pt
                JOIN customers c ON pt.customer_id = c.customer_id
                WHERE pt.payment_method IN ('oxapay_btc', 'oxapay_eth', 'oxapay_usdt')
                ORDER BY pt.initiated_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Get manual transactions pending verification
     */
    public function getManualPendingVerification($limit = 50) {
        $sql = "SELECT pt.*,
                       c.full_name as customer_name,
                       c.email as customer_email
                FROM {$this->table} pt
                JOIN customers c ON pt.customer_id = c.customer_id
                WHERE pt.payment_method IN ('telegram_manual', 'whatsapp_manual', 'bank_transfer')
                AND pt.status IN ('pending', 'processing')
                AND pt.verified_by IS NULL
                ORDER BY pt.initiated_at ASC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }
}
