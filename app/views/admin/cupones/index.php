<?php
// Vista de Gestión de Cupones
$current_page = 'cupones';
$page_title = 'Cupones de Descuento';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <div>
            <h2 class="crm-card-title">
                <i class="fas fa-tags"></i>
                Cupones de Descuento
            </h2>
            <p class="crm-card-subtitle">
                Total: <?= number_format($stats['total_coupons'] ?? 0) ?> cupones |
                Activos: <?= number_format($stats['active_coupons'] ?? 0) ?>
            </p>
        </div>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar cupones..." class="search-input">
            </div>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="expired">Expirados</option>
                <option value="inactive">Inactivos</option>
            </select>
            <button class="btn btn-primary" onclick="window.location.href='/admin/cupones/crear'">
                <i class="fas fa-plus"></i>
                Nuevo Cupón
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Cupones</div>
                <div class="stat-value"><?= number_format($stats['total_coupons'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Activos</div>
                <div class="stat-value"><?= number_format($stats['active_coupons'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Usos Totales</div>
                <div class="stat-value"><?= number_format($stats['total_uses'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Descuento Total</div>
                <div class="stat-value">€<?= number_format($stats['total_discount_given'] ?? 0, 2) ?></div>
            </div>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="cuponesTable">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Descuento</th>
                        <th>Mínimo</th>
                        <th>Máx/Cliente</th>
                        <th>Usos</th>
                        <th>Expiración</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cupones)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-tags"></i>
                                    <p class="empty-state-title">No hay cupones</p>
                                    <p class="empty-state-text">Comienza creando tu primer cupón de descuento</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cupones as $cupon): ?>
                            <?php
                            $isExpired = $cupon['expiry_date'] && strtotime($cupon['expiry_date']) < time();
                            $isActive = $cupon['is_active'] && !$isExpired;
                            ?>
                            <tr class="table-row-clickable" onclick="openCuponModal(<?= $cupon['coupon_id'] ?>)">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="badge badge-<?= $isActive ? 'success' : 'secondary' ?>" style="font-family: monospace; font-size: 0.9rem;">
                                            <?= htmlspecialchars($cupon['code']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($cupon['description'] ?? '-') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($cupon['discount_type'] === 'percentage'): ?>
                                        <span class="badge badge-primary">
                                            <i class="fas fa-percent"></i> Porcentaje
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-euro-sign"></i> Fijo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?php if ($cupon['discount_type'] === 'percentage'): ?>
                                            <?= $cupon['discount_value'] ?>%
                                        <?php else: ?>
                                            €<?= number_format($cupon['discount_value'], 2) ?>
                                        <?php endif; ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= $cupon['min_order_amount'] ? '€' . number_format($cupon['min_order_amount'], 2) : '-' ?>
                                </td>
                                <td>
                                    <?= $cupon['max_uses_per_customer'] ?? '∞' ?>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?= number_format($cupon['total_uses'] ?? 0) ?> usos
                                    </span>
                                </td>
                                <td>
                                    <?php if ($cupon['expiry_date']): ?>
                                        <div style="font-size: 0.85rem;">
                                            <?= date('d/m/Y', strtotime($cupon['expiry_date'])) ?>
                                            <?php if ($isExpired): ?>
                                                <span class="badge badge-danger" style="font-size: 0.7rem;">Expirado</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--gray-500);">Sin expiración</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isActive): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="badge badge-danger">Expirado</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); openCuponModal(<?= $cupon['coupon_id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); window.location.href='/admin/cupones/editar/<?= $cupon['coupon_id'] ?>'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteCoupon(<?= $cupon['coupon_id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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
window.openCuponModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

// Eliminar cupón
window.deleteCoupon = async function(id) {
    if (!confirm('¿Estás seguro de eliminar este cupón?')) return;

    try {
        const response = await fetch(`/admin/cupones/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to delete');

        crmAdmin.showSuccess('Cupón eliminado correctamente');
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error deleting coupon:', error);
        crmAdmin.showError('Error al eliminar el cupón');
    }
};

// Filtros
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#cuponesTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});

document.getElementById('filterStatus')?.addEventListener('change', function() {
    window.location.href = `?status=${this.value}`;
});
</script>
