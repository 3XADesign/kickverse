<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard - Admin Kickverse' ?></title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: var(--gray-50);">
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <div class="admin-nav-brand">
                <img src="/img/logo.png" alt="Kickverse" style="height: 40px;">
                <span class="admin-badge">ADMIN</span>
            </div>

            <div class="admin-nav-user">
                <span class="admin-user-name">
                    <i class="fas fa-user-shield"></i>
                    <?= htmlspecialchars($admin_name) ?>
                </span>
                <a href="/admin/logout" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesion
                </a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">
                <i class="fas fa-chart-line"></i>
                Dashboard CRM
            </h1>
            <p class="admin-subtitle">Resumen completo de tu negocio - <?= date('d/m/Y H:i') ?></p>
        </div>

        <!-- Stats Grid (4 Cards principales) -->
        <div class="stats-grid">
            <!-- Total Clientes -->
            <a href="/admin/customers" class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Clientes</div>
                    <div class="stat-value"><?= number_format($stats['total_customers']) ?></div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        Base de clientes
                    </div>
                </div>
            </a>

            <!-- Total Pedidos -->
            <a href="/admin/orders" class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Pedidos</div>
                    <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
                    <div class="stat-trend">
                        <i class="fas fa-chart-line"></i>
                        Todos los tiempos
                    </div>
                </div>
            </a>

            <!-- Ingresos del Mes -->
            <a href="/admin/reports" class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Ingresos del Mes</div>
                    <div class="stat-value"><?= number_format($stats['month_revenue'], 2) ?></div>
                    <div class="stat-trend">
                        <i class="fas fa-calendar-alt"></i>
                        <?= date('F Y') ?>
                    </div>
                </div>
            </a>

            <!-- Suscripciones Activas -->
            <a href="/admin/subscriptions" class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Suscripciones Activas</div>
                    <div class="stat-value"><?= number_format($stats['active_subscriptions']) ?></div>
                    <div class="stat-trend">
                        <i class="fas fa-sync-alt"></i>
                        Ingresos recurrentes
                    </div>
                </div>
            </a>
        </div>

        <!-- Acciones Rapidas -->
        <div class="admin-card" style="margin-bottom: var(--space-8);">
            <div class="card-header">
                <h3>
                    <i class="fas fa-bolt"></i>
                    Acciones Rapidas
                </h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="/admin/orders/create" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Crear Pedido</span>
                    </a>
                    <a href="/admin/customers/create" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Anadir Cliente</span>
                    </a>
                    <a href="/admin/products" class="quick-action-btn">
                        <i class="fas fa-boxes"></i>
                        <span>Gestionar Stock</span>
                    </a>
                    <a href="/admin/reports" class="quick-action-btn">
                        <i class="fas fa-chart-bar"></i>
                        <span>Ver Reportes</span>
                    </a>
                    <a href="/" class="quick-action-btn" target="_blank">
                        <i class="fas fa-store"></i>
                        <span>Ver Tienda</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 1: Pedidos Recientes + Stock Bajo -->
        <div class="admin-grid">
            <!-- Pedidos Recientes -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-clock"></i>
                        Pedidos Recientes
                    </h3>
                    <a href="/admin/orders" class="card-link">Ver todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No hay pedidos recientes</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr onclick="window.location='/admin/orders/<?= $order['order_id'] ?>'" style="cursor: pointer;">
                                            <td><strong>#<?= $order['order_id'] ?></strong></td>
                                            <td>
                                                <div class="customer-cell">
                                                    <div><?= htmlspecialchars($order['customer_name'] ?? 'Invitado') ?></div>
                                                    <div class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? '-') ?></div>
                                                </div>
                                            </td>
                                            <td><strong><?= number_format($order['total_amount'], 2) ?></strong></td>
                                            <td>
                                                <span class="badge badge-<?= getOrderStatusColor($order['order_status']) ?>">
                                                    <?= getOrderStatusText($order['order_status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stock Bajo -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-exclamation-triangle"></i>
                        Productos con Stock Bajo
                    </h3>
                    <a href="/admin/products?filter=low_stock" class="card-link">Ver inventario</a>
                </div>
                <div class="card-body">
                    <?php if (empty($low_stock_products)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>Todo el stock esta bien</p>
                        </div>
                    <?php else: ?>
                        <div class="low-stock-list">
                            <?php foreach ($low_stock_products as $product): ?>
                                <a href="/admin/products/<?= $product['product_id'] ?>" class="low-stock-item">
                                    <div class="low-stock-info">
                                        <div class="low-stock-name"><?= htmlspecialchars($product['name']) ?></div>
                                        <div class="low-stock-team">
                                            <?= htmlspecialchars($product['team_name'] ?? 'Sin equipo') ?> - Talla <?= $product['size'] ?>
                                        </div>
                                    </div>
                                    <div class="low-stock-quantity">
                                        <span class="badge <?= $product['stock_quantity'] == 0 ? 'badge-danger' : 'badge-warning' ?>">
                                            <?= $product['stock_quantity'] ?> unidades
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Row 2: Nuevos Clientes + Pagos Pendientes -->
        <div class="admin-grid">
            <!-- Nuevos Clientes esta Semana -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-user-plus"></i>
                        Nuevos Clientes (Ultima Semana)
                    </h3>
                    <a href="/admin/customers?filter=new" class="card-link">Ver todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($new_customers_week)): ?>
                        <div class="empty-state">
                            <i class="fas fa-user-clock"></i>
                            <p>No hay clientes nuevos esta semana</p>
                        </div>
                    <?php else: ?>
                        <div class="customers-list">
                            <?php foreach ($new_customers_week as $customer): ?>
                                <a href="/admin/customers/<?= $customer['customer_id'] ?>" class="customer-item">
                                    <div class="customer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="customer-info">
                                        <div class="customer-name"><?= htmlspecialchars($customer['full_name']) ?></div>
                                        <div class="customer-meta">
                                            <?= htmlspecialchars($customer['email'] ?? $customer['phone'] ?? 'Sin contacto') ?>
                                        </div>
                                    </div>
                                    <div class="customer-tier">
                                        <span class="badge badge-info"><?= ucfirst($customer['loyalty_tier']) ?></span>
                                        <div class="text-muted-sm"><?= isset($customer['created_at']) ? date('d/m/Y', strtotime($customer['created_at'])) : 'N/A' ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagos Pendientes de Confirmacion -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-credit-card"></i>
                        Pagos Pendientes
                    </h3>
                    <a href="/admin/orders?filter=pending_payment" class="card-link">Ver todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($pending_payments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>No hay pagos pendientes</p>
                        </div>
                    <?php else: ?>
                        <div class="payments-list">
                            <?php foreach ($pending_payments as $payment): ?>
                                <a href="/admin/orders/<?= $payment['order_id'] ?>" class="payment-item">
                                    <div class="payment-info">
                                        <div class="payment-order">Pedido #<?= $payment['order_id'] ?></div>
                                        <div class="payment-customer"><?= htmlspecialchars($payment['customer_name'] ?? 'Invitado') ?></div>
                                        <div class="payment-method">
                                            <i class="fas fa-<?= getPaymentIcon($payment['payment_method']) ?>"></i>
                                            <?= ucfirst($payment['payment_method']) ?>
                                        </div>
                                    </div>
                                    <div class="payment-amount">
                                        <div class="payment-total"><?= number_format($payment['total_amount'], 2) ?></div>
                                        <div class="text-muted-sm"><?= date('d/m/Y', strtotime($payment['order_date'])) ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Row 3: Top Productos + Suscripciones por Vencer -->
        <div class="admin-grid">
            <!-- Top 5 Productos Mas Vendidos -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-trophy"></i>
                        Top Productos Mas Vendidos
                    </h3>
                    <a href="/admin/reports/products" class="card-link">Ver reporte completo</a>
                </div>
                <div class="card-body">
                    <?php if (empty($top_products)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>No hay datos de ventas</p>
                        </div>
                    <?php else: ?>
                        <div class="top-products-list">
                            <?php foreach ($top_products as $index => $product): ?>
                                <a href="/admin/products/<?= $product['product_id'] ?>" class="top-product-item">
                                    <div class="product-rank">#<?= $index + 1 ?></div>
                                    <div class="product-info-top">
                                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                        <div class="product-team"><?= htmlspecialchars($product['team_name'] ?? 'Sin equipo') ?></div>
                                    </div>
                                    <div class="product-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-shopping-cart"></i>
                                            <?= number_format($product['units_sold']) ?> vendidas
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-euro-sign"></i>
                                            <?= number_format($product['revenue'], 2) ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Suscripciones que Vencen Proximamente -->
            <div class="admin-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-calendar-times"></i>
                        Suscripciones Proximas a Vencer
                    </h3>
                    <a href="/admin/subscriptions?filter=expiring" class="card-link">Ver todas</a>
                </div>
                <div class="card-body">
                    <?php if (empty($expiring_subscriptions)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p>No hay suscripciones proximas a vencer</p>
                        </div>
                    <?php else: ?>
                        <div class="subscriptions-list">
                            <?php foreach ($expiring_subscriptions as $sub): ?>
                                <a href="/admin/subscriptions/<?= $sub['subscription_id'] ?>" class="subscription-item">
                                    <div class="subscription-info">
                                        <div class="subscription-customer"><?= htmlspecialchars($sub['customer_name']) ?></div>
                                        <div class="subscription-plan"><?= htmlspecialchars($sub['plan_name']) ?></div>
                                        <div class="subscription-price"><?= number_format($sub['monthly_price'], 2) ?>/mes</div>
                                    </div>
                                    <div class="subscription-expiry">
                                        <div class="expiry-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y', strtotime($sub['next_billing_date'])) ?>
                                        </div>
                                        <div class="expiry-days <?= getDaysUntilClass($sub['next_billing_date']) ?>">
                                            <?= getDaysUntilText($sub['next_billing_date']) ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="admin-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-stream"></i>
                    Actividad Reciente del Sistema
                </h3>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="empty-state">
                        <i class="fas fa-bell"></i>
                        <p>No hay actividades recientes</p>
                    </div>
                <?php else: ?>
                    <div class="activity-feed">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?= getActivityIconClass($activity['type']) ?>">
                                    <i class="fas fa-<?= getActivityIcon($activity['type']) ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-description"><?= htmlspecialchars($activity['description']) ?></div>
                                    <div class="activity-time">
                                        <i class="fas fa-clock"></i>
                                        <?= isset($activity['created_at']) ? getTimeAgo($activity['created_at']) : 'N/A' ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary: #6366f1;
            --accent: #ec4899;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-full: 9999px;
            --transition: all 0.2s ease;
        }

        .admin-nav {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: var(--space-4) 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .admin-nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--space-6);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-nav-brand {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            padding: var(--space-1) var(--space-3);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .admin-nav-user {
            display: flex;
            align-items: center;
            gap: var(--space-4);
        }

        .admin-user-name {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            color: var(--gray-700);
            font-weight: 500;
        }

        .btn {
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
        }

        .btn-sm {
            padding: var(--space-2) var(--space-4);
            font-size: 0.875rem;
        }

        .btn-outline-danger {
            background: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--space-8) var(--space-6);
        }

        .admin-header {
            margin-bottom: var(--space-8);
        }

        .admin-title {
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-2);
        }

        .admin-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            display: flex;
            gap: var(--space-4);
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: var(--space-1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: var(--space-2);
        }

        .stat-trend {
            color: var(--gray-600);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .admin-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: var(--space-6);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: var(--space-2);
            margin: 0;
        }

        .card-link {
            color: var(--primary);
            font-size: 0.875rem;
            text-decoration: none;
        }

        .card-link:hover {
            text-decoration: underline;
        }

        .card-body {
            padding: var(--space-6);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--space-4);
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-6);
            background: var(--gray-50);
            border-radius: var(--radius-xl);
            text-decoration: none;
            color: var(--gray-700);
            transition: var(--transition);
            font-weight: 500;
        }

        .quick-action-btn:hover {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .quick-action-btn i {
            font-size: 1.75rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            text-align: left;
            padding: var(--space-3);
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
        }

        .admin-table td {
            padding: var(--space-3);
            border-bottom: 1px solid var(--gray-100);
        }

        .admin-table tr:hover {
            background: var(--gray-50);
        }

        .customer-cell {
            display: flex;
            flex-direction: column;
        }

        .text-muted {
            color: var(--gray-600);
            font-size: 0.75rem;
        }

        .text-muted-sm {
            color: var(--gray-600);
            font-size: 0.7rem;
        }

        .badge {
            display: inline-block;
            padding: var(--space-1) var(--space-3);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #e7e8ff; color: #4338ca; }

        .empty-state {
            text-align: center;
            padding: var(--space-8);
            color: var(--gray-600);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: var(--space-3);
            opacity: 0.5;
        }

        .low-stock-list,
        .customers-list,
        .payments-list,
        .subscriptions-list,
        .top-products-list {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }

        .low-stock-item,
        .customer-item,
        .payment-item,
        .subscription-item,
        .top-product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-4);
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .low-stock-item:hover,
        .customer-item:hover,
        .payment-item:hover,
        .subscription-item:hover,
        .top-product-item:hover {
            background: var(--gray-100);
            transform: translateX(4px);
        }

        .low-stock-name,
        .customer-name,
        .payment-order,
        .product-name,
        .subscription-customer {
            font-weight: 600;
            color: var(--gray-900);
        }

        .low-stock-team,
        .customer-meta,
        .payment-customer,
        .product-team,
        .subscription-plan {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: var(--space-1);
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .customer-info {
            flex: 1;
            margin-left: var(--space-3);
        }

        .payment-method {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: var(--space-1);
        }

        .payment-total {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--gray-900);
        }

        .product-rank {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .product-info-top {
            flex: 1;
            margin-left: var(--space-3);
        }

        .product-stats {
            display: flex;
            flex-direction: column;
            gap: var(--space-1);
            align-items: flex-end;
        }

        .stat-item {
            font-size: 0.75rem;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        .subscription-price {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: var(--space-1);
        }

        .expiry-date {
            font-size: 0.875rem;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        .expiry-days {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: var(--space-1);
        }

        .expiry-urgent { color: #dc2626; }
        .expiry-soon { color: #f59e0b; }
        .expiry-normal { color: var(--gray-600); }

        .activity-feed {
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }

        .activity-item {
            display: flex;
            gap: var(--space-3);
            align-items: flex-start;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .activity-icon.icon-order { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .activity-icon.icon-customer { background: linear-gradient(135deg, #667eea, #764ba2); }
        .activity-icon.icon-subscription { background: linear-gradient(135deg, #fa709a, #fee140); }

        .activity-content {
            flex: 1;
        }

        .activity-description {
            color: var(--gray-900);
            font-size: 0.875rem;
            margin-bottom: var(--space-1);
        }

        .activity-time {
            color: var(--gray-600);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .admin-nav-user {
                flex-direction: column;
                gap: var(--space-2);
            }
        }
    </style>
</body>
</html>

<?php
// Helper functions
function getOrderStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'danger'
    ];
    return $colors[$status] ?? 'info';
}

function getOrderStatusText($status) {
    $texts = [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'refunded' => 'Reembolsado'
    ];
    return $texts[$status] ?? ucfirst($status);
}

function getPaymentIcon($method) {
    $icons = [
        'card' => 'credit-card',
        'paypal' => 'paypal',
        'bizum' => 'mobile-alt',
        'transfer' => 'university',
        'cash' => 'money-bill'
    ];
    return $icons[$method] ?? 'credit-card';
}

function getDaysUntilClass($date) {
    $days = (strtotime($date) - time()) / 86400;
    if ($days <= 7) return 'expiry-urgent';
    if ($days <= 14) return 'expiry-soon';
    return 'expiry-normal';
}

function getDaysUntilText($date) {
    $days = floor((strtotime($date) - time()) / 86400);
    if ($days == 0) return 'Hoy';
    if ($days == 1) return 'Manana';
    if ($days < 0) return 'Vencida';
    return "En {$days} dias";
}

function getActivityIcon($type) {
    $icons = [
        'order' => 'shopping-bag',
        'customer' => 'user-plus',
        'subscription' => 'crown'
    ];
    return $icons[$type] ?? 'info-circle';
}

function getActivityIconClass($type) {
    return 'icon-' . $type;
}

function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Hace un momento';
    if ($diff < 3600) return 'Hace ' . floor($diff / 60) . ' minutos';
    if ($diff < 86400) return 'Hace ' . floor($diff / 3600) . ' horas';
    if ($diff < 604800) return 'Hace ' . floor($diff / 86400) . ' dias';
    return date('d/m/Y H:i', $time);
}
?>
