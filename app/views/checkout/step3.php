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
            <div class="progress-step completed">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Dirección</span>
            </div>
            <div class="progress-line active"></div>
            <div class="progress-step active">
                <div class="step-circle">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="step-label">Pago</span>
            </div>
        </div>

        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1 class="checkout-title">Método de Pago</h1>
            <p class="checkout-subtitle">Completa tu pedido de forma segura con OxaPay</p>
        </div>

        <div class="checkout-grid">
            <!-- Payment Section -->
            <div class="checkout-main">
                <!-- Order Review -->
                <div class="checkout-card">
                    <h3 class="card-title">
                        <i class="fas fa-box-open"></i>
                        Resumen del Pedido
                    </h3>

                    <div class="order-items-compact">
                        <?php foreach ($items as $item): ?>
                            <div class="compact-item">
                                <div class="compact-image">
                                    <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/logo.png') ?>"
                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                         onerror="this.src='/img/logo.png'">
                                    <span class="item-qty"><?= $item['quantity'] ?>x</span>
                                </div>
                                <div class="compact-details">
                                    <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <p>Talla <?= htmlspecialchars($item['size']) ?></p>
                                </div>
                                <div class="compact-price">
                                    €<?= number_format($item['item_total'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="checkout-card" style="margin-top: var(--space-4);">
                    <h3 class="card-title">
                        <i class="fas fa-truck"></i>
                        Dirección de Envío
                    </h3>

                    <div class="address-display">
                        <div class="address-info">
                            <p class="recipient"><strong><?= htmlspecialchars($address['recipient_name']) ?></strong></p>
                            <p><?= htmlspecialchars($address['street_address']) ?></p>
                            <p><?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state_province']) ?> <?= htmlspecialchars($address['postal_code']) ?></p>
                            <p><?= htmlspecialchars($address['country']) ?></p>
                            <p class="phone">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($address['phone_number']) ?>
                            </p>
                        </div>
                        <a href="/checkout/step2" class="btn-edit">
                            <i class="fas fa-edit"></i>
                            Cambiar
                        </a>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="checkout-card" style="margin-top: var(--space-4);">
                    <h3 class="card-title">
                        <i class="fas fa-wallet"></i>
                        Método de Pago
                    </h3>

                    <div class="payment-method-card">
                        <div class="payment-logo">
                            <img src="/img/payments/oxpay.png" alt="OxaPay" style="height: 40px;">
                        </div>
                        <div class="payment-info">
                            <h4>Pago con Criptomonedas</h4>
                            <p>Pago seguro procesado por OxaPay. Acepta Bitcoin, USDT, y más.</p>
                        </div>
                        <div class="payment-secure">
                            <i class="fas fa-shield-check"></i>
                            <span>Seguro</span>
                        </div>
                    </div>

                    <div class="payment-features">
                        <div class="feature">
                            <i class="fas fa-lock"></i>
                            <span>Encriptación SSL</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Pago Instantáneo</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-user-shield"></i>
                            <span>Privacidad Garantizada</span>
                        </div>
                    </div>
                </div>

                <div class="items-footer">
                    <a href="/checkout/step2" class="btn-link">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Dirección
                    </a>
                </div>
            </div>

            <!-- Order Summary & Payment -->
            <div class="checkout-sidebar">
                <div class="checkout-card summary-card">
                    <h3 class="card-title">
                        <i class="fas fa-receipt"></i>
                        Total a Pagar
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

                    <button type="button" onclick="createOrder()" class="btn btn-primary btn-block btn-lg btn-checkout" id="payButton">
                        <i class="fas fa-lock"></i>
                        <span>Proceder al Pago</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>

                    <div class="payment-info-text">
                        <i class="fas fa-info-circle"></i>
                        <p>Al hacer clic serás redirigido a OxaPay para completar el pago de forma segura</p>
                    </div>

                    <div class="security-features">
                        <div class="security-item">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>100% Seguro</strong>
                                <p>Tus datos están protegidos</p>
                            </div>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-undo-alt"></i>
                            <div>
                                <strong>Devoluciones Fáciles</strong>
                                <p>30 días de garantía</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Loading Modal -->
<div id="loadingModal" class="payment-modal" style="display: none;">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="spinner"></div>
        <h3>Procesando tu pedido...</h3>
        <p>Por favor espera mientras creamos tu pedido</p>
    </div>
</div>

<script>
async function createOrder() {
    const button = document.getElementById('payButton');
    const modal = document.getElementById('loadingModal');

    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    modal.style.display = 'flex';

    try {
        // Create order
        const response = await fetch('/api/orders/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                csrf_token: '<?= $csrf_token ?>'
            })
        });

        const data = await response.json();

        if (data.success && data.payment_url) {
            // Redirect to payment
            window.location.href = data.payment_url;
        } else {
            throw new Error(data.message || 'Error al crear el pedido');
        }
    } catch (error) {
        console.error('Error:', error);
        modal.style.display = 'none';
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-lock"></i><span>Proceder al Pago</span><i class="fas fa-arrow-right"></i>';

        alert('Error al procesar el pedido: ' + error.message);
    }
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
    background: #10b981;
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

/* Compact Order Items */
.order-items-compact {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.compact-item {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: var(--space-3);
    padding: var(--space-3);
    background: #fafafa;
    border-radius: var(--radius-md);
    align-items: center;
}

.compact-image {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.compact-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-qty {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 8px;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 700;
}

.compact-details h4 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 var(--space-1) 0;
}

.compact-details p {
    font-size: 0.8125rem;
    color: var(--gray-600);
    margin: 0;
}

.compact-price {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gray-900);
}

/* Address Display */
.address-display {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-4);
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-md);
}

.address-info p {
    margin: 0 0 var(--space-1) 0;
    font-size: 0.875rem;
    color: var(--gray-700);
    line-height: 1.5;
}

.address-info .recipient {
    margin-bottom: var(--space-2);
}

.address-info .phone {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-top: var(--space-2);
    color: var(--gray-600);
}

.address-info .phone i {
    color: #f479d9;
}

.btn-edit {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-2) var(--space-3);
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-md);
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-edit:hover {
    border-color: #8b5cf6;
    background: #faf5ff;
}

/* Payment Method */
.payment-method-card {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: var(--space-4);
    padding: var(--space-4);
    background: linear-gradient(135deg, #faf5ff 0%, #f3f4f6 100%);
    border: 2px solid #e9d5ff;
    border-radius: var(--radius-lg);
    align-items: center;
}

.payment-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-2);
    background: white;
    border-radius: var(--radius-md);
}

.payment-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 var(--space-1) 0;
}

.payment-info p {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
    line-height: 1.4;
}

.payment-secure {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-2);
}

.payment-secure i {
    font-size: 1.5rem;
    color: #10b981;
}

.payment-secure span {
    font-size: 0.75rem;
    font-weight: 600;
    color: #10b981;
}

.payment-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-3);
    margin-top: var(--space-4);
}

.feature {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3);
    background: #fafafa;
    border-radius: var(--radius-md);
    text-align: center;
}

.feature i {
    font-size: 1.25rem;
    color: #8b5cf6;
}

.feature span {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--gray-700);
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
    border-bottom: 1px solid #e5e7eb;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
}

.total-value {
    font-size: 1.75rem;
    color: var(--gray-900);
    font-weight: 700;
}

.btn-block {
    width: 100%;
    justify-content: space-between;
}

.btn-checkout {
    background: linear-gradient(135deg, #8b5cf6 0%, #f479d9 100%);
    padding: var(--space-4);
    font-size: 1.125rem;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
}

.payment-info-text {
    display: flex;
    gap: var(--space-2);
    align-items: flex-start;
    padding: var(--space-3);
    background: #fffbeb;
    border: 1px solid #fcd34d;
    border-radius: var(--radius-md);
    margin-top: var(--space-4);
}

.payment-info-text i {
    color: #f59e0b;
    flex-shrink: 0;
    margin-top: 2px;
}

.payment-info-text p {
    margin: 0;
    font-size: 0.8125rem;
    color: #92400e;
    line-height: 1.4;
}

.security-features {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
    margin-top: var(--space-5);
    padding-top: var(--space-5);
    border-top: 1px solid #f3f4f6;
}

.security-item {
    display: flex;
    gap: var(--space-3);
    align-items: flex-start;
}

.security-item i {
    font-size: 1.5rem;
    color: #f479d9;
    flex-shrink: 0;
}

.security-item strong {
    display: block;
    font-size: 0.875rem;
    color: var(--gray-900);
    margin-bottom: var(--space-1);
}

.security-item p {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--gray-600);
    line-height: 1.4;
}

/* Payment Modal */
.payment-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: relative;
    background: white;
    padding: var(--space-8);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    text-align: center;
    max-width: 400px;
    z-index: 1;
}

.spinner {
    width: 64px;
    height: 64px;
    border: 4px solid #f3f4f6;
    border-top-color: #8b5cf6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto var(--space-4);
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.modal-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.modal-content p {
    font-size: 1rem;
    color: var(--gray-600);
    margin: 0;
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
    }

    .payment-features {
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

    .compact-item {
        grid-template-columns: 60px 1fr;
        gap: var(--space-2);
    }

    .compact-image {
        width: 60px;
        height: 60px;
    }

    .compact-price {
        grid-column: 1 / -1;
        text-align: center;
        padding-top: var(--space-2);
        border-top: 1px solid #e5e7eb;
    }

    .payment-method-card {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .address-display {
        flex-direction: column;
    }

    .btn-edit {
        align-self: flex-start;
    }
}
</style>
