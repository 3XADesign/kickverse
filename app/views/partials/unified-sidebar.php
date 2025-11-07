<?php
/**
 * UNIFIED SIDEBAR - Menú lateral unificado para toda la web
 *
 * En la HOME: Muestra opciones del header
 * En MI-CUENTA: Muestra opciones del header + opciones de cuenta
 */

// Detectar si estamos en la sección de cuenta
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$accountPrefixes = [
    '/mi-cuenta',
    '/mis-pedidos'
];
$isAccountPage = isset($_SESSION['user']) && array_reduce($accountPrefixes, function($carry, $prefix) use ($currentPath) {
    return $carry || strpos($currentPath, $prefix) === 0;
}, false);
$ordersActive = isActiveLink('/mi-cuenta/pedidos', $currentPath) || isActiveLink('/mis-pedidos', $currentPath);

// Función para verificar si un link está activo
function isActiveLink($path, $currentPath) {
    // Exact match
    if ($path === $currentPath) {
        return true;
    }
    // Prefix match for account sections
    if ($path !== '/' && strpos($currentPath, $path) === 0) {
        return true;
    }
    return false;
}
?>

<!-- Unified Sidebar -->
<aside class="unified-sidebar" id="unifiedSidebar">
    <!-- Header del Sidebar -->
    <div class="unified-sidebar-header">
        <a href="/" class="unified-sidebar-logo">
            <img src="/img/logo.png" alt="Kickverse Logo">
            <span class="unified-sidebar-logo-text">Kickverse</span>
        </a>

        <!-- Botón de Toggle (PC) -->
        <button class="unified-sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
            <i class="fas fa-chevron-left unified-sidebar-toggle-icon"></i>
        </button>

        <!-- Botón de cierre (móvil) -->
        <button class="unified-sidebar-close" id="sidebarCloseBtn" aria-label="Cerrar menú">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navegación -->
    <nav class="unified-sidebar-nav">
        <!-- Sección Header (siempre visible) -->
        <div class="unified-sidebar-section">
            <h3 class="unified-sidebar-section-title">Menú Principal</h3>
            <ul class="unified-sidebar-list">
                <li class="unified-sidebar-item">
                    <a href="/" class="unified-sidebar-link <?= isActiveLink('/', $currentPath) ? 'active' : '' ?>">
                        <i class="fas fa-home unified-sidebar-icon"></i>
                        <span class="unified-sidebar-text"><?= __('nav.home') ?></span>
                    </a>
                </li>
                <li class="unified-sidebar-item">
                    <a href="/mystery-box" class="unified-sidebar-link highlight <?= isActiveLink('/mystery-box', $currentPath) ? 'active' : '' ?>">
                        <i class="fas fa-gift unified-sidebar-icon"></i>
                        <span class="unified-sidebar-text"><?= __('nav.mystery_box') ?></span>
                    </a>
                </li>
                <li class="unified-sidebar-item">
                    <a href="/productos" class="unified-sidebar-link <?= isActiveLink('/productos', $currentPath) ? 'active' : '' ?>">
                        <i class="fas fa-tshirt unified-sidebar-icon"></i>
                        <span class="unified-sidebar-text"><?= __('nav.jerseys') ?></span>
                    </a>
                </li>
                <li class="unified-sidebar-item">
                    <a href="/ligas" class="unified-sidebar-link <?= isActiveLink('/ligas', $currentPath) ? 'active' : '' ?>">
                        <i class="fas fa-trophy unified-sidebar-icon"></i>
                        <span class="unified-sidebar-text"><?= __('nav.leagues') ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <?php if ($isAccountPage): ?>
            <!-- Sección Mi Cuenta (solo visible en páginas de cuenta) -->
            <div class="unified-sidebar-section">
                <h3 class="unified-sidebar-section-title">Mi Cuenta</h3>
                <ul class="unified-sidebar-list">
                    <li class="unified-sidebar-item">
                        <a href="/mi-cuenta" class="unified-sidebar-link <?= $currentPath === '/mi-cuenta' ? 'active' : '' ?>">
                            <i class="fas fa-home unified-sidebar-icon"></i>
                            <span class="unified-sidebar-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="unified-sidebar-item">
                        <a href="/mi-cuenta/perfil" class="unified-sidebar-link <?= isActiveLink('/mi-cuenta/perfil', $currentPath) ? 'active' : '' ?>">
                            <i class="fas fa-user unified-sidebar-icon"></i>
                            <span class="unified-sidebar-text">Mi Perfil</span>
                        </a>
                    </li>
                    <li class="unified-sidebar-item">
                        <a href="/mi-cuenta/pedidos" class="unified-sidebar-link <?= $ordersActive ? 'active' : '' ?>">
                            <i class="fas fa-shopping-bag unified-sidebar-icon"></i>
                            <span class="unified-sidebar-text">Mis Pedidos</span>
                        </a>
                    </li>
                    <li class="unified-sidebar-item">
                        <a href="/mi-cuenta/suscripciones" class="unified-sidebar-link <?= isActiveLink('/mi-cuenta/suscripciones', $currentPath) ? 'active' : '' ?>">
                            <i class="fas fa-crown unified-sidebar-icon"></i>
                            <span class="unified-sidebar-text">Mis Suscripciones</span>
                        </a>
                    </li>
                    <li class="unified-sidebar-item">
                        <a href="/mi-cuenta/direcciones" class="unified-sidebar-link <?= isActiveLink('/mi-cuenta/direcciones', $currentPath) ? 'active' : '' ?>">
                            <i class="fas fa-map-marker-alt unified-sidebar-icon"></i>
                            <span class="unified-sidebar-text">Mis Direcciones</span>
                        </a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <!-- Sección de Acceso (visible cuando NO está logueado o no está en cuenta) -->
            <?php if (!isset($_SESSION['user'])): ?>
                <div class="unified-sidebar-section">
                    <h3 class="unified-sidebar-section-title">Cuenta</h3>
                    <ul class="unified-sidebar-list">
                        <li class="unified-sidebar-item">
                            <a href="#" onclick="openLoginModal(); return false;" class="unified-sidebar-link">
                                <i class="fas fa-sign-in-alt unified-sidebar-icon"></i>
                                <span class="unified-sidebar-text"><?= __('nav.login') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="unified-sidebar-section">
                    <h3 class="unified-sidebar-section-title">Cuenta</h3>
                    <ul class="unified-sidebar-list">
                        <li class="unified-sidebar-item">
                            <a href="/mi-cuenta" class="unified-sidebar-link">
                                <i class="fas fa-user unified-sidebar-icon"></i>
                                <span class="unified-sidebar-text"><?= __('nav.account') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- Footer del Sidebar (Cerrar Sesión - solo si está logueado y en cuenta) -->
    <?php if ($isAccountPage && isset($_SESSION['user'])): ?>
        <div class="unified-sidebar-footer">
            <a href="/api/auth/logout" class="unified-sidebar-logout">
                <i class="fas fa-sign-out-alt unified-sidebar-logout-icon"></i>
                <span class="unified-sidebar-logout-text">Cerrar Sesión</span>
            </a>
        </div>
    <?php endif; ?>
</aside>

<!-- Overlay para móvil -->
<div class="unified-sidebar-overlay" id="unifiedSidebarOverlay"></div>
