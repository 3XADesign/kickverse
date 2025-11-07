<?php
/**
 * Admin Ligas - Vista principal de gestión de ligas
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'ligas';
$page_title = 'Gestión de Ligas - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Ligas']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-trophy"></i>
            Gestión de Ligas
        </h1>
        <p class="page-subtitle">Administra las ligas y competiciones disponibles</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="createLeague()">
            <i class="fas fa-plus"></i>
            Nueva Liga
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
                   placeholder="Buscar por nombre o país...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterCountry" class="form-control">
                <option value="">Todos los países</option>
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

<!-- Leagues Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Ligas</h3>
        <span id="totalLeagues" class="badge badge-primary">0 ligas</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="leaguesTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; width: 80px;">Logo</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Nombre</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">País</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Equipos</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Display Order</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="leaguesTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando ligas...</p>
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
// Leagues management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load leagues on page load
document.addEventListener('DOMContentLoaded', () => {
    loadLeagues();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadLeagues();
    }, 500));

    document.getElementById('filterCountry').addEventListener('change', () => {
        currentPage = 1;
        loadLeagues();
    });

    document.getElementById('filterStatus').addEventListener('change', () => {
        currentPage = 1;
        loadLeagues();
    });
});

// Load leagues from API
async function loadLeagues() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const country = document.getElementById('filterCountry').value;
        if (country) params.append('country', country);

        const status = document.getElementById('filterStatus').value;
        if (status !== '') params.append('is_active', status);

        const response = await fetch(`/api/admin/ligas?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderLeagues(data.leagues);
            renderPagination(data.pagination);
            document.getElementById('totalLeagues').textContent = `${data.pagination.total} ligas`;

            // Populate country filter
            populateCountryFilter(data.leagues);
        } else {
            showToast('Error al cargar ligas', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading leagues:', error);
        showToast('Error de conexión', 'error');
    }
}

// Render leagues table
function renderLeagues(leagues) {
    const tbody = document.getElementById('leaguesTableBody');

    if (leagues.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-trophy"></i></div>
                    <div class="empty-state-title">No se encontraron ligas</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = leagues.map(league => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewLeague(${league.league_id})">
            <td style="padding: 12px 16px;">
                ${league.logo_path
                    ? `<img src="${escapeHtml(league.logo_path)}" alt="${escapeHtml(league.name)}" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; background: white; padding: 4px; border: 1px solid var(--admin-gray-200);">`
                    : `<div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: var(--admin-gray-100); border-radius: 8px; color: var(--admin-gray-400);"><i class="fas fa-trophy"></i></div>`
                }
            </td>
            <td style="padding: 12px 16px; font-weight: 500;">#${league.league_id}</td>
            <td style="padding: 12px 16px;">
                <strong>${escapeHtml(league.name)}</strong>
                <br><small style="color: var(--admin-gray-500);">${escapeHtml(league.slug)}</small>
            </td>
            <td style="padding: 12px 16px;">${league.country ? escapeHtml(league.country) : '<span style="color: var(--admin-gray-400);">-</span>'}</td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-info">${league.teams_count} equipos</span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-secondary">${league.display_order}</span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-${league.is_active ? 'success' : 'warning'}">
                    ${league.is_active ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewLeague(${league.league_id})">
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
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} ligas`;

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
    loadLeagues();
}

// View league details
function viewLeague(leagueId) {
    showToast('Detalles de liga en desarrollo', 'info');
}

// Create new league
function createLeague() {
    showToast('Funcionalidad de creación en desarrollo', 'info');
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCountry').value = '';
    document.getElementById('filterStatus').value = '';
    currentPage = 1;
    loadLeagues();
}

// Populate country filter with unique values
function populateCountryFilter(leagues) {
    const countries = [...new Set(leagues.map(l => l.country).filter(c => c))];
    const select = document.getElementById('filterCountry');
    const currentValue = select.value;

    // Keep first option and add countries
    select.innerHTML = '<option value="">Todos los países</option>' +
        countries.sort().map(country => `<option value="${escapeHtml(country)}">${escapeHtml(country)}</option>`).join('');

    select.value = currentValue;
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
