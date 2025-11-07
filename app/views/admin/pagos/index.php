<?php
/**
 * Admin Pagos - Gestión de Transacciones de Pago
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'pagos';
$page_title = 'Gestión de Pagos - Admin Kickverse';
$breadcrumbs = [['label' => 'Pagos']];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-credit-card"></i>
            Transacciones de Pago
        </h1>
        <p class="page-subtitle">Gestiona todas las transacciones y métodos de pago</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="exportPayments()">
            <i class="fas fa-download"></i>
            Exportar CSV
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por ID, cliente...">
        <select id="filterStatus" class="form-control">
            <option value="">Todos los estados</option>
            <option value="pending">Pendiente</option>
            <option value="processing">Procesando</option>
            <option value="completed">Completado</option>
            <option value="failed">Fallido</option>
            <option value="expired">Expirado</option>
            <option value="refunded">Reembolsado</option>
        </select>
        <select id="filterMethod" class="form-control">
            <option value="">Todos los métodos</option>
            <option value="oxapay_btc">Bitcoin</option>
            <option value="oxapay_eth">Ethereum</option>
            <option value="oxapay_usdt">USDT</option>
            <option value="telegram_manual">Telegram Manual</option>
            <option value="whatsapp_manual">WhatsApp Manual</option>
        </select>
    </div>
    <div class="filters-actions">
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fas fa-redo"></i>
            Limpiar filtros
        </button>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Pagos</h3>
        <span id="totalPayments" class="badge badge-primary">0 pagos</span>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table id="paymentsTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left;">#ID</th>
                    <th style="padding: 12px 16px; text-align: left;">Cliente</th>
                    <th style="padding: 12px 16px; text-align: left;">Método</th>
                    <th style="padding: 12px 16px; text-align: left;">Estado</th>
                    <th style="padding: 12px 16px; text-align: right;">Monto</th>
                    <th style="padding: 12px 16px; text-align: left;">Fecha</th>
                    <th style="padding: 12px 16px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="paymentsTableBody">
                <tr>
                    <td colspan="7" style="padding: 60px; text-align: center;">
                        <div class="spinner"></div>
                        <p style="margin-top: 20px;">Cargando pagos...</p>
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
// TODO: Implementar carga de datos desde API /api/admin/pagos
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Página de Pagos lista - TODO: Implementar API');
    // Simular carga vacía
    setTimeout(() => {
        document.getElementById('paymentsTableBody').innerHTML = `
            <tr>
                <td colspan="7" style="padding: 60px; text-align: center;">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="empty-state-title">No hay transacciones</div>
                        <div class="empty-state-description">Las transacciones de pago aparecerán aquí. API pendiente de implementar.</div>
                    </div>
                </td>
            </tr>
        `;
    }, 500);
});

function exportPayments() {
    showToast('Función de exportación en desarrollo', 'info');
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterMethod').value = '';
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
