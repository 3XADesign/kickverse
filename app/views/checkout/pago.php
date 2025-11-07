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
                <div class="progress-step active">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label"><?= __('checkout.step_3_short') ?></div>
                </div>
            </div>
        </div>

        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1 class="checkout-title"><?= __('checkout.step_3_title') ?></h1>
            <p class="checkout-subtitle"><?= __('checkout.step_3_subtitle') ?></p>
        </div>

        <div class="checkout-grid">
            <!-- Payment Method Section -->
            <div class="checkout-main">
                <!-- Order Summary Compact -->
                <div class="checkout-card">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-box-open"></i>
                        <?= __('checkout.order_summary') ?>
                    </h2>

                    <div class="order-summary-compact">
                        <div class="summary-row">
                            <span class="summary-label"><?= __('checkout.order_total') ?>:</span>
                            <span class="summary-amount">€<?= number_format($order_summary['total'] ?? $total, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label"><?= __('checkout.shipping_address') ?>:</span>
                            <span class="summary-text"><?= htmlspecialchars($order_summary['shipping_address'] ?? '') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="checkout-card" style="margin-top: var(--space-4);">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-wallet"></i>
                        <?= __('checkout.payment_method') ?>
                    </h2>

                    <div class="payment-methods-grid">
                        <!-- Option 1: OxaPay (Cryptocurrencies) -->
                        <div class="payment-method-option">
                            <div class="payment-method-header">
                                <div class="payment-icon crypto-icon">
                                    <i class="fab fa-bitcoin"></i>
                                </div>
                                <div class="payment-method-info">
                                    <h4><?= __('checkout.payment_method_oxapay') ?></h4>
                                    <p><?= __('checkout.payment_method_oxapay_desc') ?></p>
                                </div>
                            </div>
                            <div class="payment-method-features">
                                <span class="feature-badge">
                                    <i class="fas fa-bolt"></i>
                                    <?= __('checkout.instant') ?>
                                </span>
                                <span class="feature-badge">
                                    <i class="fas fa-shield-alt"></i>
                                    <?= __('checkout.secure') ?>
                                </span>
                                <span class="feature-badge">
                                    <i class="fas fa-user-secret"></i>
                                    <?= __('checkout.private') ?>
                                </span>
                            </div>
                            <button type="button" onclick="selectPaymentMethod('oxapay')" class="btn btn-primary btn-block payment-btn" id="oxapayBtn">
                                <i class="fas fa-lock"></i>
                                <span><?= __('checkout.pay_with_crypto') ?></span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Option 2: Manual Payment (Telegram) -->
                        <div class="payment-method-option">
                            <div class="payment-method-header">
                                <div class="payment-icon telegram-icon">
                                    <i class="fab fa-telegram"></i>
                                </div>
                                <div class="payment-method-info">
                                    <h4><?= __('checkout.payment_method_telegram') ?></h4>
                                    <p><?= __('checkout.payment_method_telegram_desc') ?></p>
                                </div>
                            </div>
                            <div class="payment-method-features">
                                <span class="feature-badge">
                                    <i class="fas fa-user-check"></i>
                                    <?= __('checkout.personalized') ?>
                                </span>
                                <span class="feature-badge">
                                    <i class="fas fa-comments"></i>
                                    <?= __('checkout.support_24_7') ?>
                                </span>
                                <span class="feature-badge">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <?= __('checkout.flexible') ?>
                                </span>
                            </div>
                            <button type="button" onclick="selectPaymentMethod('telegram')" class="btn btn-outline btn-block payment-btn" id="telegramBtn">
                                <i class="fab fa-telegram"></i>
                                <span><?= __('checkout.manual_payment') ?></span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="items-footer">
                    <a href="/checkout/resumen" class="btn btn-outline btn-block">
                        <i class="fas fa-arrow-left"></i>
                        <?= __('checkout.back_to_summary') ?>
                    </a>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="checkout-sidebar">
                <div class="checkout-card summary-card">
                    <h2 class="checkout-card-title">
                        <i class="fas fa-receipt"></i>
                        <?= __('checkout.total_to_pay') ?>
                    </h2>

                    <div class="summary-breakdown">
                        <div class="breakdown-item">
                            <span><?= __('checkout.subtotal') ?></span>
                            <span>€<?= number_format($order_summary['subtotal'] ?? 0, 2) ?></span>
                        </div>
                        <div class="breakdown-item">
                            <span><?= __('checkout.shipping') ?></span>
                            <span>
                                <?php if (($shipping_cost ?? 0) > 0): ?>
                                    €<?= number_format($shipping_cost, 2) ?>
                                <?php else: ?>
                                    <span class="free-badge"><?= __('checkout.free') ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="summary-total-line">
                        <span><?= __('checkout.total') ?></span>
                        <span class="total-amount">€<?= number_format($total, 2) ?></span>
                    </div>

                    <div class="security-badges">
                        <div class="security-badge">
                            <i class="fas fa-lock"></i>
                            <span><?= __('checkout.secure_payment') ?></span>
                        </div>
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span><?= __('checkout.ssl_encrypted') ?></span>
                        </div>
                        <div class="security-badge">
                            <i class="fas fa-undo-alt"></i>
                            <span><?= __('checkout.warranty_30_days') ?></span>
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
        <h3><?= __('checkout.processing_payment') ?></h3>
        <p id="loadingMessage"><?= __('checkout.please_wait') ?></p>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="notification-modal" style="display: none;">
    <div class="notification-modal-backdrop" onclick="closeErrorModal()"></div>
    <div class="notification-modal-container">
        <button onclick="closeErrorModal()" class="notification-modal-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="notification-modal-content">
            <div class="notification-modal-icon error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 id="errorTitle" class="notification-modal-title"><?= __('checkout.payment_error') ?></h3>
            <p id="errorMessage" class="notification-modal-message"></p>
            <div class="notification-modal-footer">
                <p class="notification-modal-help">
                    <i class="fas fa-info-circle"></i>
                    <?= __('checkout.payment_error_help') ?>
                </p>
            </div>
            <button onclick="closeErrorModal()" class="notification-modal-btn">
                <i class="fas fa-redo"></i>
                <?= __('checkout.try_again') ?>
            </button>
        </div>
    </div>
</div>

<script>
let isProcessing = false;

async function selectPaymentMethod(method) {
    if (isProcessing) return;

    isProcessing = true;

    // Get buttons
    const oxapayBtn = document.getElementById('oxapayBtn');
    const telegramBtn = document.getElementById('telegramBtn');
    const modal = document.getElementById('loadingModal');
    const loadingMessage = document.getElementById('loadingMessage');

    // Disable both buttons
    oxapayBtn.disabled = true;
    telegramBtn.disabled = true;

    // Update button state
    const activeBtn = method === 'oxapay' ? oxapayBtn : telegramBtn;
    const originalHTML = activeBtn.innerHTML;
    activeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    // Show loading modal with appropriate message
    if (method === 'oxapay') {
        loadingMessage.textContent = '<?= __('checkout.creating_crypto_payment') ?>';
    } else {
        loadingMessage.textContent = '<?= __('checkout.preparing_telegram_payment') ?>';
    }
    modal.style.display = 'flex';

    try {
        // Determine API endpoint based on payment method
        const endpoint = method === 'oxapay'
            ? '/api/payment/oxapay/create'
            : '/api/payment/telegram/create';

        // Make API request
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                csrf_token: '<?= $csrf_token ?? '' ?>'
            })
        });

        const data = await response.json();

        if (data.success) {
            // Redirect to payment URL or confirmation page
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                throw new Error('No se recibió una URL de redirección');
            }
        } else {
            throw new Error(data.message || 'Error al procesar el pago');
        }
    } catch (error) {
        console.error('Error:', error);

        // Hide modal
        modal.style.display = 'none';

        // Reset buttons
        activeBtn.innerHTML = originalHTML;
        oxapayBtn.disabled = false;
        telegramBtn.disabled = false;
        isProcessing = false;

        // Show error in modal
        showErrorModal('<?= __('checkout.payment_error') ?>', error.message || '<?= __('checkout.payment_error_generic') ?>');
    }
}

function showErrorModal(title, message) {
    const modal = document.getElementById('errorModal');
    const titleEl = document.getElementById('errorTitle');
    const messageEl = document.getElementById('errorMessage');

    titleEl.textContent = title;
    messageEl.textContent = message;

    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
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
    margin-bottom: var(--space-8);
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

/* Order Summary Compact */
.order-summary-compact {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-md);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--space-3);
}

.summary-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
}

.summary-amount {
    font-size: 1.125rem;
    font-weight: 700;
    color: #8b5cf6;
}

.summary-text {
    font-size: 0.875rem;
    color: var(--gray-600);
    text-align: right;
}

/* Payment Methods Grid */
.payment-methods-grid {
    display: grid;
    gap: var(--space-5);
    margin-top: var(--space-2);
}

.payment-method-option {
    padding: var(--space-5);
    background: white;
    border: 3px solid #e5e7eb;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.payment-method-option:hover {
    border-color: #8b5cf6;
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.15);
    transform: translateY(-2px);
}

.payment-method-header {
    display: flex;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
}

.payment-icon {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
}

.crypto-icon {
    background: linear-gradient(135deg, #f7931a 0%, #f7931a 100%);
    color: white;
}

.telegram-icon {
    background: linear-gradient(135deg, #0088cc 0%, #29b6f6 100%);
    color: white;
}

.payment-method-info h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 var(--space-2) 0;
}

.payment-method-info p {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
    line-height: 1.5;
}

.payment-method-features {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    margin-bottom: var(--space-4);
    padding-bottom: var(--space-4);
    border-bottom: 1px solid #f3f4f6;
}

.feature-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-1) var(--space-3);
    background: #f3f4f6;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
}

.feature-badge i {
    font-size: 0.875rem;
    color: #8b5cf6;
}

.payment-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-3);
    padding: var(--space-4);
    font-size: 1.125rem;
}

.payment-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Items Footer */
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

.summary-breakdown {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
    padding-bottom: var(--space-4);
    border-bottom: 2px solid #e5e7eb;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1rem;
    color: var(--gray-700);
}

.breakdown-item span:last-child {
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

.summary-total-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-5) 0;
    margin-bottom: var(--space-5);
    border-bottom: 1px solid #e5e7eb;
}

.summary-total-line span:first-child {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
}

.total-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
}

/* Security Badges */
.security-badges {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.security-badge {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    background: #fafafa;
    border-radius: var(--radius-md);
}

.security-badge i {
    font-size: 1.25rem;
    color: #10b981;
}

.security-badge span {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
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
}

@media (max-width: 768px) {
    .checkout-section {
        padding: var(--space-6) 0;
    }

    .checkout-progress {
        overflow-x: auto;
    }

    .step-circle {
        width: 48px;
        height: 48px;
        font-size: 1.125rem;
    }

    .step-label {
        font-size: 0.7rem;
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

    .payment-method-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .payment-icon {
        width: 56px;
        height: 56px;
        font-size: 1.75rem;
    }

    .payment-method-features {
        justify-content: center;
    }

    .summary-row {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--space-1);
    }

    .summary-text {
        text-align: left;
    }
}

/* Notification Modal */
.notification-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-4);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
}

.notification-modal-container {
    position: relative;
    background: white;
    border-radius: var(--radius-xl);
    max-width: 500px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    z-index: 1;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-modal-close {
    position: absolute;
    top: var(--space-4);
    right: var(--space-4);
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: var(--gray-400);
    cursor: pointer;
    padding: var(--space-2);
    line-height: 1;
    transition: color 0.2s;
}

.notification-modal-close:hover {
    color: var(--gray-600);
}

.notification-modal-content {
    padding: var(--space-8) var(--space-6);
    text-align: center;
}

.notification-modal-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-5);
    font-size: 2.5rem;
}

.notification-modal-icon.error {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(185, 28, 28, 0.1));
    color: #dc2626;
}

.notification-modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-3);
}

.notification-modal-message {
    font-size: 1rem;
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: var(--space-4);
}

.notification-modal-footer {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.05));
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--radius-md);
    padding: var(--space-4);
    margin-bottom: var(--space-6);
}

.notification-modal-help {
    font-size: 0.875rem;
    color: #3b82f6;
    line-height: 1.6;
    margin: 0;
    display: flex;
    align-items: flex-start;
    gap: var(--space-2);
}

.notification-modal-help i {
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.notification-modal-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    width: 100%;
    padding: var(--space-4);
    background: linear-gradient(135deg, #b054e9, #ec4899);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}

.notification-modal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(176, 84, 233, 0.4);
}
</style>
