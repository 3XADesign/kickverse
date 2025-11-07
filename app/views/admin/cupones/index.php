<?php
/**
 * Admin Cupones - Vista principal de gestión de cupones
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'cupones';
$page_title = 'Gestión de Cupones - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Cupones']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-tags"></i>
            Gestión de Cupones
        </h1>
        <p class="page-subtitle">Crea y administra cupones de descuento para tu tienda</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportCoupons()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
        <a href="/admin/cupones/crear" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Cupón
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group" style="margin: 0;">
            <input type="text"
                   id="searchInput"
                   class="form-control"
                   placeholder="Buscar por código o descripción...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterDiscountType" class="form-control">
                <option value="">Todos los tipos</option>
                <option value="fixed">Descuento Fijo</option>
                <option value="percentage">Porcentaje</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterIsActive" class="form-control">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <input type="date"
                   id="filterValidFrom"
                   class="form-control"
                   placeholder="Válido desde">
        </div>
        <div class="form-group" style="margin: 0;">
            <input type="date"
                   id="filterValidUntil"
                   class="form-control"
                   placeholder="Válido hasta">
        </div>
    </div>
    <div class="filters-actions">
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fas fa-redo"></i>
            Limpiar filtros
        </button>
    </div>
</div>

<!-- Coupons Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Cupones</h3>
        <span id="totalCoupons" class="badge badge-primary">0 cupones</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="couponsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Código</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Descripción</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Tipo</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Descuento</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Usos</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Validez</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="couponsTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando cupones...</p>
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

<!-- Coupon Detail Modal -->
<div id="couponModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCouponModal()"></div>
    <div class="modal-container" style="max-width: 900px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-tag"></i>
                Detalles del Cupón
            </h3>
            <button class="modal-close" onclick="closeCouponModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="couponModalBody" class="modal-body">
            <div style="padding: 60px; text-align: center;">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando detalles...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Coupons management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load coupons on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCoupons();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadCoupons();
    }, 500));

    document.getElementById('filterDiscountType').addEventListener('change', () => {
        currentPage = 1;
        loadCoupons();
    });

    document.getElementById('filterIsActive').addEventListener('change', () => {
        currentPage = 1;
        loadCoupons();
    });

    document.getElementById('filterValidFrom').addEventListener('change', () => {
        currentPage = 1;
        loadCoupons();
    });

    document.getElementById('filterValidUntil').addEventListener('change', () => {
        currentPage = 1;
        loadCoupons();
    });
});

// Load coupons from API
async function loadCoupons() {
    try {
        // Build query params
        const params = new URLSearchParams({
            page: currentPage
        });

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const discountType = document.getElementById('filterDiscountType').value;
        if (discountType) params.append('discount_type', discountType);

        const isActive = document.getElementById('filterIsActive').value;
        if (isActive !== '') params.append('is_active', isActive);

        const validFrom = document.getElementById('filterValidFrom').value;
        if (validFrom) params.append('valid_from', validFrom);

        const validUntil = document.getElementById('filterValidUntil').value;
        if (validUntil) params.append('valid_until', validUntil);

        currentFilters = Object.fromEntries(params);

        const response = await fetch(`/api/admin/cupones?${params.toString()}`);
        if (!response.ok) throw new Error('Failed to load coupons');

        const data = await response.json();

        if (data.success) {
            renderCouponsTable(data.coupons);
            renderPagination(data.pagination);
        } else {
            throw new Error(data.message || 'Error loading coupons');
        }
    } catch (error) {
        console.error('Error loading coupons:', error);
        document.getElementById('couponsTableBody').innerHTML = `
            <tr>
                <td colspan="8" style="padding: 60px; text-align: center; color: var(--admin-red);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <p><strong>Error al cargar cupones</strong></p>
                    <p style="color: var(--admin-gray-600);">${error.message}</p>
                    <button class="btn btn-primary" onclick="loadCoupons()" style="margin-top: 16px;">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </td>
            </tr>
        `;
    }
}

// Render coupons table
function renderCouponsTable(coupons) {
    const tbody = document.getElementById('couponsTableBody');

    if (!coupons || coupons.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="padding: 60px; text-align: center;">
                    <i class="fas fa-tags" style="font-size: 48px; color: var(--admin-gray-400); margin-bottom: 16px;"></i>
                    <p style="color: var(--admin-gray-600); font-weight: 500;">No se encontraron cupones</p>
                    <p style="color: var(--admin-gray-500); font-size: 14px;">Intenta ajustar los filtros o crea un nuevo cupón</p>
                </td>
            </tr>
        `;
        document.getElementById('totalCoupons').textContent = '0 cupones';
        return;
    }

    tbody.innerHTML = coupons.map(coupon => {
        const isExpired = coupon.valid_until && new Date(coupon.valid_until) < new Date();
        const isActive = coupon.is_active && !isExpired;

        return `
            <tr onclick="openCouponModal(${coupon.coupon_id})" style="cursor: pointer;">
                <td style="padding: 12px 16px;">
                    <code style="background: var(--admin-gray-100); padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                        ${escapeHtml(coupon.code)}
                    </code>
                </td>
                <td style="padding: 12px 16px;">
                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${escapeHtml(coupon.description || '-')}
                    </div>
                </td>
                <td style="padding: 12px 16px; text-align: center;">
                    ${coupon.discount_type === 'percentage'
                        ? '<span class="badge badge-primary"><i class="fas fa-percent"></i> Porcentaje</span>'
                        : '<span class="badge badge-info"><i class="fas fa-euro-sign"></i> Fijo</span>'}
                </td>
                <td style="padding: 12px 16px; text-align: right; font-weight: 600;">
                    ${coupon.discount_type === 'percentage'
                        ? `${coupon.discount_value}%`
                        : `€${parseFloat(coupon.discount_value).toFixed(2)}`}
                </td>
                <td style="padding: 12px 16px; text-align: center;">
                    <div style="font-size: 13px;">
                        <strong>${coupon.times_used || 0}</strong> /
                        ${coupon.usage_limit_total || '∞'}
                    </div>
                </td>
                <td style="padding: 12px 16px; font-size: 13px;">
                    ${formatDateRange(coupon.valid_from, coupon.valid_until, isExpired)}
                </td>
                <td style="padding: 12px 16px; text-align: center;">
                    ${isActive
                        ? '<span class="badge badge-success">Activo</span>'
                        : isExpired
                            ? '<span class="badge badge-danger">Expirado</span>'
                            : '<span class="badge badge-secondary">Inactivo</span>'}
                </td>
                <td style="padding: 12px 16px; text-align: center;" onclick="event.stopPropagation();">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <button class="btn btn-sm btn-primary" onclick="openCouponModal(${coupon.coupon_id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="/admin/cupones/editar/${coupon.coupon_id}" class="btn btn-sm btn-secondary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteCoupon(${coupon.coupon_id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('totalCoupons').textContent = `${coupons.length} cupones`;
}

// Format date range
function formatDateRange(validFrom, validUntil, isExpired) {
    if (!validFrom && !validUntil) {
        return '<span style="color: var(--admin-gray-500);">Sin límite</span>';
    }

    let html = '<div>';
    if (validFrom) {
        html += `<div>Desde: ${formatDate(validFrom)}</div>`;
    }
    if (validUntil) {
        html += `<div>Hasta: ${formatDate(validUntil)}`;
        if (isExpired) {
            html += ' <span class="badge badge-danger" style="font-size: 10px;">Expirado</span>';
        }
        html += '</div>';
    }
    html += '</div>';
    return html;
}

// Format date
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

// Render pagination
function renderPagination(pagination) {
    if (!pagination) return;

    totalPages = pagination.pages;
    currentPage = pagination.current_page;

    const info = document.getElementById('paginationInfo');
    const buttons = document.getElementById('paginationButtons');

    info.innerHTML = `Mostrando ${pagination.from} - ${pagination.to} de ${pagination.total} cupones`;

    if (totalPages <= 1) {
        buttons.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    html += `
        <button class="btn btn-sm btn-secondary"
                onclick="changePage(${currentPage - 1})"
                ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}"
                        onclick="changePage(${i})">
                    ${i}
                </button>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += '<span style="padding: 0 8px;">...</span>';
        }
    }

    // Next button
    html += `
        <button class="btn btn-sm btn-secondary"
                onclick="changePage(${currentPage + 1})"
                ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    buttons.innerHTML = html;
}

// Change page
function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadCoupons();
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterDiscountType').value = '';
    document.getElementById('filterIsActive').value = '';
    document.getElementById('filterValidFrom').value = '';
    document.getElementById('filterValidUntil').value = '';
    currentPage = 1;
    loadCoupons();
}

// Open coupon detail modal
async function openCouponModal(couponId) {
    const modal = document.getElementById('couponModal');
    const modalBody = document.getElementById('couponModalBody');

    modal.style.display = 'flex';
    modalBody.innerHTML = `
        <div style="padding: 60px; text-align: center;">
            <div class="spinner"></div>
            <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando detalles...</p>
        </div>
    `;

    try {
        const response = await fetch(`/api/admin/cupones/${couponId}`);
        if (!response.ok) throw new Error('Failed to load coupon details');

        const data = await response.json();

        if (data.success) {
            renderCouponDetails(data.data);
        } else {
            throw new Error(data.message || 'Error loading coupon details');
        }
    } catch (error) {
        console.error('Error loading coupon details:', error);
        modalBody.innerHTML = `
            <div style="padding: 60px; text-align: center; color: var(--admin-red);">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px;"></i>
                <p><strong>Error al cargar detalles del cupón</strong></p>
                <p style="color: var(--admin-gray-600);">${error.message}</p>
            </div>
        `;
    }
}

// Render coupon details in modal
function renderCouponDetails(coupon) {
    const modalBody = document.getElementById('couponModalBody');

    const isExpired = coupon.valid_until && new Date(coupon.valid_until) < new Date();
    const isActive = coupon.is_active && !isExpired;

    modalBody.innerHTML = `
        <div style="display: grid; gap: 24px;">
            <!-- Basic Info -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Información Básica</h4>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Código</label>
                            <div style="margin-top: 4px;">
                                <code style="background: var(--admin-gray-100); padding: 8px 12px; border-radius: 4px; font-size: 16px; font-weight: 600;">
                                    ${escapeHtml(coupon.code)}
                                </code>
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Estado</label>
                            <div style="margin-top: 4px;">
                                ${isActive
                                    ? '<span class="badge badge-success">Activo</span>'
                                    : isExpired
                                        ? '<span class="badge badge-danger">Expirado</span>'
                                        : '<span class="badge badge-secondary">Inactivo</span>'}
                            </div>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Descripción</label>
                            <div style="margin-top: 4px;">${escapeHtml(coupon.description || '-')}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Info -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Descuento</h4>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Tipo</label>
                            <div style="margin-top: 4px;">
                                ${coupon.discount_type === 'percentage'
                                    ? '<span class="badge badge-primary"><i class="fas fa-percent"></i> Porcentaje</span>'
                                    : '<span class="badge badge-info"><i class="fas fa-euro-sign"></i> Fijo</span>'}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Valor</label>
                            <div style="margin-top: 4px; font-weight: 600; font-size: 18px;">
                                ${coupon.discount_type === 'percentage'
                                    ? `${coupon.discount_value}%`
                                    : `€${parseFloat(coupon.discount_value).toFixed(2)}`}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Descuento Máximo</label>
                            <div style="margin-top: 4px;">
                                ${coupon.max_discount_amount ? `€${parseFloat(coupon.max_discount_amount).toFixed(2)}` : '-'}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Compra Mínima</label>
                            <div style="margin-top: 4px;">
                                €${parseFloat(coupon.min_purchase_amount || 0).toFixed(2)}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Aplica a</label>
                            <div style="margin-top: 4px;">
                                ${coupon.applies_to_product_type === 'all' ? 'Todos los productos' : coupon.applies_to_product_type}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Solo Primera Compra</label>
                            <div style="margin-top: 4px;">
                                ${coupon.applies_to_first_order_only ? 'Sí' : 'No'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Info -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Límites de Uso</h4>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Usos Totales</label>
                            <div style="margin-top: 4px; font-weight: 600; font-size: 18px;">
                                ${coupon.times_used || 0} / ${coupon.usage_limit_total || '∞'}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Límite por Cliente</label>
                            <div style="margin-top: 4px;">
                                ${coupon.usage_limit_per_customer || '∞'}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Descuento Total Otorgado</label>
                            <div style="margin-top: 4px; font-weight: 600; font-size: 18px; color: var(--admin-green);">
                                €${parseFloat(coupon.total_discount_given || 0).toFixed(2)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validity Period -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Período de Validez</h4>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Válido Desde</label>
                            <div style="margin-top: 4px;">
                                ${coupon.valid_from ? formatDate(coupon.valid_from) : 'Sin límite'}
                            </div>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: var(--admin-gray-600); font-size: 13px;">Válido Hasta</label>
                            <div style="margin-top: 4px;">
                                ${coupon.valid_until ? formatDate(coupon.valid_until) : 'Sin límite'}
                                ${isExpired ? ' <span class="badge badge-danger" style="font-size: 10px;">Expirado</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            ${coupon.usage_history && coupon.usage_history.length > 0 ? `
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Historial de Uso (últimos 50)</h4>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: var(--admin-gray-50);">
                                <tr>
                                    <th style="padding: 12px; text-align: left;">Cliente</th>
                                    <th style="padding: 12px; text-align: left;">Pedido</th>
                                    <th style="padding: 12px; text-align: right;">Total Pedido</th>
                                    <th style="padding: 12px; text-align: right;">Descuento</th>
                                    <th style="padding: 12px; text-align: left;">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${coupon.usage_history.map(usage => `
                                    <tr style="border-bottom: 1px solid var(--admin-gray-200);">
                                        <td style="padding: 12px;">
                                            <div style="font-weight: 500;">${escapeHtml(usage.customer_name)}</div>
                                            <div style="font-size: 12px; color: var(--admin-gray-600);">${escapeHtml(usage.customer_email)}</div>
                                        </td>
                                        <td style="padding: 12px;">
                                            <a href="/admin/pedidos?id=${usage.order_id}" style="color: var(--admin-blue);">
                                                #${usage.order_id}
                                            </a>
                                        </td>
                                        <td style="padding: 12px; text-align: right;">
                                            €${parseFloat(usage.order_total).toFixed(2)}
                                        </td>
                                        <td style="padding: 12px; text-align: right; font-weight: 600; color: var(--admin-green);">
                                            -€${parseFloat(usage.discount_applied).toFixed(2)}
                                        </td>
                                        <td style="padding: 12px;">
                                            ${formatDate(usage.used_at)}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            ` : ''}

            <!-- Actions -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="/admin/cupones/editar/${coupon.coupon_id}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Cupón
                </a>
                <button class="btn btn-danger" onclick="deleteCoupon(${coupon.coupon_id})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    `;
}

// Close coupon modal
function closeCouponModal() {
    document.getElementById('couponModal').style.display = 'none';
}

// Delete coupon
async function deleteCoupon(couponId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este cupón? Esta acción no se puede deshacer.')) {
        return;
    }

    try {
        const response = await fetch(`/admin/cupones/${couponId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to delete coupon');

        const data = await response.json();

        if (data.success) {
            closeCouponModal();
            loadCoupons();
            showNotification('Cupón eliminado correctamente', 'success');
        } else {
            throw new Error(data.message || 'Error deleting coupon');
        }
    } catch (error) {
        console.error('Error deleting coupon:', error);
        showNotification('Error al eliminar el cupón: ' + error.message, 'error');
    }
}

// Export coupons
function exportCoupons() {
    const params = new URLSearchParams(currentFilters);
    params.append('export', 'csv');
    window.location.href = `/api/admin/cupones?${params.toString()}`;
}

// Show notification
function showNotification(message, type = 'info') {
    // Simple notification - you can enhance this
    alert(message);
}

// Utility: Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Utility: Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
