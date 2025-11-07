<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Kickverse Admin' ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="/css/admin/admin.css">
    <link rel="stylesheet" href="/css/admin/components.css">
    <link rel="stylesheet" href="/css/admin/tables.css">
    <link rel="stylesheet" href="/css/admin/modals.css">

    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="/img/logo.png" alt="Kickverse" class="logo-full">
                <img src="/img/logo-icon.png" alt="K" class="logo-icon">
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <a href="/admin" class="nav-item <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Resumen general</span>
                </a>
            </div>

            <!-- Ventas -->
            <div class="nav-section">
                <div class="nav-section-title">Ventas</div>
                <a href="/admin/pedidos" class="nav-item <?= ($current_page ?? '') === 'pedidos' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="nav-text">Pedidos</span>
                </a>
                <a href="/admin/pedidos-mystery-box" class="nav-item <?= ($current_page ?? '') === 'pedidos-mystery-box' ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i>
                    <span class="nav-text">Pedidos Mystery Box</span>
                </a>
                <a href="/admin/pagos" class="nav-item <?= ($current_page ?? '') === 'pagos' ? 'active' : '' ?>">
                    <i class="fas fa-credit-card"></i>
                    <span class="nav-text">Pagos</span>
                </a>
                <a href="/admin/cupones" class="nav-item <?= ($current_page ?? '') === 'cupones' ? 'active' : '' ?>">
                    <i class="fas fa-tag"></i>
                    <span class="nav-text">Cupones</span>
                </a>
                <a href="/admin/campanas" class="nav-item <?= ($current_page ?? '') === 'campanas' ? 'active' : '' ?>">
                    <i class="fas fa-bullhorn"></i>
                    <span class="nav-text">Campañas</span>
                </a>
            </div>

            <!-- Clientes -->
            <div class="nav-section">
                <div class="nav-section-title">Clientes</div>
                <a href="/admin/clientes" class="nav-item <?= ($current_page ?? '') === 'clientes' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Clientes</span>
                </a>
                <a href="/admin/mensajes-clientes" class="nav-item <?= ($current_page ?? '') === 'mensajes-clientes' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i>
                    <span class="nav-text">Mensajes</span>
                </a>
                <a href="/admin/suscripciones" class="nav-item <?= ($current_page ?? '') === 'suscripciones' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Suscripciones</span>
                </a>
                <a href="/admin/club-fidelidad" class="nav-item <?= ($current_page ?? '') === 'club-fidelidad' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i>
                    <span class="nav-text">Club de Fidelidad</span>
                </a>
                <a href="/admin/listas-deseos" class="nav-item <?= ($current_page ?? '') === 'listas-deseos' ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i>
                    <span class="nav-text">Listas de deseos</span>
                </a>
            </div>

            <!-- Catálogo -->
            <div class="nav-section">
                <div class="nav-section-title">Catálogo</div>
                <a href="/admin/productos" class="nav-item <?= ($current_page ?? '') === 'productos' ? 'active' : '' ?>">
                    <i class="fas fa-tshirt"></i>
                    <span class="nav-text">Productos</span>
                </a>
                <a href="/admin/variantes-producto" class="nav-item <?= ($current_page ?? '') === 'variantes-producto' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i>
                    <span class="nav-text">Variantes</span>
                </a>
                <a href="/admin/imagenes-producto" class="nav-item <?= ($current_page ?? '') === 'imagenes-producto' ? 'active' : '' ?>">
                    <i class="fas fa-images"></i>
                    <span class="nav-text">Imágenes</span>
                </a>
            </div>

            <!-- Fútbol -->
            <div class="nav-section">
                <div class="nav-section-title">Fútbol</div>
                <a href="/admin/ligas" class="nav-item <?= ($current_page ?? '') === 'ligas' ? 'active' : '' ?>">
                    <i class="fas fa-trophy"></i>
                    <span class="nav-text">Ligas</span>
                </a>
                <a href="/admin/equipos" class="nav-item <?= ($current_page ?? '') === 'equipos' ? 'active' : '' ?>">
                    <i class="fas fa-shield-alt"></i>
                    <span class="nav-text">Equipos</span>
                </a>
            </div>

            <!-- Stock -->
            <div class="nav-section">
                <div class="nav-section-title">Stock y Logística</div>
                <a href="/admin/movimientos-stock" class="nav-item <?= ($current_page ?? '') === 'movimientos-stock' ? 'active' : '' ?>">
                    <i class="fas fa-exchange-alt"></i>
                    <span class="nav-text">Movimientos</span>
                </a>
                <a href="/admin/alertas-stock" class="nav-item <?= ($current_page ?? '') === 'alertas-stock' ? 'active' : '' ?>">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span class="nav-text">Alertas de stock</span>
                </a>
            </div>

            <!-- Sistema -->
            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                <a href="/admin/usuarios-admin" class="nav-item <?= ($current_page ?? '') === 'usuarios-admin' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Usuarios admin</span>
                </a>
                <a href="/admin/log-auditoria" class="nav-item <?= ($current_page ?? '') === 'log-auditoria' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-text">Log de auditoría</span>
                </a>
                <a href="/admin/ajustes-sistema" class="nav-item <?= ($current_page ?? '') === 'ajustes-sistema' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Ajustes</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="admin-main" id="adminMain">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left">
                <!-- Breadcrumbs -->
                <nav class="breadcrumbs">
                    <a href="/admin" class="breadcrumb-item">Admin</a>
                    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $breadcrumb): ?>
                            <?php if (isset($breadcrumb['url'])): ?>
                                <i class="fas fa-chevron-right breadcrumb-separator"></i>
                                <a href="<?= $breadcrumb['url'] ?>" class="breadcrumb-item"><?= $breadcrumb['label'] ?></a>
                            <?php else: ?>
                                <i class="fas fa-chevron-right breadcrumb-separator"></i>
                                <span class="breadcrumb-item active"><?= $breadcrumb['label'] ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="header-right">
                <!-- Global Search -->
                <div class="global-search">
                    <input type="text"
                           id="globalSearch"
                           placeholder="Buscar clientes, pedidos, productos..."
                           class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <!-- Notifications -->
                <div class="header-notifications">
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notificaciones</h4>
                            <a href="#" class="mark-all-read">Marcar todas como leídas</a>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                        <div class="notification-footer">
                            <a href="/admin/notificaciones">Ver todas</a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="user-menu">
                    <button class="user-menu-btn" id="userMenuBtn">
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="user-name"><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <div class="user-menu-header">
                            <div class="user-avatar-large">
                                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?= $_SESSION['admin_name'] ?? 'Admin' ?></div>
                                <div class="user-email"><?= $_SESSION['admin_email'] ?? '' ?></div>
                                <div class="user-role"><?= ucfirst($_SESSION['admin_role'] ?? 'admin') ?></div>
                            </div>
                        </div>
                        <div class="user-menu-items">
                            <a href="/admin/perfil" class="user-menu-item">
                                <i class="fas fa-user"></i>
                                Mi perfil
                            </a>
                            <a href="/admin/ajustes" class="user-menu-item">
                                <i class="fas fa-cog"></i>
                                Ajustes
                            </a>
                            <div class="user-menu-divider"></div>
                            <a href="/admin/logout" class="user-menu-item text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="admin-content">
