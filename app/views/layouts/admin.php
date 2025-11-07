<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'CRM - Admin Kickverse' ?></title>
    <link rel="stylesheet" href="/css/admin/admin-crm.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="/img/logo.png" alt="Kickverse" class="logo-img">
                <span class="logo-text">KICKVERSE</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <a href="/admin/dashboard" class="nav-item <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <!-- Clientes -->
            <a href="/admin/clientes" class="nav-item <?= ($current_page ?? '') === 'clientes' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span class="nav-text">Clientes</span>
            </a>

            <!-- Productos -->
            <a href="/admin/productos" class="nav-item <?= ($current_page ?? '') === 'productos' ? 'active' : '' ?>">
                <i class="fas fa-box"></i>
                <span class="nav-text">Productos</span>
            </a>

            <!-- Pedidos -->
            <a href="/admin/pedidos" class="nav-item <?= ($current_page ?? '') === 'pedidos' ? 'active' : '' ?>">
                <i class="fas fa-shopping-bag"></i>
                <span class="nav-text">Pedidos</span>
            </a>

            <!-- Suscripciones -->
            <a href="/admin/suscripciones" class="nav-item <?= ($current_page ?? '') === 'suscripciones' ? 'active' : '' ?>">
                <i class="fas fa-crown"></i>
                <span class="nav-text">Suscripciones</span>
            </a>

            <!-- Mystery Boxes -->
            <a href="/admin/mystery-boxes" class="nav-item <?= ($current_page ?? '') === 'mystery-boxes' ? 'active' : '' ?>">
                <i class="fas fa-gift"></i>
                <span class="nav-text">Mystery Boxes</span>
            </a>

            <!-- Pagos -->
            <a href="/admin/pagos" class="nav-item <?= ($current_page ?? '') === 'pagos' ? 'active' : '' ?>">
                <i class="fas fa-credit-card"></i>
                <span class="nav-text">Pagos</span>
            </a>

            <!-- Ligas -->
            <a href="/admin/ligas" class="nav-item <?= ($current_page ?? '') === 'ligas' ? 'active' : '' ?>">
                <i class="fas fa-trophy"></i>
                <span class="nav-text">Ligas</span>
            </a>

            <!-- Equipos -->
            <a href="/admin/equipos" class="nav-item <?= ($current_page ?? '') === 'equipos' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i>
                <span class="nav-text">Equipos</span>
            </a>

            <!-- Cupones -->
            <a href="/admin/cupones" class="nav-item <?= ($current_page ?? '') === 'cupones' ? 'active' : '' ?>">
                <i class="fas fa-ticket-alt"></i>
                <span class="nav-text">Cupones</span>
            </a>

            <!-- Inventario -->
            <a href="/admin/inventario" class="nav-item <?= ($current_page ?? '') === 'inventario' ? 'active' : '' ?>">
                <i class="fas fa-warehouse"></i>
                <span class="nav-text">Inventario</span>
            </a>

            <!-- Analytics -->
            <a href="/admin/analytics" class="nav-item <?= ($current_page ?? '') === 'analytics' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Analytics</span>
            </a>

            <div class="nav-divider"></div>

            <!-- Configuración -->
            <a href="/admin/configuracion" class="nav-item <?= ($current_page ?? '') === 'configuracion' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i>
                <span class="nav-text">Configuración</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-user">
                <div class="user-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($admin_name ?? 'Admin') ?></div>
                    <div class="user-role">Administrador</div>
                </div>
            </div>
            <a href="/admin/logout" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <button class="topbar-menu-btn" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="topbar-title">
                <h1><?= $page_title ?? 'Dashboard' ?></h1>
            </div>

            <div class="topbar-actions">
                <a href="/" target="_blank" class="topbar-btn" title="Ver tienda">
                    <i class="fas fa-store"></i>
                </a>
                <button class="topbar-btn" id="notificationsBtn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <?php if (isset($unread_notifications) && $unread_notifications > 0): ?>
                        <span class="notification-badge"><?= $unread_notifications ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <!-- Page Content -->
        <div class="admin-content">
            <?php if (isset($content)): ?>
                <?= $content ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Container -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal-container" id="modalContainer"></div>

    <!-- Scripts -->
    <script src="/js/admin/admin-crm.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
