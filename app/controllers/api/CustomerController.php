<?php
/**
 * Customer API Controller
 * Handles customer profile and preferences
 */

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../models/Customer.php';

class CustomerController extends Controller {
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
    }

    /**
     * GET /api/customer/profile
     * Get customer profile
     */
    public function profile() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $customer = $this->customerModel->find($user['customer_id']);

            // Remove sensitive data
            unset($customer['password_hash']);
            unset($customer['email_verification_token']);

            $this->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el perfil'
            ], 500);
        }
    }

    /**
     * PUT /api/customer/profile
     * Update customer profile
     */
    public function updateProfile() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            // Allowed fields to update
            $updateData = [];
            $allowedFields = ['full_name', 'phone', 'preferred_language', 'newsletter_subscribed'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if (empty($updateData)) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay datos para actualizar'
                ], 400);
            }

            $this->customerModel->update($user['customer_id'], $updateData);

            $this->json([
                'success' => true,
                'message' => 'Perfil actualizado'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ], 500);
        }
    }

    /**
     * GET /api/customer/addresses
     * Get customer shipping addresses
     */
    public function addresses() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $addresses = $this->customerModel->getAddresses($user['customer_id']);

            $this->json([
                'success' => true,
                'data' => $addresses
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar las direcciones'
            ], 500);
        }
    }

    /**
     * POST /api/customer/addresses
     * Add new shipping address
     */
    public function addAddress() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        // Validate
        $errors = $this->validate($data, [
            'recipient_name' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'country' => 'required',
            'phone' => 'required'
        ]);

        if ($errors !== true) {
            $this->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        try {
            $addressData = [
                'customer_id' => $user['customer_id'],
                'recipient_name' => $data['recipient_name'],
                'street_address' => $data['street_address'],
                'apartment_suite' => $data['apartment_suite'] ?? null,
                'city' => $data['city'],
                'state_province' => $data['state_province'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
                'phone' => $data['phone'],
                'is_default' => isset($data['is_default']) ? 1 : 0,
                'is_active' => 1
            ];

            // If this is default, unset other defaults
            if ($addressData['is_default']) {
                $sql = "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?";
                $this->customerModel->query($sql, [$user['customer_id']]);
            }

            $fields = implode(',', array_keys($addressData));
            $placeholders = str_repeat('?,', count($addressData) - 1) . '?';
            $sql = "INSERT INTO shipping_addresses ({$fields}) VALUES ({$placeholders})";
            $this->customerModel->query($sql, array_values($addressData));

            $this->json([
                'success' => true,
                'message' => 'Dirección añadida'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al añadir la dirección'
            ], 500);
        }
    }

    /**
     * PUT /api/customer/addresses/:id
     * Update shipping address
     */
    public function updateAddress($addressId) {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            // Verify address belongs to customer
            $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?";
            $address = $this->customerModel->fetchOne($sql, [$addressId, $user['customer_id']]);

            if (!$address) {
                $this->json([
                    'success' => false,
                    'message' => 'Dirección no encontrada'
                ], 404);
            }

            // Allowed fields to update
            $updateData = [];
            $allowedFields = ['recipient_name', 'street_address', 'apartment_suite', 'city',
                            'state_province', 'postal_code', 'country', 'phone', 'is_default'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if (empty($updateData)) {
                $this->json([
                    'success' => false,
                    'message' => 'No hay datos para actualizar'
                ], 400);
            }

            // If setting as default, unset other defaults
            if (isset($updateData['is_default']) && $updateData['is_default']) {
                $sql = "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?";
                $this->customerModel->query($sql, [$user['customer_id']]);
            }

            $updates = [];
            $params = [];
            foreach ($updateData as $key => $value) {
                $updates[] = "{$key} = ?";
                $params[] = $value;
            }
            $params[] = $addressId;

            $sql = "UPDATE shipping_addresses SET " . implode(', ', $updates) . " WHERE address_id = ?";
            $this->customerModel->query($sql, $params);

            $this->json([
                'success' => true,
                'message' => 'Dirección actualizada'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar la dirección'
            ], 500);
        }
    }

    /**
     * DELETE /api/customer/addresses/:id
     * Delete shipping address (soft delete)
     */
    public function deleteAddress($addressId) {
        $this->requireAuth();

        $user = $this->getUser();

        try {
            // Verify address belongs to customer
            $sql = "SELECT * FROM shipping_addresses WHERE address_id = ? AND customer_id = ?";
            $address = $this->customerModel->fetchOne($sql, [$addressId, $user['customer_id']]);

            if (!$address) {
                $this->json([
                    'success' => false,
                    'message' => 'Dirección no encontrada'
                ], 404);
            }

            // Soft delete
            $sql = "UPDATE shipping_addresses SET is_active = 0 WHERE address_id = ?";
            $this->customerModel->query($sql, [$addressId]);

            $this->json([
                'success' => true,
                'message' => 'Dirección eliminada'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al eliminar la dirección'
            ], 500);
        }
    }

    /**
     * GET /api/customer/preferences
     * Get customer preferences
     */
    public function preferences() {
        $this->requireAuth();

        try {
            $user = $this->getUser();
            $preferences = $this->customerModel->getPreferences($user['customer_id']);

            // Decode JSON fields
            if ($preferences) {
                if (isset($preferences['favorite_teams'])) {
                    $preferences['favorite_teams'] = json_decode($preferences['favorite_teams'], true);
                }
                if (isset($preferences['favorite_leagues'])) {
                    $preferences['favorite_leagues'] = json_decode($preferences['favorite_leagues'], true);
                }
            }

            $this->json([
                'success' => true,
                'data' => $preferences ?? []
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar las preferencias'
            ], 500);
        }
    }

    /**
     * PUT /api/customer/preferences
     * Update customer preferences
     */
    public function updatePreferences() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            $this->customerModel->updatePreferences($user['customer_id'], $data);

            $this->json([
                'success' => true,
                'message' => 'Preferencias actualizadas'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar las preferencias'
            ], 500);
        }
    }

    /**
     * GET /api/customer/loyalty
     * Get loyalty points history
     */
    public function loyalty() {
        $this->requireAuth();

        try {
            $user = $this->getUser();

            $sql = "SELECT * FROM loyalty_points_history
                    WHERE customer_id = ?
                    ORDER BY transaction_date DESC
                    LIMIT 50";
            $history = $this->customerModel->fetchAll($sql, [$user['customer_id']]);

            $this->json([
                'success' => true,
                'data' => [
                    'current_points' => $user['loyalty_points'],
                    'tier' => $user['loyalty_tier'],
                    'history' => $history
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al cargar el historial de puntos'
            ], 500);
        }
    }

    /**
     * POST /api/account/update-profile
     * Update customer profile (API endpoint)
     */
    public function updateProfileAPI() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            // Validate
            $errors = $this->validate($data, [
                'full_name' => 'required|min:3',
                'phone' => 'max:20'
            ]);

            if ($errors !== true) {
                $this->json([
                    'success' => false,
                    'errors' => $errors
                ], 400);
                return;
            }

            // Prepare update data
            $updateData = [
                'full_name' => trim($data['full_name']),
                'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                'whatsapp_number' => !empty($data['whatsapp_number']) ? trim($data['whatsapp_number']) : null,
                'telegram_username' => !empty($data['telegram_username']) ? trim($data['telegram_username']) : null,
                'preferred_language' => $data['preferred_language'] ?? 'es'
            ];

            // Update customer
            $this->customerModel->update($user['customer_id'], $updateData);

            // Update session data
            $_SESSION['user']['full_name'] = $updateData['full_name'];

            $this->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/account/update-password
     * Update customer password (API endpoint)
     */
    public function updatePasswordAPI() {
        $this->requireAuth();

        $data = $this->input();
        $user = $this->getUser();

        try {
            // Validate
            if (empty($data['current_password'])) {
                $this->json([
                    'success' => false,
                    'message' => 'Debes ingresar tu contraseña actual'
                ], 400);
                return;
            }

            if (empty($data['new_password']) || strlen($data['new_password']) < 6) {
                $this->json([
                    'success' => false,
                    'message' => 'La nueva contraseña debe tener al menos 6 caracteres'
                ], 400);
                return;
            }

            if ($data['new_password'] !== $data['confirm_password']) {
                $this->json([
                    'success' => false,
                    'message' => 'Las contraseñas no coinciden'
                ], 400);
                return;
            }

            // Get customer
            $customer = $this->customerModel->find($user['customer_id']);

            // Check if customer has password (social login users might not)
            if (empty($customer['password_hash'])) {
                $this->json([
                    'success' => false,
                    'message' => 'No puedes cambiar la contraseña porque iniciaste sesión con redes sociales'
                ], 400);
                return;
            }

            // Verify current password
            if (!password_verify($data['current_password'], $customer['password_hash'])) {
                $this->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 400);
                return;
            }

            // Update password
            $newPasswordHash = password_hash($data['new_password'], PASSWORD_BCRYPT, ['cost' => 10]);
            $this->customerModel->update($user['customer_id'], [
                'password_hash' => $newPasswordHash
            ]);

            $this->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error al actualizar la contraseña: ' . $e->getMessage()
            ], 500);
        }
    }
}
