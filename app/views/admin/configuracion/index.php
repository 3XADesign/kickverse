<?php
/**
 * Admin Configuración - Configuración del Sistema
 * Usa layout/header.php y layout/footer.php
 */

// Variables para el layout
$current_page = 'configuracion';
$page_title = 'Configuración - Admin Kickverse';
$breadcrumbs = [['label' => 'Configuración']];

// Load header
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-cog"></i>
            Configuración del Sistema
        </h1>
        <p class="page-subtitle">Gestión de ajustes y parámetros del sistema</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" onclick="clearCache()">
            <i class="fas fa-broom"></i>
            Limpiar Caché
        </button>
        <button class="btn btn-primary" onclick="saveSettings()">
            <i class="fas fa-save"></i>
            Guardar Cambios
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Total Clientes -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Clientes</span>
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-database"></i>
            en base de datos
        </div>
    </div>

    <!-- Total Productos -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Productos</span>
            <div class="stat-icon success">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-warehouse"></i>
            en inventario
        </div>
    </div>

    <!-- Total Pedidos -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Pedidos</span>
            <div class="stat-icon warning">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-chart-line"></i>
            procesados
        </div>
    </div>

    <!-- Suscripciones -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Suscripciones</span>
            <div class="stat-icon danger">
                <i class="fas fa-redo"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-change">
            <i class="fas fa-sync-alt"></i>
            activas
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div style="border-bottom: 1px solid var(--admin-gray-200); margin-top: 30px; margin-bottom: 24px;">
    <div style="display: flex; gap: 8px; overflow-x: auto;">
        <button class="tab-button active" onclick="switchTab('settings')" data-tab="settings">
            <i class="fas fa-sliders-h"></i>
            Configuración
        </button>
        <button class="tab-button" onclick="switchTab('system')" data-tab="system">
            <i class="fas fa-server"></i>
            Sistema
        </button>
        <button class="tab-button" onclick="switchTab('admins')" data-tab="admins">
            <i class="fas fa-user-shield"></i>
            Administradores
        </button>
    </div>
</div>

<!-- Tab Content: Settings -->
<div id="tab-settings" class="tab-content active">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cog"></i>
                Configuración General
            </h3>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div class="empty-state-title">Configuración del Sistema</div>
                <div class="empty-state-description">Los ajustes de configuración aparecerán aquí. API pendiente de implementar.</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content: System Info -->
<div id="tab-system" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                Información del Sistema
            </h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                        <td style="padding: 16px; font-weight: 600; width: 250px;">Versión de PHP</td>
                        <td style="padding: 16px;"><?= phpversion() ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                        <td style="padding: 16px; font-weight: 600;">Servidor</td>
                        <td style="padding: 16px;"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                        <td style="padding: 16px; font-weight: 600;">Tamaño Max. de Archivo</td>
                        <td style="padding: 16px;"><?= ini_get('upload_max_filesize') ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                        <td style="padding: 16px; font-weight: 600;">Límite de Memoria</td>
                        <td style="padding: 16px;"><?= ini_get('memory_limit') ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--admin-gray-100);">
                        <td style="padding: 16px; font-weight: 600;">Zona Horaria</td>
                        <td style="padding: 16px;"><?= date_default_timezone_get() ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 16px; font-weight: 600;">Fecha/Hora del Servidor</td>
                        <td style="padding: 16px;"><?= date('Y-m-d H:i:s') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tools"></i>
                Herramientas de Mantenimiento
            </h3>
        </div>
        <div class="card-body">
            <div style="display: grid; gap: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--admin-gray-50); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 4px;">Limpiar Caché</div>
                        <div style="font-size: 14px; color: var(--admin-gray-600);">
                            Elimina archivos temporales y caché del sistema
                        </div>
                    </div>
                    <button class="btn btn-secondary" onclick="clearCache()">
                        <i class="fas fa-broom"></i>
                        Limpiar
                    </button>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--admin-gray-50); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 4px;">Probar Email</div>
                        <div style="font-size: 14px; color: var(--admin-gray-600);">
                            Envía un email de prueba para verificar la configuración
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="testEmail()">
                        <i class="fas fa-paper-plane"></i>
                        Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content: Admins -->
<div id="tab-admins" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-shield"></i>
                Administradores del Sistema
            </h3>
            <button class="btn btn-primary" onclick="addAdmin()">
                <i class="fas fa-plus"></i>
                Agregar Admin
            </button>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="empty-state-title">Gestión de Administradores</div>
                <div class="empty-state-description">La lista de administradores aparecerá aquí. API pendiente de implementar.</div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button {
    padding: 12px 24px;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--admin-gray-600);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.tab-button:hover {
    color: var(--admin-primary);
}

.tab-button.active {
    color: var(--admin-primary);
    border-bottom-color: var(--admin-primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
// TODO: Implementar carga de datos desde API /api/admin/configuracion

document.addEventListener('DOMContentLoaded', () => {
    console.log('Página de Configuración lista - TODO: Implementar API');
});

function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');
}

function saveSettings() {
    showToast('Función de guardar configuración en desarrollo', 'info');
}

function clearCache() {
    if (confirm('¿Estás seguro de que deseas limpiar la caché del sistema?')) {
        showToast('Función de limpiar caché en desarrollo', 'info');
    }
}

function testEmail() {
    const email = prompt('Ingresa el email de destino:');
    if (email) {
        showToast('Función de test de email en desarrollo', 'info');
    }
}

function addAdmin() {
    showToast('Función de agregar administrador en desarrollo', 'info');
}
</script>

<?php
// Load footer
require_once __DIR__ . '/../layout/footer.php';
?>
