<?php
/**
 * Shipping Address Model
 */

require_once __DIR__ . '/Model.php';

class ShippingAddress extends Model {
    protected $table = 'shipping_addresses';
    protected $primaryKey = 'address_id';

    /**
     * Get all addresses for a customer
     */
    public function getByCustomer($customerId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE customer_id = ? AND is_active = 1
                ORDER BY is_default DESC, created_at DESC";
        return $this->fetchAll($sql, [$customerId]);
    }

    /**
     * Get a specific address
     */
    public function getAddress($addressId, $customerId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE address_id = ? AND customer_id = ? AND is_active = 1";
        return $this->fetchOne($sql, [$addressId, $customerId]);
    }

    /**
     * Create a new address
     */
    public function createAddress($data) {
        $sql = "INSERT INTO {$this->table}
                (customer_id, recipient_name, phone, email, street_address,
                 additional_address, city, province, postal_code, country,
                 additional_notes, is_default)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->query($sql, [
            $data['customer_id'],
            $data['recipient_name'],
            $data['phone'],
            $data['email'] ?? null,
            $data['street_address'],
            $data['additional_address'] ?? null,
            $data['city'],
            $data['province'],
            $data['postal_code'],
            $data['country'] ?? 'España',
            $data['additional_notes'] ?? null,
            $data['is_default'] ?? 0
        ]);
    }

    /**
     * Update an address
     */
    public function updateAddress($addressId, $customerId, $data) {
        $sql = "UPDATE {$this->table}
                SET recipient_name = ?, phone = ?, email = ?, street_address = ?,
                    additional_address = ?, city = ?, province = ?, postal_code = ?,
                    country = ?, additional_notes = ?, is_default = ?
                WHERE address_id = ? AND customer_id = ?";

        return $this->query($sql, [
            $data['recipient_name'],
            $data['phone'],
            $data['email'] ?? null,
            $data['street_address'],
            $data['additional_address'] ?? null,
            $data['city'],
            $data['province'],
            $data['postal_code'],
            $data['country'] ?? 'España',
            $data['additional_notes'] ?? null,
            $data['is_default'] ?? 0,
            $addressId,
            $customerId
        ]);
    }

    /**
     * Soft delete an address
     */
    public function deleteAddress($addressId, $customerId) {
        $sql = "UPDATE {$this->table}
                SET is_active = 0
                WHERE address_id = ? AND customer_id = ?";

        return $this->query($sql, [$addressId, $customerId]);
    }

    /**
     * Set an address as default
     */
    public function setAsDefault($addressId, $customerId) {
        // First, unset all defaults for this customer
        $sql1 = "UPDATE {$this->table}
                 SET is_default = 0
                 WHERE customer_id = ?";
        $this->query($sql1, [$customerId]);

        // Then set the new default
        $sql2 = "UPDATE {$this->table}
                 SET is_default = 1
                 WHERE address_id = ? AND customer_id = ?";
        return $this->query($sql2, [$addressId, $customerId]);
    }

    /**
     * Get default address for a customer
     */
    public function getDefault($customerId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE customer_id = ? AND is_default = 1 AND is_active = 1
                LIMIT 1";
        return $this->fetchOne($sql, [$customerId]);
    }
}
