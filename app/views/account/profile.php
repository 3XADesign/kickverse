<?php
/**
 * Profile Page
 * Allows customer to update their profile information
 */
?>

<style>
    .profile-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    }

    .profile-page ~ .footer {
        margin-top: 0;
    }

    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .profile-header {
        margin-bottom: 2rem;
    }

    .profile-header h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .profile-header p {
        color: var(--gray-600);
        font-size: 1rem;
    }

    .profile-sections {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .profile-section {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid var(--gray-100);
        padding: 2rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .section-header-with-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .section-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-header i {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .section-header-left i {
        font-size: 1.5rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-header-left h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.2s;
        background: white;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(176, 84, 233, 0.1);
    }

    .form-group input:disabled {
        background: var(--gray-50);
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-update {
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    .btn-update:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert i {
        font-size: 1.25rem;
    }

    .alert.success {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 2px solid rgba(34, 197, 94, 0.2);
    }

    .alert.error {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        border: 2px solid rgba(220, 38, 38, 0.2);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-item {
        background: var(--gray-50);
        padding: 1.25rem;
        border-radius: var(--radius-md);
        border: 2px solid var(--gray-100);
    }

    .info-label {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: var(--gray-900);
        font-weight: 700;
        font-size: 1.25rem;
    }

    .address-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .address-card {
        background: var(--gray-50);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        position: relative;
        transition: all 0.2s;
    }

    .address-card:hover {
        border-color: var(--primary);
        background: white;
    }

    .address-card.default {
        border-color: var(--primary);
        background: rgba(176, 84, 233, 0.05);
    }

    .address-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .address-content {
        margin-bottom: 1rem;
    }

    .address-name {
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        font-size: 1.125rem;
    }

    .address-details {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .address-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-address {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-address.edit {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-address.edit:hover {
        background: var(--primary);
        color: white;
    }

    .btn-address.delete {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }

    .btn-address.delete:hover {
        background: #dc2626;
        color: white;
    }

    .btn-address.set-default {
        background: rgba(176, 84, 233, 0.1);
        color: var(--primary);
    }

    .btn-address.set-default:hover {
        background: var(--primary);
        color: white;
    }

    .btn-add-address {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-add-address:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    .btn-add-address i {
        font-size: 0.875rem;
    }

    .empty-addresses {
        text-align: center;
        padding: 3rem 2rem;
        background: var(--gray-50);
        border-radius: var(--radius-md);
        border: 2px dashed var(--gray-300);
    }

    .empty-addresses i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 1rem;
    }

    .empty-addresses h3 {
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .empty-addresses p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-lg);
        padding: 0;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2rem 2rem 1rem 2rem;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        border-bottom: 2px solid var(--gray-100);
        flex-shrink: 0;
    }

    .modal-body {
        padding: 2rem;
        flex: 1;
        overflow-y: auto;
    }

    .modal-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--gray-600);
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: var(--gray-900);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .checkbox-group label {
        margin: 0;
        font-weight: 500;
    }

    .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 1.5rem 2rem;
        border-top: 2px solid var(--gray-100);
        z-index: 10;
        flex-shrink: 0;
    }

    /* Confirm Modal */
    .confirm-modal-content {
        background: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        max-width: 400px;
        width: 90%;
    }

    .confirm-modal-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1.5rem;
        background: rgba(220, 38, 38, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .confirm-modal-icon i {
        font-size: 2rem;
        color: #dc2626;
    }

    .confirm-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .confirm-modal-message {
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 0.875rem;
    }

    .confirm-modal-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn-confirm-delete {
        flex: 1;
        padding: 0.875rem;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-confirm-delete:hover {
        background: #b91c1c;
    }

    .btn-confirm-cancel {
        flex: 1;
        padding: 0.875rem;
        background: var(--gray-200);
        color: var(--gray-700);
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-confirm-cancel:hover {
        background: var(--gray-300);
    }

    @media (max-width: 768px) {
        .profile-header h1 {
            font-size: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-section {
            padding: 1.5rem;
        }

        .address-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1><?= __('account.profile') ?></h1>
            <p><?= __('account.profile_subtitle') ?></p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span><?= $_SESSION['success'] ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $_SESSION['error'] ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="profile-sections">
            <!-- Account Overview -->
            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-user-circle"></i>
                    <h2><?= __('account.account_overview') ?></h2>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><?= __('account.loyalty_tier') ?></div>
                        <div class="info-value"><?= ucfirst($customer['loyalty_tier'] ?? 'Bronze') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><?= __('account.loyalty_points') ?></div>
                        <div class="info-value"><?= number_format($customer['loyalty_points'] ?? 0) ?></div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-id-card"></i>
                    <h2><?= __('account.personal_info') ?></h2>
                </div>

                <form action="/mi-cuenta/perfil/actualizar" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name"><?= __('account.full_name') ?></label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?= htmlspecialchars($customer['full_name']) ?>"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label for="email"><?= __('account.email') ?></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($customer['email']) ?>"
                                disabled
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone"><?= __('account.phone_optional') ?></label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                        >
                    </div>

                    <button type="submit" class="btn-update">
                        <i class="fas fa-save"></i>
                        <?= __('account.save_changes') ?>
                    </button>
                </form>
            </div>

            <!-- Shipping Addresses -->
            <div class="profile-section" id="addresses">
                <div class="section-header-with-action">
                    <div class="section-header-left">
                        <i class="fas fa-map-marker-alt"></i>
                        <h2><?= __('account.addresses') ?></h2>
                    </div>
                    <button onclick="openAddressModal()" class="btn-add-address">
                        <i class="fas fa-plus"></i> <?= __('account.add_address') ?>
                    </button>
                </div>

                <?php if (!empty($addresses)): ?>
                    <div class="address-list">
                        <?php foreach ($addresses as $address): ?>
                            <div class="address-card <?= $address['is_default'] ? 'default' : '' ?>">
                                <?php if ($address['is_default']): ?>
                                    <div class="address-badge"><?= __('account.default_address') ?></div>
                                <?php endif; ?>

                                <div class="address-content">
                                    <div class="address-name"><?= htmlspecialchars($address['recipient_name']) ?></div>
                                    <div class="address-details">
                                        <?= htmlspecialchars($address['street_address']) ?><br>
                                        <?php if ($address['additional_address']): ?>
                                            <?= htmlspecialchars($address['additional_address']) ?><br>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($address['postal_code']) ?> <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['province']) ?><br>
                                        <?= htmlspecialchars($address['country']) ?><br>
                                        <?= htmlspecialchars($address['phone']) ?>
                                    </div>
                                </div>

                                <div class="address-actions">
                                    <button onclick="editAddress(<?= $address['address_id'] ?>)" class="btn-address edit">
                                        <i class="fas fa-edit"></i> <?= __('account.edit_address') ?>
                                    </button>
                                    <?php if (!$address['is_default']): ?>
                                        <form method="POST" action="/mi-cuenta/perfil/direccion/predeterminada/<?= $address['address_id'] ?>" style="display: inline;">
                                            <button type="submit" class="btn-address set-default">
                                                <i class="fas fa-star"></i> <?= __('account.set_default') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" onclick="confirmDeleteAddress(<?= $address['address_id'] ?>)" class="btn-address delete">
                                        <i class="fas fa-trash"></i> <?= __('account.delete_address') ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-addresses">
                        <i class="fas fa-map-marked-alt"></i>
                        <h3><?= __('account.no_addresses_title') ?></h3>
                        <p><?= __('account.no_addresses_message') ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Change Password -->
            <div class="profile-section">
                <div class="section-header">
                    <i class="fas fa-lock"></i>
                    <h2><?= __('account.change_password') ?></h2>
                </div>

                <form action="/mi-cuenta/perfil/cambiar-contrasena" method="POST">
                    <div class="form-group">
                        <label for="current_password"><?= __('account.current_password') ?></label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password"><?= __('account.new_password') ?></label>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                minlength="6"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label for="confirm_password"><?= __('account.confirm_password') ?></label>
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-update">
                        <i class="fas fa-key"></i>
                        <?= __('account.update_password') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div id="addressModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><?= __('account.add_address') ?></h3>
            <button class="modal-close" onclick="closeAddressModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="addressForm" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient_name"><?= __('account.recipient_name') ?></label>
                    <input type="text" id="recipient_name" name="recipient_name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address_phone"><?= __('account.phone') ?></label>
                        <input type="tel" id="address_phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="address_email"><?= __('account.email') ?></label>
                        <input type="email" id="address_email" name="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="street_address"><?= __('account.street_address') ?></label>
                    <input type="text" id="street_address" name="street_address" required>
                </div>

                <div class="form-group">
                    <label for="additional_address"><?= __('account.additional_address') ?></label>
                    <input type="text" id="additional_address" name="additional_address">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code"><?= __('account.postal_code') ?></label>
                        <input type="text" id="postal_code" name="postal_code" required>
                    </div>
                    <div class="form-group">
                        <label for="city"><?= __('account.city') ?></label>
                        <input type="text" id="city" name="city" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="province"><?= __('account.province') ?></label>
                        <input type="text" id="province" name="province" required>
                    </div>
                    <div class="form-group">
                        <label for="country"><?= __('account.country') ?></label>
                        <input type="text" id="country" name="country" value="España" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="additional_notes"><?= __('account.additional_notes') ?></label>
                    <input type="text" id="additional_notes" name="additional_notes">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_default" name="is_default" value="1">
                    <label for="is_default"><?= __('account.is_default_address') ?></label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-update" style="width: 100%;">
                    <i class="fas fa-save"></i>
                    <?= __('account.save_changes') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal" class="modal">
    <div class="confirm-modal-content">
        <div class="confirm-modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="confirm-modal-title"><?= __('account.confirm_delete_title') ?></h3>
        <p class="confirm-modal-message">
            <?= __('account.confirm_delete_address') ?><br>
            <?= __('account.confirm_delete_message') ?>
        </p>
        <div class="confirm-modal-buttons">
            <button onclick="closeConfirmDeleteModal()" class="btn-confirm-cancel">
                <?= __('account.cancel_button') ?>
            </button>
            <button onclick="executeDeleteAddress()" class="btn-confirm-delete">
                <i class="fas fa-trash"></i>
                <?= __('account.confirm_delete_button') ?>
            </button>
        </div>
    </div>
</div>

<script>
let editingAddressId = null;
let deletingAddressId = null;
const addresses = <?= json_encode($addresses ?? []) ?>;

function openAddressModal(addressId = null) {
    const modal = document.getElementById('addressModal');
    const form = document.getElementById('addressForm');
    const modalTitle = document.getElementById('modalTitle');

    if (addressId) {
        // Edit mode
        editingAddressId = addressId;
        const address = addresses.find(a => a.address_id == addressId);
        if (address) {
            modalTitle.textContent = '<?= __('account.edit_address') ?>';
            form.action = `/mi-cuenta/perfil/direccion/editar/${addressId}`;

            // Fill form
            document.getElementById('recipient_name').value = address.recipient_name;
            document.getElementById('address_phone').value = address.phone;
            document.getElementById('address_email').value = address.email || '';
            document.getElementById('street_address').value = address.street_address;
            document.getElementById('additional_address').value = address.additional_address || '';
            document.getElementById('postal_code').value = address.postal_code;
            document.getElementById('city').value = address.city;
            document.getElementById('province').value = address.province;
            document.getElementById('country').value = address.country;
            document.getElementById('additional_notes').value = address.additional_notes || '';
            document.getElementById('is_default').checked = address.is_default == 1;
        }
    } else {
        // Add mode
        editingAddressId = null;
        modalTitle.textContent = '<?= __('account.add_address') ?>';
        form.action = '/mi-cuenta/perfil/direccion/agregar';
        form.reset();
        document.getElementById('country').value = 'España';
    }

    modal.classList.add('active');
}

function editAddress(addressId) {
    openAddressModal(addressId);
}

function closeAddressModal() {
    const modal = document.getElementById('addressModal');
    modal.classList.remove('active');
    editingAddressId = null;
}

// Close modal on outside click
document.getElementById('addressModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddressModal();
    }
});

// Confirm delete functions
function confirmDeleteAddress(addressId) {
    deletingAddressId = addressId;
    const modal = document.getElementById('confirmDeleteModal');
    modal.classList.add('active');
}

function closeConfirmDeleteModal() {
    const modal = document.getElementById('confirmDeleteModal');
    modal.classList.remove('active');
    deletingAddressId = null;
}

function executeDeleteAddress() {
    if (deletingAddressId) {
        // Create and submit a form to delete the address
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/mi-cuenta/perfil/direccion/eliminar/${deletingAddressId}`;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close confirm modal on outside click
document.getElementById('confirmDeleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmDeleteModal();
    }
});
</script>
