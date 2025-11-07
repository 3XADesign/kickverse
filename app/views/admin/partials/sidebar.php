<!-- Admin Sidebar -->
<aside id="adminSidebar" class="admin-sidebar">
    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuToggle" class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-header">
        <img src="/img/logo.png" alt="Kickverse" class="sidebar-logo">
        <span class="sidebar-brand">Kickverse</span>
        <span class="admin-badge-sidebar">ADMIN</span>
    </div>

    <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="sidebar-nav">
        <a href="/admin/dashboard" class="nav-item sidebar-link <?= $active_page === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="/admin/clientes" class="nav-item sidebar-link <?= $active_page === 'clientes' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span class="sidebar-text">Clientes</span>
        </a>

        <a href="/admin/pedidos" class="nav-item sidebar-link <?= $active_page === 'pedidos' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i>
            <span class="sidebar-text">Pedidos</span>
        </a>

        <a href="/admin/productos" class="nav-item sidebar-link <?= $active_page === 'productos' ? 'active' : '' ?>">
            <i class="fas fa-tshirt"></i>
            <span class="sidebar-text">Productos</span>
        </a>

        <a href="/admin/ligas" class="nav-item sidebar-link <?= $active_page === 'ligas' ? 'active' : '' ?>">
            <i class="fas fa-trophy"></i>
            <span class="sidebar-text">Ligas</span>
        </a>

        <a href="/admin/equipos" class="nav-item sidebar-link <?= $active_page === 'equipos' ? 'active' : '' ?>">
            <i class="fas fa-shield-alt"></i>
            <span class="sidebar-text">Equipos</span>
        </a>

        <a href="/admin/suscripciones" class="nav-item sidebar-link <?= $active_page === 'suscripciones' ? 'active' : '' ?>">
            <i class="fas fa-crown"></i>
            <span class="sidebar-text">Suscripciones</span>
        </a>

        <a href="/admin/pagos" class="nav-item sidebar-link <?= $active_page === 'pagos' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i>
            <span class="sidebar-text">Pagos</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="/admin/mystery-boxes" class="nav-item sidebar-link <?= $active_page === 'mystery-boxes' ? 'active' : '' ?>">
            <i class="fas fa-box-open"></i>
            <span class="sidebar-text">Mystery Boxes</span>
        </a>

        <a href="/admin/cupones" class="nav-item sidebar-link <?= $active_page === 'cupones' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i>
            <span class="sidebar-text">Cupones</span>
        </a>

        <a href="/admin/inventario" class="nav-item sidebar-link <?= $active_page === 'inventario' ? 'active' : '' ?>">
            <i class="fas fa-warehouse"></i>
            <span class="sidebar-text">Inventario</span>
        </a>

        <a href="/admin/analytics" class="nav-item sidebar-link <?= $active_page === 'analytics' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span class="sidebar-text">Analytics</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="/admin/configuracion" class="nav-item sidebar-link <?= $active_page === 'configuracion' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span class="sidebar-text">Configuración</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <i class="fas fa-user-shield"></i>
            <span class="sidebar-text"><?= htmlspecialchars($admin_name ?? 'Admin') ?></span>
        </div>
        <a href="/admin/logout" class="sidebar-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span class="sidebar-text">Cerrar Sesión</span>
        </a>
    </div>
</aside>
