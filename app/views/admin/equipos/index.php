<?php
/**
 * Admin Equipos - Vista principal de gestión de equipos
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'equipos';
$page_title = 'Gestión de Equipos - Admin Kickverse';
$breadcrumbs = [
    ['label' => 'Equipos']
];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-shield-alt"></i>
            Gestión de Equipos
        </h1>
        <p class="page-subtitle">Administra los equipos y clubes disponibles</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="createTeam()">
            <i class="fas fa-plus"></i>
            Nuevo Equipo
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
                   placeholder="Buscar por nombre o liga...">
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterLeague" class="form-control">
                <option value="">Todas las ligas</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <select id="filterTopTeam" class="form-control">
                <option value="">Todos los equipos</option>
                <option value="1">Top Teams</option>
                <option value="0">Normal</option>
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

<!-- Teams Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Equipos</h3>
        <span id="totalTeams" class="badge badge-primary">0 equipos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="teamsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; width: 80px;">Logo</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Nombre</th>
                    <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600;">Liga</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Top Team</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Display Order</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Estado</th>
                    <th style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody id="teamsTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px; color: var(--admin-gray-600);">Cargando equipos...</p>
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
// Teams management script
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};

// Load teams on page load
document.addEventListener('DOMContentLoaded', () => {
    loadTeams();
    loadLeaguesForFilter();

    // Setup filter listeners
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadTeams();
    }, 500));

    document.getElementById('filterLeague').addEventListener('change', () => {
        currentPage = 1;
        loadTeams();
    });

    document.getElementById('filterTopTeam').addEventListener('change', () => {
        currentPage = 1;
        loadTeams();
    });

    document.getElementById('filterStatus').addEventListener('change', () => {
        currentPage = 1;
        loadTeams();
    });
});

// Load teams from API
async function loadTeams() {
    try {
        showLoading();

        // Build query string
        const params = new URLSearchParams();
        params.append('page', currentPage);

        const search = document.getElementById('searchInput').value.trim();
        if (search) params.append('search', search);

        const leagueId = document.getElementById('filterLeague').value;
        if (leagueId) params.append('league_id', leagueId);

        const topTeam = document.getElementById('filterTopTeam').value;
        if (topTeam !== '') params.append('is_top_team', topTeam);

        const status = document.getElementById('filterStatus').value;
        if (status !== '') params.append('is_active', status);

        const response = await fetch(`/api/admin/equipos?${params.toString()}`);
        const data = await response.json();

        hideLoading();

        if (data.success) {
            renderTeams(data.teams);
            renderPagination(data.pagination);
            document.getElementById('totalTeams').textContent = `${data.pagination.total} equipos`;
        } else {
            showToast('Error al cargar equipos', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading teams:', error);
        showToast('Error de conexión', 'error');
    }
}

// Load leagues for filter
async function loadLeaguesForFilter() {
    try {
        const response = await fetch('/api/admin/ligas?per_page=100');
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('filterLeague');
            select.innerHTML = '<option value="">Todas las ligas</option>' +
                data.leagues.map(league =>
                    `<option value="${league.league_id}">${escapeHtml(league.name)}</option>`
                ).join('');
        }
    } catch (error) {
        console.error('Error loading leagues:', error);
    }
}

// Render teams table
function renderTeams(teams) {
    const tbody = document.getElementById('teamsTableBody');

    if (teams.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="empty-state-title">No se encontraron equipos</div>
                    <div class="empty-state-description">Intenta cambiar los filtros de búsqueda</div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = teams.map(team => `
        <tr style="border-bottom: 1px solid var(--admin-gray-100); cursor: pointer;" onclick="viewTeam(${team.team_id})">
            <td style="padding: 12px 16px;">
                ${team.logo_path
                    ? `<img src="${escapeHtml(team.logo_path)}" alt="${escapeHtml(team.name)}" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; background: white; padding: 4px; border: 1px solid var(--admin-gray-200);">`
                    : `<div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: var(--admin-gray-100); border-radius: 8px; color: var(--admin-gray-400);"><i class="fas fa-shield-alt"></i></div>`
                }
            </td>
            <td style="padding: 12px 16px; font-weight: 500;">#${team.team_id}</td>
            <td style="padding: 12px 16px;">
                <strong>${escapeHtml(team.name)}</strong>
                <br><small style="color: var(--admin-gray-500);">${escapeHtml(team.slug)}</small>
            </td>
            <td style="padding: 12px 16px;">
                ${team.league_name
                    ? `<span class="badge badge-info">${escapeHtml(team.league_name)}</span>`
                    : '<span style="color: var(--admin-gray-400);">-</span>'}
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                ${team.is_top_team
                    ? '<span class="badge badge-warning"><i class="fas fa-star"></i> Top</span>'
                    : '<span style="color: var(--admin-gray-400);">-</span>'}
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-secondary">${team.display_order}</span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <span class="badge badge-${team.is_active ? 'success' : 'warning'}">
                    ${team.is_active ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td style="padding: 12px 16px; text-align: center;">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); viewTeam(${team.team_id})">
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
        `Mostrando ${pagination.from}-${pagination.to} de ${pagination.total} equipos`;

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
    loadTeams();
}

// View team details
function viewTeam(teamId) {
    showToast('Detalles de equipo en desarrollo', 'info');
}

// Create new team
function createTeam() {
    showToast('Funcionalidad de creación en desarrollo', 'info');
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterLeague').value = '';
    document.getElementById('filterTopTeam').value = '';
    document.getElementById('filterStatus').value = '';
    currentPage = 1;
    loadTeams();
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
