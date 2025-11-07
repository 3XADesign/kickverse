<?php
/**
 * Customer Model
 * Manages customer accounts (hybrid auth system)
 */

require_once __DIR__ . '/Model.php';

class Customer extends Model {
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    /**
     * Find customer by email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL LIMIT 1";
        return $this->fetchOne($sql, [$email]);
    }

    /**
     * Find customer by Telegram
     */
    public function findByTelegram($username) {
        return $this->whereOne([
            'telegram_username' => $username,
            'deleted_at' => null
        ]);
    }

    /**
     * Find customer by WhatsApp
     */
    public function findByWhatsApp($number) {
        return $this->whereOne([
            'whatsapp_number' => $number,
            'deleted_at' => null
        ]);
    }

    /**
     * Register new customer (classic auth)
     * Now requires email verification before activation
     */
    public function register($email, $password, $fullName, $phone = null, $preferredLanguage = 'es') {
        // Generate unique verification token
        $verificationToken = bin2hex(random_bytes(32));

        $data = [
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]),
            'full_name' => $fullName,
            'phone' => $phone,
            'customer_status' => 'pending', // Pending until email verification
            'email_verified' => 0,
            'email_verification_token' => $verificationToken,
            'loyalty_tier' => 'standard',
            'loyalty_points' => 0,
            'preferred_language' => $preferredLanguage,
        ];

        $customerId = $this->create($data);

        // Return both customer ID and verification token
        return [
            'customer_id' => $customerId,
            'verification_token' => $verificationToken
        ];
    }

    /**
     * Find customer by verification token
     */
    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE email_verification_token = ? AND deleted_at IS NULL LIMIT 1";
        return $this->fetchOne($sql, [$token]);
    }

    /**
     * Verify customer email
     */
    public function verifyEmail($customerId) {
        return $this->update($customerId, [
            'email_verified' => 1,
            'customer_status' => 'active',
            'email_verification_token' => null
        ]);
    }

    /**
     * Register via Telegram/WhatsApp
     */
    public function registerSocial($fullName, $telegram = null, $whatsapp = null, $phone = null) {
        $data = [
            'full_name' => $fullName,
            'telegram_username' => $telegram,
            'whatsapp_number' => $whatsapp,
            'phone' => $phone,
            'customer_status' => 'active',
            'loyalty_tier' => 'standard',
            'loyalty_points' => 0,
        ];

        return $this->create($data);
    }

    /**
     * Verify password
     */
    public function verifyPassword($email, $password) {
        $customer = $this->findByEmail($email);

        if (!$customer) {
            return false;
        }

        return password_verify($password, $customer['password_hash']);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($customerId, $ipAddress = null) {
        return $this->update($customerId, [
            'last_login_date' => date('Y-m-d H:i:s'),
            'last_activity_date' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get customer preferences
     */
    public function getPreferences($customerId) {
        $sql = "SELECT * FROM customer_preferences WHERE customer_id = ?";
        return $this->fetchOne($sql, [$customerId]);
    }

    /**
     * Update customer preferences
     */
    public function updatePreferences($customerId, $preferences) {
        // Check if preferences exist
        $existing = $this->getPreferences($customerId);

        if ($existing) {
            $sql = "UPDATE customer_preferences SET ";
            $updates = [];
            $params = [];

            foreach ($preferences as $key => $value) {
                $updates[] = "{$key} = ?";
                $params[] = is_array($value) ? json_encode($value) : $value;
            }

            $params[] = $customerId;
            $sql .= implode(', ', $updates) . " WHERE customer_id = ?";

            return $this->query($sql, $params);
        } else {
            $preferences['customer_id'] = $customerId;

            // Convert arrays to JSON
            foreach ($preferences as $key => $value) {
                if (is_array($value)) {
                    $preferences[$key] = json_encode($value);
                }
            }

            $fields = implode(',', array_keys($preferences));
            $placeholders = str_repeat('?,', count($preferences) - 1) . '?';

            $sql = "INSERT INTO customer_preferences ({$fields}) VALUES ({$placeholders})";
            return $this->query($sql, array_values($preferences));
        }
    }

    /**
     * Get customer addresses
     */
    public function getAddresses($customerId) {
        $sql = "SELECT * FROM shipping_addresses
                WHERE customer_id = ? AND is_active = 1
                ORDER BY is_default DESC, created_at DESC";

        return $this->fetchAll($sql, [$customerId]);
    }

    /**
     * Get default address
     */
    public function getDefaultAddress($customerId) {
        $sql = "SELECT * FROM shipping_addresses
                WHERE customer_id = ? AND is_default = 1 AND is_active = 1
                LIMIT 1";

        return $this->fetchOne($sql, [$customerId]);
    }

    /**
     * Add loyalty points
     */
    public function addLoyaltyPoints($customerId, $points, $type, $orderId = null, $description = '') {
        // Get current points
        $customer = $this->find($customerId);
        $newBalance = $customer['loyalty_points'] + $points;

        // Update customer
        $this->update($customerId, ['loyalty_points' => $newBalance]);

        // Record transaction
        $sql = "INSERT INTO loyalty_points_history
                (customer_id, points_change, points_balance_after, transaction_type, reference_order_id, description)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->query($sql, [$customerId, $points, $newBalance, $type, $orderId, $description]);

        // Check tier upgrade
        $this->checkTierUpgrade($customerId);

        return $newBalance;
    }

    /**
     * Check and upgrade loyalty tier
     */
    public function checkTierUpgrade($customerId) {
        $customer = $this->find($customerId);

        $sql = "SELECT tier FROM loyalty_tier_benefits
                WHERE min_orders_required <= ? AND min_total_spent <= ?
                ORDER BY min_total_spent DESC
                LIMIT 1";

        $result = $this->fetchOne($sql, [$customer['total_orders_count'], $customer['total_spent']]);

        if ($result && $result['tier'] != $customer['loyalty_tier']) {
            $this->update($customerId, ['loyalty_tier' => $result['tier']]);
        }
    }

    /**
     * Get VIP customers
     */
    public function getVIPCustomers($limit = 50) {
        $sql = "SELECT * FROM {$this->table}
                WHERE customer_status = 'active' AND deleted_at IS NULL
                ORDER BY total_spent DESC, loyalty_points DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$limit]);
    }

    /**
     * Soft delete customer
     */
    public function deleteCustomer($customerId) {
        return $this->softDelete($customerId);
    }

    /**
     * Add new address
     */
    public function addAddress($customerId, $data) {
        // If this is marked as default, unset others
        if (isset($data['is_default']) && $data['is_default']) {
            $sql = "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?";
            $this->query($sql, [$customerId]);
        }

        // Check if customer has no addresses, make this default
        $existingAddresses = $this->getAddresses($customerId);
        if (empty($existingAddresses)) {
            $data['is_default'] = 1;
        }

        $data['customer_id'] = $customerId;
        $data['is_active'] = 1;

        $fields = implode(',', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';

        $sql = "INSERT INTO shipping_addresses ({$fields}) VALUES ({$placeholders})";
        return $this->query($sql, array_values($data));
    }

    /**
     * Update address
     */
    public function updateAddress($addressId, $customerId, $data) {
        // Verify address belongs to customer
        $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?";
        $address = $this->fetchOne($sql, [$addressId, $customerId]);

        if (!$address) {
            return false;
        }

        // If this is marked as default, unset others
        if (isset($data['is_default']) && $data['is_default']) {
            $sql = "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?";
            $this->query($sql, [$customerId]);
        }

        $updates = [];
        $params = [];

        foreach ($data as $key => $value) {
            $updates[] = "{$key} = ?";
            $params[] = $value;
        }

        $params[] = $addressId;
        $params[] = $customerId;

        $sql = "UPDATE shipping_addresses SET " . implode(', ', $updates) . " WHERE address_id = ? AND customer_id = ?";
        return $this->query($sql, $params);
    }

    /**
     * Delete address (soft delete)
     */
    public function deleteAddress($addressId, $customerId) {
        // Verify address belongs to customer
        $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?";
        $address = $this->fetchOne($sql, [$addressId, $customerId]);

        if (!$address) {
            return false;
        }

        // Check if this is the only address
        $addresses = $this->getAddresses($customerId);
        if (count($addresses) <= 1) {
            return false; // Cannot delete the only address
        }

        // If deleting default address, set another as default
        if ($address['is_default']) {
            $sql = "SELECT address_id FROM shipping_addresses
                    WHERE customer_id = ? AND address_id != ? AND is_active = 1
                    LIMIT 1";
            $nextAddress = $this->fetchOne($sql, [$customerId, $addressId]);

            if ($nextAddress) {
                $this->setDefaultAddress($nextAddress['address_id'], $customerId);
            }
        }

        // Soft delete
        $sql = "UPDATE shipping_addresses SET is_active = 0 WHERE address_id = ? AND customer_id = ?";
        return $this->query($sql, [$addressId, $customerId]);
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($addressId, $customerId) {
        // Verify address belongs to customer
        $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?";
        $address = $this->fetchOne($sql, [$addressId, $customerId]);

        if (!$address) {
            return false;
        }

        // Unset all defaults for this customer
        $sql = "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?";
        $this->query($sql, [$customerId]);

        // Set new default
        $sql = "UPDATE shipping_addresses SET is_default = 1 WHERE address_id = ? AND customer_id = ?";
        return $this->query($sql, [$addressId, $customerId]);
    }

    /**
     * Get single address by ID
     */
    public function getAddress($addressId, $customerId) {
        $sql = "SELECT * FROM shipping_addresses
                WHERE address_id = ? AND customer_id = ? AND is_active = 1";
        return $this->fetchOne($sql, [$addressId, $customerId]);
    }
}
