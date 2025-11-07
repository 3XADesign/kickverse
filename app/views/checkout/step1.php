<section class="checkout-section">
    <div class="container">
        <!-- Progress Steps -->
        <div class="checkout-progress">
            <div class="progress-step active completed">
                <div class="step-circle">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <span class="step-label">Carrito</span>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step">
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
            <h1 class="checkout-title">Revisar Pedido</h1>
            <p class="checkout-subtitle">Verifica los artículos antes de continuar</p>
        </div>

        <div class="checkout-grid">
            <!-- Cart Items Review -->
            <div class="checkout-main">
                <div class="checkout-card">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i>
                        Artículos del Pedido
                    </h3>

                    <div class="checkout-items">
                        <?php foreach ($items as $item): ?>
                            <div class="checkout-item">
                                <div class="item-image">
                                    <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/logo.png') ?>"
                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                         onerror="this.src='/img/logo.png'">
                                </div>

                                <div class="item-details">
                                    <h4 class="item-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <?php if (!empty($item['team_name'])): ?>
                                        <p class="item-team">
                                            <i class="fas fa-shield-alt"></i>
                                            <?= htmlspecialchars($item['team_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="item-size">
                                        Talla: <strong><?= htmlspecialchars($item['size']) ?></strong>
                                    </p>

                                    <?php if ($item['has_patches']): ?>
                                        <p class="item-addon">
                                            <i class="fas fa-check-circle"></i>
                                            Parches (+€1.99)
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($item['has_personalization']): ?>
                                        <p class="item-addon">
                                            <i class="fas fa-check-circle"></i>
                                            <?= htmlspecialchars($item['personalization_name']) ?> #<?= htmlspecialchars($item['personalization_number']) ?> (+€2.99)
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="item-quantity">
                                    <span class="quantity-label">Cantidad:</span>
                                    <span class="quantity-value"><?= $item['quantity'] ?></span>
                                </div>

                                <div class="item-price">
                                    €<?= number_format($item['item_total'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="items-footer">
                        <a href="/carrito" class="btn-link">
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

                    <?php if ($shipping_cost == 0): ?>
                        <div class="summary-notice success">
                            <i class="fas fa-check-circle"></i>
                            <span>Envío gratis aplicado</span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="total-value">€<?= number_format($total, 2) ?></span>
                    </div>

                    <a href="/checkout/step2" class="btn btn-primary btn-block btn-lg">
                        Continuar a Dirección
                        <i class="fas fa-arrow-right"></i>
                    </a>

                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Pago 100% seguro</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-lock"></i>
                            <span>Datos protegidos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

/* Checkout Items */
.checkout-items {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.checkout-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto;
    gap: var(--space-4);
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-lg);
    align-items: center;
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
    overflow: hidden;
    background: white;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.item-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.item-team,
.item-size {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-1);
}

.item-addon {
    font-size: 0.8125rem;
    color: #10b981;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-1);
}

.item-quantity {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-2) var(--space-3);
    background: white;
    border-radius: var(--radius-md);
}

.quantity-label {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.quantity-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
}

.item-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
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

.summary-notice {
    margin: var(--space-4) 0;
    padding: var(--space-3);
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    color: #059669;
    display: flex;
    align-items: center;
    gap: var(--space-2);
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
    justify-content: center;
}

/* Trust Badges */
.trust-badges {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-3);
    margin-top: var(--space-5);
    padding-top: var(--space-5);
    border-top: 1px solid #f3f4f6;
}

.trust-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3);
    background: #fafafa;
    border-radius: var(--radius-md);
    text-align: center;
}

.trust-badge i {
    font-size: 1.25rem;
    color: #f479d9;
}

.trust-badge span {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--gray-700);
    line-height: 1.3;
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
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

    .checkout-item {
        grid-template-columns: 60px 1fr;
        gap: var(--space-3);
    }

    .item-image {
        width: 60px;
        height: 60px;
    }

    .item-quantity,
    .item-price {
        grid-column: 1 / -1;
        justify-content: center;
        text-align: center;
    }

    .item-quantity {
        flex-direction: row;
        gap: var(--space-2);
    }
}
</style>
