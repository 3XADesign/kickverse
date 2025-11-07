<!-- Success Section -->
<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-container">

            <?php if ($payment_method === 'oxapay'): ?>
                <!-- OxaPay Payment Completed -->
                <div class="success-animation">
                    <div class="success-icon">
                        <div class="icon-circle success">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <canvas id="confetti-canvas"></canvas>
                </div>

                <h1 class="confirmation-title"><?= __('checkout.order_confirmed') ?></h1>
                <p class="confirmation-subtitle">
                    <?= __('checkout.order_confirmed_msg') ?>
                </p>

                <!-- Order Details Card -->
                <div class="confirmation-card">
                    <div class="order-summary">
                        <div class="order-detail">
                            <span class="detail-label"><?= __('checkout.order_number') ?></span>
                            <span class="detail-value">#<?= htmlspecialchars($order['order_number']) ?></span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label"><?= __('checkout.order_date') ?></span>
                            <span class="detail-value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="order-detail highlight">
                            <span class="detail-label"><?= __('checkout.total') ?></span>
                            <span class="detail-value total">€<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="confirmation-actions">
                    <a href="/mi-cuenta/pedidos/<?= $order['order_id'] ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                        <?= __('checkout.view_order') ?>
                    </a>
                    <a href="/productos" class="btn btn-outline">
                        <i class="fas fa-shopping-bag"></i>
                        <?= __('checkout.continue_shopping') ?>
                    </a>
                </div>

            <?php elseif ($payment_method === 'telegram'): ?>
                <!-- Telegram Payment Pending -->
                <div class="success-animation">
                    <div class="success-icon">
                        <div class="icon-circle telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                    </div>
                </div>

                <h1 class="confirmation-title telegram"><?= __('checkout.telegram_order_pending') ?></h1>
                <p class="confirmation-subtitle">
                    <?= __('checkout.telegram_order_pending_msg') ?>
                </p>

                <!-- Payment Code Card -->
                <div class="confirmation-card telegram-payment">
                    <div class="payment-code-section">
                        <p class="code-label"><?= __('checkout.payment_code') ?></p>
                        <div class="payment-code" id="payment-code">
                            <?= htmlspecialchars($payment_code) ?>
                        </div>
                        <button onclick="copyCode()" class="btn-copy" id="copy-btn">
                            <i class="fas fa-copy"></i>
                            <?= __('checkout.copy_code') ?>
                        </button>
                        <div class="copy-success" id="copy-success" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <?= __('checkout.code_copied') ?>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="telegram-instructions">
                        <h3><?= __('checkout.telegram_instructions_title') ?></h3>
                        <ol class="instructions-list">
                            <li>
                                <span class="step-number">1</span>
                                <div class="step-content">
                                    <strong><?= __('checkout.telegram_step1_title') ?></strong>
                                    <p><?= __('checkout.telegram_step1_desc') ?></p>
                                </div>
                            </li>
                            <li>
                                <span class="step-number">2</span>
                                <div class="step-content">
                                    <strong><?= __('checkout.telegram_step2_title') ?></strong>
                                    <p><?= __('checkout.telegram_step2_desc') ?></p>
                                </div>
                            </li>
                            <li>
                                <span class="step-number">3</span>
                                <div class="step-content">
                                    <strong><?= __('checkout.telegram_step3_title') ?></strong>
                                    <p><?= __('checkout.telegram_step3_desc') ?></p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Telegram Actions -->
                <div class="confirmation-actions">
                    <a href="<?= htmlspecialchars($telegram_url) ?>" target="_blank" class="btn btn-primary telegram-btn">
                        <i class="fab fa-telegram-plane"></i>
                        <?= __('checkout.contact_telegram') ?>
                    </a>
                    <a href="/mi-cuenta/pedidos" class="btn btn-outline">
                        <i class="fas fa-box"></i>
                        <?= __('checkout.view_orders') ?>
                    </a>
                </div>

            <?php endif; ?>

            <!-- Support Notice -->
            <div class="support-notice">
                <i class="fas fa-headset"></i>
                <p><?= __('checkout.need_help') ?> <a href="/contacto"><?= __('checkout.contact_support') ?></a></p>
            </div>

        </div>
    </div>
</section>

<style>
/* Confirmation Section */
.confirmation-section {
    padding: var(--space-12) 0;
    min-height: 80vh;
    background: linear-gradient(135deg, #faf5ff 0%, #fafafa 100%);
}

.confirmation-container {
    max-width: 680px;
    margin: 0 auto;
    text-align: center;
}

/* Success Animation */
.success-animation {
    position: relative;
    margin-bottom: var(--space-6);
}

#confetti-canvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 9999;
}

/* Success Icon */
.success-icon {
    display: inline-block;
    animation: successBounce 0.6s ease;
}

@keyframes successBounce {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.icon-circle {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.icon-circle.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.icon-circle.telegram {
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
}

.icon-circle i {
    font-size: 4rem;
    color: white;
}

/* Confirmation Title */
.confirmation-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-3);
}

.confirmation-title.telegram {
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.confirmation-subtitle {
    font-size: 1.125rem;
    color: var(--gray-600);
    margin-bottom: var(--space-8);
    line-height: 1.6;
}

/* Confirmation Card */
.confirmation-card {
    background: white;
    padding: var(--space-8);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid #f3f4f6;
    margin-bottom: var(--space-6);
}

/* Order Summary */
.order-summary {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.order-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-lg);
}

.order-detail.highlight {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(244, 121, 217, 0.1));
    border: 2px solid rgba(139, 92, 246, 0.2);
}

.detail-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
}

.detail-value.total {
    font-size: 1.75rem;
    font-weight: 700;
    color: #10b981;
}

/* Telegram Payment Card */
.confirmation-card.telegram-payment {
    text-align: left;
}

.payment-code-section {
    text-align: center;
    padding: var(--space-6);
    background: linear-gradient(135deg, rgba(176, 84, 233, 0.05), rgba(236, 72, 153, 0.05));
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-6);
}

.code-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--space-3);
}

.payment-code {
    font-size: 2rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-color: white;
    padding: var(--space-5);
    border-radius: var(--radius-lg);
    border: 2px dashed #b054e9;
    margin-bottom: var(--space-4);
    letter-spacing: 2px;
    word-break: break-all;
}

.btn-copy {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3) var(--space-6);
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-copy:hover {
    background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(176, 84, 233, 0.3);
}

.btn-copy:active {
    transform: translateY(0);
}

.copy-success {
    margin-top: var(--space-3);
    color: #10b981;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Telegram Instructions */
.telegram-instructions {
    text-align: left;
}

.telegram-instructions h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.instructions-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.instructions-list li {
    display: flex;
    gap: var(--space-4);
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-lg);
}

.step-number {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.125rem;
}

.step-content {
    flex: 1;
}

.step-content strong {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-1);
}

.step-content p {
    font-size: 0.9375rem;
    color: var(--gray-600);
    line-height: 1.5;
    margin: 0;
}

/* Confirmation Actions */
.confirmation-actions {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    margin-bottom: var(--space-6);
}

.confirmation-actions .btn {
    padding: var(--space-4) var(--space-6);
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    text-decoration: none;
}

.telegram-btn {
    background: linear-gradient(135deg, #b054e9 0%, #ec4899 100%);
    border: none;
}

.telegram-btn:hover {
    background: linear-gradient(135deg, #9333ea 0%, #db2777 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(176, 84, 233, 0.4);
}

/* Support Notice */
.support-notice {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-3);
    padding: var(--space-4);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.support-notice i {
    font-size: 1.5rem;
    color: #8b5cf6;
}

.support-notice p {
    margin: 0;
    font-size: 0.9375rem;
    color: var(--gray-700);
}

.support-notice a {
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.support-notice a:hover {
    color: #7c3aed;
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .confirmation-section {
        padding: var(--space-8) 0;
    }

    .icon-circle {
        width: 100px;
        height: 100px;
    }

    .icon-circle i {
        font-size: 3rem;
    }

    .confirmation-title {
        font-size: 1.75rem;
    }

    .confirmation-subtitle {
        font-size: 1rem;
    }

    .confirmation-card {
        padding: var(--space-5);
    }

    .payment-code {
        font-size: 1.5rem;
        padding: var(--space-4);
    }

    .confirmation-actions {
        flex-direction: column;
    }

    .confirmation-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .instructions-list li {
        flex-direction: column;
        text-align: center;
    }

    .step-number {
        margin: 0 auto;
    }
}
</style>

<script>
// Copy payment code to clipboard
function copyCode() {
    const codeElement = document.getElementById('payment-code');
    const copyBtn = document.getElementById('copy-btn');
    const successMsg = document.getElementById('copy-success');

    if (!codeElement) return;

    const code = codeElement.textContent.trim();

    // Modern clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
            showCopySuccess();
        }).catch(err => {
            console.error('Error copying code:', err);
            // Fallback to old method
            fallbackCopyCode(code);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyCode(code);
    }
}

function fallbackCopyCode(code) {
    const textArea = document.createElement('textarea');
    textArea.value = code;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();

    try {
        document.execCommand('copy');
        showCopySuccess();
    } catch (err) {
        console.error('Error copying code:', err);
        alert('Error al copiar el código. Por favor, cópialo manualmente.');
    }

    document.body.removeChild(textArea);
}

function showCopySuccess() {
    const copyBtn = document.getElementById('copy-btn');
    const successMsg = document.getElementById('copy-success');

    if (copyBtn && successMsg) {
        copyBtn.style.display = 'none';
        successMsg.style.display = 'flex';

        setTimeout(() => {
            copyBtn.style.display = 'inline-flex';
            successMsg.style.display = 'none';
        }, 3000);
    }
}

// Confetti animation for successful payments
<?php if ($payment_method === 'oxapay'): ?>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('confetti-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const confetti = [];
    const confettiCount = 100;
    const colors = ['#8b5cf6', '#f479d9', '#10b981', '#fbbf24', '#ef4444'];

    class ConfettiPiece {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height - canvas.height;
            this.size = Math.random() * 5 + 5;
            this.speedY = Math.random() * 3 + 2;
            this.speedX = Math.random() * 2 - 1;
            this.color = colors[Math.floor(Math.random() * colors.length)];
            this.rotation = Math.random() * 360;
            this.rotationSpeed = Math.random() * 10 - 5;
        }

        update() {
            this.y += this.speedY;
            this.x += this.speedX;
            this.rotation += this.rotationSpeed;

            if (this.y > canvas.height) {
                this.y = -10;
                this.x = Math.random() * canvas.width;
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation * Math.PI / 180);
            ctx.fillStyle = this.color;
            ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
            ctx.restore();
        }
    }

    for (let i = 0; i < confettiCount; i++) {
        confetti.push(new ConfettiPiece());
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        confetti.forEach(piece => {
            piece.update();
            piece.draw();
        });

        requestAnimationFrame(animate);
    }

    animate();

    // Stop animation after 5 seconds
    setTimeout(() => {
        canvas.style.opacity = '0';
        canvas.style.transition = 'opacity 1s ease';
        setTimeout(() => {
            canvas.remove();
        }, 1000);
    }, 5000);

    // Resize canvas on window resize
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
});
<?php endif; ?>
</script>
