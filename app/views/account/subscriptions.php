<?php
/**
 * Subscriptions Page
 * Displays customer's active subscriptions
 */
?>

<style>
    .subscriptions-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    }

    .subscriptions-page ~ .footer {
        margin-top: 0;
    }

    .subscriptions-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .subscriptions-header {
        margin-bottom: 2rem;
    }

    .subscriptions-header h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #b054e9, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .subscriptions-header p {
        color: var(--gray-600);
        font-size: 1rem;
    }

    .subscriptions-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .subscription-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid var(--gray-100);
        overflow: hidden;
        transition: all 0.2s;
    }

    .subscription-card:hover {
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.15);
        border-color: var(--primary);
    }

    .subscription-header {
        background: linear-gradient(135deg, #b054e9, #ec4899);
        padding: 1.5rem;
        color: white;
    }

    .subscription-plan {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .subscription-price {
        font-size: 1.125rem;
        opacity: 0.9;
    }

    .subscription-body {
        padding: 1.5rem;
    }

    .subscription-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-label {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .info-value {
        color: var(--gray-900);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .subscription-status {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .subscription-status.active {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }

    .subscription-status.paused {
        background: rgba(255, 177, 66, 0.1);
        color: #ff9933;
    }

    .subscription-status.cancelled {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }

    .subscription-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-subscription {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        border: none;
    }

    .btn-subscription.primary {
        background: linear-gradient(135deg, #b054e9, #ec4899);
        color: white;
    }

    .btn-subscription.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    .btn-subscription.secondary {
        background: white;
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-subscription.secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-subscription.danger {
        background: white;
        color: #dc2626;
        border: 2px solid rgba(220, 38, 38, 0.2);
    }

    .btn-subscription.danger:hover {
        background: rgba(220, 38, 38, 0.05);
        border-color: #dc2626;
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

    .btn-browse {
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

    .btn-browse:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(176, 84, 233, 0.4);
    }

    @media (max-width: 768px) {
        .subscriptions-header h1 {
            font-size: 1.5rem;
        }

        .subscriptions-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="subscriptions-page">
    <div class="subscriptions-container">
        <div class="subscriptions-header">
            <h1><?= __('account.subscriptions') ?></h1>
            <p><?= __('account.subscriptions_subtitle') ?></p>
        </div>

        <?php if (!empty($subscriptions)): ?>
            <div class="subscriptions-list">
                <?php foreach ($subscriptions as $subscription): ?>
                    <div class="subscription-card">
                        <div class="subscription-header">
                            <div class="subscription-plan"><?= htmlspecialchars($subscription['plan_name']) ?></div>
                            <div class="subscription-price">€<?= number_format($subscription['price'], 2) ?><?= __('account.per_month') ?></div>
                        </div>

                        <div class="subscription-body">
                            <div class="subscription-info">
                                <div class="info-row">
                                    <span class="info-label"><?= __('account.subscription_status') ?></span>
                                    <span class="subscription-status <?= strtolower($subscription['status']) ?>">
                                        <?= __('account.subscription_' . strtolower($subscription['status'])) ?>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><?= __('account.subscription_start') ?></span>
                                    <span class="info-value"><?= date('d M Y', strtotime($subscription['start_date'])) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><?= __('account.subscription_next') ?></span>
                                    <span class="info-value"><?= date('d M Y', strtotime($subscription['next_billing_date'])) ?></span>
                                </div>
                            </div>

                            <div class="subscription-actions">
                                <a href="/mi-cuenta/suscripciones/<?= $subscription['subscription_id'] ?>" class="btn-subscription primary">
                                    <i class="fas fa-eye"></i> <?= __('account.view_details') ?>
                                </a>
                                <?php if ($subscription['status'] === 'active'): ?>
                                    <button class="btn-subscription secondary">
                                        <i class="fas fa-pause"></i> <?= __('account.subscription_pause') ?>
                                    </button>
                                <?php endif; ?>
                                <button class="btn-subscription danger">
                                    <i class="fas fa-times"></i> <?= __('account.subscription_cancel') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="empty-title"><?= __('account.no_subscriptions_title') ?></h2>
                <p class="empty-message">
                    <?= __('account.no_subscriptions_message') ?>
                </p>
                <a href="/mystery-box" class="btn-browse">
                    <i class="fas fa-gift"></i> <?= __('account.view_mystery_boxes') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
