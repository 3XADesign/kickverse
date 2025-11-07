/**
 * KICKVERSE CRM - Admin Panel JavaScript
 * Funcionalidad principal del CRM con sistema de modales y URL
 */

class CRMAdmin {
    constructor() {
        this.sidebar = document.getElementById('adminSidebar');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.modalContainer = document.getElementById('modalContainer');

        this.init();
    }

    init() {
        this.setupSidebar();
        this.setupModal();
        this.checkURLParams();

        // Listen to browser back/forward
        window.addEventListener('popstate', () => this.checkURLParams());
    }

    /**
     * SIDEBAR - Manejo del menú lateral colapsable
     */
    setupSidebar() {
        // Toggle sidebar desktop
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => {
                this.sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', this.sidebar.classList.contains('collapsed'));
            });
        }

        // Toggle sidebar mobile
        if (this.mobileMenuToggle) {
            this.mobileMenuToggle.addEventListener('click', () => {
                this.sidebar.classList.toggle('mobile-open');
            });
        }

        // Restore sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebar-collapsed');
        if (sidebarCollapsed === 'true') {
            this.sidebar.classList.add('collapsed');
        }

        // Close mobile sidebar when clicking nav item
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    this.sidebar.classList.remove('mobile-open');
                }
            });
        });
    }

    /**
     * MODAL - Sistema de modales con URL
     */
    setupModal() {
        // Close modal on overlay click
        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => {
                this.closeModal();
            });
        }

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }

    /**
     * Abre un modal y actualiza la URL
     */
    openModal(id, title, content) {
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('id', id);
        window.history.pushState({}, '', url);

        // Show modal
        this.modalContainer.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">${title}</h2>
                    <button class="modal-close" onclick="crmAdmin.closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    ${content}
                </div>
            </div>
        `;

        this.modalOverlay.classList.add('active');
        this.modalContainer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Cierra el modal y limpia la URL
     */
    closeModal() {
        // Remove ID from URL
        const url = new URL(window.location);
        url.searchParams.delete('id');
        window.history.pushState({}, '', url);

        // Hide modal
        this.modalOverlay.classList.remove('active');
        this.modalContainer.classList.remove('active');
        document.body.style.overflow = '';

        // Clear modal content after animation
        setTimeout(() => {
            this.modalContainer.innerHTML = '';
        }, 300);
    }

    /**
     * Verifica si hay un ID en la URL al cargar la página
     */
    checkURLParams() {
        const url = new URL(window.location);
        const id = url.searchParams.get('id');

        if (id) {
            // Fetch data and open modal
            this.fetchAndOpenModal(id);
        } else {
            // Close modal if open
            if (this.modalContainer.classList.contains('active')) {
                this.modalOverlay.classList.remove('active');
                this.modalContainer.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    }

    /**
     * Fetch data from API and open modal
     */
    async fetchAndOpenModal(id) {
        try {
            // Determine endpoint based on current page
            const path = window.location.pathname;
            let endpoint = '';

            if (path.includes('/admin/clientes')) {
                endpoint = `/api/admin/clientes/${id}`;
            } else if (path.includes('/admin/productos')) {
                endpoint = `/api/admin/productos/${id}`;
            } else if (path.includes('/admin/pedidos')) {
                endpoint = `/api/admin/pedidos/${id}`;
            } else if (path.includes('/admin/suscripciones')) {
                endpoint = `/api/admin/suscripciones/${id}`;
            } else if (path.includes('/admin/mystery-boxes')) {
                endpoint = `/api/admin/mystery-boxes/${id}`;
            } else if (path.includes('/admin/pagos')) {
                endpoint = `/api/admin/pagos/${id}`;
            } else if (path.includes('/admin/ligas')) {
                endpoint = `/api/admin/ligas/${id}`;
            } else if (path.includes('/admin/equipos')) {
                endpoint = `/api/admin/equipos/${id}`;
            }

            if (!endpoint) {
                console.error('No endpoint found for current page');
                return;
            }

            // Show loading
            this.showLoading();

            // Fetch data
            const response = await fetch(endpoint);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const data = await response.json();

            // Hide loading
            this.hideLoading();

            // Open modal with data
            if (typeof window.renderModalContent === 'function') {
                const content = window.renderModalContent(data);
                this.openModal(id, data.title || `ID: ${id}`, content);
            } else {
                console.error('renderModalContent function not found');
            }

        } catch (error) {
            console.error('Error fetching modal data:', error);
            this.hideLoading();
            this.showError('Error al cargar los datos');
        }
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        this.modalContainer.innerHTML = `
            <div class="modal-content">
                <div class="modal-body" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                    <p style="margin-top: 1rem; color: var(--gray-600);">Cargando...</p>
                </div>
            </div>
        `;
        this.modalOverlay.classList.add('active');
        this.modalContainer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Hide loading
     */
    hideLoading() {
        // Loading is replaced by actual content
    }

    /**
     * Show error message
     */
    showError(message) {
        this.modalContainer.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Error</h2>
                    <button class="modal-close" onclick="crmAdmin.closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                        <p>${message}</p>
                    </div>
                </div>
            </div>
        `;
        this.modalOverlay.classList.add('active');
        this.modalContainer.classList.add('active');
    }

    /**
     * Show success notification
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? 'var(--success)' : 'var(--info)'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Confirm dialog
     */
    confirm(message, onConfirm) {
        const confirmed = window.confirm(message);
        if (confirmed && typeof onConfirm === 'function') {
            onConfirm();
        }
        return confirmed;
    }

    /**
     * Delete item with confirmation
     */
    async deleteItem(endpoint, id, onSuccess) {
        if (!this.confirm('¿Estás seguro de que deseas eliminar este elemento?')) {
            return;
        }

        try {
            const response = await fetch(`${endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to delete item');
            }

            this.showSuccess('Elemento eliminado correctamente');

            if (typeof onSuccess === 'function') {
                onSuccess();
            } else {
                // Refresh page
                window.location.reload();
            }

        } catch (error) {
            console.error('Error deleting item:', error);
            this.showError('Error al eliminar el elemento');
        }
    }

    /**
     * Save item (POST or PUT)
     */
    async saveItem(endpoint, data, method = 'POST') {
        try {
            const response = await fetch(endpoint, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('Failed to save item');
            }

            const result = await response.json();
            this.showSuccess('Guardado correctamente');
            return result;

        } catch (error) {
            console.error('Error saving item:', error);
            this.showError('Error al guardar');
            throw error;
        }
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize CRM Admin
const crmAdmin = new CRMAdmin();

// ============================================================================
// GLOBAL HELPER FUNCTIONS
// ============================================================================

/**
 * Abrir modal de pedido
 */
window.openPedidoModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de cliente
 */
window.openClienteModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de producto
 */
window.openProductoModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de suscripción
 */
window.openSuscripcionModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de pago
 */
window.openPagoModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de liga
 */
window.openLigaModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Abrir modal de equipo
 */
window.openEquipoModal = function(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.fetchAndOpenModal(id);
};

/**
 * Actualizar estado de pedido
 */
window.updatePedidoStatus = async function(orderId, newStatus) {
    try {
        const response = await fetch(`/api/admin/pedidos/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) {
            throw new Error('Failed to update status');
        }

        crmAdmin.showSuccess('Estado actualizado correctamente');

        // Reload modal or page
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error updating status:', error);
        crmAdmin.showError('Error al actualizar el estado');
    }
};

/**
 * Actualizar tracking de pedido
 */
window.updatePedidoTracking = async function(orderId, tracking) {
    try {
        const response = await fetch(`/api/admin/pedidos/${orderId}/tracking`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ tracking_number: tracking })
        });

        if (!response.ok) {
            throw new Error('Failed to update tracking');
        }

        crmAdmin.showSuccess('Tracking actualizado correctamente');

        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error updating tracking:', error);
        crmAdmin.showError('Error al actualizar el tracking');
    }
};

/**
 * Pausar suscripción
 */
window.pauseSuscripcion = async function(id) {
    if (!confirm('¿Pausar esta suscripción?')) return;

    try {
        const response = await fetch(`/admin/suscripciones/pause/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to pause subscription');
        }

        crmAdmin.showSuccess('Suscripción pausada');
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error pausing subscription:', error);
        crmAdmin.showError('Error al pausar la suscripción');
    }
};

/**
 * Cancelar suscripción
 */
window.cancelSuscripcion = async function(id) {
    if (!confirm('¿Cancelar esta suscripción? Esta acción no se puede deshacer.')) return;

    try {
        const response = await fetch(`/admin/suscripciones/cancel/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to cancel subscription');
        }

        crmAdmin.showSuccess('Suscripción cancelada');
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error canceling subscription:', error);
        crmAdmin.showError('Error al cancelar la suscripción');
    }
};

/**
 * Reactivar suscripción
 */
window.reactivateSuscripcion = async function(id) {
    if (!confirm('¿Reactivar esta suscripción?')) return;

    try {
        const response = await fetch(`/admin/suscripciones/reactivate/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to reactivate subscription');
        }

        crmAdmin.showSuccess('Suscripción reactivada');
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error reactivating subscription:', error);
        crmAdmin.showError('Error al reactivar la suscripción');
    }
};

/**
 * Verificar pago manual
 */
window.completarPago = async function(paymentId) {
    if (!confirm('¿Marcar este pago como completado?')) return;

    try {
        const response = await fetch(`/api/admin/pagos/${paymentId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to complete payment');
        }

        crmAdmin.showSuccess('Pago completado');
        setTimeout(() => window.location.reload(), 1000);

    } catch (error) {
        console.error('Error completing payment:', error);
        crmAdmin.showError('Error al completar el pago');
    }
};
