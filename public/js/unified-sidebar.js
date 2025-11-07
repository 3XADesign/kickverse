/**
 * UNIFIED SIDEBAR - JavaScript
 * Menú lateral unificado para Kickverse
 *
 * Características:
 * - Expandible/colapsable en PC con botón
 * - Abre/cierra con botón flotante en móvil
 * - Persistencia del estado en localStorage
 * - Iconos centrados cuando está colapsado
 * - Animaciones suaves
 */

(function() {
    'use strict';

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUnifiedSidebar);
    } else {
        initUnifiedSidebar();
    }

    function initUnifiedSidebar() {
        console.log('Initializing Unified Sidebar...');

        // Elementos del DOM
        const sidebar = document.getElementById('unifiedSidebar');
        const overlay = document.getElementById('unifiedSidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const floatingMenuBtn = document.getElementById('floating-menu-btn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const body = document.body;

        // Verificar que existan los elementos necesarios
        if (!sidebar) {
            console.warn('Unified sidebar not found in DOM');
            return;
        }

        // Añadir clase al body para indicar que tiene sidebar
        body.classList.add('has-unified-sidebar');

        // =========================================
        // FUNCIONES AUXILIARES
        // =========================================

        /**
         * Verifica si estamos en modo desktop
         */
        function isDesktop() {
            return window.innerWidth > 1024;
        }

        /**
         * Actualiza el icono del botón de toggle
         */
        function updateToggleIcon() {
            if (!toggleBtn) return;

            const icon = toggleBtn.querySelector('.unified-sidebar-toggle-icon');
            if (!icon) return;

            if (body.classList.contains('sidebar-collapsed')) {
                icon.className = 'fas fa-chevron-right unified-sidebar-toggle-icon';
            } else {
                icon.className = 'fas fa-chevron-left unified-sidebar-toggle-icon';
            }
        }

        /**
         * Colapsar sidebar (solo PC)
         */
        function collapseSidebar() {
            if (!isDesktop()) return;

            body.classList.add('sidebar-collapsed');
            localStorage.setItem('unified-sidebar-collapsed', 'true');
            updateToggleIcon();

            console.log('Sidebar collapsed');
        }

        /**
         * Expandir sidebar (solo PC)
         */
        function expandSidebar() {
            if (!isDesktop()) return;

            body.classList.remove('sidebar-collapsed');
            localStorage.setItem('unified-sidebar-collapsed', 'false');
            updateToggleIcon();

            console.log('Sidebar expanded');
        }

        /**
         * Toggle sidebar (PC: colapsar/expandir, Móvil: abrir/cerrar)
         */
        function toggleSidebar() {
            if (isDesktop()) {
                // PC: Toggle collapse
                if (body.classList.contains('sidebar-collapsed')) {
                    expandSidebar();
                } else {
                    collapseSidebar();
                }
            } else {
                // Móvil: Toggle open
                if (body.classList.contains('sidebar-open')) {
                    closeSidebarMobile();
                } else {
                    openSidebarMobile();
                }
            }
        }

        /**
         * Abrir sidebar en móvil
         */
        function openSidebarMobile() {
            body.classList.add('sidebar-open');
            body.style.overflow = 'hidden';

            // También cerrar el menú móvil del header si está abierto
            const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const floatingBtn = document.getElementById('floating-menu-btn');

            if (mobileMenuSidebar) {
                mobileMenuSidebar.classList.remove('active');
            }
            if (mobileMenuOverlay) {
                mobileMenuOverlay.classList.remove('active');
            }
            if (floatingBtn) {
                floatingBtn.classList.add('active');
            }

            console.log('Sidebar opened (mobile)');
        }

        /**
         * Cerrar sidebar en móvil
         */
        function closeSidebarMobile() {
            body.classList.remove('sidebar-open');
            body.style.overflow = '';

            if (floatingMenuBtn) {
                floatingMenuBtn.classList.remove('active');
            }

            console.log('Sidebar closed (mobile)');
        }

        // =========================================
        // INICIALIZACIÓN
        // =========================================

        // Restaurar estado colapsado desde localStorage (solo desktop)
        if (isDesktop()) {
            const isCollapsed = localStorage.getItem('unified-sidebar-collapsed') === 'true';
            if (isCollapsed) {
                body.classList.add('sidebar-collapsed');
            }
            updateToggleIcon();
        }

        // =========================================
        // EVENT LISTENERS
        // =========================================

        // Botón de toggle en el sidebar (PC)
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
        }

        // Botón de cierre (móvil)
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (!isDesktop()) {
                    closeSidebarMobile();
                }
            });
        }

        // Botón flotante (móvil) - reutilizar el del header
        if (floatingMenuBtn) {
            // Modificar el comportamiento del botón flotante
            floatingMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Si el unified sidebar está presente, abrirlo en lugar del menú móvil del header
                if (sidebar && !isDesktop()) {
                    // Cerrar menú móvil del header si está abierto
                    const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
                    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

                    if (mobileMenuSidebar && mobileMenuSidebar.classList.contains('active')) {
                        mobileMenuSidebar.classList.remove('active');
                        if (mobileMenuOverlay) {
                            mobileMenuOverlay.classList.remove('active');
                        }
                        floatingMenuBtn.classList.remove('active');
                        body.style.overflow = '';
                    } else {
                        // Abrir unified sidebar
                        toggleSidebar();

                        // Actualizar estado visual del botón flotante
                        if (body.classList.contains('sidebar-open')) {
                            floatingMenuBtn.classList.add('active');
                        } else {
                            floatingMenuBtn.classList.remove('active');
                        }
                    }
                }
            });
        }

        // Overlay (móvil) - cerrar al hacer click
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (!isDesktop() && body.classList.contains('sidebar-open')) {
                    closeSidebarMobile();
                }
            });
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!isDesktop() && body.classList.contains('sidebar-open')) {
                    closeSidebarMobile();
                }
            }
        });

        // Prevenir cierre al hacer click dentro del sidebar
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // =========================================
        // RESPONSIVE - WINDOW RESIZE
        // =========================================

        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (isDesktop()) {
                    // Desktop mode
                    body.classList.remove('sidebar-open');
                    body.style.overflow = '';

                    // Restaurar estado colapsado desde localStorage
                    const isCollapsed = localStorage.getItem('unified-sidebar-collapsed') === 'true';
                    if (isCollapsed) {
                        body.classList.add('sidebar-collapsed');
                    } else {
                        body.classList.remove('sidebar-collapsed');
                    }

                    updateToggleIcon();

                    // Resetear botón flotante
                    if (floatingMenuBtn) {
                        floatingMenuBtn.classList.remove('active');
                    }
                } else {
                    // Mobile mode
                    body.classList.remove('sidebar-collapsed');
                    updateToggleIcon();
                }
            }, 100);
        });

        // =========================================
        // CERRAR SIDEBAR AL NAVEGAR (MÓVIL)
        // =========================================

        // Cerrar sidebar cuando se hace click en un link (solo móvil)
        const sidebarLinks = sidebar.querySelectorAll('.unified-sidebar-link');
        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (!isDesktop()) {
                    // Pequeño delay para permitir que la navegación comience
                    setTimeout(function() {
                        closeSidebarMobile();
                    }, 150);
                }
            });
        });

        // =========================================
        // INTEGRACIÓN CON MENÚ MÓVIL DEL HEADER
        // =========================================

        // Cerrar el menú móvil del header cuando se abre el unified sidebar
        const mobileMenuSidebar = document.getElementById('mobile-menu-sidebar');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        // Observer para detectar cuando el unified sidebar se abre
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (body.classList.contains('sidebar-open')) {
                        // Unified sidebar abierto - cerrar menú móvil del header
                        if (mobileMenuSidebar) {
                            mobileMenuSidebar.classList.remove('active');
                        }
                        if (mobileMenuOverlay) {
                            mobileMenuOverlay.classList.remove('active');
                        }
                    }
                }
            });
        });

        observer.observe(body, {
            attributes: true,
            attributeFilter: ['class']
        });

        // =========================================
        // FINALIZACIÓN
        // =========================================

        console.log('Unified Sidebar initialized successfully');
    }

    // =========================================
    // FUNCIONES GLOBALES (para uso externo)
    // =========================================

    window.UnifiedSidebar = {
        /**
         * Abrir sidebar (móvil)
         */
        open: function() {
            if (window.innerWidth <= 1024) {
                document.body.classList.add('sidebar-open');
                document.body.style.overflow = 'hidden';
            }
        },

        /**
         * Cerrar sidebar (móvil)
         */
        close: function() {
            document.body.classList.remove('sidebar-open');
            document.body.style.overflow = '';
        },

        /**
         * Toggle sidebar
         */
        toggle: function() {
            const body = document.body;
            if (window.innerWidth > 1024) {
                // Desktop: toggle collapse
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('unified-sidebar-collapsed', body.classList.contains('sidebar-collapsed') ? 'true' : 'false');
            } else {
                // Mobile: toggle open
                if (body.classList.contains('sidebar-open')) {
                    this.close();
                } else {
                    this.open();
                }
            }
        },

        /**
         * Expandir sidebar (PC)
         */
        expand: function() {
            if (window.innerWidth > 1024) {
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('unified-sidebar-collapsed', 'false');
            }
        },

        /**
         * Colapsar sidebar (PC)
         */
        collapse: function() {
            if (window.innerWidth > 1024) {
                document.body.classList.add('sidebar-collapsed');
                localStorage.setItem('unified-sidebar-collapsed', 'true');
            }
        }
    };

})();
