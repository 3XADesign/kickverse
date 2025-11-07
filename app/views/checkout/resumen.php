<section class="checkout-section">
    <div class="container">
        <!-- Progress Steps -->
        <div class="checkout-progress">
            <div class="progress-steps">
                <div class="progress-step active">
                    <div class="progress-step-circle">1</div>
                    <div class="progress-step-label"><?= __('checkout.step_1_short') ?></div>
                </div>
                <div class="progress-step active">
                    <div class="progress-step-circle">2</div>
                    <div class="progress-step-label"><?= __('checkout.step_2_short') ?></div>
                </div>
                <div class="progress-step">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label"><?= __('checkout.step_3_short') ?></div>
                </div>
            </div>
        </div>

        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1 class="checkout-title"><?= __('checkout.step_2_title') ?></h1>
            <p class="checkout-subtitle"><?= __('checkout.step_2_subtitle') ?></p>
        </div>

        <div class="checkout-layout">
            <!-- Columna Principal -->
            <div class="checkout-main">

                <!-- Productos del Carrito -->
                <div class="checkout-card">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-shopping-bag"></i>
                        <?= __('checkout.order_summary') ?>
                    </h2>

                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-image">
                                <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/placeholder.jpg') ?>"
                                     alt="<?= htmlspecialchars($item['product_name']) ?>">
                            </div>
                            <div class="order-item-details">
                                <h3 class="order-item-name"><?= htmlspecialchars($item['product_name']) ?></h3>
                                <div class="order-item-meta">
                                    <span class="order-item-size">Talla: <?= htmlspecialchars($item['size'] ?? 'N/A') ?></span>
                                    <span class="order-item-qty">Cantidad: <?= $item['quantity'] ?></span>
                                </div>
                            </div>
                            <div class="order-item-price">
                                <span class="price"><?= number_format($item['item_total'] ?? 0, 2) ?> €</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Dirección de Envío -->
                <div class="checkout-card">
                    <div class="checkout-card-header">
                        <h2 class="checkout-card-title">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= __('checkout.shipping_address') ?>
                        </h2>
                        <a href="/checkout/datos" class="edit-link"><?= __('common.edit') ?></a>
                    </div>

                    <div class="address-summary">
                        <div class="address-row">
                            <strong><?= htmlspecialchars($address['recipient_name']) ?></strong>
                        </div>
                        <div class="address-row">
                            <?= htmlspecialchars($address['street_address']) ?>
                            <?php if (!empty($address['additional_address'])): ?>
                                <br><?= htmlspecialchars($address['additional_address']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="address-row">
                            <?= htmlspecialchars($address['city']) ?>,
                            <?= htmlspecialchars($address['postal_code']) ?> -
                            <?= htmlspecialchars($address['province']) ?>
                        </div>
                        <div class="address-row">
                            <?= htmlspecialchars($address['country']) ?>
                        </div>
                        <?php if (!empty($address['phone'])): ?>
                        <div class="address-row">
                            Teléfono: <?= htmlspecialchars($address['phone']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Datos de Contacto -->
                <div class="checkout-card">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-user"></i>
                        <?= __('checkout.contact_info') ?>
                    </h2>

                    <div class="contact-summary">
                        <div class="contact-row">
                            <span class="contact-label"><?= __('checkout.email') ?>:</span>
                            <span class="contact-value"><?= htmlspecialchars($customer['email']) ?></span>
                        </div>
                        <?php if (!empty($customer['phone'])): ?>
                        <div class="contact-row">
                            <span class="contact-label"><?= __('checkout.phone') ?>:</span>
                            <span class="contact-value"><?= htmlspecialchars($customer['phone']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Columna Lateral - Resumen -->
            <div class="checkout-sidebar">
                <div class="checkout-card checkout-summary">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-receipt"></i>
                        <?= __('checkout.order_total') ?>
                    </h2>

                    <!-- Cupón -->
                    <div class="coupon-section">
                        <?php if (isset($coupon) && $coupon): ?>
                        <!-- Cupón Aplicado -->
                        <div class="coupon-applied">
                            <div class="coupon-info">
                                <span class="coupon-icon">🎉</span>
                                <div class="coupon-details">
                                    <strong><?= htmlspecialchars($coupon['code']) ?></strong>
                                    <small><?= __('checkout.coupon_applied') ?></small>
                                </div>
                            </div>
                            <button type="button" class="btn-remove-coupon" onclick="removeCoupon()">
                                <i class="icon-close"></i>
                            </button>
                        </div>
                        <?php else: ?>
                        <!-- Formulario de Cupón -->
                        <form id="coupon-form" class="coupon-form">
                            <div class="form-group">
                                <label for="coupon-code"><?= __('checkout.have_coupon') ?></label>
                                <div class="input-group">
                                    <input type="text"
                                           id="coupon-code"
                                           name="coupon_code"
                                           class="form-control"
                                           placeholder="<?= __('checkout.enter_coupon') ?>">
                                    <button type="submit" class="btn btn-secondary" id="apply-coupon-btn">
                                        <span class="btn-text"><?= __('checkout.apply') ?></span>
                                        <span class="btn-spinner" style="display: none;">
                                            <i class="spinner"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>

                        <!-- Mensaje de Cupón -->
                        <div id="coupon-message" class="coupon-message" style="display: none;"></div>
                    </div>

                    <!-- Resumen de Costos -->
                    <div class="order-summary">
                        <div class="cost-row">
                            <span class="cost-label"><?= __('checkout.subtotal') ?></span>
                            <span class="cost-value"><?= number_format($subtotal, 2) ?> €</span>
                        </div>

                        <div class="cost-row">
                            <span class="cost-label"><?= __('checkout.shipping') ?></span>
                            <span class="cost-value">
                                <?php if ($shipping_cost > 0): ?>
                                    <?= number_format($shipping_cost, 2) ?> €
                                <?php else: ?>
                                    <?= __('checkout.free') ?>
                                <?php endif; ?>
                            </span>
                        </div>

                        <?php if (isset($discount) && $discount > 0): ?>
                        <div class="cost-row discount">
                            <span class="cost-label"><?= __('checkout.discount') ?></span>
                            <span class="cost-value">-<?= number_format($discount, 2) ?> €</span>
                        </div>
                        <?php endif; ?>

                        <div class="cost-divider"></div>

                        <div class="cost-total">
                            <span class="cost-total-label"><?= __('checkout.total') ?></span>
                            <span class="cost-total-value"><?= number_format($total, 2) ?> €</span>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="checkout-actions">
                        <button type="button" class="btn btn-primary btn-block" onclick="proceedToPayment()">
                            <?= __('checkout.proceed_to_payment') ?>
                        </button>
                        <a href="/checkout/datos" class="btn btn-outline btn-block">
                            <?= __('checkout.back_to_data') ?>
                        </a>
                    </div>

                    <!-- Información Adicional -->
                    <div class="checkout-info">
                        <p class="info-text">
                            <i class="fas fa-shield-alt"></i>
                            <?= __('checkout.secure_payment_info') ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
// Aplicar Cupón
document.getElementById('coupon-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const couponCode = document.getElementById('coupon-code').value.trim();
    const btn = document.getElementById('apply-coupon-btn');
    const btnText = btn.querySelector('.btn-text');
    const btnSpinner = btn.querySelector('.btn-spinner');
    const messageEl = document.getElementById('coupon-message');

    if (!couponCode) {
        showMessage('<?= __('checkout.enter_coupon_code') ?>', 'error');
        return;
    }

    // Mostrar spinner
    btn.disabled = true;
    btnText.style.display = 'none';
    btnSpinner.style.display = 'inline-block';
    messageEl.style.display = 'none';

    try {
        const response = await fetch('/api/checkout/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                coupon_code: couponCode,
                subtotal: <?= $subtotal ?>
            })
        });

        const data = await response.json();

        if (data.success) {
            showMessage(data.message || '<?= __('checkout.coupon_applied_success') ?>', 'success');
            // Recargar la página para mostrar el descuento
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showMessage(data.message || '<?= __('checkout.coupon_error') ?>', 'error');
            btn.disabled = false;
            btnText.style.display = 'inline';
            btnSpinner.style.display = 'none';
        }
    } catch (error) {
        console.error('Error applying coupon:', error);
        showMessage('<?= __('checkout.coupon_error') ?>', 'error');
        btn.disabled = false;
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
    }
});

// Quitar Cupón
async function removeCoupon() {
    if (!confirm('<?= __('checkout.remove_coupon_confirm') ?>')) {
        return;
    }

    try {
        const response = await fetch('/api/checkout/remove-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || '<?= __('checkout.remove_coupon_error') ?>');
        }
    } catch (error) {
        console.error('Error removing coupon:', error);
        alert('<?= __('checkout.remove_coupon_error') ?>');
    }
}

// Mostrar mensaje
function showMessage(message, type) {
    const messageEl = document.getElementById('coupon-message');
    messageEl.textContent = message;
    messageEl.className = 'coupon-message ' + type;
    messageEl.style.display = 'block';
}

// Proceder al Pago
function proceedToPayment() {
    // Validar que haya productos en el carrito
    <?php if (empty($cart_items)): ?>
    alert('<?= __('cart.empty_cart') ?>');
    window.location.href = '/cart';
    return;
    <?php endif; ?>

    // Redirigir a la página de pago
    window.location.href = '/checkout/pago';
}
</script>
