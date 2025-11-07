<?php
/**
 * Admin Productos - Vista principal de gestión de productos
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'productos';
$page_title = 'Gestión de Productos - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Productos']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-tshirt"></i>
            Gestión de Productos
        </h1>
        <p class="page-subtitle">Visualiza y gestiona todos los productos de la tienda</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="window.location.href='/admin/productos/crear'">
            <i class="fas fa-plus"></i>
            Nuevo Producto
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
                   placeholder="Buscar por ID, nombre...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterProductType" class="form-control">
                <option value="">Todos los tipos</option>
                <option value="jersey">Camisetas</option>
                <option value="accessory">Accesorios</option>
                <option value="mystery_box">Mystery Box</option>
                <option value="subscription">Suscripción</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterLeague" class="form-control">
                <option value="">Todas las ligas</option>
                <option value="1">LaLiga</option>
                <option value="2">Premier League</option>
                <option value="3">Serie A</option>
                <option value="4">Bundesliga</option>
                <option value="5">Ligue 1</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterTeam" class="form-control">
                <option value="">Todos los equipos</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterStatus" class="form-control">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
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

<!-- Products Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Productos</h3>
        <span id="totalProducts" class="badge badge-primary">0 productos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="productsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Imagen</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Nombre</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Tipo</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Liga</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Equipo</th>
                    <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600;">Precio</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Stock</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <tr>
                    <td colspan="10" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando productos...</p>
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
// Products management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load products on page load
document.addEventListener('DOMContentLoaded', () => {
    loadProducts();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadProducts();
    }, 500));

    document.getElementById('filterProductType').addEventListener('change', () => {
        currentPage = 1;
        loadProducts();
    });

    document.getElementById('filterLeague').addEventListener('change', () => {
        currentPage = 1;
        loadProducts();
    });

    document.getElementById('filterTeam').addEventListener('change', () => {
        currentPage = 1;
        loadProducts();
    });

    document.getElementById('filterStatus').addEventListener('change', () => {
        currentPage = 1;
        loadProducts();
    });
});

// Load products from API
async function loadProducts() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const productType = document.getElementById('filterProductType').value;
        if (productType) params.append('product_type', productType);

        const leagueId = document.getElementById('filterLeague').value;
        if (leagueId) params.append('league_id', leagueId);

        const teamId = document.getElementById('filterTeam').value;
        if (teamId) params.append('team_id', teamId);

        const status = document.getElementById('filterStatus').value;
        if (status !== '') params.append('is_active', status);

        const response = await fetch(`/api/admin/productos?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderProducts(data.products);
            renderPagination(data.pagination);
            document.getElementById('totalProducts').textContent = `${data.pagination.total} productos`;
        } else {
            showToast('Error al cargar productos', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading products:', error);
        showToast('Error de conexión', 'error');
    }
}

// Render products table
function renderProducts(products) {
    const tbody = document.getElementById('productsTableBody');

    if (products.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-tshirt"></i></div>
                    <div class="empty-state-title">No se encontraron productos</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = products.map(product => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewProduct(${product.product_id})">
            <td style="padding: 12px 16px; font-weight: 500;">#${product.product_id}</td>
            <td style="padding: 12px 16px;">
                ${product.main_image ? `
                    <img src="${escapeHtml(product.main_image)}"
                         alt="${escapeHtml(product.name)}"
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid var(--admin-gray-200);">
                ` : `
                    <div style="width: 50px; height: 50px; background: var(--admin-gray-100); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-tshirt" style="color: var(--admin-gray-400);"></i>
                    </div>
                `}
            </td>
            <td style="padding: 12px 16px;">
                <div style="font-weight: 500;">${escapeHtml(product.name)}</div>
                ${product.season ? `<small style="color: var(--admin-gray-500);">${escapeHtml(product.season)}</small>` : ''}
            </td>
            <td style="padding: 12px 16px;">
                <span class="badge badge-${getProductTypeColor(product.product_type)}">${getProductTypeLabel(product.product_type)}</span>
            </td>
            <td style="padding: 12px 16px;">
                ${product.league_name ? escapeHtml(product.league_name) : '<span style="color: var(--admin-gray-400);">—</span>'}
            </td>
            <td style="padding: 12px 16px;">
                ${product.team_name ? escapeHtml(product.team_name) : '<span style="color: var(--admin-gray-400);">—</span>'}
            </td>
            <td style="padding: 12px 16px; text-align: right; font-weight: 500;">
                €${parseFloat(product.base_price).toFixed(2)}
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-${getStockColor(product.total_stock)}">${product.total_stock} uds.</span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-${product.is_active ? 'success' : 'danger'}">
                    ${product.is_active ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewProduct(${product.product_id})">
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
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} productos`;

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
    loadProducts();
}

// View product details
function viewProduct(productId) {
    window.location.href = `/admin/productos?product_id=${productId}`;
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterProductType').value = '';
    document.getElementById('filterLeague').value = '';
    document.getElementById('filterTeam').value = '';
    document.getElementById('filterStatus').value = '';
    currentPage = 1;
    loadProducts();
}

// Helper functions
function getProductTypeLabel(type) {
    const labels = {
        'jersey': 'Camiseta',
        'accessory': 'Accesorio',
        'mystery_box': 'Mystery Box',
        'subscription': 'Suscripción'
    };
    return labels[type] || type;
}

function getProductTypeColor(type) {
    const colors = {
        'jersey': 'info',
        'accessory': 'secondary',
        'mystery_box': 'purple',
        'subscription': 'warning'
    };
    return colors[type] || 'secondary';
}

function getStockColor(stock) {
    if (stock > 20) return 'success';
    if (stock > 5) return 'warning';
    return 'danger';
}

function showLoading() {
    document.getElementById('productsTableBody').innerHTML = `
        <tr>
            <td colspan="10" style="padding: 60px; text-align: center;">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando productos...</p>
            </td>
        </tr>
    `;
}

function hideLoading() {
    // Loading handled by renderProducts
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
