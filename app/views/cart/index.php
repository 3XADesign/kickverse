<section class="cart-section">
    <div class="container">
        <div class="cart-header">
            <h1 class="page-title">
                <i class="fas fa-shopping-cart"></i>
                <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Mi Carrito' : 'My Cart' ?>
            </h1>
        </div>

        <?php if (empty($items)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Tu carrito está vacío' : 'Your cart is empty' ?></h2>
                <p><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '¡Añade algunos productos para comenzar!' : 'Add some products to get started!' ?></p>
                <a href="/productos" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i>
                    <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Explorar Productos' : 'Browse Products' ?>
                </a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <!-- Cart Items -->
                <div class="cart-items-container">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item" data-item-id="<?= $item['cart_item_id'] ?>">
                            <a href="/productos/<?= htmlspecialchars($item['product_slug']) ?>" class="cart-item-image">
                                <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/logo.png') ?>"
                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                     onerror="this.src='/img/logo.png'">
                            </a>

                            <div class="cart-item-info">
                                <a href="/productos/<?= htmlspecialchars($item['product_slug']) ?>" class="cart-item-title">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </a>
                                <?php if (!empty($item['team_name'])): ?>
                                    <p class="cart-item-team">
                                        <i class="fas fa-shield-alt"></i>
                                        <?= htmlspecialchars($item['team_name']) ?>
                                    </p>
                                <?php endif; ?>
                                <p class="cart-item-size">
                                    <i class="fas fa-ruler"></i>
                                    <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Talla' : 'Size' ?>: <strong><?= htmlspecialchars($item['size']) ?></strong>
                                </p>

                                <?php if ($item['has_patches']): ?>
                                    <p class="cart-item-addon">
                                        <i class="fas fa-check-circle"></i>
                                        <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Parches' : 'Patches' ?> (+€1.99)
                                    </p>
                                <?php endif; ?>

                                <?php if ($item['has_personalization']): ?>
                                    <p class="cart-item-addon">
                                        <i class="fas fa-check-circle"></i>
                                        <?= htmlspecialchars($item['personalization_name']) ?> #<?= htmlspecialchars($item['personalization_number']) ?> (+€2.99)
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="cart-item-quantity">
                                <button type="button" class="qty-btn" onclick="updateQuantity(<?= $item['cart_item_id'] ?>, <?= $item['quantity'] - 1 ?>)" <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>"
                                       class="qty-input" readonly>
                                <button type="button" class="qty-btn" onclick="updateQuantity(<?= $item['cart_item_id'] ?>, <?= $item['quantity'] + 1 ?>)" <?= $item['quantity'] >= $item['stock_quantity'] ? 'disabled' : '' ?>>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <div class="cart-item-price-section">
                                <span class="cart-item-price">€<?= number_format($item['item_total'], 2) ?></span>
                                <span class="cart-item-price-unit">€<?= number_format($item['unit_price'], 2) ?> c/u</span>
                            </div>

                            <button type="button" class="cart-item-remove" onclick="removeItem(<?= $item['cart_item_id'] ?>)" title="<?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Eliminar' : 'Remove' ?>">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary-wrapper">
                    <div class="cart-summary">
                        <h3 class="summary-title">
                            <i class="fas fa-receipt"></i>
                            <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Resumen del Pedido' : 'Order Summary' ?>
                        </h3>

                        <div class="summary-line">
                            <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Subtotal' : 'Subtotal' ?></span>
                            <span class="summary-value">€<?= number_format($subtotal, 2) ?></span>
                        </div>

                        <div class="summary-line">
                            <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Envío' : 'Shipping' ?></span>
                            <span class="summary-value">
                                <?php if ($shipping_cost > 0): ?>
                                    €<?= number_format($shipping_cost, 2) ?>
                                <?php else: ?>
                                    <span class="free-badge"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'GRATIS' : 'FREE' ?></span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <?php if ($subtotal < $free_shipping_threshold): ?>
                            <div class="shipping-notice">
                                <i class="fas fa-truck"></i>
                                <span>
                                    <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] === 'es'): ?>
                                        Te faltan <strong>€<?= number_format($free_shipping_threshold - $subtotal, 2) ?></strong> para envío gratis
                                    <?php else: ?>
                                        You're <strong>€<?= number_format($free_shipping_threshold - $subtotal, 2) ?></strong> away from free shipping
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="shipping-notice success">
                                <i class="fas fa-check-circle"></i>
                                <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '¡Envío gratis aplicado!' : 'Free shipping applied!' ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-total">
                            <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Total' : 'Total' ?></span>
                            <span class="total-value">€<?= number_format($total, 2) ?></span>
                        </div>

                        <?php if (isset($_SESSION['user']['customer_id'])): ?>
                            <a href="/checkout/datos" class="btn btn-checkout btn-block btn-lg">
                                <i class="fas fa-lock"></i>
                                <span class="btn-checkout-text">
                                    <?= __('cart.proceed_to_checkout') ?>
                                </span>
                                <i class="fas fa-arrow-right btn-checkout-arrow"></i>
                            </a>
                        <?php else: ?>
                            <button onclick="openLoginModal()" class="btn btn-checkout btn-block btn-lg">
                                <i class="fas fa-lock"></i>
                                <span class="btn-checkout-text">
                                    <?= __('cart.proceed_to_checkout') ?>
                                </span>
                                <i class="fas fa-arrow-right btn-checkout-arrow"></i>
                            </button>
                        <?php endif; ?>

                        <a href="/productos" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i>
                            <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Seguir Comprando' : 'Continue Shopping' ?>
                        </a>

                        <div class="payment-methods">
                            <p class="payment-methods-title"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Métodos de pago' : 'Payment methods' ?></p>
                            <div class="payment-icons">
                                <img src="/img/payments/oxpay.png" alt="OxaPay" title="OxaPay - Crypto Payments">
                            </div>
                        </div>

                        <div class="trust-badges">
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Pago 100% seguro' : '100% Secure Payment' ?></span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-undo"></i>
                                <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Devolución fácil' : 'Easy Returns' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cart Modal -->
<div id="cartModal" class="cart-modal" style="display: none;">
    <div class="cart-modal-backdrop" onclick="closeCartModal()"></div>
    <div class="cart-modal-container">
        <button onclick="closeCartModal()" class="cart-modal-close">
            <i class="fas fa-times"></i>
        </button>
        <div class="cart-modal-content">
            <div id="cartModalIcon" class="cart-modal-icon"></div>
            <h3 id="cartModalTitle" class="cart-modal-title"></h3>
            <p id="cartModalMessage" class="cart-modal-message"></p>
            <div id="cartModalActions" class="cart-modal-actions"></div>
        </div>
    </div>
</div>

<script>
// Modal functions
function showCartModal(type, title, message, buttons) {
    const modal = document.getElementById('cartModal');
    const icon = document.getElementById('cartModalIcon');
    const titleEl = document.getElementById('cartModalTitle');
    const messageEl = document.getElementById('cartModalMessage');
    const actions = document.getElementById('cartModalActions');

    // Set icon based on type
    if (type === 'confirm') {
        icon.innerHTML = '<i class="fas fa-question-circle"></i>';
        icon.className = 'cart-modal-icon cart-modal-icon-confirm';
    } else if (type === 'error') {
        icon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
        icon.className = 'cart-modal-icon cart-modal-icon-error';
    } else {
        icon.innerHTML = '<i class="fas fa-check-circle"></i>';
        icon.className = 'cart-modal-icon cart-modal-icon-success';
    }

    titleEl.textContent = title;
    messageEl.textContent = message;

    // Add buttons
    actions.innerHTML = '';
    buttons.forEach(btn => {
        const button = document.createElement('button');
        button.className = `cart-modal-btn ${btn.primary ? 'cart-modal-btn-primary' : 'cart-modal-btn-secondary'}`;
        button.innerHTML = btn.icon ? `<i class="${btn.icon}"></i> ${btn.text}` : btn.text;
        button.onclick = btn.action;
        actions.appendChild(button);
    });

    modal.style.display = 'flex';
    modal.offsetHeight;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCartModal() {
    const modal = document.getElementById('cartModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

// Cart functions
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
        showCartModal(
            'confirm',
            isSpanish ? '¿Eliminar producto?' : 'Remove product?',
            isSpanish ? '¿Deseas eliminar este producto del carrito?' : 'Do you want to remove this product from the cart?',
            [
                {
                    text: isSpanish ? 'Cancelar' : 'Cancel',
                    icon: 'fas fa-times',
                    primary: false,
                    action: closeCartModal
                },
                {
                    text: isSpanish ? 'Eliminar' : 'Remove',
                    icon: 'fas fa-trash-alt',
                    primary: true,
                    action: () => {
                        closeCartModal();
                        removeItem(itemId);
                    }
                }
            ]
        );
        return;
    }

    fetch(`/api/cart/update/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
            showCartModal(
                'error',
                isSpanish ? 'Error al actualizar' : 'Update error',
                data.message || <?= json_encode(__('common.cart_update_error_msg')) ?>,
                [{
                    text: 'OK',
                    primary: true,
                    action: closeCartModal
                }]
            );
        }
    })
    .catch(err => {
        console.error('Error:', err);
        const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
        showCartModal(
            'error',
            isSpanish ? 'Error' : 'Error',
            <?= json_encode(__('common.unexpected_error')) ?>,
            [{
                text: 'OK',
                primary: true,
                action: closeCartModal
            }]
        );
    });
}

function removeItem(itemId) {
    const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
    showCartModal(
        'confirm',
        isSpanish ? '¿Eliminar producto?' : 'Remove product?',
        isSpanish ? '¿Estás seguro de eliminar este producto?' : 'Are you sure you want to remove this product?',
        [
            {
                text: isSpanish ? 'Cancelar' : 'Cancel',
                icon: 'fas fa-times',
                primary: false,
                action: closeCartModal
            },
            {
                text: isSpanish ? 'Eliminar' : 'Remove',
                icon: 'fas fa-trash-alt',
                primary: true,
                action: () => {
                    closeCartModal();
                    performRemoveItem(itemId);
                }
            }
        ]
    );
}

function performRemoveItem(itemId) {
    fetch(`/api/cart/remove/${itemId}`, {
        method: 'DELETE'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
            showCartModal(
                'error',
                isSpanish ? 'Error al eliminar' : 'Remove error',
                data.message || <?= json_encode(__('common.cart_remove_error_msg')) ?>,
                [{
                    text: 'OK',
                    primary: true,
                    action: closeCartModal
                }]
            );
        }
    })
    .catch(err => {
        console.error('Error:', err);
        const isSpanish = <?= json_encode(isset($_SESSION['lang']) && $_SESSION['lang'] === 'es') ?>;
        showCartModal(
            'error',
            isSpanish ? 'Error' : 'Error',
            <?= json_encode(__('common.unexpected_error')) ?>,
            [{
                text: 'OK',
                primary: true,
                action: closeCartModal
            }]
        );
    });
}
</script>

<style>
/* Cart Section */
.cart-section {
    padding: var(--space-8) 0;
    min-height: 70vh;
    background: var(--gray-50);
}

.cart-header {
    margin-bottom: var(--space-6);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.page-title i {
    color: #f479d9;
}

/* Empty Cart */
.empty-cart {
    text-align: center;
    padding: var(--space-12) var(--space-4);
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
}

.empty-cart-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto var(--space-6);
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-cart-icon i {
    font-size: 4rem;
    color: var(--gray-400);
}

.empty-cart h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.empty-cart p {
    font-size: 1.125rem;
    color: var(--gray-600);
    margin-bottom: var(--space-6);
}

/* Cart Grid */
.cart-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: var(--space-6);
    align-items: start;
}

/* Cart Items Container */
.cart-items-container {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

/* Cart Item */
.cart-item {
    display: grid;
    grid-template-columns: 120px 1fr auto auto auto;
    gap: var(--space-4);
    padding: var(--space-5);
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    align-items: center;
    transition: var(--transition);
}

.cart-item:hover {
    box-shadow: var(--shadow-md);
}

.cart-item-image {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--gray-50);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.cart-item-image:hover {
    transform: scale(1.05);
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-info {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.cart-item-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    text-decoration: none;
    transition: var(--transition);
}

.cart-item-title:hover {
    color: var(--primary);
}

.cart-item-team {
    font-size: 0.875rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin: 0;
}

.cart-item-size {
    font-size: 0.875rem;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin: 0;
}

.cart-item-addon {
    font-size: 0.8125rem;
    color: var(--success);
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin: 0;
}

/* Quantity Controls */
.cart-item-quantity {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    background: var(--gray-50);
    padding: var(--space-1);
    border-radius: var(--radius-lg);
}

.qty-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: white;
    border-radius: var(--radius-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-700);
    transition: var(--transition);
    font-size: 0.875rem;
}

.qty-btn:hover:not(:disabled) {
    background: var(--primary);
    color: white;
}

.qty-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.qty-input {
    width: 50px;
    text-align: center;
    border: none;
    background: transparent;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
}

/* Price Section */
.cart-item-price-section {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-1);
}

.cart-item-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
}

.cart-item-price-unit {
    font-size: 0.8125rem;
    color: var(--gray-500);
}

/* Remove Button */
.cart-item-remove {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    cursor: pointer;
    color: var(--error);
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item-remove:hover {
    background: var(--error);
    color: white;
}

/* Cart Summary */
.cart-summary-wrapper {
    position: sticky;
    top: var(--space-4);
}

.cart-summary {
    background: white;
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.summary-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-5);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.summary-title i {
    color: #f479d9;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-3) 0;
    border-bottom: 1px solid var(--gray-200);
    font-size: 1rem;
    color: var(--gray-700);
}

.summary-value {
    font-weight: 600;
    color: var(--gray-900);
}

.free-badge {
    background: var(--success);
    color: white;
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 700;
}

.shipping-notice {
    margin: var(--space-4) 0;
    padding: var(--space-3) var(--space-4);
    background: #fff4e6;
    border: 1px solid #ffa94d;
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    color: #e8590c;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.shipping-notice i {
    font-size: 1.125rem;
}

.shipping-notice.success {
    background: #e7f5ff;
    border-color: #4dabf7;
    color: #1971c2;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-5) 0;
    margin: var(--space-4) 0;
    border-top: 2px solid var(--gray-200);
    border-bottom: 1px solid var(--gray-200);
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
}

.total-value {
    font-size: 1.5rem;
    color: var(--gray-900);
    font-weight: 700;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.btn-block + .btn-block {
    margin-top: var(--space-3);
}

/* Checkout Button */
.btn-checkout {
    background: var(--primary);
    color: white;
    font-weight: 600;
    font-size: 1rem;
    padding: var(--space-4);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
}

.btn-checkout:hover {
    background: #7c3aed;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-checkout-text {
    flex: 1;
}

.btn-checkout-arrow {
    transition: transform 0.3s ease;
}

.btn-checkout:hover .btn-checkout-arrow {
    transform: translateX(3px);
}

/* Payment Methods */
.payment-methods {
    margin-top: var(--space-5);
    padding-top: var(--space-5);
    border-top: 1px solid var(--gray-200);
}

.payment-methods-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--space-3);
    text-align: center;
}

.payment-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--space-3);
}

.payment-icons img {
    height: 28px;
    opacity: 0.7;
    transition: var(--transition);
}

.payment-icons img:hover {
    opacity: 1;
}

/* Trust Badges */
.trust-badges {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-3);
    margin-top: var(--space-4);
}

.trust-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3);
    background: var(--gray-50);
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
    .cart-grid {
        grid-template-columns: 1fr;
    }

    .cart-summary-wrapper {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-section {
        padding: var(--space-6) 0;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: var(--space-3);
        padding: var(--space-4);
    }

    .cart-item-image {
        width: 80px;
        height: 80px;
    }

    .cart-item-quantity {
        grid-column: 1 / -1;
        justify-content: center;
    }

    .cart-item-price-section {
        grid-column: 1 / -1;
        align-items: center;
        text-align: center;
    }

    .cart-item-remove {
        position: absolute;
        top: var(--space-3);
        right: var(--space-3);
    }

    .cart-summary {
        padding: var(--space-5);
    }

    .trust-badges {
        grid-template-columns: 1fr;
    }
}

/* Cart Modal */
.cart-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cart-modal.active {
    opacity: 1;
}

.cart-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(4px);
}

.cart-modal-container {
    position: relative;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    max-width: 480px;
    width: 90%;
    padding: var(--space-8);
    z-index: 1;
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.cart-modal.active .cart-modal-container {
    transform: scale(1);
}

.cart-modal-close {
    position: absolute;
    top: var(--space-4);
    right: var(--space-4);
    width: 36px;
    height: 36px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius-full);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    transition: var(--transition);
    z-index: 2;
}

.cart-modal-close:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.cart-modal-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.cart-modal-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-5);
    font-size: 2.5rem;
}

.cart-modal-icon-confirm {
    background: #fff4e6;
    color: #fd7e14;
}

.cart-modal-icon-error {
    background: #ffe9e9;
    color: #f03e3e;
}

.cart-modal-icon-success {
    background: #e7f5ff;
    color: #1971c2;
}

.cart-modal-icon i {
    animation: cartModalIconBounce 0.5s ease;
}

@keyframes cartModalIconBounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.cart-modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-3);
}

.cart-modal-message {
    font-size: 1rem;
    color: var(--gray-600);
    margin-bottom: var(--space-6);
    line-height: 1.6;
}

.cart-modal-actions {
    display: flex;
    gap: var(--space-3);
    width: 100%;
}

.cart-modal-btn {
    flex: 1;
    padding: var(--space-3) var(--space-4);
    border: none;
    border-radius: var(--radius-lg);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
}

.cart-modal-btn-primary {
    background: var(--primary);
    color: white;
}

.cart-modal-btn-primary:hover {
    background: #7c3aed;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.cart-modal-btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
}

.cart-modal-btn-secondary:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

@media (max-width: 640px) {
    .cart-modal-container {
        padding: var(--space-6);
    }

    .cart-modal-icon {
        width: 64px;
        height: 64px;
        font-size: 2rem;
    }

    .cart-modal-title {
        font-size: 1.25rem;
    }

    .cart-modal-actions {
        flex-direction: column-reverse;
    }

    .cart-modal-btn {
        width: 100%;
    }
}
</style>
