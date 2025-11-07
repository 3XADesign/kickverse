<?php
/**
 * Subscription Model
 * Manages customer subscriptions
 */

require_once __DIR__ . '/Model.php';

class Subscription extends Model {
    protected $table = 'subscriptions';
    protected $primaryKey = 'subscription_id';

    /**
     * Get all subscriptions with customer and plan details
     */
    public function getAllWithDetails($filters = [], $orderBy = 's.created_at DESC', $limit = null, $offset = null) {
        $where = ['s.subscription_id IS NOT NULL'];
        $params = [];

        // Customer filter
        if (!empty($filters['customer_id'])) {
            $where[] = 's.customer_id = ?';
            $params[] = $filters['customer_id'];
        }

        // Status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                // Handle array of statuses
                $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
                $where[] = "s.status IN ({$placeholders})";
                $params = array_merge($params, $filters['status']);
            } else {
                // Handle single status
                $where[] = 's.status = ?';
                $params[] = $filters['status'];
            }
        }

        // Plan filter
        if (!empty($filters['plan_id'])) {
            $where[] = 's.plan_id = ?';
            $params[] = $filters['plan_id'];
        }

        // Customer search
        if (!empty($filters['customer_search'])) {
            $where[] = '(c.full_name LIKE ? OR c.email LIKE ? OR c.telegram_username LIKE ?)';
            $searchTerm = '%' . $filters['customer_search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT
                    s.*,
                    c.full_name as customer_name,
                    c.email as customer_email,
                    c.telegram_username,
                    c.whatsapp_number,
                    c.phone,
                    sp.plan_name,
                    sp.monthly_price,
                    sp.plan_type,
                    sp.jersey_quality,
                    (SELECT COUNT(*) FROM subscription_shipments WHERE subscription_id = s.subscription_id) as total_shipments,
                    (SELECT SUM(amount) FROM subscription_payments WHERE subscription_id = s.subscription_id AND status = 'completed') as total_paid
                FROM subscriptions s
                INNER JOIN customers c ON s.customer_id = c.customer_id
                INNER JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE {$whereClause}
                ORDER BY {$orderBy}";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;

            if ($offset) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
            }
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get subscription with full details (for modal)
     */
    public function getFullDetails($subscriptionId) {
        $sql = "SELECT
                    s.*,
                    c.customer_id,
                    c.full_name as customer_name,
                    c.email as customer_email,
                    c.telegram_username,
                    c.whatsapp_number,
                    c.phone,
                    c.preferred_language,
                    sp.plan_name,
                    sp.plan_type,
                    sp.monthly_price,
                    sp.jersey_quality,
                    sp.jersey_quantity,
                    sp.description as plan_description
                FROM subscriptions s
                INNER JOIN customers c ON s.customer_id = c.customer_id
                INNER JOIN subscription_plans sp ON s.plan_id = sp.plan_id
                WHERE s.subscription_id = ?
                LIMIT 1";

        return $this->fetchOne($sql, [$subscriptionId]);
    }

    /**
     * Get subscription payments history
     */
    public function getPaymentHistory($subscriptionId) {
        $sql = "SELECT
                    payment_id,
                    payment_date,
                    amount,
                    payment_method,
                    payment_status,
                    transaction_reference,
                    notes,
                    created_at
                FROM subscription_payments
                WHERE subscription_id = ?
                ORDER BY payment_date DESC";

        return $this->fetchAll($sql, [$subscriptionId]);
    }

    /**
     * Get subscription shipments history
     */
    public function getShipmentHistory($subscriptionId) {
        $sql = "SELECT
                    shipment_id,
                    shipment_date,
                    expected_delivery_date,
                    actual_delivery_date,
                    tracking_number,
                    carrier,
                    status,
                    contents,
                    notes,
                    created_at
                FROM subscription_shipments
                WHERE subscription_id = ?
                ORDER BY shipment_date DESC";

        return $this->fetchAll($sql, [$subscriptionId]);
    }

    /**
     * Get all subscription plans
     */
    public function getAllPlans() {
        $sql = "SELECT * FROM subscription_plans
                WHERE is_active = 1
                ORDER BY display_order ASC, monthly_price ASC";

        return $this->fetchAll($sql);
    }

    /**
     * Get leagues from preferences
     */
    public function getLeagueNames($leagueIds) {
        if (empty($leagueIds)) {
            return [];
        }

        // Decode if JSON
        if (is_string($leagueIds)) {
            $leagueIds = json_decode($leagueIds, true);
        }

        if (empty($leagueIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($leagueIds), '?'));
        $sql = "SELECT league_id, name FROM leagues WHERE league_id IN ({$placeholders})";

        return $this->fetchAll($sql, $leagueIds);
    }

    /**
     * Get teams from preferences
     */
    public function getTeamNames($teamIds) {
        if (empty($teamIds)) {
            return [];
        }

        // Decode if JSON
        if (is_string($teamIds)) {
            $teamIds = json_decode($teamIds, true);
        }

        if (empty($teamIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($teamIds), '?'));
        $sql = "SELECT team_id, name, logo_path FROM teams WHERE team_id IN ({$placeholders})";

        return $this->fetchAll($sql, $teamIds);
    }

    /**
     * Count subscriptions with filters
     */
    public function countWithFilters($filters = []) {
        $where = ['s.subscription_id IS NOT NULL'];
        $params = [];

        // Status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                // Handle array of statuses
                $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
                $where[] = "s.status IN ({$placeholders})";
                $params = array_merge($params, $filters['status']);
            } else {
                // Handle single status
                $where[] = 's.status = ?';
                $params[] = $filters['status'];
            }
        }

        // Plan filter
        if (!empty($filters['plan_id'])) {
            $where[] = 's.plan_id = ?';
            $params[] = $filters['plan_id'];
        }

        // Customer search
        if (!empty($filters['customer_search'])) {
            $where[] = '(c.full_name LIKE ? OR c.email LIKE ? OR c.telegram_username LIKE ?)';
            $searchTerm = '%' . $filters['customer_search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) as count
                FROM subscriptions s
                INNER JOIN customers c ON s.customer_id = c.customer_id
                WHERE {$whereClause}";

        $result = $this->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Pause subscription
     */
    public function pauseSubscription($subscriptionId, $reason = null) {
        return $this->update($subscriptionId, [
            'status' => 'paused',
            'pause_date' => date('Y-m-d'),
            'pause_reason' => $reason
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription($subscriptionId, $reason = null) {
        return $this->update($subscriptionId, [
            'status' => 'cancelled',
            'cancellation_date' => date('Y-m-d'),
            'cancellation_reason' => $reason,
            'next_billing_date' => null
        ]);
    }

    /**
     * Reactivate subscription
     */
    public function reactivateSubscription($subscriptionId) {
        $subscription = $this->find($subscriptionId);

        if (!$subscription) {
            return false;
        }

        // Calculate new period dates
        $today = date('Y-m-d');
        $nextBilling = date('Y-m-d', strtotime('+1 month'));
        $periodEnd = date('Y-m-d', strtotime('+1 month'));

        return $this->update($subscriptionId, [
            'status' => 'active',
            'current_period_start' => $today,
            'current_period_end' => $periodEnd,
            'next_billing_date' => $nextBilling,
            'pause_date' => null,
            'pause_reason' => null,
            'cancellation_date' => null,
            'cancellation_reason' => null
        ]);
    }

    /**
     * Get subscription stats
     */
    public function getStats() {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired
                FROM subscriptions";

        return $this->fetchOne($sql);
    }
}
