        </div><!-- .admin-content -->
    </div><!-- .admin-main -->

    <!-- Global Modal Container -->
    <div id="modalContainer"></div>

    <!-- Core Scripts -->
    <script src="/js/admin/admin-core.js"></script>
    <script src="/js/admin/modal-manager.js"></script>
    <script src="/js/admin/table-utils.js"></script>
    <script src="/js/admin/api-client.js"></script>

    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Initialize sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarState = localStorage.getItem('kickverse_admin_sidebar_collapsed');

            // Restore sidebar state
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
            }

            // Toggle sidebar (works for open and close)
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isCollapsed = sidebar.classList.toggle('collapsed');
                localStorage.setItem('kickverse_admin_sidebar_collapsed', isCollapsed);
            });

            // User menu dropdown
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');

            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('show');
            });

            // Notification dropdown
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');

            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                if (notificationDropdown.classList.contains('show')) {
                    loadNotifications();
                }
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                userMenuDropdown.classList.remove('show');
                notificationDropdown.classList.remove('show');
            });

            // Global search
            const globalSearch = document.getElementById('globalSearch');
            let searchTimeout;

            globalSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) return;

                searchTimeout = setTimeout(() => {
                    performGlobalSearch(query);
                }, 300);
            });
        });

        // Load notifications
        async function loadNotifications() {
            try {
                const response = await fetch('/admin/api/notifications/unread');
                const data = await response.json();

                if (data.success) {
                    renderNotifications(data.notifications);
                    updateNotificationBadge(data.unread_count);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        // Render notifications
        function renderNotifications(notifications) {
            const container = document.getElementById('notificationList');

            if (notifications.length === 0) {
                container.innerHTML = '<div class="notification-empty">No hay notificaciones nuevas</div>';
                return;
            }

            container.innerHTML = notifications.map(notif => `
                <div class="notification-item ${notif.read_at ? 'read' : 'unread'}" data-id="${notif.notification_id}">
                    <div class="notification-icon ${notif.notification_type}">
                        <i class="fas ${getNotificationIcon(notif.notification_type)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notif.title}</div>
                        <div class="notification-time">${formatTimeAgo(notif.sent_at)}</div>
                    </div>
                </div>
            `).join('');
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Get notification icon
        function getNotificationIcon(type) {
            const icons = {
                'stock_alert': 'fa-exclamation-triangle',
                'payment_pending': 'fa-credit-card',
                'new_order': 'fa-shopping-bag',
                'customer_message': 'fa-comment',
                'system': 'fa-cog'
            };
            return icons[type] || 'fa-bell';
        }

        // Format time ago
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return 'Hace un momento';
            if (seconds < 3600) return `Hace ${Math.floor(seconds / 60)} min`;
            if (seconds < 86400) return `Hace ${Math.floor(seconds / 3600)}h`;
            if (seconds < 604800) return `Hace ${Math.floor(seconds / 86400)}d`;

            return date.toLocaleDateString('es-ES');
        }

        // Global search
        async function performGlobalSearch(query) {
            try {
                const response = await fetch(`/admin/api/search/global?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success) {
                    showSearchResults(data.results);
                }
            } catch (error) {
                console.error('Error performing search:', error);
            }
        }

        // Show search results
        function showSearchResults(results) {
            // TODO: Implement search results dropdown
            console.log('Search results:', results);
        }
    </script>
</body>
</html>
