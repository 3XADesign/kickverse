<?php
// Vista de Gestión de Mystery Boxes
$current_page = 'mystery-boxes';
$page_title = 'Mystery Boxes';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <div>
            <h2 class="crm-card-title">
                <i class="fas fa-box-open"></i>
                Mystery Boxes
            </h2>
            <p class="crm-card-subtitle">
                Total: <?= number_format($stats['total_boxes'] ?? 0) ?> boxes |
                Ingresos: €<?= number_format($stats['total_revenue'] ?? 0, 2) ?>
            </p>
        </div>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por ID o cliente..." class="search-input">
            </div>
            <select id="filterType" class="form-select" style="width: auto;">
                <option value="">Todos los tipos</option>
                <?php if (!empty($boxTypes)): ?>
                    <?php foreach ($boxTypes as $type): ?>
                        <option value="<?= $type['box_type_id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="pending_payment">Pago Pendiente</option>
                <option value="processing">En Proceso</option>
                <option value="shipped">Enviado</option>
                <option value="delivered">Entregado</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Boxes</div>
                <div class="stat-value"><?= number_format($stats['total_boxes'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Entregadas</div>
                <div class="stat-value"><?= number_format($stats['delivered'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">En Proceso</div>
                <div class="stat-value"><?= number_format($stats['processing'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ingresos</div>
                <div class="stat-value">€<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
            </div>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="mysteryBoxesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Box</th>
                        <th>Cliente</th>
                        <th>Liga</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($boxes)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <p class="empty-state-title">No hay Mystery Boxes</p>
                                    <p class="empty-state-text">Las Mystery Boxes aparecerán aquí cuando los clientes las compren</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($boxes as $box): ?>
                            <tr class="table-row-clickable" onclick="openMysteryBoxModal(<?= $box['mystery_box_order_id'] ?>)">
                                <td><strong>#<?= $box['mystery_box_order_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-box-open" style="color: var(--primary);"></i>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($box['box_type_name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                Precio base: €<?= number_format($box['base_price'], 2) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div style="font-weight: 600;"><?= htmlspecialchars($box['customer_name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--gray-500);"><?= htmlspecialchars($box['customer_email']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($box['league_name']): ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-trophy"></i>
                                            <?= htmlspecialchars($box['league_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Random</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-primary">
                                        <?= $box['items_count'] ?> items
                                    </span>
                                </td>
                                <td><strong>€<?= number_format($box['total_amount'], 2) ?></strong></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending_payment' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusTexts = [
                                        'pending_payment' => 'Pago Pendiente',
                                        'processing' => 'Procesando',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    $color = $statusColors[$box['order_status']] ?? 'secondary';
                                    $text = $statusTexts[$box['order_status']] ?? $box['order_status'];
                                    ?>
                                    <span class="badge badge-<?= $color ?>"><?= $text ?></span>
                                </td>
                                <td>
                                    <?php
                                    $paymentColors = [
                                        'pending' => 'warning',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'secondary'
                                    ];
                                    $paymentTexts = [
                                        'pending' => 'Pendiente',
                                        'completed' => 'Completado',
                                        'failed' => 'Fallido',
                                        'refunded' => 'Reembolsado'
                                    ];
                                    $payColor = $paymentColors[$box['payment_status']] ?? 'secondary';
                                    $payText = $paymentTexts[$box['payment_status']] ?? $box['payment_status'];
                                    ?>
                                    <span class="badge badge-<?= $payColor ?>"><?= $payText ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($box['order_date'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); openMysteryBoxModal(<?= $box['mystery_box_order_id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
// Agregar función global para modal
window.openMysteryBoxModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

// Filtros
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#mysteryBoxesTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});

document.getElementById('filterType')?.addEventListener('change', function() {
    window.location.href = `?type=${this.value}`;
});

document.getElementById('filterStatus')?.addEventListener('change', function() {
    window.location.href = `?status=${this.value}`;
});
</script>
