<?php
/**
 * Customer API Controller
 * Handles customer-related API endpoints
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController extends Controller {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    /**
     * Get customer profile
     */
    public function profile() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $customer = $this->customerModel->find($user['customer_id']);

            if (!$customer) {
                return $this->json(['success' => false, 'message' => 'Customer not found'], 404);
            }

            // Remove sensitive data
            unset($customer['password_hash']);
            unset($customer['password_reset_token']);
            unset($customer['email_verification_token']);

            return $this->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update customer profile
     */
    public function updateProfile() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $data = $this->input();

            // Validate CSRF
            if (!$this->validateCSRF($data['csrf_token'] ?? '')) {
                return $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }

            // Validate data
            $errors = $this->validate($data, [
                'full_name' => 'required|min:3',
                'phone' => 'max:20'
            ]);

            if ($errors !== true) {
                return $this->json(['success' => false, 'errors' => $errors], 400);
            }

            // Prepare update data
            $updateData = [
                'full_name' => trim($data['full_name']),
                'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                'preferred_language' => $data['preferred_language'] ?? 'es'
            ];

            // Update customer
            $this->customerModel->update($user['customer_id'], $updateData);

            return $this->json(['success' => true, 'message' => 'Profile updated successfully']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get customer addresses
     */
    public function addresses() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $addresses = $this->customerModel->getAddresses($user['customer_id']);

            return $this->json([
                'success' => true,
                'addresses' => $addresses
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get single address
     */
    public function getAddress($addressId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $address = $this->customerModel->getAddress($addressId, $user['customer_id']);

            if (!$address) {
                return $this->json(['success' => false, 'message' => 'Address not found'], 404);
            }

            return $this->json([
                'success' => true,
                'address' => $address
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add new address
     */
    public function addAddress() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $data = $this->input();

            // Validate CSRF
            if (!$this->validateCSRF($data['csrf_token'] ?? '')) {
                return $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }

            // Validate required fields
            $requiredFields = ['recipient_name', 'street_address', 'city', 'province', 'postal_code', 'country', 'phone'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->json(['success' => false, 'message' => "Field {$field} is required"], 400);
                }
            }

            // Prepare address data
            $addressData = [
                'recipient_name' => trim($data['recipient_name']),
                'street_address' => trim($data['street_address']),
                'additional_address' => !empty($data['additional_address']) ? trim($data['additional_address']) : null,
                'city' => trim($data['city']),
                'province' => trim($data['province']),
                'postal_code' => trim($data['postal_code']),
                'country' => trim($data['country']),
                'phone' => trim($data['phone']),
                'email' => !empty($data['email']) ? trim($data['email']) : null,
                'additional_notes' => !empty($data['additional_notes']) ? trim($data['additional_notes']) : null,
                'is_default' => isset($data['is_default']) && $data['is_default'] ? 1 : 0
            ];

            // Add address
            $result = $this->customerModel->addAddress($user['customer_id'], $addressData);

            if ($result) {
                return $this->json(['success' => true, 'message' => 'Address added successfully']);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to add address'], 500);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update address
     */
    public function updateAddress($addressId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $data = $this->input();

            // Validate CSRF
            if (!$this->validateCSRF($data['csrf_token'] ?? '')) {
                return $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }

            // Prepare address data
            $addressData = [];
            $allowedFields = ['recipient_name', 'street_address', 'additional_address', 'city', 'province', 'postal_code', 'country', 'phone', 'email', 'additional_notes', 'is_default'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    if ($field === 'is_default') {
                        $addressData[$field] = $data[$field] ? 1 : 0;
                    } else {
                        $addressData[$field] = trim($data[$field]);
                    }
                }
            }

            // Update address
            $result = $this->customerModel->updateAddress($addressId, $user['customer_id'], $addressData);

            if ($result) {
                return $this->json(['success' => true, 'message' => 'Address updated successfully']);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to update address or address not found'], 404);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete address
     */
    public function deleteAddress($addressId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $result = $this->customerModel->deleteAddress($addressId, $user['customer_id']);

            if ($result) {
                return $this->json(['success' => true, 'message' => 'Address deleted successfully']);
            } else {
                return $this->json(['success' => false, 'message' => 'Cannot delete address or address not found'], 400);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($addressId) {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $result = $this->customerModel->setDefaultAddress($addressId, $user['customer_id']);

            if ($result) {
                return $this->json(['success' => true, 'message' => 'Default address updated successfully']);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to set default address or address not found'], 404);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get customer preferences
     */
    public function preferences() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $preferences = $this->customerModel->getPreferences($user['customer_id']);

            return $this->json([
                'success' => true,
                'preferences' => $preferences
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update customer preferences
     */
    public function updatePreferences() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $data = $this->input();

            // Update preferences
            $result = $this->customerModel->updatePreferences($user['customer_id'], $data);

            if ($result) {
                return $this->json(['success' => true, 'message' => 'Preferences updated successfully']);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to update preferences'], 500);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get customer loyalty info
     */
    public function loyalty() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $customer = $this->customerModel->find($user['customer_id']);

            // Get loyalty history
            $sql = "SELECT * FROM loyalty_points_history
                    WHERE customer_id = ?
                    ORDER BY created_at DESC
                    LIMIT 20";
            $history = $this->customerModel->fetchAll($sql, [$user['customer_id']]);

            return $this->json([
                'success' => true,
                'loyalty' => [
                    'tier' => $customer['loyalty_tier'],
                    'points' => $customer['loyalty_points'],
                    'total_spent' => $customer['total_spent'],
                    'total_orders' => $customer['total_orders_count'],
                    'history' => $history
                ]
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
