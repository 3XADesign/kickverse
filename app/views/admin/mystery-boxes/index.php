<?php
/**
 * Admin Mystery Boxes - Gestión de Mystery Boxes
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'mystery-boxes';
$page_title = 'Mystery Boxes - Admin Kickverse';
$breadcrumbs = [['label' => 'Mystery Boxes']];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-box-open"></i>
            Mystery Boxes
        </h1>
        <p class="page-subtitle">Gestiona todas las Mystery Boxes y sus pedidos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportBoxes()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por ID, cliente...">
        <select id="filterType" class="form-control">
            <option value="">Todos los tipos</option>
            <option value="1">Box Básica</option>
            <option value="2">Box Premium</option>
            <option value="3">Box VIP</option>
        </select>
        <select id="filterStatus" class="form-control">
            <option value="">Todos los estados</option>
            <option value="pending_payment">Pago Pendiente</option>
            <option value="processing">En Proceso</option>
            <option value="shipped">Enviado</option>
            <option value="delivered">Entregado</option>
        </select>
    </div>
    <div class="filters-actions">
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fas fa-redo"></i>
            Limpiar filtros
        </button>
    </div>
</div>

<!-- Mystery Boxes Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Mystery Boxes</h3>
        <span id="totalBoxes" class="badge badge-primary">0 boxes</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="boxesTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left;">Tipo</th>
                    <th style="padding: 12px 16px; text-align: left;">Cliente</th>
                    <th style="padding: 12px 16px; text-align: left;">Liga</th>
                    <th style="padding: 12px 16px; text-align: left;">Estado</th>
                    <th style="padding: 12px 16px; text-align: right;">Total</th>
                    <th style="padding: 12px 16px; text-align: left;">Fecha</th>
                    <th style="padding: 12px 16px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="boxesTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px;">Cargando boxes...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer" id="paginationContainer">
        <div id="paginationInfo"></div>
        <div id="paginationButtons"></div>
    </div>
</div>

<script>
// TODO: Implementar carga de datos desde API /api/admin/mystery-boxes
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Página de Mystery Boxes lista - TODO: Implementar API');
    // Simular carga vacía
    setTimeout(() => {
        document.getElementById('boxesTableBody').innerHTML = `
            <tr>
                <td colspan="8" style="padding: 60px; text-align: center;">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="empty-state-title">No hay Mystery Boxes</div>
                        <div class="empty-state-description">Las Mystery Boxes aparecerán aquí. API pendiente de implementar.</div>
                    </div>
                </td>
            </tr>
        `;
    }, 500);
});

function exportBoxes() {
    showToast('Función de exportación en desarrollo', 'info');
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterType').value = '';
    document.getElementById('filterStatus').value = '';
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
