<?php
/**
 * Admin Inventario - Gestión de Inventario
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'inventario';
$page_title = 'Inventario - Admin Kickverse';
$breadcrumbs = [['label' => 'Inventario']];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-warehouse"></i>
            Gestión de Inventario
        </h1>
        <p class="page-subtitle">Control de stock y movimientos de productos</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportInventory()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
        <button class="btn btn-primary" onclick="openAddStockModal()">
            <i class="fas fa-plus"></i>
            Ajustar Stock
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar producto, SKU...">
        <select id="filterProduct" class="form-control">
            <option value="">Todos los productos</option>
        </select>
        <select id="filterMovementType" class="form-control">
            <option value="">Todos los movimientos</option>
            <option value="purchase">Compra</option>
            <option value="sale">Venta</option>
            <option value="return">Devolución</option>
            <option value="adjustment">Ajuste</option>
            <option value="damaged">Dañado</option>
        </select>
    </div>
    <div class="filters-actions">
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fas fa-redo"></i>
            Limpiar filtros
        </button>
    </div>
</div>

<!-- Inventory Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Movimientos de Inventario</h3>
        <span id="totalMovements" class="badge badge-primary">0 movimientos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="inventoryTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left;">Producto</th>
                    <th style="padding: 12px 16px; text-align: left;">Tipo</th>
                    <th style="padding: 12px 16px; text-align: right;">Cantidad</th>
                    <th style="padding: 12px 16px; text-align: right;">Stock Actual</th>
                    <th style="padding: 12px 16px; text-align: left;">Referencia</th>
                    <th style="padding: 12px 16px; text-align: left;">Fecha</th>
                    <th style="padding: 12px 16px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody">
                <tr>
                    <td colspan="8" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px;">Cargando inventario...</p>
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
// TODO: Implementar carga de datos desde API /api/admin/inventario
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Página de Inventario lista - TODO: Implementar API');
    // Simular carga vacía
    setTimeout(() => {
        document.getElementById('inventoryTableBody').innerHTML = `
            <tr>
                <td colspan="8" style="padding: 60px; text-align: center;">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div class="empty-state-title">No hay movimientos de inventario</div>
                        <div class="empty-state-description">Los movimientos de stock aparecerán aquí. API pendiente de implementar.</div>
                    </div>
                </td>
            </tr>
        `;
    }, 500);
});

function exportInventory() {
    showToast('Función de exportación en desarrollo', 'info');
}

function openAddStockModal() {
    showToast('Función de ajuste de stock en desarrollo', 'info');
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterProduct').value = '';
    document.getElementById('filterMovementType').value = '';
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
