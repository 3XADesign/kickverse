<?php
/**
 * Admin Pedidos - Vista principal de gestión de pedidos
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'pedidos';
$page_title = 'Gestión de Pedidos - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Pedidos']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-shopping-bag"></i>
            Gestión de Pedidos
        </h1>
        <p class="page-subtitle">Visualiza y gestiona todos los pedidos de la tienda</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportOrders()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group" style="margin: 0;">
            <input type="text"
                   id="searchInput"
                   class="form-control"
                   placeholder="Buscar por ID, cliente o tracking...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterOrderStatus" class="form-control">
                <option value="">Todos los estados</option>
                <option value="pending_payment">Pago Pendiente</option>
                <option value="processing">En Proceso</option>
                <option value="shipped">Enviado</option>
                <option value="delivered">Entregado</option>
                <option value="cancelled">Cancelado</option>
                <option value="refunded">Reembolsado</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterPaymentStatus" class="form-control">
                <option value="">Estado de pago</option>
                <option value="pending">Pendiente</option>
                <option value="completed">Completado</option>
                <option value="failed">Fallido</option>
                <option value="refunded">Reembolsado</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterOrderType" class="form-control">
                <option value="">Tipo de pedido</option>
                <option value="catalog">Catálogo</option>
                <option value="mystery_box">Mystery Box</option>
                <option value="subscription_initial">Suscripción</option>
                <option value="drop">Drop</option>
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

<!-- Orders Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Pedidos</h3>
        <span id="totalOrders" class="badge badge-primary">0 pedidos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="ordersTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Cliente</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Tipo</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Pago</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Total</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Fecha</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando pedidos...</p>
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

<script>
// Orders management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load orders on page load
document.addEventListener('DOMContentLoaded', () => {
    loadOrders();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadOrders();
    }, 500));

    document.getElementById('filterOrderStatus').addEventListener('change', () => {
        currentPage = 1;
        loadOrders();
    });

    document.getElementById('filterPaymentStatus').addEventListener('change', () => {
        currentPage = 1;
        loadOrders();
    });

    document.getElementById('filterOrderType').addEventListener('change', () => {
        currentPage = 1;
        loadOrders();
    });
});

// Load orders from API
async function loadOrders() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const orderStatus = document.getElementById('filterOrderStatus').value;
        if (orderStatus) params.append('order_status', orderStatus);

        const paymentStatus = document.getElementById('filterPaymentStatus').value;
        if (paymentStatus) params.append('payment_status', paymentStatus);

        const orderType = document.getElementById('filterOrderType').value;
        if (orderType) params.append('order_type', orderType);

        const response = await fetch(`/api/admin/pedidos?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderOrders(data.orders);
            renderPagination(data.pagination);
            document.getElementById('totalOrders').textContent = `${data.pagination.total} pedidos`;
        } else {
            showToast('Error al cargar pedidos', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading orders:', error);
        showToast('Error de conexión', 'error');
    }
}

// Render orders table
function renderOrders(orders) {
    const tbody = document.getElementById('ordersTableBody');

    if (orders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="empty-state-title">No se encontraron pedidos</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = orders.map(order => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewOrder(${order.order_id})">
            <td style="padding: 12px 16px; font-weight: 500;">#${order.order_id}</td>
            <td style="padding: 12px 16px;">
                ${escapeHtml(order.customer_name || 'N/A')}
                <br><small style="color: var(--admin-gray-500);">${escapeHtml(order.customer_email || '')}</small>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-secondary">${getOrderTypeLabel(order.order_type)}</span>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getOrderStatusColor(order.order_status)}">${getOrderStatusLabel(order.order_status)}</span>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getPaymentStatusColor(order.payment_status)}">${getPaymentStatusLabel(order.payment_status)}</span>
            </td>
            <td style="padding: 12px 16px; text-align: right; font-weight: 500;">
                €${parseFloat(order.total_amount).toFixed(2)}
            </td>
            <td style="padding: 12px 16px; font-size: 13px;">
                ${formatDate(order.order_date, 'datetime')}
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewOrder(${order.order_id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Render pagination
function renderPagination(pagination) {
    totalPages = pagination.pages;
    currentPage = pagination.current_page;

    document.getElementById('paginationInfo').textContent =
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} pedidos`;

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
    loadOrders();
}

// View order details
function viewOrder(orderId) {
    window.location.href = `/admin/pedidos?order_id=${orderId}`;
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterOrderStatus').value = '';
    document.getElementById('filterPaymentStatus').value = '';
    document.getElementById('filterOrderType').value = '';
    currentPage = 1;
    loadOrders();
}

// Export orders
function exportOrders() {
    showToast('Función de exportación en desarrollo', 'info');
}

// Helper functions
function getOrderTypeLabel(type) {
    const labels = {
        'catalog': 'Catálogo',
        'mystery_box': 'Mystery Box',
        'subscription_initial': 'Suscripción',
        'drop': 'Drop'
    };
    return labels[type] || type;
}

function getOrderStatusLabel(status) {
    const labels = {
        'pending_payment': 'Pago pendiente',
        'processing': 'Procesando',
        'shipped': 'Enviado',
        'delivered': 'Entregado',
        'cancelled': 'Cancelado',
        'refunded': 'Reembolsado'
    };
    return labels[status] || status;
}

function getOrderStatusColor(status) {
    const colors = {
        'pending_payment': 'warning',
        'processing': 'info',
        'shipped': 'primary',
        'delivered': 'success',
        'cancelled': 'danger',
        'refunded': 'secondary'
    };
    return colors[status] || 'secondary';
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
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
