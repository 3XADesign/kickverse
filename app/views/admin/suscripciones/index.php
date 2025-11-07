<?php
// Vista de Gestión de Suscripciones
$current_page = 'suscripciones';
$page_title = 'Gestión de Suscripciones';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-crown"></i>
            Suscripciones
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por cliente..." class="search-input" value="<?= htmlspecialchars($filters['customer_search'] ?? '') ?>">
            </div>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Activas</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                <option value="paused" <?= ($filters['status'] ?? '') === 'paused' ? 'selected' : '' ?>>Pausadas</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
                <option value="expired" <?= ($filters['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expiradas</option>
            </select>
            <select id="filterPlan" class="form-select" style="width: auto;">
                <option value="">Todos los planes</option>
                <?php foreach ($planes as $plan): ?>
                    <option value="<?= $plan['plan_id'] ?>" <?= ($filters['plan_id'] ?? '') == $plan['plan_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($plan['plan_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <?php if (isset($stats)): ?>
        <div class="stats-grid" style="margin: 1.5rem 0;">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Suscripciones</div>
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Activas</div>
                    <div class="stat-value"><?= $stats['active'] ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Pendientes</div>
                    <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #30cfd0, #330867);">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Pausadas</div>
                    <div class="stat-value"><?= $stats['paused'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="suscripcionesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Talla</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Próximo Pago</th>
                        <th>Meses Pagados</th>
                        <th>Total Pagado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suscripciones)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-crown"></i>
                                    <p class="empty-state-title">No hay suscripciones</p>
                                    <p class="empty-state-text">No se encontraron suscripciones con los filtros seleccionados</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suscripciones as $sub): ?>
                            <tr class="table-row-clickable" onclick="openSuscripcionModal(<?= $sub['subscription_id'] ?>)">
                                <td><strong>#<?= $sub['subscription_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            <?= strtoupper(substr($sub['customer_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-900);">
                                                <?= htmlspecialchars($sub['customer_name']) ?>
                                            </div>
                                            <?php if ($sub['telegram_username']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fab fa-telegram"></i> @<?= htmlspecialchars($sub['telegram_username']) ?>
                                                </div>
                                            <?php elseif ($sub['customer_email']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($sub['customer_email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($sub['plan_name']) ?></strong>
                                        <div style="font-size: 0.75rem; color: var(--gray-500);">
                                            <?php
                                            $planTypeLabels = [
                                                'fan' => 'Fan',
                                                'premium_random' => 'Premium Random',
                                                'premium_top' => 'Premium TOP',
                                                'retro_top' => 'Retro TOP'
                                            ];
                                            ?>
                                            <?= $planTypeLabels[$sub['plan_type']] ?? $sub['plan_type'] ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= $sub['preferred_size'] ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger',
                                        'paused' => 'info',
                                        'expired' => 'secondary'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Activa',
                                        'pending' => 'Pendiente',
                                        'cancelled' => 'Cancelada',
                                        'paused' => 'Pausada',
                                        'expired' => 'Expirada'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusColors[$sub['status']] ?? 'secondary' ?>">
                                        <?= $statusLabels[$sub['status']] ?? $sub['status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($sub['start_date'])) ?></td>
                                <td>
                                    <?php if ($sub['next_billing_date']): ?>
                                        <strong><?= date('d/m/Y', strtotime($sub['next_billing_date'])) ?></strong>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--primary);"><?= $sub['total_months_paid'] ?></strong> meses
                                </td>
                                <td>
                                    <strong style="color: var(--success);">€<?= number_format($sub['total_paid'] ?? 0, 2) ?></strong>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary" onclick="openSuscripcionModal(<?= $sub['subscription_id'] ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($sub['status'] === 'active'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="pauseSuscripcion(<?= $sub['subscription_id'] ?>)" title="Pausar">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        <?php elseif (in_array($sub['status'], ['paused', 'cancelled'])): ?>
                                            <button class="btn btn-sm btn-success" onclick="reactivateSuscripcion(<?= $sub['subscription_id'] ?>)" title="Reactivar">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($sub['status'] === 'active' || $sub['status'] === 'paused'): ?>
                                            <button class="btn btn-sm btn-danger" onclick="cancelSuscripcion(<?= $sub['subscription_id'] ?>)" title="Cancelar">
                                                <i class="fas fa-times"></i>
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

        <?php if (!empty($suscripciones) && $total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?><?= isset($_GET['plan_id']) ? '&plan_id=' . $_GET['plan_id'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="pagination-link <?= $i === $current_page ? 'active' : '' ?>">
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    padding: 0 1.5rem;
}

.stat-card {
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
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

@media (max-width: 768px) {
    .crm-card-actions {
        width: 100%;
        flex-direction: column;
    }

    .search-input {
        width: 100%;
    }

    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<script>
// Función para abrir modal con detalles de la suscripción
function openSuscripcionModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    const sub = data.subscription;
    const payments = data.payments || [];
    const shipments = data.shipments || [];

    // Parse dates
    const startDate = new Date(sub.start_date).toLocaleDateString('es-ES');
    const periodStart = new Date(sub.current_period_start).toLocaleDateString('es-ES');
    const periodEnd = new Date(sub.current_period_end).toLocaleDateString('es-ES');
    const nextBilling = sub.next_billing_date ? new Date(sub.next_billing_date).toLocaleDateString('es-ES') : 'N/A';

    // Status colors
    const statusColors = {
        'active': 'success',
        'pending': 'warning',
        'cancelled': 'danger',
        'paused': 'info',
        'expired': 'secondary'
    };

    const statusLabels = {
        'active': 'Activa',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada',
        'paused': 'Pausada',
        'expired': 'Expirada'
    };

    // Plan type labels
    const planTypeLabels = {
        'fan': 'Fan',
        'premium_random': 'Premium Random',
        'premium_top': 'Premium TOP',
        'retro_top': 'Retro TOP'
    };

    // Payment status
    const paymentStatusColors = {
        'completed': 'success',
        'pending': 'warning',
        'failed': 'danger',
        'refunded': 'info'
    };

    const paymentStatusLabels = {
        'completed': 'Completado',
        'pending': 'Pendiente',
        'failed': 'Fallido',
        'refunded': 'Reembolsado'
    };

    // Shipment status
    const shipmentStatusColors = {
        'pending': 'warning',
        'preparing': 'info',
        'shipped': 'primary',
        'in_transit': 'primary',
        'delivered': 'success',
        'returned': 'danger',
        'failed': 'danger'
    };

    const shipmentStatusLabels = {
        'pending': 'Pendiente',
        'preparing': 'Preparando',
        'shipped': 'Enviado',
        'in_transit': 'En tránsito',
        'delivered': 'Entregado',
        'returned': 'Devuelto',
        'failed': 'Fallido'
    };

    // Generate payments HTML
    let paymentsHTML = '';
    if (payments.length > 0) {
        paymentsHTML = payments.map(p => `
            <div class="payment-item">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; color: var(--gray-900);">€${parseFloat(p.amount).toFixed(2)}</span>
                    <span class="badge badge-${paymentStatusColors[p.payment_status] || 'secondary'}">
                        ${paymentStatusLabels[p.payment_status] || p.payment_status}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">
                    <div><i class="fas fa-calendar"></i> ${new Date(p.payment_date).toLocaleDateString('es-ES')}</div>
                    <div><i class="fas fa-credit-card"></i> ${p.payment_method || 'N/A'}</div>
                    ${p.transaction_reference ? `<div><i class="fas fa-hashtag"></i> ${p.transaction_reference}</div>` : ''}
                    ${p.notes ? `<div style="margin-top: 0.25rem;"><i class="fas fa-note-sticky"></i> ${p.notes}</div>` : ''}
                </div>
            </div>
        `).join('');
    } else {
        paymentsHTML = '<p style="color: var(--gray-500); text-align: center;">No hay pagos registrados</p>';
    }

    // Generate shipments HTML
    let shipmentsHTML = '';
    if (shipments.length > 0) {
        shipmentsHTML = shipments.map(s => `
            <div class="shipment-item">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; color: var(--gray-900);">Envío #${s.shipment_id}</span>
                    <span class="badge badge-${shipmentStatusColors[s.status] || 'secondary'}">
                        ${shipmentStatusLabels[s.status] || s.status}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">
                    <div><i class="fas fa-calendar"></i> ${new Date(s.shipment_date).toLocaleDateString('es-ES')}</div>
                    ${s.tracking_number ? `<div><i class="fas fa-barcode"></i> ${s.tracking_number}</div>` : ''}
                    ${s.carrier ? `<div><i class="fas fa-truck"></i> ${s.carrier}</div>` : ''}
                    ${s.actual_delivery_date ? `<div><i class="fas fa-check-circle"></i> Entregado: ${new Date(s.actual_delivery_date).toLocaleDateString('es-ES')}</div>` : ''}
                    ${s.notes ? `<div style="margin-top: 0.25rem;"><i class="fas fa-note-sticky"></i> ${s.notes}</div>` : ''}
                </div>
            </div>
        `).join('');
    } else {
        shipmentsHTML = '<p style="color: var(--gray-500); text-align: center;">No hay envíos registrados</p>';
    }

    // Generate leagues HTML
    let leaguesHTML = '';
    if (sub.leagues && sub.leagues.length > 0) {
        leaguesHTML = sub.leagues.map(l => `<span class="badge badge-info" style="margin-right: 0.25rem;">${l.league_name}</span>`).join('');
    } else {
        leaguesHTML = '<span style="color: var(--gray-500);">No especificadas</span>';
    }

    // Generate teams HTML
    let teamsHTML = '';
    if (sub.teams && sub.teams.length > 0) {
        teamsHTML = sub.teams.map(t => `<span class="badge badge-primary" style="margin-right: 0.25rem;">${t.team_name}</span>`).join('');
    } else {
        teamsHTML = '<span style="color: var(--gray-500);">No especificados</span>';
    }

    return `
        <div class="modal-suscripcion-details">
            <div class="suscripcion-header">
                <div class="cliente-avatar-large">
                    ${sub.customer_name.substring(0, 2).toUpperCase()}
                </div>
                <div class="cliente-info-main">
                    <h3>${sub.customer_name}</h3>
                    <div class="cliente-meta">
                        <span class="badge badge-${statusColors[sub.status]}">
                            ${statusLabels[sub.status]}
                        </span>
                        <span class="badge badge-purple">
                            <i class="fas fa-crown"></i> ${sub.plan_name}
                        </span>
                    </div>
                </div>
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-info-circle"></i> Información del Cliente</h4>
                ${sub.customer_email ? `<p><strong>Email:</strong> ${sub.customer_email}</p>` : ''}
                ${sub.telegram_username ? `<p><strong>Telegram:</strong> @${sub.telegram_username}</p>` : ''}
                ${sub.whatsapp_number ? `<p><strong>WhatsApp:</strong> ${sub.whatsapp_number}</p>` : ''}
                ${sub.phone ? `<p><strong>Teléfono:</strong> ${sub.phone}</p>` : ''}
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-crown"></i> Detalles del Plan</h4>
                <p><strong>Plan:</strong> ${sub.plan_name}</p>
                <p><strong>Tipo:</strong> ${planTypeLabels[sub.plan_type] || sub.plan_type}</p>
                <p><strong>Precio mensual:</strong> <span style="color: var(--success); font-weight: 700;">€${parseFloat(sub.monthly_price).toFixed(2)}</span></p>
                <p><strong>Calidad de camiseta:</strong> ${sub.jersey_quality}</p>
                <p><strong>Cantidad por mes:</strong> ${sub.jersey_quantity} camiseta(s)</p>
                <p><strong>Talla preferida:</strong> <span class="badge badge-secondary">${sub.preferred_size}</span></p>
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-heart"></i> Preferencias</h4>
                <p><strong>Ligas:</strong><br>${leaguesHTML}</p>
                <p><strong>Equipos:</strong><br>${teamsHTML}</p>
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-calendar-alt"></i> Timeline</h4>
                <div class="timeline-grid">
                    <div class="timeline-item">
                        <div class="timeline-label">Fecha de inicio</div>
                        <div class="timeline-value">${startDate}</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-label">Período actual</div>
                        <div class="timeline-value">${periodStart} - ${periodEnd}</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-label">Próxima facturación</div>
                        <div class="timeline-value">${nextBilling}</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-label">Meses pagados</div>
                        <div class="timeline-value">${sub.total_months_paid} meses</div>
                    </div>
                </div>
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-credit-card"></i> Historial de Pagos</h4>
                <div class="payments-list">
                    ${paymentsHTML}
                </div>
            </div>

            <div class="detail-section" style="margin-bottom: 1.5rem;">
                <h4><i class="fas fa-box"></i> Envíos Realizados</h4>
                <div class="shipments-list">
                    ${shipmentsHTML}
                </div>
            </div>

            ${sub.cancellation_reason ? `
                <div class="detail-section alert-danger" style="margin-bottom: 1.5rem;">
                    <h4><i class="fas fa-exclamation-triangle"></i> Motivo de Cancelación</h4>
                    <p>${sub.cancellation_reason}</p>
                    <p><small>Fecha: ${new Date(sub.cancellation_date).toLocaleDateString('es-ES')}</small></p>
                </div>
            ` : ''}

            ${sub.pause_reason ? `
                <div class="detail-section alert-warning" style="margin-bottom: 1.5rem;">
                    <h4><i class="fas fa-pause-circle"></i> Motivo de Pausa</h4>
                    <p>${sub.pause_reason}</p>
                    <p><small>Fecha: ${new Date(sub.pause_date).toLocaleDateString('es-ES')}</small></p>
                </div>
            ` : ''}

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                ${sub.status === 'active' ? `
                    <button class="btn btn-warning" onclick="pauseSuscripcion(${sub.subscription_id})">
                        <i class="fas fa-pause"></i>
                        Pausar
                    </button>
                    <button class="btn btn-danger" onclick="cancelSuscripcion(${sub.subscription_id})">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                ` : ''}
                ${sub.status === 'paused' || sub.status === 'cancelled' ? `
                    <button class="btn btn-success" onclick="reactivateSuscripcion(${sub.subscription_id})">
                        <i class="fas fa-play"></i>
                        Reactivar
                    </button>
                ` : ''}
            </div>
        </div>
    `;
};

// Pause subscription
function pauseSuscripcion(id) {
    const reason = prompt('Motivo de la pausa (opcional):');
    if (reason === null) return; // User cancelled

    if (confirm('¿Está seguro de que desea pausar esta suscripción?')) {
        fetch(`/admin/suscripciones/pause/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Suscripción pausada correctamente', () => {
                    location.reload();
                });
            } else {
                showErrorModal('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error al pausar la suscripción');
        });
    }
}

// Cancel subscription
function cancelSuscripcion(id) {
    const reason = prompt('Motivo de la cancelación (opcional):');
    if (reason === null) return; // User cancelled

    showConfirmModal('¿Está seguro de que desea cancelar esta suscripción? Esta acción no se puede deshacer fácilmente.', () => {
        fetch(`/admin/suscripciones/cancel/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Suscripción cancelada correctamente', () => {
                    location.reload();
                });
            } else {
                showErrorModal('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error al cancelar la suscripción');
        });
    });
}

// Reactivate subscription
function reactivateSuscripcion(id) {
    showConfirmModal('¿Está seguro de que desea reactivar esta suscripción?', () => {
        fetch(`/admin/suscripciones/reactivate/${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Suscripción reactivada correctamente', () => {
                    location.reload();
                });
            } else {
                showErrorModal('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error al reactivar la suscripción');
        });
    });
}

// Search and filter functionality
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    applyFilters();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    applyFilters();
});

document.getElementById('filterPlan')?.addEventListener('change', () => {
    applyFilters();
});

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('filterStatus').value;
    const planId = document.getElementById('filterPlan').value;

    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (planId) params.append('plan_id', planId);

    window.location.href = '/admin/suscripciones' + (params.toString() ? '?' + params.toString() : '');
}
</script>

<style>
.suscripcion-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.cliente-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: 700;
}

.cliente-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.cliente-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
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

.timeline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.timeline-item {
    padding: 0.75rem;
    background: white;
    border-radius: var(--radius-md);
}

.timeline-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.timeline-value {
    font-weight: 600;
    color: var(--gray-900);
}

.payments-list, .shipments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-item, .shipment-item {
    padding: 1rem;
    background: white;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
}

.alert-danger {
    background: #fee;
    border: 1px solid #fcc;
}

.alert-warning {
    background: #fffbf0;
    border: 1px solid #ffe5a0;
}
</style>
