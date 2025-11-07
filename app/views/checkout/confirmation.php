<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-container">
            <!-- Success Icon -->
            <div class="success-icon">
                <div class="icon-circle">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <!-- Success Message -->
            <h1 class="confirmation-title">Pedido Confirmado</h1>
            <p class="confirmation-subtitle">
                Tu pedido #<?= htmlspecialchars($order['order_number']) ?> ha sido procesado correctamente
            </p>

            <!-- Order Details Card -->
            <div class="confirmation-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-receipt"></i>
                        Detalles del Pedido
                    </h3>
                </div>

                <div class="order-info-grid">
                    <div class="info-item">
                        <span class="info-label">Número de Pedido</span>
                        <span class="info-value">#<?= htmlspecialchars($order['order_number']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total</span>
                        <span class="info-value total">€<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado</span>
                        <span class="status-badge <?= $order['order_status'] ?>">
                            <?php
                            $statusLabels = [
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregado',
                                'cancelled' => 'Cancelado'
                            ];
                            echo $statusLabels[$order['order_status']] ?? 'Pendiente';
                            ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($order['items'])): ?>
                    <div class="order-items-section">
                        <h4>Artículos del Pedido</h4>
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="<?= htmlspecialchars($item['image_path'] ?? '/img/logo.png') ?>"
                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                             onerror="this.src='/img/logo.png'">
                                    </div>
                                    <div class="item-details">
                                        <h5><?= htmlspecialchars($item['product_name']) ?></h5>
                                        <p>Talla: <?= htmlspecialchars($item['size']) ?></p>
                                        <p>Cantidad: <?= $item['quantity'] ?></p>
                                    </div>
                                    <div class="item-price">
                                        €<?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Next Steps -->
            <div class="next-steps-card">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Próximos Pasos
                </h3>
                <ul class="steps-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>Recibirás un email de confirmación con los detalles de tu pedido</span>
                    </li>
                    <li>
                        <i class="fas fa-box"></i>
                        <span>Procesaremos tu pedido en las próximas 24-48 horas</span>
                    </li>
                    <li>
                        <i class="fas fa-truck"></i>
                        <span>Te enviaremos un código de seguimiento cuando se envíe tu pedido</span>
                    </li>
                    <li>
                        <i class="fas fa-home"></i>
                        <span>Recibirás tu pedido en 3-5 días laborables</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="confirmation-actions">
                <a href="/mi-cuenta/pedidos/<?= $order['order_id'] ?>" class="btn btn-primary">
                    <i class="fas fa-eye"></i>
                    Ver Detalles del Pedido
                </a>
                <a href="/productos" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i>
                    Seguir Comprando
                </a>
            </div>

            <!-- Support -->
            <div class="support-section">
                <p>¿Necesitas ayuda?</p>
                <a href="/contacto" class="support-link">
                    <i class="fas fa-headset"></i>
                    Contacta con Soporte
                </a>
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
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

/* Success Icon */
.success-icon {
    margin-bottom: var(--space-6);
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
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
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

.confirmation-subtitle {
    font-size: 1.125rem;
    color: var(--gray-600);
    margin-bottom: var(--space-8);
}

/* Confirmation Card */
.confirmation-card {
    background: white;
    padding: var(--space-8);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    text-align: left;
    margin-bottom: var(--space-6);
    border: 1px solid #f3f4f6;
}

.card-header {
    margin-bottom: var(--space-6);
}

.card-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.card-header h3 i {
    color: #f479d9;
}

/* Order Info Grid */
.order-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-5);
    padding: var(--space-6);
    background: #fafafa;
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-6);
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
}

.info-value.total {
    font-size: 1.5rem;
    color: #10b981;
}

.status-badge {
    display: inline-block;
    padding: var(--space-2) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.shipped {
    background: #e0e7ff;
    color: #4338ca;
}

.status-badge.delivered {
    background: #d1fae5;
    color: #065f46;
}

/* Order Items Section */
.order-items-section {
    padding-top: var(--space-6);
    border-top: 1px solid #e5e7eb;
}

.order-items-section h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
}

.order-items {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.order-item {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: var(--space-4);
    padding: var(--space-4);
    background: #fafafa;
    border-radius: var(--radius-md);
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

.item-details h5 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 var(--space-1) 0;
}

.item-details p {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
}

.item-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
}

/* Next Steps Card */
.next-steps-card {
    background: white;
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    text-align: left;
    margin-bottom: var(--space-6);
    border: 1px solid #f3f4f6;
}

.next-steps-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.next-steps-card h3 i {
    color: #8b5cf6;
}

.steps-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.steps-list li {
    display: flex;
    align-items: flex-start;
    gap: var(--space-3);
    padding: var(--space-3);
    background: #fafafa;
    border-radius: var(--radius-md);
}

.steps-list i {
    font-size: 1.25rem;
    color: #f479d9;
    flex-shrink: 0;
    margin-top: 2px;
}

.steps-list span {
    font-size: 0.9375rem;
    color: var(--gray-700);
    line-height: 1.5;
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
}

/* Support Section */
.support-section {
    padding: var(--space-6);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.support-section p {
    font-size: 1rem;
    color: var(--gray-700);
    margin-bottom: var(--space-3);
}

.support-link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: var(--transition);
}

.support-link:hover {
    color: #7c3aed;
    gap: var(--space-3);
}

.support-link i {
    font-size: 1.125rem;
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

    .order-info-grid {
        grid-template-columns: 1fr;
        gap: var(--space-4);
    }

    .order-item {
        grid-template-columns: 60px 1fr;
        gap: var(--space-3);
    }

    .item-image {
        width: 60px;
        height: 60px;
    }

    .item-price {
        grid-column: 1 / -1;
        text-align: center;
        padding-top: var(--space-2);
        border-top: 1px solid #e5e7eb;
    }

    .confirmation-actions {
        flex-direction: column;
    }

    .confirmation-actions .btn {
        width: 100%;
    }
}
</style>
