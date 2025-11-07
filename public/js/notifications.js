/**
 * Sistema de Notificaciones Modales - Kickverse
 * Reemplaza los alerts nativos con modales bonitos y modernos
 */

// Estado global para manejar solo un modal a la vez
let currentModal = null;

/**
 * Crear estructura HTML del modal si no existe
 */
function createNotificationModal() {
    // Verificar si ya existe
    if (document.getElementById('notificationModal')) {
        return;
    }

    const modalHTML = `
        <div class="notification-modal" id="notificationModal">
            <div class="notification-overlay" onclick="closeNotificationModal()"></div>
            <div class="notification-content">
                <button class="notification-close" onclick="closeNotificationModal()" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
                <div class="notification-icon" id="notificationIcon"></div>
                <h3 class="notification-title" id="notificationTitle"></h3>
                <p class="notification-message" id="notificationMessage"></p>
                <div class="notification-buttons" id="notificationButtons"></div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

/**
 * Cerrar modal actual
 */
function closeNotificationModal(callback) {
    const modal = document.getElementById('notificationModal');
    if (!modal) return;

    modal.classList.remove('active');
    document.body.style.overflow = 'auto';

    setTimeout(() => {
        modal.style.display = 'none';
        currentModal = null;

        // Ejecutar callback si existe
        if (callback && typeof callback === 'function') {
            callback();
        }
    }, 300);
}

/**
 * Mostrar modal de éxito (verde)
 * @param {string} message - Mensaje a mostrar
 * @param {function} callback - Función a ejecutar al cerrar (opcional)
 */
function showSuccessModal(message, callback) {
    createNotificationModal();

    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const title = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');
    const buttons = document.getElementById('notificationButtons');

    // Configurar contenido
    icon.className = 'notification-icon success';
    icon.innerHTML = '<i class="fas fa-check-circle"></i>';
    title.textContent = 'Éxito';
    messageEl.textContent = message;

    // Botón de aceptar
    buttons.innerHTML = `
        <button class="notification-btn success" onclick="closeNotificationModal(${callback ? 'window.successCallback' : 'null'})">
            <i class="fas fa-check"></i> Aceptar
        </button>
    `;

    // Guardar callback en window si existe
    if (callback && typeof callback === 'function') {
        window.successCallback = callback;
    }

    // Mostrar modal
    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    currentModal = 'success';
}

/**
 * Mostrar modal de error (rojo)
 * @param {string} message - Mensaje a mostrar
 * @param {function} callback - Función a ejecutar al cerrar (opcional)
 */
function showErrorModal(message, callback) {
    createNotificationModal();

    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const title = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');
    const buttons = document.getElementById('notificationButtons');

    // Configurar contenido
    icon.className = 'notification-icon error';
    icon.innerHTML = '<i class="fas fa-times-circle"></i>';
    title.textContent = 'Error';
    messageEl.textContent = message;

    // Botón de aceptar
    buttons.innerHTML = `
        <button class="notification-btn error" onclick="closeNotificationModal(${callback ? 'window.errorCallback' : 'null'})">
            <i class="fas fa-check"></i> Aceptar
        </button>
    `;

    // Guardar callback en window si existe
    if (callback && typeof callback === 'function') {
        window.errorCallback = callback;
    }

    // Mostrar modal
    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    currentModal = 'error';
}

/**
 * Mostrar modal de advertencia (naranja)
 * @param {string} message - Mensaje a mostrar
 * @param {function} callback - Función a ejecutar al cerrar (opcional)
 */
function showWarningModal(message, callback) {
    createNotificationModal();

    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const title = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');
    const buttons = document.getElementById('notificationButtons');

    // Configurar contenido
    icon.className = 'notification-icon warning';
    icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    title.textContent = 'Advertencia';
    messageEl.textContent = message;

    // Botón de aceptar
    buttons.innerHTML = `
        <button class="notification-btn warning" onclick="closeNotificationModal(${callback ? 'window.warningCallback' : 'null'})">
            <i class="fas fa-check"></i> Aceptar
        </button>
    `;

    // Guardar callback en window si existe
    if (callback && typeof callback === 'function') {
        window.warningCallback = callback;
    }

    // Mostrar modal
    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    currentModal = 'warning';
}

/**
 * Mostrar modal de confirmación con botones Sí/No (azul)
 * @param {string} message - Mensaje a mostrar
 * @param {function} onConfirm - Función a ejecutar al confirmar
 * @param {function} onCancel - Función a ejecutar al cancelar (opcional)
 */
function showConfirmModal(message, onConfirm, onCancel) {
    createNotificationModal();

    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const title = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');
    const buttons = document.getElementById('notificationButtons');

    // Configurar contenido
    icon.className = 'notification-icon confirm';
    icon.innerHTML = '<i class="fas fa-question-circle"></i>';
    title.textContent = 'Confirmación';
    messageEl.textContent = message;

    // Guardar callbacks en window
    window.confirmCallback = onConfirm;
    window.cancelCallback = onCancel;

    // Botones de confirmar/cancelar
    buttons.innerHTML = `
        <button class="notification-btn cancel" onclick="handleCancelConfirm()">
            <i class="fas fa-times"></i> No
        </button>
        <button class="notification-btn confirm" onclick="handleConfirm()">
            <i class="fas fa-check"></i> Sí
        </button>
    `;

    // Mostrar modal
    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    currentModal = 'confirm';
}

/**
 * Handler para botón de confirmación
 */
function handleConfirm() {
    const callback = window.confirmCallback;
    closeNotificationModal(() => {
        if (callback && typeof callback === 'function') {
            callback();
        }
        window.confirmCallback = null;
        window.cancelCallback = null;
    });
}

/**
 * Handler para botón de cancelación
 */
function handleCancelConfirm() {
    const callback = window.cancelCallback;
    closeNotificationModal(() => {
        if (callback && typeof callback === 'function') {
            callback();
        }
        window.confirmCallback = null;
        window.cancelCallback = null;
    });
}

/**
 * Cerrar modal con tecla ESC
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentModal) {
        // Si es confirmación, ejecutar cancel callback
        if (currentModal === 'confirm') {
            handleCancelConfirm();
        } else {
            closeNotificationModal();
        }
    }
});

/**
 * Compatibilidad con código legacy que usa alert()
 * Descomentar solo para debugging si es necesario
 */
// window.alert = function(message) {
//     showWarningModal(message);
// };

/**
 * Compatibilidad con código legacy que usa confirm()
 * Descomentar solo para debugging si es necesario
 */
// window.confirm = function(message) {
//     return new Promise((resolve) => {
//         showConfirmModal(message,
//             () => resolve(true),  // onConfirm
//             () => resolve(false)  // onCancel
//         );
//     });
// };

console.log('Sistema de notificaciones Kickverse cargado correctamente');
