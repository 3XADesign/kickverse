<?php
/**
 * Admin Clientes - Vista principal de gestión de clientes
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'clientes';
$page_title = 'Gestión de Clientes - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Clientes']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-users"></i>
            Gestión de Clientes
        </h1>
        <p class="page-subtitle">Administra la base de clientes de Kickverse</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="createClient()">
            <i class="fas fa-plus"></i>
            Nuevo Cliente
        </button>
        <button class="btn btn-secondary" onclick="exportClients()">
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
                   placeholder="Buscar por nombre, email, teléfono...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterStatus" class="form-control">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
                <option value="blocked">Bloqueado</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterTier" class="form-control">
                <option value="">Todos los niveles</option>
                <option value="standard">Standard</option>
                <option value="silver">Silver</option>
                <option value="gold">Gold</option>
                <option value="platinum">Platinum</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterLanguage" class="form-control">
                <option value="">Todos los idiomas</option>
                <option value="es">Español</option>
                <option value="en">English</option>
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

<!-- Customers Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Clientes</h3>
        <span id="totalCustomers" class="badge badge-primary">0 clientes</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="customersTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Nombre</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Email</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Teléfono</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Tier</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Puntos</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Total Gastado</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Pedidos</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="customersTableBody">
                <tr>
                    <td colspan="10" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando clientes...</p>
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
// Customers management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load customers on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCustomers();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadCustomers();
    }, 500));

    document.getElementById('filterStatus').addEventListener('change', () => {
        currentPage = 1;
        loadCustomers();
    });

    document.getElementById('filterTier').addEventListener('change', () => {
        currentPage = 1;
        loadCustomers();
    });

    document.getElementById('filterLanguage').addEventListener('change', () => {
        currentPage = 1;
        loadCustomers();
    });
});

// Load customers from API
async function loadCustomers() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const status = document.getElementById('filterStatus').value;
        if (status) params.append('status', status);

        const tier = document.getElementById('filterTier').value;
        if (tier) params.append('tier', tier);

        const language = document.getElementById('filterLanguage').value;
        if (language) params.append('language', language);

        const response = await fetch(`/api/admin/clientes?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderCustomers(data.customers);
            renderPagination(data.pagination);
            document.getElementById('totalCustomers').textContent = `${data.pagination.total} clientes`;
        } else {
            showToast('Error al cargar clientes', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading customers:', error);
        showToast('Error de conexión', 'error');
    }
}

// Render customers table
function renderCustomers(customers) {
    const tbody = document.getElementById('customersTableBody');

    if (customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-users"></i></div>
                    <div class="empty-state-title">No se encontraron clientes</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = customers.map(customer => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewCustomer(${customer.customer_id})">
            <td style="padding: 12px 16px; font-weight: 500;">#${customer.customer_id}</td>
            <td style="padding: 12px 16px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="avatar">${getInitials(customer.full_name)}</div>
                    <div>
                        <div style="font-weight: 500;">${escapeHtml(customer.full_name)}</div>
                        ${customer.telegram_username ? `<small style="color: var(--admin-gray-500);"><i class="fab fa-telegram"></i> @${escapeHtml(customer.telegram_username)}</small>` : ''}
                    </div>
                </div>
            </td>
            <td style="padding: 12px 16px; font-size: 13px;">
                ${customer.email ? escapeHtml(customer.email) : '<span style="color: var(--admin-gray-400);">N/A</span>'}
            </td>
            <td style="padding: 12px 16px; font-size: 13px;">
                ${customer.phone ? escapeHtml(customer.phone) :
                  customer.whatsapp_number ? `<i class="fab fa-whatsapp"></i> ${escapeHtml(customer.whatsapp_number)}` :
                  '<span style="color: var(--admin-gray-400);">N/A</span>'}
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getStatusColor(customer.customer_status)}">${getStatusLabel(customer.customer_status)}</span>
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getTierColor(customer.loyalty_tier)}">${getTierLabel(customer.loyalty_tier)}</span>
            </td>
            <td style="padding: 12px 16px; text-align: right; font-weight: 500;">
                ${customer.loyalty_points || 0}
            </td>
            <td style="padding: 12px 16px; text-align: right; font-weight: 500;">
                €${parseFloat(customer.total_spent || 0).toFixed(2)}
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-secondary">${customer.total_orders || 0}</span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewCustomer(${customer.customer_id})">
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
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} clientes`;

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
    loadCustomers();
}

// View customer details
function viewCustomer(customerId) {
    // TODO: Implementar modal full-screen para ver detalles del cliente
    console.log('View customer:', customerId);
    showToast('Modal de detalles en desarrollo', 'info');
}

// Create new client
function createClient() {
    window.location.href = '/admin/clientes/crear';
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterTier').value = '';
    document.getElementById('filterLanguage').value = '';
    currentPage = 1;
    loadCustomers();
}

// Export customers
function exportClients() {
    showToast('Función de exportación en desarrollo', 'info');
}

// Helper functions
function getInitials(name) {
    if (!name) return '??';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function getStatusLabel(status) {
    const labels = {
        'active': 'Activo',
        'inactive': 'Inactivo',
        'blocked': 'Bloqueado'
    };
    return labels[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'active': 'success',
        'inactive': 'secondary',
        'blocked': 'danger'
    };
    return colors[status] || 'secondary';
}

function getTierLabel(tier) {
    const labels = {
        'standard': 'Standard',
        'silver': 'Silver',
        'gold': 'Gold',
        'platinum': 'Platinum'
    };
    return labels[tier] || tier;
}

function getTierColor(tier) {
    const colors = {
        'standard': 'secondary',
        'silver': 'info',
        'gold': 'warning',
        'platinum': 'primary'
    };
    return colors[tier] || 'secondary';
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
