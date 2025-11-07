<?php
// Vista de Analytics y Estadísticas
$current_page = 'analytics';
$page_title = 'Analytics';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <div>
            <h2 class="crm-card-title">
                <i class="fas fa-chart-line"></i>
                Analytics & Estadísticas
            </h2>
            <p class="crm-card-subtitle">
                Análisis de ventas, productos y clientes
            </p>
        </div>
        <div class="crm-card-actions">
            <select id="periodFilter" class="form-select" style="width: auto;">
                <option value="7days" <?= $period === '7days' ? 'selected' : '' ?>>Últimos 7 días</option>
                <option value="30days" <?= $period === '30days' ? 'selected' : '' ?>>Últimos 30 días</option>
                <option value="90days" <?= $period === '90days' ? 'selected' : '' ?>>Últimos 90 días</option>
                <option value="12months" <?= $period === '12months' ? 'selected' : '' ?>>Últimos 12 meses</option>
                <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Este año</option>
                <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Todo el tiempo</option>
            </select>
            <button class="btn btn-secondary" onclick="exportReport()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ingresos Totales</div>
                <div class="stat-value">€<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                    <?= number_format($stats['total_orders'] ?? 0) ?> pedidos
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ticket Medio</div>
                <div class="stat-value">€<?= number_format($stats['avg_order_value'] ?? 0, 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                    por pedido
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Clientes Únicos</div>
                <div class="stat-value"><?= number_format($stats['unique_customers'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                    compradores
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Pendientes</div>
                <div class="stat-value"><?= number_format($stats['pending_orders'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                    sin pagar
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Revenue Trend Chart -->
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-chart-area"></i>
                    Tendencia de Ingresos
                </h3>
            </div>
            <div class="crm-card-body">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-pie-chart"></i>
                    Pedidos por Estado
                </h3>
            </div>
            <div class="crm-card-body">
                <canvas id="statusChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Payment Methods -->
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-credit-card"></i>
                    Métodos de Pago
                </h3>
            </div>
            <div class="crm-card-body">
                <?php if (!empty($paymentMethods)): ?>
                    <div style="display: grid; gap: 0.75rem;">
                        <?php foreach ($paymentMethods as $method): ?>
                            <?php
                            $methodNames = [
                                'credit_card' => 'Tarjeta de Crédito',
                                'crypto' => 'Criptomonedas',
                                'bank_transfer' => 'Transferencia',
                                'paypal' => 'PayPal'
                            ];
                            $methodIcons = [
                                'credit_card' => 'fa-credit-card',
                                'crypto' => 'fa-bitcoin',
                                'bank_transfer' => 'fa-university',
                                'paypal' => 'fa-paypal'
                            ];
                            $name = $methodNames[$method['payment_method']] ?? $method['payment_method'];
                            $icon = $methodIcons[$method['payment_method']] ?? 'fa-wallet';
                            $percentage = ($method['count'] / array_sum(array_column($paymentMethods, 'count'))) * 100;
                            ?>
                            <div style="padding: 0.75rem; background: var(--gray-50); border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas <?= $icon ?>" style="color: var(--primary);"></i>
                                        <span style="font-weight: 600;"><?= htmlspecialchars($name) ?></span>
                                    </div>
                                    <span style="font-weight: 600; color: var(--primary);">€<?= number_format($method['revenue'], 2) ?></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                                    <div style="flex: 1; background: var(--gray-200); height: 6px; border-radius: 3px; overflow: hidden;">
                                        <div style="width: <?= $percentage ?>%; height: 100%; background: var(--primary);"></div>
                                    </div>
                                    <span><?= $method['count'] ?> (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-credit-card"></i>
                        <p class="empty-state-text">No hay datos de pagos</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer Segments -->
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-users"></i>
                    Segmentos de Clientes
                </h3>
            </div>
            <div class="crm-card-body">
                <?php if (!empty($customerSegments)): ?>
                    <div style="display: grid; gap: 0.75rem;">
                        <?php
                        $totalCustomers = array_sum(array_column($customerSegments, 'customer_count'));
                        foreach ($customerSegments as $segment):
                            $percentage = ($segment['customer_count'] / $totalCustomers) * 100;
                            $segmentColors = [
                                'Sin Pedidos' => '#6b7280',
                                'Nuevo (1 pedido)' => '#3b82f6',
                                'Regular (2-5 pedidos)' => '#8b5cf6',
                                'VIP (6+ pedidos)' => '#f59e0b'
                            ];
                            $color = $segmentColors[$segment['segment']] ?? 'var(--primary)';
                        ?>
                            <div style="padding: 0.75rem; background: var(--gray-50); border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 12px; height: 12px; border-radius: 50%; background: <?= $color ?>;"></div>
                                        <span style="font-weight: 600;"><?= htmlspecialchars($segment['segment']) ?></span>
                                    </div>
                                    <span style="font-weight: 600; color: var(--primary);">€<?= number_format($segment['total_revenue'] ?? 0, 2) ?></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                                    <div style="flex: 1; background: var(--gray-200); height: 6px; border-radius: 3px; overflow: hidden;">
                                        <div style="width: <?= $percentage ?>%; height: 100%; background: <?= $color ?>;"></div>
                                    </div>
                                    <span><?= $segment['customer_count'] ?> (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                    Promedio: €<?= number_format($segment['avg_spent'] ?? 0, 2) ?> / cliente
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p class="empty-state-text">No hay datos de clientes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="crm-card">
        <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                <i class="fas fa-trophy"></i>
                Top Productos Más Vendidos
            </h3>
        </div>
        <div class="crm-card-body">
            <?php if (!empty($topProducts)): ?>
                <div class="crm-table-container">
                    <table class="crm-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Unidades</th>
                                <th>Pedidos</th>
                                <th>Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $index => $product): ?>
                                <tr>
                                    <td>
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white;">
                                            <?= $index + 1 ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <?php if ($product['image_url']): ?>
                                                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: var(--gray-200); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image" style="color: var(--gray-400);"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($product['name']) ?></div>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    SKU: <?= htmlspecialchars($product['sku']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: var(--primary);">
                                            <?= number_format($product['total_sold']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($product['order_count']) ?></td>
                                    <td>
                                        <span style="font-weight: 600; color: var(--success);">
                                            €<?= number_format($product['total_revenue'], 2) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <p class="empty-state-title">No hay datos de ventas</p>
                    <p class="empty-state-text">Los productos más vendidos aparecerán aquí</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Period filter
document.getElementById('periodFilter')?.addEventListener('change', function() {
    window.location.href = '?period=' + this.value;
});

// Revenue Trend Chart
<?php if (!empty($revenueTrend)): ?>
const revenueData = <?= json_encode($revenueTrend) ?>;
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
            }),
            datasets: [{
                label: 'Ingresos (€)',
                data: revenueData.map(d => parseFloat(d.revenue)),
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '€' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
<?php endif; ?>

// Orders by Status Chart
<?php if (!empty($ordersByStatus)): ?>
const statusData = <?= json_encode($ordersByStatus) ?>;
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    const statusLabels = {
        'pending_payment': 'Pago Pendiente',
        'processing': 'Procesando',
        'shipped': 'Enviado',
        'delivered': 'Entregado',
        'cancelled': 'Cancelado'
    };
    const statusColors = {
        'pending_payment': '#f59e0b',
        'processing': '#3b82f6',
        'shipped': '#8b5cf6',
        'delivered': '#10b981',
        'cancelled': '#ef4444'
    };

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(d => statusLabels[d.order_status] || d.order_status),
            datasets: [{
                data: statusData.map(d => d.count),
                backgroundColor: statusData.map(d => statusColors[d.order_status] || '#6b7280')
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
<?php endif; ?>

function exportReport() {
    alert('Función de exportación de reportes - Por implementar');
}
</script>
