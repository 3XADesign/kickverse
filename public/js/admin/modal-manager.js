/**
 * Kickverse Admin - Modal Manager
 * Sistema unificado de modales full-screen con gestión de URL
 */

window.ModalManager = {
    currentModal: null,
    originalUrl: null,

    /**
     * Abrir modal full-screen
     * @param {string} title - Título del modal
     * @param {string} content - Contenido HTML del modal
     * @param {string} paramName - Nombre del parámetro URL (ej: 'order_id')
     * @param {string|number} paramValue - Valor del parámetro (ej: 123)
     */
    open: function(title, content, paramName = null, paramValue = null) {
        // Guardar URL original
        if (!this.originalUrl) {
            this.originalUrl = window.location.href;
        }

        // Crear modal si no existe
        if (!this.currentModal) {
            this.createModal();
        }

        // Actualizar contenido
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').innerHTML = content;

        // Mostrar modal
        this.currentModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Actualizar URL si se proporcionan parámetros
        if (paramName && paramValue) {
            this.updateUrl(paramName, paramValue);
        }
    },

    /**
     * Cerrar modal
     */
    close: function() {
        if (!this.currentModal) return;

        // Ocultar modal
        this.currentModal.classList.remove('show');
        document.body.style.overflow = '';

        // Restaurar URL original
        if (this.originalUrl) {
            window.history.replaceState({}, '', this.originalUrl);
            this.originalUrl = null;
        }

        // Limpiar contenido después de la animación
        setTimeout(() => {
            if (this.currentModal) {
                document.getElementById('modalContent').innerHTML = '';
            }
        }, 300);
    },

    /**
     * Crear estructura del modal
     */
    createModal: function() {
        const modalHTML = `
            <div id="adminModal" class="admin-modal">
                <div class="admin-modal-overlay" onclick="ModalManager.close()"></div>
                <div class="admin-modal-container">
                    <div class="admin-modal-header">
                        <h2 id="modalTitle" class="admin-modal-title"></h2>
                        <button class="admin-modal-close" onclick="ModalManager.close()" aria-label="Cerrar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="admin-modal-body" id="modalContent">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        `;

        // Insertar en el DOM
        const container = document.getElementById('modalContainer');
        if (container) {
            container.innerHTML = modalHTML;
            this.currentModal = document.getElementById('adminModal');

            // Listener para tecla ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.currentModal?.classList.contains('show')) {
                    this.close();
                }
            });
        }
    },

    /**
     * Actualizar URL con parámetro
     */
    updateUrl: function(paramName, paramValue) {
        const url = new URL(window.location);
        url.searchParams.set(paramName, paramValue);
        window.history.pushState({}, '', url);
    },

    /**
     * Verificar si hay parámetro en URL al cargar
     * @param {string} paramName - Nombre del parámetro a buscar
     * @param {function} callback - Función a ejecutar con el valor del parámetro
     */
    checkUrlParam: function(paramName, callback) {
        const urlParams = new URLSearchParams(window.location.search);
        const paramValue = urlParams.get(paramName);

        if (paramValue) {
            callback(paramValue);
        }
    },

    /**
     * Modal de confirmación
     * @param {string} message - Mensaje de confirmación
     * @param {string} title - Título del modal (opcional)
     * @returns {Promise<boolean>}
     */
    confirm: function(message, title = '¿Estás seguro?') {
        return new Promise((resolve) => {
            const content = `
                <div class="modal-confirm">
                    <div class="modal-confirm-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="modal-confirm-message">${escapeHtml(message)}</p>
                    <div class="modal-confirm-actions">
                        <button class="btn btn-secondary" onclick="ModalManager.closeConfirm(false)">
                            Cancelar
                        </button>
                        <button class="btn btn-danger" onclick="ModalManager.closeConfirm(true)">
                            Confirmar
                        </button>
                    </div>
                </div>
            `;

            this.confirmResolve = resolve;
            this.open(title, content);
        });
    },

    /**
     * Cerrar modal de confirmación
     */
    closeConfirm: function(result) {
        this.close();
        if (this.confirmResolve) {
            this.confirmResolve(result);
            this.confirmResolve = null;
        }
    },

    /**
     * Modal de carga
     * @param {string} message - Mensaje de carga
     */
    showLoading: function(message = 'Cargando...') {
        const content = `
            <div class="modal-loading">
                <div class="spinner"></div>
                <p>${escapeHtml(message)}</p>
            </div>
        `;
        this.open('', content);
    }
};

// Estilos del modal (si no están en CSS)
if (!document.getElementById('modalManagerStyles')) {
    const style = document.createElement('style');
    style.id = 'modalManagerStyles';
    style.textContent = `
        .admin-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .admin-modal.show {
            display: flex;
        }

        .admin-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .admin-modal-container {
            position: relative;
            width: 95%;
            height: 95%;
            max-width: 1400px;
            max-height: 90vh;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            animation: slideUp 0.3s ease;
            z-index: 10000;
        }

        .admin-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 32px;
            border-bottom: 1px solid var(--admin-gray-200);
            flex-shrink: 0;
        }

        .admin-modal-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--admin-gray-900);
            margin: 0;
        }

        .admin-modal-close {
            width: 40px;
            height: 40px;
            border: none;
            background: var(--admin-gray-100);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: var(--admin-gray-600);
            font-size: 20px;
        }

        .admin-modal-close:hover {
            background: var(--admin-gray-200);
            color: var(--admin-gray-900);
        }

        .admin-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 32px;
        }

        .modal-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            gap: 20px;
        }

        .modal-loading p {
            font-size: 16px;
            color: var(--admin-gray-600);
        }

        .modal-confirm {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
            max-width: 500px;
            margin: 0 auto;
        }

        .modal-confirm-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--admin-warning-light, #fef3c7);
            color: var(--admin-warning, #f59e0b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 24px;
        }

        .modal-confirm-message {
            font-size: 18px;
            color: var(--admin-gray-700);
            text-align: center;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .modal-confirm-actions {
            display: flex;
            gap: 12px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-modal-container {
                width: 100%;
                height: 100%;
                max-width: none;
                max-height: none;
                border-radius: 0;
            }

            .admin-modal-header {
                padding: 16px 20px;
            }

            .admin-modal-title {
                font-size: 20px;
            }

            .admin-modal-body {
                padding: 20px;
            }
        }
    `;
    document.head.appendChild(style);
}
