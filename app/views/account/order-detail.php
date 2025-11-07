<?php
/**
 * Order Detail Page
 * Displays detailed information about a single order
 */
?>

<div class="order-detail-page">
    <div class="container">
        <div class="order-detail-header">
            <a href="/mi-cuenta/pedidos" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <?= __('common.back') ?>
            </a>
            <h1><?= __('checkout.order_number') ?>: #<?= $order['order_id'] ?></h1>
            <span class="order-status-badge <?= strtolower($order['order_status']) ?>">
                <?= __('account.status_' . strtolower($order['order_status'])) ?>
            </span>
        </div>

        <div class="order-detail-content">
            <!-- Order Summary Card -->
            <div class="order-detail-card">
                <h2><?= __('checkout.order_details') ?></h2>
                <div class="order-detail-grid">
                    <div class="detail-item">
                        <span class="label"><?= __('checkout.order_date') ?>:</span>
                        <span class="value"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><?= __('checkout.order_status') ?>:</span>
                        <span class="value"><?= __('account.status_' . strtolower($order['order_status'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><?= __('checkout.payment_status') ?>:</span>
                        <span class="value"><?= __('checkout.payment_' . strtolower($order['payment_status'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><?= __('checkout.payment_method') ?>:</span>
                        <span class="value">
                            <i class="fas fa-credit-card"></i>
                            <?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')) ?>
                        </span>
                    </div>
                    <?php if (!empty($order['tracking_number'])): ?>
                    <div class="detail-item">
                        <span class="label"><?= __('checkout.tracking_number') ?>:</span>
                        <span class="value"><?= htmlspecialchars($order['tracking_number']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-detail-card">
                <h2><?= __('checkout.order_items') ?></h2>
                <div class="order-items-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                <?php else: ?>
                                    <div class="no-image"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                                <p class="item-meta">
                                    <?php if (!empty($item['team_name'])): ?>
                                        <?= htmlspecialchars($item['team_name']) ?> -
                                    <?php endif; ?>
                                    <?= __('product.size') ?>: <?= htmlspecialchars($item['size']) ?>
                                </p>
                                <?php if ($item['has_personalization']): ?>
                                    <p class="item-personalization">
                                        <i class="fas fa-user-edit"></i>
                                        <?= htmlspecialchars($item['personalization_name']) ?> #<?= htmlspecialchars($item['personalization_number']) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($item['has_patches']): ?>
                                    <p class="item-patches">
                                        <i class="fas fa-shield-alt"></i>
                                        <?= __('product.patches_included') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="item-quantity">
                                x<?= $item['quantity'] ?>
                            </div>
                            <div class="item-price">
                                €<?= number_format($item['subtotal'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Shipping Address -->
            <?php if (!empty($order['shipping_address'])): ?>
            <div class="order-detail-card">
                <h2><?= __('checkout.shipping_address') ?></h2>
                <div class="address-info">
                    <p><strong><?= htmlspecialchars($order['shipping_address']['recipient_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order['shipping_address']['street_address']) ?></p>
                    <?php if (!empty($order['shipping_address']['additional_address'])): ?>
                        <p><?= htmlspecialchars($order['shipping_address']['additional_address']) ?></p>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($order['shipping_address']['postal_code']) ?> <?= htmlspecialchars($order['shipping_address']['city']) ?></p>
                    <p><?= htmlspecialchars($order['shipping_address']['province']) ?>, <?= htmlspecialchars($order['shipping_address']['country']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Order Total -->
            <div class="order-detail-card">
                <h2><?= __('checkout.order_total') ?></h2>
                <div class="order-totals">
                    <div class="total-row">
                        <span><?= __('checkout.subtotal') ?>:</span>
                        <span>€<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="total-row discount">
                        <span>
                            <?= __('checkout.discount') ?>
                            <?php if (!empty($order['coupon_code'])): ?>
                                <span style="font-weight: 700; text-transform: uppercase; margin-left: 0.5rem;">(<?= htmlspecialchars($order['coupon_code']) ?>)</span>
                            <?php endif; ?>:
                        </span>
                        <span>-€<?= number_format($order['discount_amount'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="total-row">
                        <span><?= __('checkout.shipping') ?>:</span>
                        <span><?= $order['shipping_cost'] == 0 ? __('checkout.free') : '€' . number_format($order['shipping_cost'], 2) ?></span>
                    </div>
                    <div class="total-row final">
                        <span><?= __('checkout.total') ?>:</span>
                        <span>€<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-detail-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        padding: 2rem 0;
    }

    .order-detail-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: white;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-lg);
        color: var(--gray-700);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: var(--gray-50);
        border-color: var(--primary);
        color: var(--primary);
    }

    .order-detail-header h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
        flex: 1;
    }

    .order-status-badge {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-full);
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .order-status-badge.pending_payment {
        background: rgba(251, 191, 36, 0.1);
        color: #f59e0b;
    }

    .order-status-badge.processing {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .order-status-badge.shipped {
        background: rgba(168, 85, 247, 0.1);
        color: #a855f7;
    }

    .order-status-badge.delivered {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }

    .order-detail-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-detail-card {
        background: white;
        border-radius: var(--radius-xl);
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid var(--gray-100);
    }

    .order-detail-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 1.5rem 0;
    }

    .order-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-item .label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-item .value {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
    }

    .order-items-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .order-item {
        display: grid;
        grid-template-columns: 100px 1fr auto auto;
        gap: 1.5rem;
        align-items: center;
        padding: 1.25rem;
        background: var(--gray-50);
        border-radius: var(--radius-lg);
    }

    .item-image {
        width: 100px;
        height: 100px;
        border-radius: var(--radius-md);
        overflow: hidden;
        background: white;
        border: 2px solid var(--gray-200);
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--gray-400);
    }

    .item-details h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 0.5rem 0;
    }

    .item-meta {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
    }

    .item-personalization,
    .item-patches {
        font-size: 0.875rem;
        color: var(--primary);
        margin: 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .item-quantity {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-700);
    }

    .item-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .address-info p {
        margin: 0.5rem 0;
        color: var(--gray-700);
        line-height: 1.6;
    }

    .order-totals {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .total-row:last-child {
        border-bottom: none;
    }

    .total-row span:first-child {
        font-size: 1rem;
        color: var(--gray-600);
    }

    .total-row span:last-child {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
    }

    .total-row.discount span:last-child {
        color: #22c55e;
    }

    .total-row.final {
        border-top: 2px solid var(--gray-300);
        padding-top: 1.5rem;
        margin-top: 0.5rem;
    }

    .total-row.final span:first-child {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .total-row.final span:last-child {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @media (max-width: 768px) {
        .order-item {
            grid-template-columns: 80px 1fr;
            gap: 1rem;
        }

        .item-image {
            width: 80px;
            height: 80px;
        }

        .item-quantity,
        .item-price {
            grid-column: 2;
            text-align: right;
        }

        .order-detail-header h1 {
            font-size: 1.5rem;
        }
    }
</style>
