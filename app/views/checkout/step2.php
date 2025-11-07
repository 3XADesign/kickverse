<section class="checkout-section">
    <div class="container">
        <!-- Progress Steps -->
        <div class="checkout-progress">
            <div class="progress-step completed">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Carrito</span>
            </div>
            <div class="progress-line active"></div>
            <div class="progress-step active">
                <div class="step-circle">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <span class="step-label">Dirección</span>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step">
                <div class="step-circle">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="step-label">Pago</span>
            </div>
        </div>

        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1 class="checkout-title">Dirección de Envío</h1>
            <p class="checkout-subtitle">Selecciona o añade una dirección de envío</p>
        </div>

        <div class="checkout-grid">
            <!-- Address Selection -->
            <div class="checkout-main">
                <div class="checkout-card">
                    <h3 class="card-title">
                        <i class="fas fa-map-marked-alt"></i>
                        Mis Direcciones
                    </h3>

                    <?php if (empty($addresses)): ?>
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>No tienes direcciones guardadas</h4>
                            <p>Añade una dirección de envío para continuar</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="/checkout/process-step2" id="addressForm">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="action" value="select">

                            <div class="addresses-grid">
                                <?php foreach ($addresses as $address): ?>
                                    <label class="address-card <?= $address['is_default'] ? 'default' : '' ?>">
                                        <input type="radio" name="address_id" value="<?= $address['address_id'] ?>"
                                               <?= $address['is_default'] ? 'checked' : '' ?> required>
                                        <div class="address-content">
                                            <div class="address-header">
                                                <span class="address-label">
                                                    <i class="fas fa-home"></i>
                                                    <?= htmlspecialchars($address['address_label']) ?>
                                                </span>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="default-badge">Predeterminada</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="address-details">
                                                <p class="recipient-name">
                                                    <strong><?= htmlspecialchars($address['recipient_name']) ?></strong>
                                                </p>
                                                <p><?= htmlspecialchars($address['street_address']) ?></p>
                                                <p><?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state_province']) ?> <?= htmlspecialchars($address['postal_code']) ?></p>
                                                <p><?= htmlspecialchars($address['country']) ?></p>
                                                <p class="phone">
                                                    <i class="fas fa-phone"></i>
                                                    <?= htmlspecialchars($address['phone_number']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="radio-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-actions">
                                <button type="button" onclick="showNewAddressForm()" class="btn btn-outline">
                                    <i class="fas fa-plus"></i>
                                    Añadir Nueva Dirección
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Continuar al Pago
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <!-- New Address Form (Hidden by default) -->
                    <div id="newAddressForm" style="display: none;">
                        <h3 class="card-title" style="margin-top: var(--space-6);">
                            <i class="fas fa-plus-circle"></i>
                            Nueva Dirección
                        </h3>

                        <form method="POST" action="/checkout/process-step2" class="address-form">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="action" value="new">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address_label">
                                        <i class="fas fa-tag"></i>
                                        Etiqueta
                                    </label>
                                    <select name="address_label" id="address_label" class="form-control" required>
                                        <option value="Casa">Casa</option>
                                        <option value="Trabajo">Trabajo</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="recipient_name">
                                        <i class="fas fa-user"></i>
                                        Nombre Completo
                                    </label>
                                    <input type="text" name="recipient_name" id="recipient_name"
                                           class="form-control" required placeholder="Nombre y apellidos">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="street_address">
                                    <i class="fas fa-road"></i>
                                    Dirección
                                </label>
                                <input type="text" name="street_address" id="street_address"
                                       class="form-control" required placeholder="Calle, número, piso, puerta">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">
                                        <i class="fas fa-city"></i>
                                        Ciudad
                                    </label>
                                    <input type="text" name="city" id="city"
                                           class="form-control" required placeholder="Madrid">
                                </div>

                                <div class="form-group">
                                    <label for="state_province">
                                        <i class="fas fa-map"></i>
                                        Provincia
                                    </label>
                                    <input type="text" name="state_province" id="state_province"
                                           class="form-control" required placeholder="Madrid">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="postal_code">
                                        <i class="fas fa-mail-bulk"></i>
                                        Código Postal
                                    </label>
                                    <input type="text" name="postal_code" id="postal_code"
                                           class="form-control" required placeholder="28001">
                                </div>

                                <div class="form-group">
                                    <label for="country">
                                        <i class="fas fa-globe"></i>
                                        País
                                    </label>
                                    <input type="text" name="country" id="country"
                                           class="form-control" value="España" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone_number">
                                    <i class="fas fa-phone"></i>
                                    Teléfono
                                </label>
                                <input type="tel" name="phone_number" id="phone_number"
                                       class="form-control" required placeholder="+34 600 000 000">
                            </div>

                            <div class="form-group-checkbox">
                                <label>
                                    <input type="checkbox" name="is_default" value="1">
                                    <span>Establecer como dirección predeterminada</span>
                                </label>
                            </div>

                            <div class="form-actions">
                                <button type="button" onclick="hideNewAddressForm()" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Guardar y Continuar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="items-footer">
                        <a href="/checkout/step1" class="btn-link">
                            <i class="fas fa-arrow-left"></i>
                            Volver al Carrito
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="checkout-sidebar">
                <div class="checkout-card summary-card">
                    <h3 class="card-title">
                        <i class="fas fa-receipt"></i>
                        Resumen del Pedido
                    </h3>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span class="summary-value">€<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <div class="summary-line">
                        <span>Envío</span>
                        <span class="summary-value">
                            <?php if ($shipping_cost > 0): ?>
                                €<?= number_format($shipping_cost, 2) ?>
                            <?php else: ?>
                                <span class="free-badge">GRATIS</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="total-value">€<?= number_format($total, 2) ?></span>
                    </div>

                    <div class="security-notice">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Compra Segura</strong>
                            <p>Tus datos están protegidos con encriptación SSL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function showNewAddressForm() {
    document.getElementById('newAddressForm').style.display = 'block';
    document.getElementById('newAddressForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideNewAddressForm() {
    document.getElementById('newAddressForm').style.display = 'none';
}
</script>

<style>
/* Checkout Section */
.checkout-section {
    padding: var(--space-8) 0;
    min-height: 80vh;
    background: #fafafa;
}

/* Progress Steps */
.checkout-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-8);
    padding: var(--space-6);
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    position: relative;
    z-index: 1;
}

.step-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: white;
    border: 3px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #9ca3af;
    transition: all 0.3s ease;
}

.progress-step.active .step-circle {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #8b5cf6 0%, #f479d9 100%);
    color: white;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
}

.progress-step.completed .step-circle {
    border-color: #10b981;
    background: #10b981;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-align: center;
}

.progress-step.active .step-label {
    color: #8b5cf6;
}

.progress-line {
    flex: 1;
    height: 3px;
    background: #e5e7eb;
    margin: 0 var(--space-4);
    max-width: 120px;
    position: relative;
    top: -20px;
}

.progress-line.active {
    background: linear-gradient(to right, #10b981 0%, #8b5cf6 100%);
}

/* Checkout Header */
.checkout-header {
    text-align: center;
    margin-bottom: var(--space-6);
}

.checkout-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.checkout-subtitle {
    font-size: 1.125rem;
    color: var(--gray-600);
}

/* Checkout Grid */
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: var(--space-6);
    align-items: start;
}

/* Checkout Card */
.checkout-card {
    background: white;
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    border: 1px solid #f3f4f6;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-5);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.card-title i {
    color: #f479d9;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--space-8) var(--space-4);
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: var(--space-4);
}

.empty-state h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.empty-state p {
    color: var(--gray-600);
}

/* Addresses Grid */
.addresses-grid {
    display: grid;
    gap: var(--space-4);
    margin-bottom: var(--space-5);
}

.address-card {
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--space-4);
    padding: var(--space-5);
    background: #fafafa;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-card:hover {
    border-color: #c4b5fd;
    background: #faf5ff;
}

.address-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.address-card input[type="radio"]:checked ~ .address-content {
    opacity: 1;
}

.address-card input[type="radio"]:checked ~ .radio-check {
    background: linear-gradient(135deg, #8b5cf6 0%, #f479d9 100%);
    color: white;
    border-color: #8b5cf6;
}

.address-card.default {
    border-color: #a7f3d0;
}

.address-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-3);
}

.address-label {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-weight: 600;
    color: var(--gray-900);
    font-size: 1rem;
}

.address-label i {
    color: #f479d9;
}

.default-badge {
    background: #10b981;
    color: white;
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
}

.address-details {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.address-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-700);
    line-height: 1.5;
}

.recipient-name {
    margin-bottom: var(--space-2) !important;
}

.phone {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-top: var(--space-2) !important;
    color: var(--gray-600) !important;
}

.phone i {
    color: #f479d9;
}

.radio-check {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.radio-check i {
    font-size: 1rem;
}

/* Form Styles */
.address-form {
    margin-top: var(--space-5);
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
    color: var(--gray-700);
    margin-bottom: var(--space-2);
    font-size: 0.875rem;
}

.form-group label i {
    color: #f479d9;
    font-size: 0.875rem;
}

.form-control {
    width: 100%;
    padding: var(--space-3);
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-group-checkbox {
    margin: var(--space-4) 0;
}

.form-group-checkbox label {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    cursor: pointer;
}

.form-group-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: var(--space-3);
    margin-top: var(--space-5);
}

.form-actions .btn {
    flex: 1;
}

.items-footer {
    margin-top: var(--space-5);
    padding-top: var(--space-5);
    border-top: 1px solid #e5e7eb;
}

.btn-link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.btn-link:hover {
    color: #7c3aed;
    gap: var(--space-3);
}

/* Summary Card */
.summary-card {
    position: sticky;
    top: var(--space-4);
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-3) 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 1rem;
    color: var(--gray-700);
}

.summary-value {
    font-weight: 600;
    color: var(--gray-900);
}

.free-badge {
    background: #10b981;
    color: white;
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 700;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-5) 0;
    margin: var(--space-4) 0;
    border-top: 2px solid #e5e7eb;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
}

.total-value {
    font-size: 1.75rem;
    color: var(--gray-900);
    font-weight: 700;
}

.security-notice {
    display: flex;
    gap: var(--space-3);
    padding: var(--space-4);
    background: #faf5ff;
    border: 1px solid #e9d5ff;
    border-radius: var(--radius-md);
    margin-top: var(--space-5);
}

.security-notice i {
    font-size: 1.5rem;
    color: #8b5cf6;
    flex-shrink: 0;
}

.security-notice strong {
    display: block;
    color: var(--gray-900);
    margin-bottom: var(--space-1);
    font-size: 0.875rem;
}

.security-notice p {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--gray-600);
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .checkout-section {
        padding: var(--space-6) 0;
    }

    .checkout-progress {
        padding: var(--space-4);
    }

    .step-circle {
        width: 48px;
        height: 48px;
        font-size: 1.125rem;
    }

    .step-label {
        font-size: 0.75rem;
    }

    .progress-line {
        max-width: 60px;
        margin: 0 var(--space-2);
    }

    .checkout-title {
        font-size: 1.5rem;
    }

    .checkout-subtitle {
        font-size: 1rem;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>
