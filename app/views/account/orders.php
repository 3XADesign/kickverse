<?php
/**
 * My Orders Page
 * Displays customer's order history
 */
?>

<style>
    .orders-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    }

    .orders-page ~ .footer {
        margin-top: 0;
    }

    .orders-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .orders-header {
        margin-bottom: 2rem;
    }

    .orders-header h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .orders-header p {
        color: var(--gray-600);
        font-size: 1rem;
    }

    .orders-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid var(--gray-100);
    }

    .stat-card h3 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }

    .stat-card p {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .order-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid var(--gray-100);
        overflow: hidden;
        transition: all 0.2s;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.15);
        border-color: rgba(176, 84, 233, 0.4);
    }

    .order-card-header {
        background: var(--gray-50);
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .order-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .order-number {
        font-weight: 700;
        color: var(--gray-900);
        font-size: 1.125rem;
    }

    .order-date {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .order-status {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .order-status.pending,
    .order-status.pending_payment {
        background: rgba(255, 177, 66, 0.1);
        color: #ff9933;
    }

    .order-status.processing {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .order-status.shipped {
        background: rgba(168, 85, 247, 0.1);
        color: #a855f7;
    }

    .order-status.delivered {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }

    .order-status.cancelled {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }

    .order-card-body {
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .order-items-list {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .order-item-row {
        display: grid;
        grid-template-columns: 60px 1fr auto;
        gap: 1rem;
        align-items: center;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: var(--radius-md);
        border: 1px solid var(--gray-100);
    }

    .item-image {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 2px solid var(--gray-200);
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .item-name {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.9375rem;
    }

    .item-meta {
        font-size: 0.8125rem;
        color: var(--gray-600);
    }

    .item-quantity {
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.9375rem;
    }

    .more-items {
        padding: 0.75rem;
        background: linear-gradient(135deg, rgba(176, 84, 233, 0.05), rgba(236, 72, 153, 0.05));
        border-radius: var(--radius-md);
        border: 1px dashed rgba(176, 84, 233, 0.3);
        text-align: center;
        color: #b054e9;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .order-summary {
        min-width: 250px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .order-total-box {
        background: white;
        padding: 1rem;
        border-radius: var(--radius-md);
        border: 2px solid var(--gray-200);
    }

    .order-total-label {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .order-total-amount {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .order-payment-method {
        font-size: 0.8125rem;
        color: var(--gray-600);
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .order-payment-method i {
        color: #b054e9;
    }

    .order-discount-badge {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.1));
        border: 1px solid rgba(34, 197, 94, 0.3);
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
    }

    .discount-label {
        color: #16a34a;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .discount-amount {
        color: #16a34a;
        font-weight: 700;
    }

    .btn-view-order {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    .btn-view-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--radius-lg);
        border: 2px solid var(--gray-100);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 1.5rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .empty-message {
        color: var(--gray-600);
        margin-bottom: 2rem;
    }

    .btn-shop {
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-shop:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    @media (max-width: 768px) {
        .orders-header h1 {
            font-size: 1.5rem;
        }

        .order-card-body {
            flex-direction: column;
        }

        .order-summary {
            width: 100%;
            min-width: unset;
        }

        .order-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-info {
            width: 100%;
        }

        .item-image {
            width: 50px;
            height: 50px;
        }

        .order-item-row {
            grid-template-columns: 50px 1fr auto;
            gap: 0.75rem;
            padding: 0.5rem;
        }

        .item-name {
            font-size: 0.875rem;
        }

        .item-meta {
            font-size: 0.75rem;
        }
    }
</style>

<div class="orders-page">
    <div class="orders-container">
        <div class="orders-header">
            <h1><?= __('account.orders') ?></h1>
            <p><?= __('account.orders_subtitle') ?></p>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="orders-stats">
                <div class="stat-card">
                    <h3><?= count($orders) ?></h3>
                    <p><?= __('account.total_orders') ?></p>
                </div>
                <div class="stat-card">
                    <h3>€<?= number_format(array_sum(array_column($orders, 'total')), 2) ?></h3>
                    <p><?= __('account.total_spent') ?></p>
                </div>
            </div>

            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-card-header">
                            <div class="order-info">
                                <span class="order-number">#<?= $order['order_id'] ?></span>
                                <span class="order-date"><?= date('d M Y', strtotime($order['order_date'])) ?></span>
                            </div>
                            <span class="order-status <?= strtolower($order['order_status']) ?>">
                                <?= __('account.status_' . strtolower($order['order_status'])) ?>
                            </span>
                        </div>

                        <div class="order-card-body">
                            <div class="order-items-list">
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $index => $item): ?>
                                        <?php if ($index < 3): ?>
                                            <div class="order-item-row">
                                                <div class="item-image">
                                                    <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/placeholder.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" loading="lazy">
                                                </div>
                                                <div class="item-details">
                                                    <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                                    <div class="item-meta">Talla: <?= htmlspecialchars($item['size']) ?></div>
                                                </div>
                                                <div class="item-quantity">x<?= $item['quantity'] ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if ($order['item_count'] > 3): ?>
                                        <div class="more-items">
                                            +<?= ($order['item_count'] - 3) ?> <?= ($order['item_count'] - 3) == 1 ? __('common.product') : __('common.products') ?> más
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="order-summary">
                                <div class="order-total-box">
                                    <div class="order-total-label"><?= __('checkout.total') ?></div>
                                    <div class="order-total-amount">€<?= number_format($order['total_amount'], 2) ?></div>
                                    <div class="order-payment-method">
                                        <i class="fas fa-credit-card"></i>
                                        <?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')) ?>
                                    </div>
                                </div>

                                <?php if ($order['discount_amount'] > 0): ?>
                                <div class="order-discount-badge">
                                    <span class="discount-label">
                                        <i class="fas fa-tag"></i>
                                        <?= !empty($order['coupon_code']) ? strtoupper($order['coupon_code']) : __('checkout.discount') ?>
                                    </span>
                                    <span class="discount-amount">-€<?= number_format($order['discount_amount'], 2) ?></span>
                                </div>
                                <?php endif; ?>

                                <a href="/mi-cuenta/pedidos/<?= $order['order_id'] ?>" class="btn-view-order">
                                    <i class="fas fa-eye"></i>
                                    <?= __('account.view_details') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h2 class="empty-title"><?= __('account.no_orders_title') ?></h2>
                <p class="empty-message">
                    <?= __('account.no_orders_message') ?>
                </p>
                <a href="/productos" class="btn-shop">
                    <i class="fas fa-shopping-bag"></i> <?= __('account.go_to_shop') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
