<?php
// Vista de Gestión de Pedidos
$current_page = 'pedidos';
$page_title = 'Gestión de Pedidos';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-shopping-bag"></i>
            Pedidos
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por ID, cliente o tracking..." class="search-input">
            </div>
            <select id="filterOrderStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="pending_payment">Pago Pendiente</option>
                <option value="processing">En Proceso</option>
                <option value="shipped">Enviado</option>
                <option value="delivered">Entregado</option>
                <option value="cancelled">Cancelado</option>
                <option value="refunded">Reembolsado</option>
            </select>
            <select id="filterPaymentStatus" class="form-select" style="width: auto;">
                <option value="">Estado de pago</option>
                <option value="pending">Pendiente</option>
                <option value="completed">Completado</option>
                <option value="failed">Fallido</option>
                <option value="refunded">Reembolsado</option>
            </select>
            <select id="filterOrderType" class="form-select" style="width: auto;">
                <option value="">Tipo de pedido</option>
                <option value="catalog">Catálogo</option>
                <option value="mystery_box">Mystery Box</option>
                <option value="subscription_initial">Suscripción</option>
                <option value="drop">Drop</option>
            </select>
            <select id="filterPaymentMethod" class="form-select" style="width: auto;">
                <option value="">Método de pago</option>
                <option value="oxapay">Oxapay (Crypto)</option>
                <option value="telegram">Telegram</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="manual">Manual</option>
            </select>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="pedidosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado Pedido</th>
                        <th>Estado Pago</th>
                        <th>Método Pago</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-bag"></i>
                                    <p class="empty-state-title">No hay pedidos</p>
                                    <p class="empty-state-text">Los pedidos aparecerán aquí cuando se realicen compras</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr class="table-row-clickable" onclick="openPedidoModal(<?= $pedido['order_id'] ?>)">
                                <td><strong>#<?= $pedido['order_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                            <?= strtoupper(substr($pedido['customer_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-900);">
                                                <?= htmlspecialchars($pedido['customer_name']) ?>
                                            </div>
                                            <?php if ($pedido['customer_email']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($pedido['customer_email']) ?>
                                                </div>
                                            <?php elseif ($pedido['telegram_username']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fab fa-telegram"></i> @<?= htmlspecialchars($pedido['telegram_username']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.875rem;">
                                        <div style="font-weight: 600; color: var(--gray-700);">
                                            <?= $pedido['items_count'] ?> producto<?= $pedido['items_count'] > 1 ? 's' : '' ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--gray-500); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($pedido['product_names']) ?>">
                                            <?= htmlspecialchars($pedido['product_names']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--success); font-size: 1rem;">€<?= number_format($pedido['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $orderStatusColors = [
                                        'pending_payment' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'danger'
                                    ];
                                    $orderStatusLabels = [
                                        'pending_payment' => 'Pago Pendiente',
                                        'processing' => 'En Proceso',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado',
                                        'refunded' => 'Reembolsado'
                                    ];
                                    $orderStatusIcons = [
                                        'pending_payment' => 'fa-clock',
                                        'processing' => 'fa-box',
                                        'shipped' => 'fa-shipping-fast',
                                        'delivered' => 'fa-check-circle',
                                        'cancelled' => 'fa-times-circle',
                                        'refunded' => 'fa-undo'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $orderStatusColors[$pedido['order_status']] ?? 'secondary' ?>">
                                        <i class="fas <?= $orderStatusIcons[$pedido['order_status']] ?? 'fa-question' ?>"></i>
                                        <?= $orderStatusLabels[$pedido['order_status']] ?? $pedido['order_status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $paymentStatusColors = [
                                        'pending' => 'warning',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'danger',
                                        'partially_refunded' => 'warning'
                                    ];
                                    $paymentStatusLabels = [
                                        'pending' => 'Pendiente',
                                        'completed' => 'Completado',
                                        'failed' => 'Fallido',
                                        'refunded' => 'Reembolsado',
                                        'partially_refunded' => 'Parcial'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $paymentStatusColors[$pedido['payment_status']] ?? 'secondary' ?>">
                                        <?= $paymentStatusLabels[$pedido['payment_status']] ?? $pedido['payment_status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($pedido['payment_method']): ?>
                                        <?php
                                        $paymentMethodLabels = [
                                            'oxapay' => 'Oxapay',
                                            'telegram' => 'Telegram',
                                            'whatsapp' => 'WhatsApp',
                                            'manual' => 'Manual'
                                        ];
                                        $paymentMethodIcons = [
                                            'oxapay' => 'fa-bitcoin',
                                            'telegram' => 'fab fa-telegram',
                                            'whatsapp' => 'fab fa-whatsapp',
                                            'manual' => 'fa-hand-holding-usd'
                                        ];
                                        ?>
                                        <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                            <i class="<?= $paymentMethodIcons[$pedido['payment_method']] ?? 'fas fa-credit-card' ?>"></i>
                                            <?= $paymentMethodLabels[$pedido['payment_method']] ?? $pedido['payment_method'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido['order_date'])) ?></td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary" onclick="openPedidoModal(<?= $pedido['order_id'] ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($pedido['tracking_number']): ?>
                                            <button class="btn btn-sm btn-info" onclick="copyTracking('<?= htmlspecialchars($pedido['tracking_number']) ?>')" title="Copiar tracking">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pedidos) && $total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-link <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 1rem;
    color: var(--gray-400);
}

.search-input {
    padding-left: 2.5rem;
    min-width: 300px;
}

.table-row-clickable {
    cursor: pointer;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.pagination-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--gray-700);
    font-weight: 500;
    transition: var(--transition);
}

.pagination-link:hover {
    background: var(--gray-100);
    border-color: var(--primary);
}

.pagination-link.active {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    border-color: transparent;
}

@media (max-width: 1400px) {
    .crm-card-actions {
        flex-wrap: wrap;
    }
}

@media (max-width: 768px) {
    .crm-card-actions {
        width: 100%;
        flex-direction: column;
    }

    .search-input {
        width: 100%;
        min-width: auto;
    }
}
</style>

<script>
// Función para abrir modal con detalles del pedido
function openPedidoModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para copiar tracking number
function copyTracking(tracking) {
    navigator.clipboard.writeText(tracking);
    crmAdmin.showSuccess('Tracking number copiado: ' + tracking);
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    return `
        <div class="modal-pedido-details">
            <div class="pedido-header">
                <div class="pedido-info-main">
                    <h3>Pedido #${data.order_id}</h3>
                    <div class="pedido-meta">
                        <span class="badge badge-${getOrderStatusColor(data.order_status)}">
                            <i class="fas ${getOrderStatusIcon(data.order_status)}"></i>
                            ${getOrderStatusLabel(data.order_status)}
                        </span>
                        <span class="badge badge-${getPaymentStatusColor(data.payment_status)}">
                            <i class="fas fa-credit-card"></i>
                            ${getPaymentStatusLabel(data.payment_status)}
                        </span>
                        <span class="badge badge-info">
                            <i class="fas fa-tag"></i>
                            ${getOrderTypeLabel(data.order_type)}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pedido-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Productos</div>
                        <div class="stat-value">${data.items.length}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total</div>
                        <div class="stat-value">€${parseFloat(data.total_amount).toFixed(2)}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Cliente</div>
                        <div class="stat-value" style="font-size: 0.875rem;">${data.customer.full_name}</div>
                    </div>
                </div>
            </div>

            <div class="pedido-details-grid">
                <div class="detail-section">
                    <h4><i class="fas fa-shopping-bag"></i> Productos del Pedido</h4>
                    <div class="order-items-list">
                        ${data.items.map(item => `
                            <div class="order-item">
                                <div class="order-item-info">
                                    <div style="font-weight: 600; color: var(--gray-900);">${item.product_name}</div>
                                    <div style="font-size: 0.875rem; color: var(--gray-600);">
                                        ${item.team_name ? item.team_name + ' - ' : ''}${item.league_name || ''}<br>
                                        Talla: ${item.size} | Cantidad: ${item.quantity}
                                    </div>
                                    ${item.has_personalization ? `
                                        <div style="font-size: 0.75rem; color: var(--primary); margin-top: 0.25rem;">
                                            <i class="fas fa-font"></i> ${item.personalization_name} ${item.personalization_number}
                                        </div>
                                    ` : ''}
                                    ${item.has_patches ? `
                                        <div style="font-size: 0.75rem; color: var(--primary); margin-top: 0.25rem;">
                                            <i class="fas fa-certificate"></i> Con parches
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="order-item-price">
                                    <strong>€${parseFloat(item.subtotal).toFixed(2)}</strong>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="order-totals">
                        <div class="order-total-row">
                            <span>Subtotal:</span>
                            <span>€${parseFloat(data.subtotal).toFixed(2)}</span>
                        </div>
                        ${data.discount_amount > 0 ? `
                            <div class="order-total-row" style="color: var(--success);">
                                <span>Descuento:</span>
                                <span>-€${parseFloat(data.discount_amount).toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div class="order-total-row">
                            <span>Envío:</span>
                            <span>${data.shipping_cost > 0 ? '€' + parseFloat(data.shipping_cost).toFixed(2) : 'GRATIS'}</span>
                        </div>
                        <div class="order-total-row total">
                            <span><strong>TOTAL:</strong></span>
                            <span><strong>€${parseFloat(data.total_amount).toFixed(2)}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Dirección de Envío</h4>
                    ${data.shipping_address ? `
                        <p><strong>${data.shipping_address.recipient_name}</strong></p>
                        <p>${data.shipping_address.street_address}</p>
                        ${data.shipping_address.additional_address ? `<p>${data.shipping_address.additional_address}</p>` : ''}
                        <p>${data.shipping_address.postal_code} ${data.shipping_address.city}</p>
                        <p>${data.shipping_address.province}, ${data.shipping_address.country}</p>
                        <p style="margin-top: 0.5rem;">
                            <i class="fas fa-phone"></i> ${data.shipping_address.phone}
                        </p>
                        ${data.shipping_address.additional_notes ? `
                            <p style="margin-top: 0.5rem; font-style: italic; color: var(--gray-600);">
                                <i class="fas fa-sticky-note"></i> ${data.shipping_address.additional_notes}
                            </p>
                        ` : ''}
                    ` : '<p>No hay dirección de envío</p>'}
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Información General</h4>
                    <p><strong>ID:</strong> #${data.order_id}</p>
                    <p><strong>Fecha:</strong> ${new Date(data.order_date).toLocaleString('es-ES')}</p>
                    <p><strong>Tipo:</strong> ${getOrderTypeLabel(data.order_type)}</p>
                    <p><strong>Origen:</strong> ${getOrderSourceLabel(data.order_source)}</p>
                    ${data.payment_method ? `<p><strong>Método de pago:</strong> ${getPaymentMethodLabel(data.payment_method)}</p>` : ''}
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-shipping-fast"></i> Tracking</h4>
                    ${data.tracking_number ? `
                        <p><strong>Número de seguimiento:</strong></p>
                        <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem;">
                            <input type="text" value="${data.tracking_number}" readonly style="flex: 1; padding: 0.5rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md);">
                            <button class="btn btn-sm btn-secondary" onclick="copyTracking('${data.tracking_number}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        ${data.carrier ? `<p style="margin-top: 0.5rem;"><strong>Transportista:</strong> ${data.carrier}</p>` : ''}
                    ` : `
                        <p style="color: var(--gray-500);">Sin número de seguimiento</p>
                        <button class="btn btn-sm btn-primary" style="margin-top: 0.5rem;" onclick="addTracking(${data.order_id})">
                            <i class="fas fa-plus"></i> Añadir Tracking
                        </button>
                    `}
                </div>
            </div>

            ${data.timeline && data.timeline.length > 0 ? `
                <div class="detail-section" style="margin-top: 1.5rem;">
                    <h4><i class="fas fa-history"></i> Timeline del Pedido</h4>
                    <div class="order-timeline">
                        ${data.timeline.map((event, index) => `
                            <div class="timeline-item ${event.completed ? 'completed' : ''}">
                                <div class="timeline-icon ${event.color}">
                                    <i class="fas ${event.icon}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">${event.status}</div>
                                    <div class="timeline-date">${event.date ? new Date(event.date).toLocaleString('es-ES') : 'Pendiente'}</div>
                                    ${event.tracking ? `<div class="timeline-tracking">Tracking: ${event.tracking}</div>` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            ${data.admin_notes ? `
                <div class="detail-section" style="margin-top: 1.5rem;">
                    <h4><i class="fas fa-sticky-note"></i> Notas del Administrador</h4>
                    <p style="white-space: pre-wrap;">${data.admin_notes}</p>
                </div>
            ` : ''}

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                ${data.order_status !== 'delivered' && data.order_status !== 'cancelled' ? `
                    <button class="btn btn-primary" onclick="updateOrderStatus(${data.order_id})">
                        <i class="fas fa-edit"></i>
                        Actualizar Estado
                    </button>
                ` : ''}
                ${data.order_status === 'pending_payment' || data.order_status === 'processing' ? `
                    <button class="btn btn-danger" onclick="cancelOrder(${data.order_id})">
                        <i class="fas fa-times"></i>
                        Cancelar Pedido
                    </button>
                ` : ''}
            </div>
        </div>
    `;
};

// Helper functions for status labels and colors
function getOrderStatusColor(status) {
    const colors = {
        'pending_payment': 'warning',
        'processing': 'info',
        'shipped': 'info',
        'delivered': 'success',
        'cancelled': 'danger',
        'refunded': 'danger'
    };
    return colors[status] || 'secondary';
}

function getOrderStatusLabel(status) {
    const labels = {
        'pending_payment': 'Pago Pendiente',
        'processing': 'En Proceso',
        'shipped': 'Enviado',
        'delivered': 'Entregado',
        'cancelled': 'Cancelado',
        'refunded': 'Reembolsado'
    };
    return labels[status] || status;
}

function getOrderStatusIcon(status) {
    const icons = {
        'pending_payment': 'fa-clock',
        'processing': 'fa-box',
        'shipped': 'fa-shipping-fast',
        'delivered': 'fa-check-circle',
        'cancelled': 'fa-times-circle',
        'refunded': 'fa-undo'
    };
    return icons[status] || 'fa-question';
}

function getPaymentStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'completed': 'success',
        'failed': 'danger',
        'refunded': 'danger',
        'partially_refunded': 'warning'
    };
    return colors[status] || 'secondary';
}

function getPaymentStatusLabel(status) {
    const labels = {
        'pending': 'Pendiente',
        'completed': 'Completado',
        'failed': 'Fallido',
        'refunded': 'Reembolsado',
        'partially_refunded': 'Parcial'
    };
    return labels[status] || status;
}

function getOrderTypeLabel(type) {
    const labels = {
        'catalog': 'Catálogo',
        'mystery_box': 'Mystery Box',
        'subscription_initial': 'Suscripción',
        'drop': 'Drop',
        'upsell': 'Upsell'
    };
    return labels[type] || type;
}

function getOrderSourceLabel(source) {
    const labels = {
        'web': 'Web',
        'telegram': 'Telegram',
        'whatsapp': 'WhatsApp',
        'instagram': 'Instagram'
    };
    return labels[source] || source;
}

function getPaymentMethodLabel(method) {
    const labels = {
        'oxapay': 'Oxapay (Crypto)',
        'telegram': 'Telegram',
        'whatsapp': 'WhatsApp',
        'manual': 'Manual'
    };
    return labels[method] || method;
}

// Update order status
async function updateOrderStatus(orderId) {
    const status = prompt('Nuevo estado del pedido:\n\n' +
        'pending_payment - Pago Pendiente\n' +
        'processing - En Proceso\n' +
        'shipped - Enviado\n' +
        'delivered - Entregado\n' +
        'cancelled - Cancelado\n' +
        'refunded - Reembolsado');

    if (!status) return;

    const validStatuses = ['pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
    if (!validStatuses.includes(status)) {
        showErrorModal('Estado inválido');
        return;
    }

    let tracking = null;
    let carrier = null;

    if (status === 'shipped') {
        tracking = prompt('Número de tracking:');
        carrier = prompt('Transportista (opcional):');
    }

    try {
        const response = await fetch(`/api/admin/pedidos/${orderId}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_status: status, tracking_number: tracking, carrier: carrier })
        });

        if (response.ok) {
            crmAdmin.showSuccess('Estado actualizado correctamente');
            crmAdmin.closeModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error('Error al actualizar');
        }
    } catch (error) {
        crmAdmin.showError('Error al actualizar el estado');
    }
}

// Add tracking
async function addTracking(orderId) {
    const tracking = prompt('Número de tracking:');
    if (!tracking) return;

    const carrier = prompt('Transportista (opcional):');

    try {
        const response = await fetch(`/api/admin/pedidos/${orderId}/tracking`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tracking_number: tracking, carrier: carrier })
        });

        if (response.ok) {
            crmAdmin.showSuccess('Tracking añadido correctamente');
            crmAdmin.closeModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error('Error al añadir tracking');
        }
    } catch (error) {
        crmAdmin.showError('Error al añadir el tracking');
    }
}

// Cancel order
async function cancelOrder(orderId) {
    const reason = prompt('Motivo de cancelación:');
    if (!reason) return;

    if (!confirm('¿Estás seguro de que deseas cancelar este pedido?')) return;

    try {
        const response = await fetch(`/api/admin/pedidos/${orderId}/cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason: reason })
        });

        if (response.ok) {
            crmAdmin.showSuccess('Pedido cancelado correctamente');
            crmAdmin.closeModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error('Error al cancelar');
        }
    } catch (error) {
        crmAdmin.showError('Error al cancelar el pedido');
    }
}

// Search and filter functionality
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    filterTable();
});

document.getElementById('filterOrderStatus')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterPaymentStatus')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterOrderType')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterPaymentMethod')?.addEventListener('change', () => {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const orderStatus = document.getElementById('filterOrderStatus').value;
    const paymentStatus = document.getElementById('filterPaymentStatus').value;
    const orderType = document.getElementById('filterOrderType').value;
    const paymentMethod = document.getElementById('filterPaymentMethod').value;
    const rows = document.querySelectorAll('#pedidosTable tbody tr:not(:first-child)');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = text.includes(searchTerm);
        const matchesOrderStatus = !orderStatus || text.includes(orderStatus);
        const matchesPaymentStatus = !paymentStatus || text.includes(paymentStatus);
        const matchesOrderType = !orderType || text.includes(orderType);
        const matchesPaymentMethod = !paymentMethod || text.includes(paymentMethod);

        row.style.display = matchesSearch && matchesOrderStatus && matchesPaymentStatus && matchesOrderType && matchesPaymentMethod ? '' : 'none';
    });
}
</script>

<style>
.pedido-header {
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 2rem;
}

.pedido-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.pedido-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pedido-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
}

.pedido-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
}

.detail-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section p {
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1rem;
    background: white;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
}

.order-item-info {
    flex: 1;
}

.order-item-price {
    font-size: 1.125rem;
    color: var(--success);
    margin-left: 1rem;
}

.order-totals {
    border-top: 1px solid var(--gray-200);
    padding-top: 1rem;
    margin-top: 1rem;
}

.order-total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.order-total-row.total {
    font-size: 1.125rem;
    color: var(--gray-900);
    padding-top: 0.5rem;
    border-top: 2px solid var(--gray-300);
    margin-top: 0.5rem;
}

.order-timeline {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    position: relative;
    padding-left: 2.5rem;
}

.timeline-item {
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 2rem;
    width: 2px;
    height: calc(100% + 1.5rem);
    background: var(--gray-300);
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-icon {
    position: absolute;
    left: -2.5rem;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.timeline-icon.success {
    background: var(--success);
}

.timeline-icon.info {
    background: var(--info);
}

.timeline-icon.warning {
    background: var(--warning);
}

.timeline-icon.danger {
    background: var(--danger);
}

.timeline-content {
    background: white;
    padding: 1rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
}

.timeline-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.timeline-tracking {
    font-size: 0.75rem;
    color: var(--primary);
    margin-top: 0.5rem;
}
</style>
