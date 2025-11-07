<?php
// Vista de Gestión de Inventario
$current_page = 'inventario';
$page_title = 'Inventario';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <div>
            <h2 class="crm-card-title">
                <i class="fas fa-boxes"></i>
                Gestión de Inventario
            </h2>
            <p class="crm-card-subtitle">
                Stock total: <?= number_format($stats['total_stock'] ?? 0) ?> unidades |
                Variantes: <?= number_format($stats['total_variants'] ?? 0) ?>
            </p>
        </div>
        <div class="crm-card-actions">
            <button class="btn btn-primary" onclick="openAddStockModal()">
                <i class="fas fa-plus"></i>
                Ajustar Stock
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Stock Total</div>
                <div class="stat-value"><?= number_format($stats['total_stock'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Stock Bajo</div>
                <div class="stat-value"><?= number_format($stats['low_stock_count'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--danger), #dc2626);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Agotados</div>
                <div class="stat-value"><?= number_format($stats['out_of_stock_count'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value"><?= number_format($stats['today_sales'] ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Bajo -->
    <?php if (!empty($lowStockAlerts)): ?>
    <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
            <div>
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Alertas de Stock Bajo</h3>
                <p style="margin: 0; font-size: 0.875rem; opacity: 0.9;">
                    <?= count($lowStockAlerts) ?> productos necesitan reposición
                </p>
            </div>
        </div>
        <div style="display: grid; gap: 0.75rem;">
            <?php foreach ($lowStockAlerts as $alert): ?>
                <div class="product-alert-item" style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.5); border-radius: 8px;">
                    <?php if ($alert['image_url']): ?>
                        <img src="<?= htmlspecialchars($alert['image_url']) ?>"
                             alt="<?= htmlspecialchars($alert['product_name']) ?>"
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <div style="width: 50px; height: 50px; background: var(--gray-200); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: var(--gray-400);"></i>
                        </div>
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--dark);">
                            <?= htmlspecialchars($alert['product_name']) ?>
                            <?php if ($alert['size']): ?>
                                - Talla <?= htmlspecialchars($alert['size']) ?>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--gray-600);">
                            SKU: <?= htmlspecialchars($alert['sku']) ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--danger);">
                            <?= $alert['stock_quantity'] ?> / <?= $alert['threshold_quantity'] ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--gray-600);">unidades</div>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="openReplenishModal(<?= $alert['variant_id'] ?>)">
                        <i class="fas fa-plus"></i>
                        Reponer
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="crm-card-body" style="border-top: 1px solid var(--gray-200);">
        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div class="search-box" style="flex: 1; min-width: 250px;">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar producto..." class="search-input">
            </div>
            <select id="filterProduct" class="form-select" style="width: auto; min-width: 200px;">
                <option value="">Todos los productos</option>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <select id="filterMovementType" class="form-select" style="width: auto;">
                <option value="">Todos los movimientos</option>
                <option value="purchase">Compra</option>
                <option value="sale">Venta</option>
                <option value="return">Devolución</option>
                <option value="adjustment">Ajuste</option>
                <option value="reserved">Reservado</option>
                <option value="unreserved">Liberado</option>
                <option value="damaged">Dañado</option>
                <option value="lost">Perdido</option>
            </select>
            <input type="date" id="filterDateFrom" class="form-control" style="width: auto;">
            <input type="date" id="filterDateTo" class="form-control" style="width: auto;">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="crm-table-container">
            <table class="crm-table" id="movementsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Tipo de Movimiento</th>
                        <th>Cantidad</th>
                        <th>Stock Después</th>
                        <th>Stock Actual</th>
                        <th>Referencia</th>
                        <th>Fecha</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movements)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-boxes"></i>
                                    <p class="empty-state-title">No hay movimientos de stock</p>
                                    <p class="empty-state-text">Los movimientos de inventario aparecerán aquí</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movements as $movement): ?>
                            <tr>
                                <td><strong>#<?= $movement['movement_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <?php if ($movement['image_url']): ?>
                                            <img src="<?= htmlspecialchars($movement['image_url']) ?>"
                                                 alt="<?= htmlspecialchars($movement['product_name']) ?>"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($movement['product_name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                SKU: <?= htmlspecialchars($movement['sku']) ?>
                                                <?php if ($movement['size']): ?>
                                                    | Talla: <?= htmlspecialchars($movement['size']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $typeColors = [
                                        'purchase' => 'success',
                                        'sale' => 'info',
                                        'return' => 'primary',
                                        'adjustment' => 'warning',
                                        'reserved' => 'secondary',
                                        'unreserved' => 'secondary',
                                        'damaged' => 'danger',
                                        'lost' => 'danger'
                                    ];
                                    $typeTexts = [
                                        'purchase' => 'Compra',
                                        'sale' => 'Venta',
                                        'return' => 'Devolución',
                                        'adjustment' => 'Ajuste',
                                        'reserved' => 'Reservado',
                                        'unreserved' => 'Liberado',
                                        'damaged' => 'Dañado',
                                        'lost' => 'Perdido'
                                    ];
                                    $color = $typeColors[$movement['movement_type']] ?? 'secondary';
                                    $text = $typeTexts[$movement['movement_type']] ?? $movement['movement_type'];
                                    ?>
                                    <span class="badge badge-<?= $color ?>">
                                        <?= $text ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: <?= $movement['quantity'] >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                                        <?= $movement['quantity'] >= 0 ? '+' : '' ?><?= $movement['quantity'] ?>
                                    </span>
                                </td>
                                <td><?= $movement['stock_after'] ?></td>
                                <td>
                                    <strong><?= $movement['current_stock'] ?></strong>
                                    <?php if ($movement['current_stock'] <= $movement['low_stock_threshold']): ?>
                                        <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-left: 0.25rem;" title="Stock bajo"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size: 0.875rem; color: var(--gray-600);">
                                        <?= htmlspecialchars($movement['reference_text']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></td>
                                <td>
                                    <?php if ($movement['notes']): ?>
                                        <span style="font-size: 0.875rem;" title="<?= htmlspecialchars($movement['notes']) ?>">
                                            <?= htmlspecialchars(substr($movement['notes'], 0, 30)) ?><?= strlen($movement['notes']) > 30 ? '...' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-link <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Filtros
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#movementsTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});

document.getElementById('filterProduct')?.addEventListener('change', function() {
    applyFilters();
});

document.getElementById('filterMovementType')?.addEventListener('change', function() {
    applyFilters();
});

document.getElementById('filterDateFrom')?.addEventListener('change', function() {
    applyFilters();
});

document.getElementById('filterDateTo')?.addEventListener('change', function() {
    applyFilters();
});

function applyFilters() {
    const params = new URLSearchParams();

    const product = document.getElementById('filterProduct').value;
    if (product) params.set('product', product);

    const movementType = document.getElementById('filterMovementType').value;
    if (movementType) params.set('movement_type', movementType);

    const dateFrom = document.getElementById('filterDateFrom').value;
    if (dateFrom) params.set('date_from', dateFrom);

    const dateTo = document.getElementById('filterDateTo').value;
    if (dateTo) params.set('date_to', dateTo);

    window.location.href = '?' + params.toString();
}

function clearFilters() {
    window.location.href = window.location.pathname;
}

function openAddStockModal() {
    alert('Función de ajuste de stock manual - Por implementar');
}

function openReplenishModal(variantId) {
    alert('Función de reposición de stock para variante #' + variantId + ' - Por implementar');
}
</script>
