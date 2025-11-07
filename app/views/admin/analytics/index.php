<?php
/**
 * Admin Analytics - Estadísticas y Análisis
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'analytics';
$page_title = 'Analytics - Admin Kickverse';
$breadcrumbs = [['label' => 'Analytics']];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-bar"></i>
            Analytics & Estadísticas
        </h1>
        <p class="page-subtitle">Análisis de ventas, productos y clientes</p>
    </div>
    <div class="page-actions">
        <select id="periodFilter" class="form-control" style="width: auto;">
            <option value="7days">Últimos 7 días</option>
            <option value="30days" selected>Últimos 30 días</option>
            <option value="90days">Últimos 90 días</option>
            <option value="year">Este año</option>
        </select>
        <button class="btn btn-secondary" onclick="exportReport()">
            <i class="fas fa-download"></i>
            Exportar
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Ingresos Totales -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Ingresos Totales</span>
            <div class="stat-icon primary">
                <i class="fas fa-euro-sign"></i>
            </div>
        </div>
        <div class="stat-value">€0.00</div>
        <div class="stat-change">
            <i class="fas fa-chart-line"></i>
            0 pedidos
        </div>
    </div>

    <!-- Ticket Medio -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Ticket Medio</span>
            <div class="stat-icon success">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-value">€0.00</div>
        <div class="stat-change">
            <i class="fas fa-calculator"></i>
            por pedido
        </div>
    </div>

    <!-- Clientes Únicos -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Clientes Únicos</span>
            <div class="stat-icon warning">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-user"></i>
            compradores
        </div>
    </div>

    <!-- Pendientes -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Pendientes</span>
            <div class="stat-icon danger">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-exclamation-circle"></i>
            sin pagar
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 30px;">
    <!-- Revenue Trend Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-area"></i>
                Tendencia de Ingresos
            </h3>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="empty-state-title">Gráfico de tendencias</div>
                <div class="empty-state-description">Los datos de ingresos aparecerán aquí. API pendiente de implementar.</div>
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-pie-chart"></i>
                Pedidos por Estado
            </h3>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="empty-state-title">Gráfico de estados</div>
                <div class="empty-state-description">Distribución de pedidos por estado.</div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Table -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-trophy"></i>
            Top Productos Más Vendidos
        </h3>
        <span id="totalProducts" class="badge badge-primary">0 productos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="topProductsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left;">#</th>
                    <th style="padding: 12px 16px; text-align: left;">Producto</th>
                    <th style="padding: 12px 16px; text-align: right;">Unidades</th>
                    <th style="padding: 12px 16px; text-align: right;">Pedidos</th>
                    <th style="padding: 12px 16px; text-align: right;">Ingresos</th>
                </tr>
            </thead>
            <tbody id="topProductsTableBody">
                <tr>
                    <td colspan="5" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px;">Cargando productos...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// TODO: Implementar carga de datos desde API /api/admin/analytics
let currentPeriod = '30days';

document.addEventListener('DOMContentLoaded', () => {
    console.log('Página de Analytics lista - TODO: Implementar API');
    // Simular carga vacía
    setTimeout(() => {
        document.getElementById('topProductsTableBody').innerHTML = `
            <tr>
                <td colspan="5" style="padding: 60px; text-align: center;">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="empty-state-title">No hay datos de ventas</div>
                        <div class="empty-state-description">Los productos más vendidos aparecerán aquí. API pendiente de implementar.</div>
                    </div>
                </td>
            </tr>
        `;
    }, 500);
});

document.getElementById('periodFilter')?.addEventListener('change', function() {
    currentPeriod = this.value;
    showToast('Filtro de período: ' + currentPeriod, 'info');
    // TODO: Recargar datos con nuevo período
});

function exportReport() {
    showToast('Función de exportación en desarrollo', 'info');
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
