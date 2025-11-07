<?php
// Vista de Configuración del Sistema
$current_page = 'configuracion';
$page_title = 'Configuración';

// Category names and icons
$categoryInfo = [
    'site' => ['name' => 'Sitio Web', 'icon' => 'fa-globe'],
    'email' => ['name' => 'Email', 'icon' => 'fa-envelope'],
    'payment' => ['name' => 'Pagos', 'icon' => 'fa-credit-card'],
    'shipping' => ['name' => 'Envío', 'icon' => 'fa-truck'],
    'crypto' => ['name' => 'Criptomonedas', 'icon' => 'fa-bitcoin'],
    'api' => ['name' => 'API', 'icon' => 'fa-code'],
    'security' => ['name' => 'Seguridad', 'icon' => 'fa-shield-alt'],
    'notifications' => ['name' => 'Notificaciones', 'icon' => 'fa-bell'],
    'general' => ['name' => 'General', 'icon' => 'fa-cog']
];
?>

<div class="crm-card">
    <div class="crm-card-header">
        <div>
            <h2 class="crm-card-title">
                <i class="fas fa-cog"></i>
                Configuración del Sistema
            </h2>
            <p class="crm-card-subtitle">
                Gestión de ajustes y parámetros del sistema
            </p>
        </div>
        <div class="crm-card-actions">
            <button class="btn btn-secondary" onclick="clearCache()">
                <i class="fas fa-broom"></i>
                Limpiar Caché
            </button>
            <button class="btn btn-primary" onclick="document.getElementById('settingsForm').submit()">
                <i class="fas fa-save"></i>
                Guardar Cambios
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?>" style="margin: 1.5rem;">
            <i class="fas fa-<?= $_SESSION['flash_message']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- System Stats -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Clientes</div>
                <div class="stat-value"><?= number_format($dbStats['total_customers'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Productos</div>
                <div class="stat-value"><?= number_format($dbStats['total_products'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Pedidos</div>
                <div class="stat-value"><?= number_format($dbStats['total_orders'] ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                <i class="fas fa-redo"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Suscripciones</div>
                <div class="stat-value"><?= number_format($dbStats['total_subscriptions'] ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div style="border-bottom: 1px solid var(--gray-200); margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.5rem; overflow-x: auto;">
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
        <form id="settingsForm" method="POST" action="/admin/configuracion/update">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <?php if (!empty($settingsByCategory)): ?>
                <?php foreach ($settingsByCategory as $category => $settings): ?>
                    <?php
                    $catInfo = $categoryInfo[$category] ?? ['name' => ucfirst($category), 'icon' => 'fa-cog'];
                    ?>
                    <div class="crm-card" style="margin-bottom: 1.5rem;">
                        <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                                <i class="fas <?= $catInfo['icon'] ?>"></i>
                                <?= htmlspecialchars($catInfo['name']) ?>
                            </h3>
                        </div>
                        <div class="crm-card-body">
                            <div style="display: grid; gap: 1.5rem;">
                                <?php foreach ($settings as $setting): ?>
                                    <div class="form-group">
                                        <label for="<?= htmlspecialchars($setting['setting_key']) ?>" class="form-label">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', substr($setting['setting_key'], strlen($category) + 1)))) ?>
                                            <?php if (!$setting['is_public']): ?>
                                                <span class="badge badge-secondary" style="font-size: 0.65rem; margin-left: 0.5rem;">Privado</span>
                                            <?php endif; ?>
                                        </label>

                                        <?php if ($setting['description']): ?>
                                            <p style="margin: 0.25rem 0 0.5rem; font-size: 0.875rem; color: var(--gray-600);">
                                                <?= htmlspecialchars($setting['description']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php
                                        $inputType = 'text';
                                        $inputValue = htmlspecialchars($setting['setting_value']);

                                        switch ($setting['setting_type']) {
                                            case 'number':
                                                $inputType = 'number';
                                                break;
                                            case 'boolean':
                                                ?>
                                                <div class="toggle-switch">
                                                    <input type="checkbox"
                                                           id="<?= htmlspecialchars($setting['setting_key']) ?>"
                                                           name="<?= htmlspecialchars($setting['setting_key']) ?>"
                                                           value="true"
                                                           <?= $setting['setting_value'] === 'true' ? 'checked' : '' ?>>
                                                    <label for="<?= htmlspecialchars($setting['setting_key']) ?>"></label>
                                                </div>
                                                <?php
                                                continue 2; // Skip to next setting
                                            case 'json':
                                                ?>
                                                <textarea
                                                    id="<?= htmlspecialchars($setting['setting_key']) ?>"
                                                    name="<?= htmlspecialchars($setting['setting_key']) ?>"
                                                    class="form-control"
                                                    rows="4"
                                                    style="font-family: monospace; font-size: 0.875rem;"><?= $inputValue ?></textarea>
                                                <?php
                                                continue 2; // Skip to next setting
                                        }
                                        ?>

                                        <input type="<?= $inputType ?>"
                                               id="<?= htmlspecialchars($setting['setting_key']) ?>"
                                               name="<?= htmlspecialchars($setting['setting_key']) ?>"
                                               value="<?= $inputValue ?>"
                                               class="form-control">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-cog"></i>
                    <p class="empty-state-title">No hay configuraciones</p>
                    <p class="empty-state-text">Las configuraciones del sistema aparecerán aquí</p>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tab Content: System Info -->
    <div id="tab-system" class="tab-content">
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-info-circle"></i>
                    Información del Sistema
                </h3>
            </div>
            <div class="crm-card-body">
                <table class="crm-table">
                    <tbody>
                        <tr>
                            <td style="font-weight: 600; width: 250px;">Versión de PHP</td>
                            <td><?= htmlspecialchars($systemInfo['php_version']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Versión de MySQL</td>
                            <td><?= htmlspecialchars($systemInfo['db_version']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Servidor</td>
                            <td><?= htmlspecialchars($systemInfo['server_software']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Tamaño Máx. de Archivo</td>
                            <td><?= htmlspecialchars($systemInfo['max_upload_size']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Tamaño Máx. POST</td>
                            <td><?= htmlspecialchars($systemInfo['max_post_size']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Límite de Memoria</td>
                            <td><?= htmlspecialchars($systemInfo['memory_limit']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Zona Horaria</td>
                            <td><?= htmlspecialchars($systemInfo['time_zone']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Fecha/Hora del Servidor</td>
                            <td><?= date('Y-m-d H:i:s') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="crm-card" style="margin-top: 1.5rem;">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                    <i class="fas fa-tools"></i>
                    Herramientas de Mantenimiento
                </h3>
            </div>
            <div class="crm-card-body">
                <div style="display: grid; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Limpiar Caché</div>
                            <div style="font-size: 0.875rem; color: var(--gray-600);">
                                Elimina archivos temporales y caché del sistema
                            </div>
                        </div>
                        <form method="POST" action="/admin/configuracion/clear-cache" style="display: inline;">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-broom"></i>
                                Limpiar
                            </button>
                        </form>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                        <div style="flex: 1; margin-right: 1rem;">
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Probar Email</div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">
                                Envía un email de prueba para verificar la configuración
                            </div>
                            <form method="POST" action="/admin/configuracion/test-email" style="display: flex; gap: 0.5rem;">
                                <input type="email"
                                       name="test_email"
                                       placeholder="tu@email.com"
                                       class="form-control"
                                       style="max-width: 300px;"
                                       required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Enviar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Admins -->
    <div id="tab-admins" class="tab-content">
        <div class="crm-card">
            <div class="crm-card-header" style="border-bottom: 1px solid var(--gray-200);">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                        <i class="fas fa-user-shield"></i>
                        Administradores del Sistema
                    </h3>
                </div>
                <button class="btn btn-primary" onclick="alert('Función de agregar admin - Por implementar')">
                    <i class="fas fa-plus"></i>
                    Agregar Admin
                </button>
            </div>
            <div class="crm-card-body">
                <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div class="stat-card" style="flex: 1;">
                            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Admins</div>
                                <div class="stat-value"><?= number_format($adminStats['total_admins'] ?? 0) ?></div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex: 1;">
                            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #059669);">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Activos (30 días)</div>
                                <div class="stat-value"><?= number_format($adminStats['active_admins'] ?? 0) ?></div>
                            </div>
                        </div>
                        <div class="stat-card" style="flex: 1;">
                            <div class="stat-icon" style="background: linear-gradient(135deg, var(--info), #2563eb);">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Último Login</div>
                                <div class="stat-value" style="font-size: 0.875rem;">
                                    <?php if ($adminStats['last_admin_login']): ?>
                                        <?= date('d/m/Y H:i', strtotime($adminStats['last_admin_login'])) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="empty-state">
                    <i class="fas fa-user-shield"></i>
                    <p class="empty-state-title">Gestión de Administradores</p>
                    <p class="empty-state-text">
                        La lista completa de administradores y sus permisos se mostrará aquí.<br>
                        Función por implementar en fase futura.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--gray-600);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.tab-button:hover {
    color: var(--primary);
}

.tab-button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch label {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: 0.3s;
    border-radius: 26px;
}

.toggle-switch label:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.toggle-switch input:checked + label {
    background-color: var(--primary);
}

.toggle-switch input:checked + label:before {
    transform: translateX(24px);
}
</style>

<script>
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

function clearCache() {
    if (confirm('¿Estás seguro de que deseas limpiar la caché del sistema?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/configuracion/clear-cache';
        document.body.appendChild(form);
        form.submit();
    }
}

// Convert boolean checkboxes to proper values on submit
document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(checkbox => {
        if (!checkbox.checked) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = checkbox.name;
            hidden.value = 'false';
            this.appendChild(hidden);
        }
    });
});
</script>
