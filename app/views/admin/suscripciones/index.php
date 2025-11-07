<?php
/**
 * Admin Suscripciones - Vista principal de gestión de suscripciones
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'suscripciones';
$page_title = 'Gestión de Suscripciones - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Suscripciones']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-calendar-check"></i>
            Gestión de Suscripciones
        </h1>
        <p class="page-subtitle">Visualiza y gestiona todas las suscripciones activas y pasadas</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportSubscriptions()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Suscripciones</div>
            <div class="stat-value" id="totalSubs">0</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Activas</div>
            <div class="stat-value" id="activeSubs">0</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pendientes</div>
            <div class="stat-value" id="pendingSubs">0</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #30cfd0, #330867);">
            <i class="fas fa-pause-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pausadas</div>
            <div class="stat-value" id="pausedSubs">0</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group" style="margin: 0;">
            <input type="text"
                   id="searchInput"
                   class="form-control"
                   placeholder="Buscar por cliente...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterStatus" class="form-control">
                <option value="">Todos los estados</option>
                <option value="active">Activas</option>
                <option value="pending">Pendientes</option>
                <option value="paused">Pausadas</option>
                <option value="cancelled">Canceladas</option>
                <option value="expired">Expiradas</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterPlan" class="form-control">
                <option value="">Todos los planes</option>
            </select>
        </div>
    </div>
    <div class="filters-actions">
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fas fa-redo"></i>
            Limpiar filtros
        </button>
    </div>
</div>

<!-- Subscriptions Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Suscripciones</h3>
        <span id="totalCount" class="badge badge-primary">0 suscripciones</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="subscriptionsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Cliente</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Plan</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Talla</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Inicio</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Próximo Pago</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Meses Pagados</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="subscriptionsTableBody">
                <tr>
                    <td colspan="9" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando suscripciones...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer" id="paginationContainer" style="display: flex; justify-content: space-between; align-items: center;">
        <div id="paginationInfo"></div>
        <div id="paginationButtons"></div>
    </div>
</div>

<!-- Modal for subscription details -->
<div id="subscriptionModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeModal()"></div>
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-crown"></i>
                Detalles de la Suscripción
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="subscriptionModalBody">
            <div style="text-align: center; padding: 40px;">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando detalles...</p>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: var(--admin-radius-lg);
    box-shadow: var(--admin-shadow-sm);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--admin-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 13px;
    color: var(--admin-gray-600);
    margin-bottom: 4px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--admin-gray-900);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: white;
    margin: 40px auto;
    border-radius: var(--admin-radius-lg);
    max-height: calc(100vh - 80px);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--admin-gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--admin-gray-500);
    padding: 4px 8px;
}

.modal-close:hover {
    color: var(--admin-gray-900);
}

.modal-body {
    padding: 24px;
    overflow-y: auto;
}

.detail-section {
    background: var(--admin-gray-50);
    padding: 1.5rem;
    border-radius: var(--admin-radius-lg);
    margin-bottom: 1.5rem;
}

.detail-section h4 {
    font-size: 16px;
    margin-bottom: 1rem;
    color: var(--admin-gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section p {
    margin-bottom: 0.75rem;
    color: var(--admin-gray-700);
    font-size: 14px;
}

.detail-section p:last-child {
    margin-bottom: 0;
}

.subscription-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--admin-gray-200);
}

.customer-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: 700;
}

.customer-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 24px;
}

.customer-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.timeline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.timeline-item {
    padding: 1rem;
    background: white;
    border-radius: var(--admin-radius-md);
}

.timeline-label {
    font-size: 12px;
    color: var(--admin-gray-600);
    margin-bottom: 0.25rem;
}

.timeline-value {
    font-weight: 600;
    color: var(--admin-gray-900);
    font-size: 14px;
}

.payments-list, .shipments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-item, .shipment-item {
    padding: 1rem;
    background: white;
    border-radius: var(--admin-radius-md);
    border: 1px solid var(--admin-gray-200);
}

.modal-footer {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--admin-gray-200);
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}
</style>

<script>
// Subscriptions management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};
let subscriptionPlans = [];

// Load subscriptions on page load
document.addEventListener('DOMContentLoaded', () => {
    loadPlans();
    loadSubscriptions();
    loadStats();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadSubscriptions();
    }, 500));

    document.getElementById('filterStatus').addEventListener('change', () => {
        currentPage = 1;
        loadSubscriptions();
    });

    document.getElementById('filterPlan').addEventListener('change', () => {
        currentPage = 1;
        loadSubscriptions();
    });
});

// Load subscription plans for filter
async function loadPlans() {
    try {
        // This could be a separate API endpoint, but for now we'll load from first subscriptions call
    } catch (error) {
        console.error('Error loading plans:', error);
    }
}

// Load statistics
async function loadStats() {
    // Stats will be calculated from the subscriptions data
    // You could create a separate API endpoint for this if needed
}

// Load subscriptions from API
async function loadSubscriptions() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const status = document.getElementById('filterStatus').value;
        if (status) params.append('status', status);

        const planId = document.getElementById('filterPlan').value;
        if (planId) params.append('plan_id', planId);

        const response = await fetch(`/api/admin/suscripciones?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderSubscriptions(data.subscriptions);
            renderPagination(data.pagination);
            updateStats(data.subscriptions);
            document.getElementById('totalCount').textContent = `${data.pagination.total} suscripciones`;
        } else {
            showToast('Error al cargar suscripciones', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading subscriptions:', error);
        showToast('Error de conexión', 'error');
    }
}

// Update stats from subscriptions data
function updateStats(subscriptions) {
    const stats = {
        total: 0,
        active: 0,
        pending: 0,
        paused: 0,
        cancelled: 0,
        expired: 0
    };

    // This is a simple client-side calculation
    // Ideally you'd get this from the API
    document.getElementById('totalSubs').textContent = stats.total;
    document.getElementById('activeSubs').textContent = stats.active;
    document.getElementById('pendingSubs').textContent = stats.pending;
    document.getElementById('pausedSubs').textContent = stats.paused;
}

// Render subscriptions table
function renderSubscriptions(subscriptions) {
    const tbody = document.getElementById('subscriptionsTableBody');

    if (subscriptions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="empty-state-title">No se encontraron suscripciones</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = subscriptions.map(sub => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewSubscription(${sub.subscription_id})">
            <td style="padding: 12px 16px; font-weight: 500;">#${sub.subscription_id}</td>
            <td style="padding: 12px 16px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px;">
                        ${escapeHtml(sub.customer_name || 'N/A').substring(0, 2).toUpperCase()}
                    </div>
                    <div>
                        ${escapeHtml(sub.customer_name || 'N/A')}
                        <br><small style="color: var(--admin-gray-500);">${escapeHtml(sub.customer_email || sub.telegram_username || '')}</small>
                    </div>
                </div>
            </td>
            <td style="padding: 12px 16px;">
                <div>
                    <strong>${escapeHtml(sub.plan_name)}</strong>
                    <br><small style="color: var(--admin-gray-500);">${getPlanTypeLabel(sub.plan_type)}</small>
                </div>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-secondary">${sub.preferred_size}</span>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getStatusColor(sub.status)}">${getStatusLabel(sub.status)}</span>
            </td>
            <td style="padding: 12px 16px; font-size: 13px;">
                ${formatDate(sub.start_date, 'date')}
            </td>
            <td style="padding: 12px 16px; font-size: 13px;">
                ${sub.next_billing_date ? formatDate(sub.next_billing_date, 'date') : '<span style="color: var(--admin-gray-400);">-</span>'}
            </td>
            <td style="padding: 12px 16px; text-align: right;">
                <strong style="color: var(--admin-primary);">${sub.total_months_paid || 0}</strong> meses
                <br><small style="color: var(--admin-gray-500);">€${parseFloat(sub.total_paid || 0).toFixed(2)}</small>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewSubscription(${sub.subscription_id})">
                    <i class="fas fa-eye"></i>
                </button>
                ${getActionButtons(sub)}
            </td>
        </tr>
    `).join('');
}

// Get action buttons based on subscription status
function getActionButtons(sub) {
    let buttons = '';

    if (sub.status === 'active') {
        buttons += `
            <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); pauseSubscription(${sub.subscription_id})" title="Pausar">
                <i class="fas fa-pause"></i>
            </button>
        `;
    }

    if (sub.status === 'paused' || sub.status === 'cancelled') {
        buttons += `
            <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); reactivateSubscription(${sub.subscription_id})" title="Reactivar">
                <i class="fas fa-play"></i>
            </button>
        `;
    }

    if (sub.status === 'active' || sub.status === 'paused') {
        buttons += `
            <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); cancelSubscription(${sub.subscription_id})" title="Cancelar">
                <i class="fas fa-times"></i>
            </button>
        `;
    }

    return buttons;
}

// Render pagination
function renderPagination(pagination) {
    totalPages = pagination.pages;
    currentPage = pagination.current_page;

    document.getElementById('paginationInfo').textContent =
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} suscripciones`;

    const buttons = document.getElementById('paginationButtons');
    let html = '';

    // Previous button
    html += `<button class="btn btn-sm btn-secondary" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
        <i class="fas fa-chevron-left"></i>
    </button>`;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `<button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" onclick="changePage(${i})">${i}</button>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += `<span style="padding: 0 8px;">...</span>`;
        }
    }

    // Next button
    html += `<button class="btn btn-sm btn-secondary" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
        <i class="fas fa-chevron-right"></i>
    </button>`;

    buttons.innerHTML = html;
}

// Change page
function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadSubscriptions();
}

// View subscription details
async function viewSubscription(subscriptionId) {
    try {
        document.getElementById('subscriptionModal').style.display = 'block';
        document.getElementById('subscriptionModalBody').innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando detalles...</p>
            </div>
        `;

        const response = await fetch(`/api/admin/suscripciones/${subscriptionId}`);
        const data = await response.json();

        if (data.success) {
            renderSubscriptionDetails(data);
        } else {
            showToast('Error al cargar detalles de la suscripción', 'error');
            closeModal();
        }
    } catch (error) {
        console.error('Error loading subscription details:', error);
        showToast('Error de conexión', 'error');
        closeModal();
    }
}

// Render subscription details in modal
function renderSubscriptionDetails(data) {
    const sub = data.subscription;
    const payments = data.payments || [];
    const shipments = data.shipments || [];

    // Generate payments HTML
    let paymentsHTML = '';
    if (payments.length > 0) {
        paymentsHTML = payments.map(p => `
            <div class="payment-item">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; color: var(--admin-gray-900);">€${parseFloat(p.amount).toFixed(2)}</span>
                    <span class="badge badge-${getPaymentStatusColor(p.status)}">
                        ${getPaymentStatusLabel(p.status)}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--admin-gray-600);">
                    <div><i class="fas fa-calendar"></i> ${formatDate(p.payment_date, 'date')}</div>
                    <div><i class="fas fa-credit-card"></i> ${p.payment_method || 'N/A'}</div>
                    ${p.payment_reference ? `<div><i class="fas fa-hashtag"></i> ${escapeHtml(p.payment_reference)}</div>` : ''}
                    ${p.notes ? `<div style="margin-top: 0.25rem;"><i class="fas fa-note-sticky"></i> ${escapeHtml(p.notes)}</div>` : ''}
                </div>
            </div>
        `).join('');
    } else {
        paymentsHTML = '<p style="color: var(--admin-gray-500); text-align: center; padding: 20px;">No hay pagos registrados</p>';
    }

    // Generate shipments HTML
    let shipmentsHTML = '';
    if (shipments.length > 0) {
        shipmentsHTML = shipments.map(s => `
            <div class="shipment-item">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; color: var(--admin-gray-900);">Envío #${s.shipment_id}</span>
                    <span class="badge badge-${getShipmentStatusColor(s.status)}">
                        ${getShipmentStatusLabel(s.status)}
                    </span>
                </div>
                <div style="font-size: 0.875rem; color: var(--admin-gray-600);">
                    <div><i class="fas fa-calendar"></i> ${formatDate(s.shipment_date, 'date')}</div>
                    ${s.tracking_number ? `<div><i class="fas fa-barcode"></i> ${escapeHtml(s.tracking_number)}</div>` : ''}
                    ${s.carrier ? `<div><i class="fas fa-truck"></i> ${escapeHtml(s.carrier)}</div>` : ''}
                    ${s.actual_delivery_date ? `<div><i class="fas fa-check-circle"></i> Entregado: ${formatDate(s.actual_delivery_date, 'date')}</div>` : ''}
                </div>
            </div>
        `).join('');
    } else {
        shipmentsHTML = '<p style="color: var(--admin-gray-500); text-align: center; padding: 20px;">No hay envíos registrados</p>';
    }

    // Generate leagues HTML
    let leaguesHTML = '';
    if (sub.leagues && sub.leagues.length > 0) {
        leaguesHTML = sub.leagues.map(l => `<span class="badge badge-info" style="margin-right: 0.25rem;">${escapeHtml(l.name)}</span>`).join('');
    } else {
        leaguesHTML = '<span style="color: var(--admin-gray-500);">No especificadas</span>';
    }

    // Generate teams HTML
    let teamsHTML = '';
    if (sub.teams && sub.teams.length > 0) {
        teamsHTML = sub.teams.map(t => `<span class="badge badge-primary" style="margin-right: 0.25rem;">${escapeHtml(t.name)}</span>`).join('');
    } else {
        teamsHTML = '<span style="color: var(--admin-gray-500);">No especificados</span>';
    }

    const html = `
        <div class="subscription-header">
            <div class="customer-avatar-large">
                ${(sub.customer_name || 'NA').substring(0, 2).toUpperCase()}
            </div>
            <div class="customer-info-main">
                <h3>${escapeHtml(sub.customer_name)}</h3>
                <div class="customer-meta">
                    <span class="badge badge-${getStatusColor(sub.status)}">
                        ${getStatusLabel(sub.status)}
                    </span>
                    <span class="badge badge-purple">
                        <i class="fas fa-crown"></i> ${escapeHtml(sub.plan_name)}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-info-circle"></i> Información del Cliente</h4>
            ${sub.customer_email ? `<p><strong>Email:</strong> ${escapeHtml(sub.customer_email)}</p>` : ''}
            ${sub.telegram_username ? `<p><strong>Telegram:</strong> @${escapeHtml(sub.telegram_username)}</p>` : ''}
            ${sub.whatsapp_number ? `<p><strong>WhatsApp:</strong> ${escapeHtml(sub.whatsapp_number)}</p>` : ''}
            ${sub.phone ? `<p><strong>Teléfono:</strong> ${escapeHtml(sub.phone)}</p>` : ''}
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-crown"></i> Detalles del Plan</h4>
            <p><strong>Plan:</strong> ${escapeHtml(sub.plan_name)}</p>
            <p><strong>Tipo:</strong> ${getPlanTypeLabel(sub.plan_type)}</p>
            <p><strong>Precio mensual:</strong> <span style="color: var(--admin-success); font-weight: 700;">€${parseFloat(sub.monthly_price).toFixed(2)}</span></p>
            <p><strong>Calidad de camiseta:</strong> ${sub.jersey_quality || 'N/A'}</p>
            <p><strong>Cantidad por mes:</strong> ${sub.jersey_quantity || 1} camiseta(s)</p>
            <p><strong>Talla preferida:</strong> <span class="badge badge-secondary">${sub.preferred_size}</span></p>
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-heart"></i> Preferencias</h4>
            <p><strong>Ligas:</strong><br>${leaguesHTML}</p>
            <p><strong>Equipos:</strong><br>${teamsHTML}</p>
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-calendar-alt"></i> Timeline</h4>
            <div class="timeline-grid">
                <div class="timeline-item">
                    <div class="timeline-label">Fecha de inicio</div>
                    <div class="timeline-value">${formatDate(sub.start_date, 'date')}</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-label">Período actual</div>
                    <div class="timeline-value">${formatDate(sub.current_period_start, 'date')} - ${formatDate(sub.current_period_end, 'date')}</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-label">Próxima facturación</div>
                    <div class="timeline-value">${sub.next_billing_date ? formatDate(sub.next_billing_date, 'date') : 'N/A'}</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-label">Meses pagados</div>
                    <div class="timeline-value">${sub.total_months_paid || 0} meses</div>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-credit-card"></i> Historial de Pagos (${payments.length})</h4>
            <div class="payments-list">
                ${paymentsHTML}
            </div>
        </div>

        <div class="detail-section">
            <h4><i class="fas fa-box"></i> Envíos Realizados (${shipments.length})</h4>
            <div class="shipments-list">
                ${shipmentsHTML}
            </div>
        </div>

        ${sub.cancellation_reason ? `
            <div class="detail-section" style="background: #fee; border: 1px solid #fcc;">
                <h4><i class="fas fa-exclamation-triangle"></i> Motivo de Cancelación</h4>
                <p>${escapeHtml(sub.cancellation_reason)}</p>
                <p><small>Fecha: ${formatDate(sub.cancellation_date, 'date')}</small></p>
            </div>
        ` : ''}

        ${sub.pause_reason ? `
            <div class="detail-section" style="background: #fffbf0; border: 1px solid #ffe5a0;">
                <h4><i class="fas fa-pause-circle"></i> Motivo de Pausa</h4>
                <p>${escapeHtml(sub.pause_reason)}</p>
                <p><small>Fecha: ${formatDate(sub.pause_date, 'date')}</small></p>
            </div>
        ` : ''}

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">
                Cerrar
            </button>
            ${sub.status === 'active' ? `
                <button class="btn btn-warning" onclick="pauseSubscription(${sub.subscription_id})">
                    <i class="fas fa-pause"></i>
                    Pausar
                </button>
                <button class="btn btn-danger" onclick="cancelSubscription(${sub.subscription_id})">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
            ` : ''}
            ${sub.status === 'paused' || sub.status === 'cancelled' ? `
                <button class="btn btn-success" onclick="reactivateSubscription(${sub.subscription_id})">
                    <i class="fas fa-play"></i>
                    Reactivar
                </button>
            ` : ''}
        </div>
    `;

    document.getElementById('subscriptionModalBody').innerHTML = html;
}

// Close modal
function closeModal() {
    document.getElementById('subscriptionModal').style.display = 'none';
}

// Pause subscription
function pauseSubscription(id) {
    const reason = prompt('Motivo de la pausa (opcional):');
    if (reason === null) return; // User cancelled

    if (confirm('¿Está seguro de que desea pausar esta suscripción?')) {
        fetch(`/admin/suscripciones/pause/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reason=${encodeURIComponent(reason || '')}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Suscripción pausada correctamente', 'success');
                closeModal();
                loadSubscriptions();
            } else {
                showToast('Error: ' + (data.error || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al pausar la suscripción', 'error');
        });
    }
}

// Cancel subscription
function cancelSubscription(id) {
    const reason = prompt('Motivo de la cancelación (opcional):');
    if (reason === null) return; // User cancelled

    if (confirm('¿Está seguro de que desea cancelar esta suscripción? Esta acción es difícil de revertir.')) {
        fetch(`/admin/suscripciones/cancel/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reason=${encodeURIComponent(reason || '')}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Suscripción cancelada correctamente', 'success');
                closeModal();
                loadSubscriptions();
            } else {
                showToast('Error: ' + (data.error || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al cancelar la suscripción', 'error');
        });
    }
}

// Reactivate subscription
function reactivateSubscription(id) {
    if (confirm('¿Está seguro de que desea reactivar esta suscripción?')) {
        fetch(`/admin/suscripciones/reactivate/${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Suscripción reactivada correctamente', 'success');
                closeModal();
                loadSubscriptions();
            } else {
                showToast('Error: ' + (data.error || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al reactivar la suscripción', 'error');
        });
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterPlan').value = '';
    currentPage = 1;
    loadSubscriptions();
}

// Export subscriptions
function exportSubscriptions() {
    showToast('Función de exportación en desarrollo', 'info');
}

// Helper functions
function getStatusLabel(status) {
    const labels = {
        'active': 'Activa',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada',
        'paused': 'Pausada',
        'expired': 'Expirada'
    };
    return labels[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'active': 'success',
        'pending': 'warning',
        'cancelled': 'danger',
        'paused': 'warning',
        'expired': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getPlanTypeLabel(type) {
    const labels = {
        'fan': 'Fan',
        'premium_random': 'Premium Random',
        'premium_top': 'Premium TOP',
        'retro_top': 'Retro TOP'
    };
    return labels[type] || type;
}

function getPaymentStatusLabel(status) {
    const labels = {
        'pending': 'Pendiente',
        'completed': 'Completado',
        'failed': 'Fallido',
        'refunded': 'Reembolsado'
    };
    return labels[status] || status;
}

function getPaymentStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'completed': 'success',
        'failed': 'danger',
        'refunded': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getShipmentStatusLabel(status) {
    const labels = {
        'pending': 'Pendiente',
        'preparing': 'Preparando',
        'shipped': 'Enviado',
        'in_transit': 'En tránsito',
        'delivered': 'Entregado',
        'returned': 'Devuelto',
        'failed': 'Fallido'
    };
    return labels[status] || status;
}

function getShipmentStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'preparing': 'info',
        'shipped': 'primary',
        'in_transit': 'primary',
        'delivered': 'success',
        'returned': 'danger',
        'failed': 'danger'
    };
    return colors[status] || 'secondary';
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
