<style>
/* Checkout Page Styles */
.checkout-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    padding: var(--space-8) 0;
}

.checkout-page ~ .footer {
    margin-top: 0;
}

.checkout-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--space-4);
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-bottom: var(--space-6);
    font-size: 0.875rem;
    color: var(--gray-600);
}

.breadcrumb a {
    color: var(--gray-600);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb a:hover {
    color: var(--primary);
}

.breadcrumb i {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.breadcrumb .current {
    color: var(--primary);
    font-weight: 600;
}

/* Page Header */
.checkout-header {
    text-align: center;
    margin-bottom: var(--space-8);
}

.checkout-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #b054e9, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: var(--space-2);
}

.checkout-header p {
    color: var(--gray-600);
    font-size: 1.125rem;
}

/* Checkout Grid */
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: var(--space-6);
    align-items: start;
}

/* Checkout Section */
.checkout-section {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid var(--gray-100);
    padding: var(--space-6);
    margin-top: 0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    margin-bottom: var(--space-5);
    padding-bottom: var(--space-4);
    border-bottom: 2px solid var(--gray-100);
}

.section-title i {
    font-size: 1.5rem;
    background: linear-gradient(135deg, #b054e9, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-title h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

/* Personal Info Display */
.info-display {
    background: var(--gray-50);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: var(--space-4);
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-2) 0;
}

.info-row:not(:last-child) {
    border-bottom: 1px solid var(--gray-200);
}

.info-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
}

.info-value {
    color: var(--gray-900);
    font-weight: 600;
    font-size: 0.9375rem;
}

/* Address Selection */
.addresses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-4);
    margin-bottom: var(--space-5);
}

.address-card {
    position: relative;
    background: var(--gray-50);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: var(--space-4);
    cursor: pointer;
    transition: all 0.2s;
}

.address-card:hover {
    border-color: var(--primary);
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(176, 84, 233, 0.15);
}

.address-card input[type="radio"] {
    position: absolute;
    top: var(--space-3);
    right: var(--space-3);
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.address-card.selected {
    border-color: var(--primary);
    background: rgba(176, 84, 233, 0.05);
    box-shadow: 0 4px 12px rgba(176, 84, 233, 0.2);
}

.address-badge {
    position: absolute;
    top: var(--space-3);
    left: var(--space-3);
    background: linear-gradient(135deg, #b054e9, #ec4899);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.address-name {
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
    margin-top: var(--space-1);
    font-size: 1.0625rem;
}

.address-details {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.6;
}

/* New Address Form */
.new-address-section {
    margin-top: var(--space-5);
}

.btn-add-address {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    background: white;
    color: var(--primary);
    border: 2px dashed var(--primary);
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
}

.btn-add-address:hover {
    background: rgba(176, 84, 233, 0.05);
    transform: translateY(-2px);
}

.btn-add-address i {
    font-size: 1rem;
}

.new-address-form {
    display: none;
    margin-top: var(--space-4);
    padding: var(--space-5);
    background: var(--gray-50);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
}

.new-address-form.active {
    display: block;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-4);
}

.form-group {
    margin-bottom: var(--space-4);
}

.form-group label {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
    font-size: 0.875rem;
}

.form-group label i {
    color: var(--primary);
    font-size: 0.875rem;
}

.form-group label .required {
    color: #dc2626;
    margin-left: 0.25rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.2s;
    background: white;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(176, 84, 233, 0.1);
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-top: var(--space-4);
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-group label {
    margin: 0;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
}

/* Empty State */
.empty-addresses {
    text-align: center;
    padding: var(--space-8) var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 2px dashed var(--gray-300);
}

.empty-addresses i {
    font-size: 3rem;
    color: var(--gray-400);
    margin-bottom: var(--space-4);
}

.empty-addresses h3 {
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.empty-addresses p {
    color: var(--gray-600);
    margin-bottom: var(--space-5);
}

/* Continue Button */
.btn-continue {
    width: 100%;
    padding: var(--space-4);
    background: linear-gradient(135deg, #b054e9, #ec4899);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1.125rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    margin-top: var(--space-6);
}

.btn-continue:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(176, 84, 233, 0.4);
}

.btn-continue:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Order Summary Sidebar */
.order-summary {
    position: static;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid var(--gray-100);
    padding: var(--space-5);
}

.summary-title {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-bottom: var(--space-5);
    padding-bottom: var(--space-4);
    border-bottom: 2px solid var(--gray-100);
}

.summary-title i {
    font-size: 1.25rem;
    color: #f479d9;
}

.summary-title h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-3) 0;
    font-size: 0.9375rem;
    color: var(--gray-700);
}

.summary-line:not(:last-child) {
    border-bottom: 1px solid var(--gray-200);
}

.summary-value {
    font-weight: 600;
    color: var(--gray-900);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-4) 0;
    margin-top: var(--space-4);
    border-top: 2px solid var(--gray-200);
    font-weight: 700;
}

.total-label {
    font-size: 1.125rem;
    color: var(--gray-900);
}

.total-value {
    font-size: 1.5rem;
    background: linear-gradient(135deg, #b054e9, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.free-badge {
    background: #22c55e;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 700;
}

/* Alert */
.alert {
    padding: var(--space-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-5);
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.alert i {
    font-size: 1.25rem;
}

.alert.error {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
    border: 2px solid rgba(220, 38, 38, 0.2);
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-grid {
        display: flex;
        flex-direction: column;
    }

    .checkout-grid > * {
        width: 100%;
        max-width: 100%;
    }

    .checkout-grid > div:first-child {
        order: 1;
    }

    .order-summary {
        position: static;
        order: 2;
    }

    .btn-continue {
        order: 3;
        margin-top: var(--space-4);
        width: 100%;
    }
}

@media (max-width: 768px) {
    .checkout-header h1 {
        font-size: 2rem;
    }

    .checkout-section {
        padding: var(--space-5);
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .addresses-grid {
        grid-template-columns: 1fr;
    }

    .breadcrumb {
        font-size: 0.8125rem;
    }
}
</style>

<div class="checkout-page">
    <div class="checkout-container">
        <!-- Progress Bar -->
        <div class="checkout-progress">
            <div class="progress-steps">
                <div class="progress-step active">
                    <div class="progress-step-circle">1</div>
                    <div class="progress-step-label"><?= __('checkout.step_1_short') ?></div>
                </div>
                <div class="progress-step">
                    <div class="progress-step-circle">2</div>
                    <div class="progress-step-label"><?= __('checkout.step_2_short') ?></div>
                </div>
                <div class="progress-step">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label"><?= __('checkout.step_3_short') ?></div>
                </div>
            </div>
        </div>

        <div class="checkout-header">
            <h1><?= __('checkout.step_1_title') ?></h1>
        </div>

        <!-- Checkout Grid -->
        <div class="checkout-grid">
            <!-- Main Content -->
            <div>
                <form id="checkoutForm" action="/checkout/procesar-paso-2" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Personal Information -->
                    <div class="checkout-section">
                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            <h2><?= __('checkout.personal_info') ?></h2>
                        </div>

                        <div class="info-display">
                            <div class="info-row">
                                <span class="info-label"><?= __('account.full_name') ?></span>
                                <span class="info-value"><?= htmlspecialchars($customer['full_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><?= __('account.email') ?></span>
                                <span class="info-value"><?= htmlspecialchars($customer['email']) ?></span>
                            </div>
                            <?php if (!empty($customer['phone'])): ?>
                                <div class="info-row">
                                    <span class="info-label"><?= __('account.phone') ?></span>
                                    <span class="info-value"><?= htmlspecialchars($customer['phone']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="checkout-section">
                        <div class="section-title">
                            <i class="fas fa-map-marker-alt"></i>
                            <h2><?= __('checkout.shipping_address') ?></h2>
                        </div>

                        <?php if (!empty($addresses)): ?>
                            <!-- Existing Addresses -->
                            <div class="addresses-grid">
                                <?php foreach ($addresses as $index => $address): ?>
                                    <label class="address-card <?= $address['is_default'] ? 'selected' : '' ?>" data-address-id="<?= $address['address_id'] ?>">
                                        <?php if ($address['is_default']): ?>
                                            <span class="address-badge"><?= __('account.default_address') ?></span>
                                        <?php endif; ?>
                                        <input
                                            type="radio"
                                            name="address_id"
                                            value="<?= $address['address_id'] ?>"
                                            <?= $address['is_default'] ? 'checked' : '' ?>
                                            required
                                        >
                                        <div class="address-name"><?= htmlspecialchars($address['recipient_name']) ?></div>
                                        <div class="address-details">
                                            <?= htmlspecialchars($address['street_address']) ?><br>
                                            <?php if ($address['additional_address']): ?>
                                                <?= htmlspecialchars($address['additional_address']) ?><br>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($address['postal_code']) ?> <?= htmlspecialchars($address['city']) ?><br>
                                            <?= htmlspecialchars($address['province']) ?>, <?= htmlspecialchars($address['country']) ?><br>
                                            <i class="fas fa-phone"></i> <?= htmlspecialchars($address['phone']) ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <!-- Add New Address Button -->
                            <div class="new-address-section">
                                <button type="button" class="btn-add-address" id="toggleNewAddressBtn">
                                    <i class="fas fa-plus"></i>
                                    <?= __('checkout.add_new_address') ?>
                                </button>

                                <!-- New Address Form (Hidden by default) -->
                                <div class="new-address-form" id="newAddressForm">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-user"></i>
                                                <?= __('account.recipient_name') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" name="new_recipient_name" id="new_recipient_name" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-phone"></i>
                                                <?= __('account.phone') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="tel" name="new_phone" id="new_phone" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= __('account.street_address') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_street_address" id="new_street_address" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-building"></i>
                                            <?= __('account.additional_address') ?>
                                        </label>
                                        <input type="text" name="new_additional_address" id="new_additional_address" disabled>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-mail-bulk"></i>
                                                <?= __('account.postal_code') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" name="new_postal_code" id="new_postal_code" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-city"></i>
                                                <?= __('account.city') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" name="new_city" id="new_city" disabled>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-map"></i>
                                                <?= __('account.province') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" name="new_province" id="new_province" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-flag"></i>
                                                <?= __('account.country') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <input type="text" name="new_country" id="new_country" value="España" disabled>
                                        </div>
                                    </div>

                                    <div class="checkbox-group">
                                        <input type="checkbox" name="save_address" id="save_address" value="1">
                                        <label for="save_address"><?= __('checkout.save_address') ?></label>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- No Addresses - Show Form -->
                            <div class="empty-addresses">
                                <i class="fas fa-map-marked-alt"></i>
                                <h3><?= __('checkout.no_addresses') ?></h3>
                                <p><?= __('account.no_addresses_message') ?></p>
                            </div>

                            <!-- New Address Form (Visible by default) -->
                            <div class="new-address-form active" style="margin-top: var(--space-5);">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-user"></i>
                                            <?= __('account.recipient_name') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_recipient_name" id="new_recipient_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-phone"></i>
                                            <?= __('account.phone') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="tel" name="new_phone" id="new_phone" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= __('account.street_address') ?>
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" name="new_street_address" id="new_street_address" required>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-building"></i>
                                        <?= __('account.additional_address') ?>
                                    </label>
                                    <input type="text" name="new_additional_address" id="new_additional_address">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-mail-bulk"></i>
                                            <?= __('account.postal_code') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_postal_code" id="new_postal_code" required>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-city"></i>
                                            <?= __('account.city') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_city" id="new_city" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-map"></i>
                                            <?= __('account.province') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_province" id="new_province" required>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-flag"></i>
                                            <?= __('account.country') ?>
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" name="new_country" id="new_country" value="España" required>
                                    </div>
                                </div>

                                <div class="checkbox-group">
                                    <input type="checkbox" name="save_address" id="save_address" value="1" checked>
                                    <label for="save_address"><?= __('checkout.save_address') ?></label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="order-summary">
                <div class="summary-title">
                    <i class="fas fa-shopping-cart"></i>
                    <h3><?= __('checkout.order_summary') ?></h3>
                </div>

                <div class="summary-line">
                    <span><?= __('checkout.subtotal') ?></span>
                    <span class="summary-value">€<?= number_format($subtotal ?? 0, 2) ?></span>
                </div>

                <?php
                $subtotalAmount = $subtotal ?? 0;
                $freeShippingThreshold = 50;
                $remainingForFreeShipping = $freeShippingThreshold - $subtotalAmount;
                ?>

                <?php if ($remainingForFreeShipping > 0): ?>
                    <!-- Free Shipping Progress -->
                    <div class="free-shipping-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= min(($subtotalAmount / $freeShippingThreshold) * 100, 100) ?>%"></div>
                        </div>
                        <p class="progress-text">
                            <?= str_replace('{amount}', '€' . number_format($remainingForFreeShipping, 2), __('checkout.free_shipping_progress')) ?>
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Free Shipping Unlocked -->
                    <div class="free-shipping-unlocked">
                        <i class="fas fa-check-circle"></i>
                        <span><?= __('checkout.free_shipping_unlocked') ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-line">
                    <span><?= __('checkout.shipping') ?></span>
                    <span class="summary-value">
                        <?php if (($shipping_cost ?? 0) > 0): ?>
                            €<?= number_format($shipping_cost, 2) ?>
                        <?php else: ?>
                            <span class="free-badge"><?= __('checkout.free_shipping') ?></span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="summary-total">
                    <span class="total-label"><?= __('checkout.total') ?></span>
                    <span class="total-value">€<?= number_format($total ?? 0, 2) ?></span>
                </div>

                <!-- Continue Button (outside form, linked via form attribute) -->
                <button type="submit" form="checkoutForm" class="btn-continue">
                    <?= __('checkout.continue') ?>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle new address form
const toggleBtn = document.getElementById('toggleNewAddressBtn');
const newAddressForm = document.getElementById('newAddressForm');

if (toggleBtn && newAddressForm) {
    toggleBtn.addEventListener('click', function() {
        newAddressForm.classList.toggle('active');

        // Toggle required attributes on new address fields
        const isActive = newAddressForm.classList.contains('active');
        const requiredFields = newAddressForm.querySelectorAll('input[id^="new_"]');

        requiredFields.forEach(field => {
            if (field.id !== 'new_additional_address') {
                if (isActive) {
                    field.setAttribute('required', 'required');
                    field.removeAttribute('disabled');
                } else {
                    field.removeAttribute('required');
                    field.setAttribute('disabled', 'disabled');
                }
            } else {
                // Campo adicional no requerido pero debe seguir el disabled
                if (isActive) {
                    field.removeAttribute('disabled');
                } else {
                    field.setAttribute('disabled', 'disabled');
                }
            }
        });

        if (isActive) {
            toggleBtn.innerHTML = '<i class="fas fa-minus"></i> <?= __('common.cancel') ?>';
            // Deselect all address radio buttons
            document.querySelectorAll('input[name="address_id"]').forEach(radio => {
                radio.checked = false;
                radio.closest('.address-card')?.classList.remove('selected');
            });
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-plus"></i> <?= __('checkout.add_new_address') ?>';
            // Clear form fields
            requiredFields.forEach(field => field.value = '');
            document.getElementById('new_country').value = 'España';
        }
    });
}

// Address card selection
const addressCards = document.querySelectorAll('.address-card');
addressCards.forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.type !== 'radio') {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        }
    });
});

// Update selected state
const addressRadios = document.querySelectorAll('input[name="address_id"]');
addressRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        addressCards.forEach(card => card.classList.remove('selected'));
        if (this.checked) {
            this.closest('.address-card')?.classList.add('selected');

            // Hide new address form if visible
            if (newAddressForm?.classList.contains('active') && toggleBtn) {
                newAddressForm.classList.remove('active');
                toggleBtn.innerHTML = '<i class="fas fa-plus"></i> <?= __('checkout.add_new_address') ?>';

                // Remove required from new address fields
                const requiredFields = newAddressForm.querySelectorAll('input[id^="new_"]');
                requiredFields.forEach(field => {
                    if (field.id !== 'new_additional_address') {
                        field.removeAttribute('required');
                    }
                });
            }
        }
    });
});

// Form validation
const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
        const hasAddresses = <?= !empty($addresses) ? 'true' : 'false' ?>;

        if (hasAddresses) {
            const selectedAddress = document.querySelector('input[name="address_id"]:checked');
            const newAddressActive = newAddressForm?.classList.contains('active');

            if (!selectedAddress && !newAddressActive) {
                e.preventDefault();
                alert('<?= __('checkout.error_no_address') ?>');
                return false;
            }

            if (newAddressActive) {
                // Validate new address fields
                const requiredFields = [
                    'new_recipient_name',
                    'new_phone',
                    'new_street_address',
                    'new_postal_code',
                    'new_city',
                    'new_province',
                    'new_country'
                ];

                let allFilled = true;
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field || !field.value.trim()) {
                        allFilled = false;
                        field?.classList.add('error');
                    } else {
                        field?.classList.remove('error');
                    }
                });

                if (!allFilled) {
                    e.preventDefault();
                    alert('<?= __('checkout.required_field') ?>');
                    return false;
                }
            }

            // Si llegamos aquí, permitir el submit
            return true;
        } else {
            // No existing addresses, new address form must be filled
            const requiredFields = [
                'new_recipient_name',
                'new_phone',
                'new_street_address',
                'new_postal_code',
                'new_city',
                'new_province',
                'new_country'
            ];

            let allFilled = true;
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field || !field.value.trim()) {
                    allFilled = false;
                    field?.classList.add('error');
                } else {
                    field?.classList.remove('error');
                }
            });

            if (!allFilled) {
                e.preventDefault();
                alert('<?= __('checkout.required_field') ?>');
                return false;
            }

            // Si llegamos aquí, permitir el submit
            return true;
        }
    });
}
</script>
