<?php
// Vista de Gestión de Pagos
$current_page = 'pagos';
$page_title = 'Gestión de Pagos';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-credit-card"></i>
            Transacciones de Pago
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por ID, cliente..." class="search-input">
            </div>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="processing">Procesando</option>
                <option value="completed">Completado</option>
                <option value="failed">Fallido</option>
                <option value="expired">Expirado</option>
                <option value="refunded">Reembolsado</option>
            </select>
            <select id="filterMethod" class="form-select" style="width: auto;">
                <option value="">Todos los métodos</option>
                <option value="oxapay_btc">Bitcoin</option>
                <option value="oxapay_eth">Ethereum</option>
                <option value="oxapay_usdt">USDT</option>
                <option value="telegram_manual">Telegram Manual</option>
                <option value="whatsapp_manual">WhatsApp Manual</option>
                <option value="bank_transfer">Transferencia</option>
            </select>
            <button class="btn btn-secondary" onclick="toggleDateFilters()">
                <i class="fas fa-calendar"></i>
                Filtrar por Fecha
            </button>
        </div>
    </div>

    <!-- Date filters (initially hidden) -->
    <div id="dateFilters" class="date-filters" style="display: none;">
        <div style="display: flex; gap: 1rem; align-items: center; padding: 1rem; background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-weight: 500; color: var(--gray-700);">Desde:</span>
                <input type="date" id="dateFrom" class="form-input" style="width: auto;">
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-weight: 500; color: var(--gray-700);">Hasta:</span>
                <input type="date" id="dateTo" class="form-input" style="width: auto;">
            </label>
            <button class="btn btn-primary" onclick="applyDateFilters()">
                <i class="fas fa-check"></i>
                Aplicar
            </button>
            <button class="btn btn-secondary" onclick="clearDateFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="pagosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Cantidad</th>
                        <th>Moneda</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Completado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagos)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-credit-card"></i>
                                    <p class="empty-state-title">No hay transacciones</p>
                                    <p class="empty-state-text">Las transacciones de pago aparecerán aquí</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $pago): ?>
                            <tr class="table-row-clickable" onclick="openPagoModal(<?= $pago['transaction_id'] ?>)">
                                <td><strong>#<?= $pago['transaction_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            <?= strtoupper(substr($pago['customer_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-900);">
                                                <?= htmlspecialchars($pago['customer_name']) ?>
                                            </div>
                                            <?php if ($pago['telegram_username']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fab fa-telegram"></i> @<?= htmlspecialchars($pago['telegram_username']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($pago['order_id']): ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-shopping-bag"></i>
                                            Pedido #<?= $pago['order_id'] ?>
                                        </span>
                                    <?php elseif ($pago['subscription_id']): ?>
                                        <span class="badge badge-purple">
                                            <i class="fas fa-sync-alt"></i>
                                            Suscripción #<?= $pago['subscription_id'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-question"></i>
                                            N/A
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $methodIcons = [
                                        'oxapay_btc' => '<i class="fab fa-bitcoin" style="color: #f7931a;"></i> Bitcoin',
                                        'oxapay_eth' => '<i class="fab fa-ethereum" style="color: #627eea;"></i> Ethereum',
                                        'oxapay_usdt' => '<i class="fas fa-dollar-sign" style="color: #26a17b;"></i> USDT',
                                        'telegram_manual' => '<i class="fab fa-telegram" style="color: #0088cc;"></i> Telegram',
                                        'whatsapp_manual' => '<i class="fab fa-whatsapp" style="color: #25d366;"></i> WhatsApp',
                                        'bank_transfer' => '<i class="fas fa-university" style="color: #667eea;"></i> Transferencia'
                                    ];
                                    echo $methodIcons[$pago['payment_method']] ?? $pago['payment_method'];
                                    ?>
                                </td>
                                <td>
                                    <strong style="color: var(--success); font-size: 1rem;">
                                        €<?= number_format($pago['amount'], 2) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?= strtoupper($pago['currency']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'expired' => 'secondary',
                                        'refunded' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'processing' => 'Procesando',
                                        'completed' => 'Completado',
                                        'failed' => 'Fallido',
                                        'expired' => 'Expirado',
                                        'refunded' => 'Reembolsado'
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'processing' => 'fa-spinner',
                                        'completed' => 'fa-check-circle',
                                        'failed' => 'fa-times-circle',
                                        'expired' => 'fa-hourglass-end',
                                        'refunded' => 'fa-undo'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusColors[$pago['status']] ?? 'secondary' ?>">
                                        <i class="fas <?= $statusIcons[$pago['status']] ?? 'fa-question' ?>"></i>
                                        <?= $statusLabels[$pago['status']] ?? $pago['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.875rem; color: var(--gray-700);">
                                        <?= date('d/m/Y H:i', strtotime($pago['initiated_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($pago['completed_at']): ?>
                                        <div style="font-size: 0.875rem; color: var(--gray-700);">
                                            <?= date('d/m/Y H:i', strtotime($pago['completed_at'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary" onclick="openPagoModal(<?= $pago['transaction_id'] ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($pago['status'] === 'pending' || $pago['status'] === 'processing'): ?>
                                            <button class="btn btn-sm btn-success" onclick="markAsCompleted(<?= $pago['transaction_id'] ?>)" title="Marcar como completado">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="markAsFailed(<?= $pago['transaction_id'] ?>)" title="Marcar como fallido">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($pago['status'] === 'completed'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="processRefund(<?= $pago['transaction_id'] ?>)" title="Procesar reembolso">
                                                <i class="fas fa-undo"></i>
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

        <?php if (!empty($pagos) && $total_pages > 1): ?>
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
    width: 250px;
}

.table-row-clickable {
    cursor: pointer;
    transition: var(--transition);
}

.table-row-clickable:hover {
    background: var(--gray-50);
}

.date-filters {
    border-top: 1px solid var(--gray-200);
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

.security-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 500;
}

.security-warning i {
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .crm-card-actions {
        width: 100%;
        flex-direction: column;
    }

    .search-input {
        width: 100%;
    }
}
</style>

<script>
// Función para abrir modal con detalles del pago
function openPagoModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para marcar como completado
async function markAsCompleted(id) {
    if (!confirm('¿Confirmar que el pago ha sido completado?')) return;

    try {
        const response = await fetch(`/admin/pagos/${id}/mark-completed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            crmAdmin.showToast('Pago marcado como completado', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            crmAdmin.showToast(data.error || 'Error al actualizar el pago', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        crmAdmin.showToast('Error al comunicar con el servidor', 'error');
    }
}

// Función para marcar como fallido
async function markAsFailed(id) {
    if (!confirm('¿Marcar este pago como fallido?')) return;

    try {
        const response = await fetch(`/admin/pagos/${id}/mark-failed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            crmAdmin.showToast('Pago marcado como fallido', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            crmAdmin.showToast(data.error || 'Error al actualizar el pago', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        crmAdmin.showToast('Error al comunicar con el servidor', 'error');
    }
}

// Función para procesar reembolso
async function processRefund(id) {
    if (!confirm('¿CONFIRMAR REEMBOLSO? Esta acción no se puede deshacer.')) return;

    try {
        const response = await fetch(`/admin/pagos/${id}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            crmAdmin.showToast('Reembolso procesado correctamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            crmAdmin.showToast(data.error || 'Error al procesar el reembolso', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        crmAdmin.showToast('Error al comunicar con el servidor', 'error');
    }
}

// Toggle date filters
function toggleDateFilters() {
    const filters = document.getElementById('dateFilters');
    filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
}

// Apply date filters
function applyDateFilters() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    const url = new URL(window.location);
    if (dateFrom) url.searchParams.set('date_from', dateFrom);
    if (dateTo) url.searchParams.set('date_to', dateTo);
    url.searchParams.delete('page'); // Reset to page 1

    window.location.href = url.toString();
}

// Clear date filters
function clearDateFilters() {
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';

    const url = new URL(window.location);
    url.searchParams.delete('date_from');
    url.searchParams.delete('date_to');
    url.searchParams.delete('page');

    window.location.href = url.toString();
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    // Payment method icon mapping
    const methodIcons = {
        'oxapay_btc': '<i class="fab fa-bitcoin" style="color: #f7931a;"></i> Bitcoin (BTC)',
        'oxapay_eth': '<i class="fab fa-ethereum" style="color: #627eea;"></i> Ethereum (ETH)',
        'oxapay_usdt': '<i class="fas fa-dollar-sign" style="color: #26a17b;"></i> Tether (USDT)',
        'telegram_manual': '<i class="fab fa-telegram" style="color: #0088cc;"></i> Telegram Manual',
        'whatsapp_manual': '<i class="fab fa-whatsapp" style="color: #25d366;"></i> WhatsApp Manual',
        'bank_transfer': '<i class="fas fa-university" style="color: #667eea;"></i> Transferencia Bancaria'
    };

    const statusColors = {
        'pending': 'warning',
        'processing': 'info',
        'completed': 'success',
        'failed': 'danger',
        'expired': 'secondary',
        'refunded': 'danger'
    };

    const statusLabels = {
        'pending': 'Pendiente',
        'processing': 'Procesando',
        'completed': 'Completado',
        'failed': 'Fallido',
        'expired': 'Expirado',
        'refunded': 'Reembolsado'
    };

    return `
        <div class="modal-pago-details">
            <!-- Security Warning -->
            <div class="security-warning">
                <i class="fas fa-shield-alt"></i>
                <span>Información sensible de pago - Manejo con confidencialidad</span>
            </div>

            <!-- Payment Header -->
            <div class="pago-header">
                <div class="pago-id">
                    <h3>Transacción #${data.transaction_id}</h3>
                    <span class="badge badge-${statusColors[data.status] || 'secondary'}">
                        ${statusLabels[data.status] || data.status}
                    </span>
                </div>
                <div class="pago-amount">
                    <div class="amount-label">Monto</div>
                    <div class="amount-value">€${parseFloat(data.amount).toFixed(2)}</div>
                    <div class="amount-currency">${data.currency}</div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="detail-section">
                <h4><i class="fas fa-user"></i> Información del Cliente</h4>
                <div class="detail-grid">
                    <div><strong>Nombre:</strong> ${data.customer_name}</div>
                    ${data.customer_email ? `<div><strong>Email:</strong> ${data.customer_email}</div>` : ''}
                    ${data.telegram_username ? `<div><strong>Telegram:</strong> @${data.telegram_username}</div>` : ''}
                    ${data.whatsapp_number ? `<div><strong>WhatsApp:</strong> ${data.whatsapp_number}</div>` : ''}
                </div>
            </div>

            <!-- Order/Subscription Info -->
            ${data.order_id ? `
                <div class="detail-section">
                    <h4><i class="fas fa-shopping-bag"></i> Pedido Relacionado</h4>
                    <div class="detail-grid">
                        <div><strong>ID Pedido:</strong> #${data.order_id}</div>
                        <div><strong>Tipo:</strong> ${data.order_type || 'N/A'}</div>
                        <div><strong>Estado:</strong> ${data.order_status || 'N/A'}</div>
                        <div><strong>Total:</strong> €${data.order_total ? parseFloat(data.order_total).toFixed(2) : '0.00'}</div>
                        ${data.tracking_number ? `<div><strong>Seguimiento:</strong> ${data.tracking_number}</div>` : ''}
                    </div>
                </div>
            ` : ''}

            ${data.subscription_id ? `
                <div class="detail-section">
                    <h4><i class="fas fa-sync-alt"></i> Suscripción Relacionada</h4>
                    <div class="detail-grid">
                        <div><strong>ID Suscripción:</strong> #${data.subscription_id}</div>
                        <div><strong>Plan:</strong> ${data.plan_name || 'N/A'}</div>
                        <div><strong>Estado:</strong> ${data.subscription_status || 'N/A'}</div>
                        <div><strong>Precio Mensual:</strong> €${data.monthly_price ? parseFloat(data.monthly_price).toFixed(2) : '0.00'}</div>
                    </div>
                </div>
            ` : ''}

            <!-- Payment Method Details -->
            <div class="detail-section payment-method-section">
                <h4><i class="fas fa-credit-card"></i> Método de Pago</h4>
                <div class="payment-method-badge">
                    ${methodIcons[data.payment_method] || data.payment_method}
                </div>

                ${data.payment_method.startsWith('oxapay_') ? `
                    <div class="crypto-details">
                        <h5>Detalles de Criptomoneda</h5>
                        <div class="detail-grid">
                            ${data.oxapay_crypto_amount ? `<div><strong>Cantidad Crypto:</strong> ${data.oxapay_crypto_amount} ${data.oxapay_crypto_currency || ''}</div>` : ''}
                            ${data.oxapay_network ? `<div><strong>Red:</strong> ${data.oxapay_network}</div>` : ''}
                            ${data.oxapay_wallet_address ? `
                                <div style="grid-column: 1 / -1;">
                                    <strong>Dirección Wallet:</strong>
                                    <div class="wallet-address">${data.oxapay_wallet_address}</div>
                                </div>
                            ` : ''}
                            ${data.oxapay_transaction_id ? `<div><strong>ID Oxapay:</strong> ${data.oxapay_transaction_id}</div>` : ''}
                            ${data.oxapay_qr_code ? `
                                <div style="grid-column: 1 / -1; text-align: center; margin-top: 1rem;">
                                    <strong>Código QR:</strong>
                                    <img src="${data.oxapay_qr_code}" alt="QR Code" style="max-width: 200px; margin-top: 0.5rem; border-radius: var(--radius-lg);">
                                </div>
                            ` : ''}
                        </div>
                    </div>
                ` : ''}

                ${data.payment_method.includes('manual') || data.payment_method === 'bank_transfer' ? `
                    <div class="manual-payment-details">
                        <h5>Detalles de Pago Manual</h5>
                        ${data.manual_payment_reference ? `<div><strong>Referencia:</strong> ${data.manual_payment_reference}</div>` : ''}
                        ${data.manual_payment_proof ? `
                            <div style="margin-top: 1rem;">
                                <strong>Prueba de Pago:</strong>
                                <a href="${data.manual_payment_proof}" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">
                                    <i class="fas fa-file-image"></i> Ver Comprobante
                                </a>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
            </div>

            <!-- Timeline -->
            <div class="detail-section">
                <h4><i class="fas fa-history"></i> Timeline</h4>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <strong>Iniciado</strong>
                            <span>${new Date(data.initiated_at).toLocaleString('es-ES')}</span>
                        </div>
                    </div>
                    ${data.completed_at ? `
                        <div class="timeline-item">
                            <div class="timeline-marker completed"></div>
                            <div class="timeline-content">
                                <strong>Completado</strong>
                                <span>${new Date(data.completed_at).toLocaleString('es-ES')}</span>
                            </div>
                        </div>
                    ` : ''}
                    ${data.verified_at ? `
                        <div class="timeline-item">
                            <div class="timeline-marker verified"></div>
                            <div class="timeline-content">
                                <strong>Verificado</strong>
                                <span>${new Date(data.verified_at).toLocaleString('es-ES')}</span>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>

            <!-- Admin Notes -->
            ${data.notes ? `
                <div class="detail-section">
                    <h4><i class="fas fa-sticky-note"></i> Notas Administrativas</h4>
                    <div class="admin-notes">${data.notes}</div>
                </div>
            ` : ''}

            <!-- Admin Actions -->
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                ${data.status === 'pending' || data.status === 'processing' ? `
                    <button class="btn btn-success" onclick="markAsCompleted(${data.transaction_id}); crmAdmin.closeModal();">
                        <i class="fas fa-check"></i>
                        Marcar Completado
                    </button>
                    <button class="btn btn-danger" onclick="markAsFailed(${data.transaction_id}); crmAdmin.closeModal();">
                        <i class="fas fa-times"></i>
                        Marcar Fallido
                    </button>
                ` : ''}
                ${data.status === 'completed' ? `
                    <button class="btn btn-warning" onclick="processRefund(${data.transaction_id}); crmAdmin.closeModal();">
                        <i class="fas fa-undo"></i>
                        Procesar Reembolso
                    </button>
                ` : ''}
            </div>
        </div>
    `;
};

// Search and filter functionality
document.getElementById('searchInput')?.addEventListener('input', () => {
    applyFilters();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    const url = new URL(window.location);
    const status = document.getElementById('filterStatus').value;
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
});

document.getElementById('filterMethod')?.addEventListener('change', () => {
    const url = new URL(window.location);
    const method = document.getElementById('filterMethod').value;
    if (method) {
        url.searchParams.set('payment_method', method);
    } else {
        url.searchParams.delete('payment_method');
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
});

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#pagosTable tbody tr:not(:first-child)');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}
</script>

<style>
/* Modal Styles */
.pago-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--gray-200);
}

.pago-id h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
    color: var(--gray-900);
}

.pago-amount {
    text-align: right;
}

.amount-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.amount-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--success);
}

.amount-currency {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.detail-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
}

.detail-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section h5 {
    font-size: 0.875rem;
    margin: 1rem 0 0.75rem 0;
    color: var(--gray-700);
    font-weight: 600;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}

.detail-grid div {
    color: var(--gray-700);
}

.payment-method-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.payment-method-section h4,
.payment-method-section h5 {
    color: white;
}

.payment-method-section .detail-grid div {
    color: rgba(255, 255, 255, 0.95);
}

.payment-method-badge {
    font-size: 1.25rem;
    font-weight: 600;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
}

.crypto-details,
.manual-payment-details {
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem;
    border-radius: var(--radius-lg);
}

.wallet-address {
    background: rgba(0, 0, 0, 0.2);
    padding: 0.75rem;
    border-radius: var(--radius-md);
    font-family: monospace;
    font-size: 0.875rem;
    word-break: break-all;
    margin-top: 0.5rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-300);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--gray-400);
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.timeline-marker.completed {
    background: var(--success);
}

.timeline-marker.verified {
    background: var(--info);
}

.timeline-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.timeline-content strong {
    color: var(--gray-900);
}

.timeline-content span {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.admin-notes {
    background: white;
    padding: 1rem;
    border-radius: var(--radius-md);
    color: var(--gray-700);
    white-space: pre-wrap;
}
</style>
