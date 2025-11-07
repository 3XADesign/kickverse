<?php
/**
 * Admin Dashboard - Vista principal del CRM
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'dashboard';
$page_title = 'Dashboard - Admin Kickverse';
$breadcrumbs = [];

// Load header
require_once __DIR__ . '/layout/header.php';
?>

<!-- Dashboard Content -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Dashboard CRM
        </h1>
        <p class="page-subtitle">Resumen completo de tu negocio - <?= date('d/m/Y H:i') ?></p>
    </div>
</div>

<!-- Stats Grid (4 Cards principales) -->
<div class="stats-grid">
    <!-- Total Clientes -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Clientes</span>
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_customers'] ?? 0) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            Base de clientes activa
        </div>
    </div>

    <!-- Total Pedidos -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Pedidos</span>
            <div class="stat-icon success">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
        <div class="stat-change">
            <i class="fas fa-chart-line"></i>
            Todos los tiempos
        </div>
    </div>

    <!-- Ingresos del Mes -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Ingresos del Mes</span>
            <div class="stat-icon warning">
                <i class="fas fa-euro-sign"></i>
            </div>
        </div>
        <div class="stat-value">€<?= number_format($stats['month_revenue'] ?? 0, 2) ?></div>
        <div class="stat-change">
            <i class="fas fa-calendar-alt"></i>
            <?= date('F Y') ?>
        </div>
    </div>

    <!-- Suscripciones Activas -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Suscripciones Activas</span>
            <div class="stat-icon danger">
                <i class="fas fa-crown"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($stats['active_subscriptions'] ?? 0) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-sync-alt"></i>
            Ingresos recurrentes
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 24px; margin-top: 30px;">
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shopping-bag"></i>
                Pedidos Recientes
            </h3>
            <a href="/admin/pedidos" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($recent_orders)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                            <tr>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">#</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Cliente</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Estado</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                                <td style="padding: 12px 16px; font-size: 14px; font-weight: 500;">#<?= $order['order_id'] ?></td>
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <?php
                                    $statusColors = [
                                        'pending_payment' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending_payment' => 'Pago pendiente',
                                        'processing' => 'Procesando',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    $statusClass = $statusColors[$order['order_status']] ?? 'secondary';
                                    $statusLabel = $statusLabels[$order['order_status']] ?? $order['order_status'];
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td style="padding: 12px 16px; text-align: right; font-size: 14px; font-weight: 500;">
                                    €<?= number_format($order['total_amount'], 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="empty-state-title">No hay pedidos recientes</div>
                    <div class="empty-state-description">Los pedidos aparecerán aquí</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle"></i>
                Productos con Stock Bajo
            </h3>
            <a href="/admin/alertas-stock" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($low_stock_products)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                            <tr>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Producto</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Talla</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($low_stock_products as $product): ?>
                            <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <?= htmlspecialchars($product['name']) ?>
                                    <?php if (!empty($product['team_name'])): ?>
                                        <br><small style="color: var(--admin-gray-500);"><?= htmlspecialchars($product['team_name']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <span class="badge badge-secondary"><?= htmlspecialchars($product['size']) ?></span>
                                </td>
                                <td style="padding: 12px 16px; text-align: right;">
                                    <span class="badge badge-<?= $product['stock_quantity'] == 0 ? 'danger' : 'warning' ?>" style="font-size: 14px;">
                                        <?= $product['stock_quantity'] ?> uds
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-state-title">Stock saludable</div>
                    <div class="empty-state-description">No hay productos con stock bajo</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- New Customers This Week -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-plus"></i>
                Nuevos Clientes (esta semana)
            </h3>
            <a href="/admin/clientes" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($new_customers_week)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                            <tr>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Cliente</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Tier</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($new_customers_week as $customer): ?>
                            <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <?= htmlspecialchars($customer['full_name']) ?>
                                    <br><small style="color: var(--admin-gray-500);"><?= htmlspecialchars($customer['email']) ?></small>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span class="badge badge-primary"><?= ucfirst($customer['loyalty_tier']) ?></span>
                                </td>
                                <td style="padding: 12px 16px; text-align: right; font-size: 13px; color: var(--admin-gray-600);">
                                    <?= isset($customer['created_at']) ? date('d/m/Y', strtotime($customer['created_at'])) : 'N/A' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="empty-state-title">No hay nuevos clientes</div>
                    <div class="empty-state-description">No se han registrado clientes esta semana</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-credit-card"></i>
                Pagos Pendientes
            </h3>
            <a href="/admin/pagos" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (!empty($pending_payments)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--admin-gray-50); border-bottom: 1px solid var(--admin-gray-200);">
                            <tr>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Pedido</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Cliente</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 600; color: var(--admin-gray-700);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_payments as $payment): ?>
                            <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                                <td style="padding: 12px 16px; font-size: 14px; font-weight: 500;">
                                    #<?= $payment['order_id'] ?>
                                    <br><small style="color: var(--admin-gray-500);"><?= date('d/m/Y', strtotime($payment['order_date'])) ?></small>
                                </td>
                                <td style="padding: 12px 16px; font-size: 14px;">
                                    <?= htmlspecialchars($payment['customer_name'] ?? 'N/A') ?>
                                </td>
                                <td style="padding: 12px 16px; text-align: right; font-size: 14px; font-weight: 500;">
                                    €<?= number_format($payment['total_amount'], 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-state-title">Sin pagos pendientes</div>
                    <div class="empty-state-description">Todos los pagos están al día</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Load footer
require_once __DIR__ . '/layout/footer.php';
?>
