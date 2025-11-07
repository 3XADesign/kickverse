<?php
/**
 * Account Page Controller
 * Handles account-related pages (orders, subscriptions, profile)
 */

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/ShippingAddress.php';

class AccountPageController extends Controller {
    private $customerModel;
    private $orderModel;
    private $subscriptionModel;
    private $addressModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->orderModel = new Order();
        $this->addressModel = new ShippingAddress();

        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        }
    }

    /**
     * Orders page - /mi-cuenta/pedidos
     */
    public function orders() {
        $customerId = $_SESSION['user']['customer_id'];
        $customer = $this->customerModel->find($customerId);

        // Get all customer orders
        $orders = $this->orderModel->getCustomerOrders($customerId, 100);

        $this->view('account/orders', [
            'customer' => $customer,
            'orders' => $orders
        ]);
    }

    /**
     * Order detail page - /mi-cuenta/pedidos/:id
     */
    public function orderDetail($orderId) {
        $customerId = $_SESSION['user']['customer_id'];
        $customer = $this->customerModel->find($customerId);

        // Get order with items
        $order = $this->orderModel->getOrderWithItems($orderId);

        // Verify that the order belongs to the customer
        if (!$order || $order['customer_id'] != $customerId) {
            $this->redirect('/mi-cuenta/pedidos');
            return;
        }

        $this->view('account/order-detail', [
            'customer' => $customer,
            'order' => $order
        ]);
    }

    /**
     * Subscriptions page - /mi-cuenta/suscripciones
     */
    public function subscriptions() {
        $customerId = $_SESSION['user']['customer_id'];
        $customer = $this->customerModel->find($customerId);

        // Get customer subscriptions (if subscription model exists)
        $subscriptions = [];
        // TODO: Implement subscription retrieval

        $this->view('account/subscriptions', [
            'customer' => $customer,
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * Subscription detail page - /mi-cuenta/suscripciones/:id
     */
    public function subscriptionDetail($subscriptionId) {
        $customerId = $_SESSION['user']['customer_id'];
        $customer = $this->customerModel->find($customerId);

        // TODO: Get subscription details

        $this->view('account/subscription-detail', [
            'customer' => $customer,
            'subscription' => []
        ]);
    }

    /**
     * Profile page - /mi-cuenta/perfil
     */
    public function profile() {
        $customerId = $_SESSION['user']['customer_id'];
        $customer = $this->customerModel->find($customerId);
        $addresses = $this->addressModel->getByCustomer($customerId);

        $this->view('account/profile', [
            'customer' => $customer,
            'addresses' => $addresses
        ]);
    }

    /**
     * Update profile - POST /mi-cuenta/perfil/actualizar
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        // Get form data
        $fullName = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';

        // Update customer
        $updated = $this->customerModel->update($customerId, [
            'full_name' => $fullName,
            'phone' => $phone
        ]);

        if ($updated) {
            // Update session
            $_SESSION['user']['full_name'] = $fullName;
            $_SESSION['success'] = __('account.profile_updated');
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil');
    }

    /**
     * Update password - POST /mi-cuenta/perfil/cambiar-contrasena
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        // Get form data
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate passwords match
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'Las contraseñas no coinciden' : 'Passwords do not match';
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        // Validate password length
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'La contraseña debe tener al menos 6 caracteres' : 'Password must be at least 6 characters';
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        // Get current customer
        $customer = $this->customerModel->find($customerId);

        // Verify current password
        if (!password_verify($currentPassword, $customer['password'])) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'La contraseña actual es incorrecta' : 'Current password is incorrect';
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        // Update password
        $updated = $this->customerModel->update($customerId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        if ($updated) {
            $_SESSION['success'] = __('account.password_updated');
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil');
    }

    /**
     * Add address - POST /mi-cuenta/perfil/direccion/agregar
     */
    public function addAddress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        $addressData = [
            'customer_id' => $customerId,
            'recipient_name' => $_POST['recipient_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'street_address' => $_POST['street_address'] ?? '',
            'additional_address' => $_POST['additional_address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'province' => $_POST['province'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'country' => $_POST['country'] ?? 'España',
            'additional_notes' => $_POST['additional_notes'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        // If setting as default, unset other defaults
        if ($addressData['is_default']) {
            $this->addressModel->query(
                "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?",
                [$customerId]
            );
        }

        $created = $this->addressModel->createAddress($addressData);

        if ($created) {
            $_SESSION['success'] = i18n::getLang() === 'es' ? 'Dirección añadida correctamente' : 'Address added successfully';
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil#addresses');
    }

    /**
     * Update address - POST /mi-cuenta/perfil/direccion/editar/:id
     */
    public function updateAddressData($addressId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        // Verify address belongs to customer
        $address = $this->addressModel->getAddress($addressId, $customerId);
        if (!$address) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'Dirección no encontrada' : 'Address not found';
            $this->redirect('/mi-cuenta/perfil#addresses');
            return;
        }

        $addressData = [
            'recipient_name' => $_POST['recipient_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'street_address' => $_POST['street_address'] ?? '',
            'additional_address' => $_POST['additional_address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'province' => $_POST['province'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'country' => $_POST['country'] ?? 'España',
            'additional_notes' => $_POST['additional_notes'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        // If setting as default, unset other defaults
        if ($addressData['is_default']) {
            $this->addressModel->query(
                "UPDATE shipping_addresses SET is_default = 0 WHERE customer_id = ?",
                [$customerId]
            );
        }

        $updated = $this->addressModel->updateAddress($addressId, $customerId, $addressData);

        if ($updated) {
            $_SESSION['success'] = i18n::getLang() === 'es' ? 'Dirección actualizada correctamente' : 'Address updated successfully';
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil#addresses');
    }

    /**
     * Delete address - POST /mi-cuenta/perfil/direccion/eliminar/:id
     */
    public function deleteAddress($addressId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        // Verify address belongs to customer
        $address = $this->addressModel->getAddress($addressId, $customerId);
        if (!$address) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'Dirección no encontrada' : 'Address not found';
            $this->redirect('/mi-cuenta/perfil#addresses');
            return;
        }

        $deleted = $this->addressModel->deleteAddress($addressId, $customerId);

        if ($deleted) {
            $_SESSION['success'] = i18n::getLang() === 'es' ? 'Dirección eliminada correctamente' : 'Address deleted successfully';
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil#addresses');
    }

    /**
     * Set default address - POST /mi-cuenta/perfil/direccion/predeterminada/:id
     */
    public function setDefaultAddress($addressId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mi-cuenta/perfil');
            return;
        }

        $customerId = $_SESSION['user']['customer_id'];

        // Verify address belongs to customer
        $address = $this->addressModel->getAddress($addressId, $customerId);
        if (!$address) {
            $_SESSION['error'] = i18n::getLang() === 'es' ? 'Dirección no encontrada' : 'Address not found';
            $this->redirect('/mi-cuenta/perfil#addresses');
            return;
        }

        $updated = $this->addressModel->setAsDefault($addressId, $customerId);

        if ($updated) {
            $_SESSION['success'] = i18n::getLang() === 'es' ? 'Dirección predeterminada actualizada' : 'Default address updated';
        } else {
            $_SESSION['error'] = __('account.error_updating');
        }

        $this->redirect('/mi-cuenta/perfil#addresses');
    }
}
