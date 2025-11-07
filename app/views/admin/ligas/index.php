<?php
// Vista de Gestión de Ligas
$current_page = 'ligas';
$page_title = 'Gestión de Ligas';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-trophy"></i>
            Ligas
        </h2>
        <div class="crm-card-actions">
            <select id="filterCountry" class="form-select" style="width: auto;">
                <option value="">Todos los países</option>
                <?php foreach ($countries as $country): ?>
                    <?php if (!empty($country['country'])): ?>
                        <option value="<?= htmlspecialchars($country['country']) ?>"
                                <?= (isset($filters['country']) && $filters['country'] === $country['country']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country['country']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="1" <?= (isset($filters['is_active']) && $filters['is_active'] === '1') ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= (isset($filters['is_active']) && $filters['is_active'] === '0') ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i>
                Nueva Liga
            </button>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="ligasTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Logo</th>
                        <th>Nombre</th>
                        <th>País</th>
                        <th style="width: 100px;">Equipos</th>
                        <th style="width: 80px;">Orden</th>
                        <th style="width: 100px;">Estado</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ligas)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-trophy"></i>
                                    <p class="empty-state-title">No hay ligas</p>
                                    <p class="empty-state-text">Comienza agregando tu primera liga</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ligas as $liga): ?>
                            <tr class="table-row-clickable" onclick="openLigaModal(<?= $liga['league_id'] ?>)">
                                <td><strong>#<?= $liga['league_id'] ?></strong></td>
                                <td>
                                    <?php if (!empty($liga['logo_path'])): ?>
                                        <img src="<?= htmlspecialchars($liga['logo_path']) ?>"
                                             alt="<?= htmlspecialchars($liga['name']) ?>"
                                             class="table-img">
                                    <?php else: ?>
                                        <div class="table-img-placeholder">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--gray-900);">
                                        <?= htmlspecialchars($liga['name']) ?>
                                    </strong>
                                    <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                        <?= htmlspecialchars($liga['slug']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($liga['country'])): ?>
                                        <span style="color: var(--gray-700);">
                                            <?= htmlspecialchars($liga['country']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= $liga['team_count'] ?> equipos
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?= $liga['display_order'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $liga['is_active'] ? 'success' : 'warning' ?>">
                                        <?= $liga['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="openLigaModal(<?= $liga['league_id'] ?>)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="editLiga(<?= $liga['league_id'] ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($liga['team_count'] == 0): ?>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="deleteLiga(<?= $liga['league_id'] ?>)"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.table-img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: var(--radius-lg);
    background: white;
    padding: 0.25rem;
    border: 1px solid var(--gray-200);
}

.table-img-placeholder {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: var(--radius-lg);
    color: var(--gray-400);
    font-size: 1.25rem;
}

.table-row-clickable {
    cursor: pointer;
    transition: var(--transition);
}

.table-row-clickable:hover {
    background: var(--gray-50);
}

.form-select {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    color: var(--gray-700);
    background: white;
    cursor: pointer;
    transition: var(--transition);
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

@media (max-width: 768px) {
    .crm-card-actions {
        width: 100%;
        flex-direction: column;
    }

    .form-select {
        width: 100% !important;
    }
}
</style>

<script>
// Función para abrir modal con detalles de la liga
function openLigaModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para crear nueva liga
function openCreateModal() {
    // TODO: Implementar modal de creación
    showWarningModal('Funcionalidad de creación en desarrollo');
}

// Función para editar liga
function editLiga(id) {
    // TODO: Implementar modal de edición
    showWarningModal('Funcionalidad de edición en desarrollo. ID: ' + id);
}

// Función para eliminar liga
function deleteLiga(id) {
    showConfirmModal('¿Estás seguro de eliminar esta liga?', () => {
        fetch(`/admin/ligas/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Liga eliminada correctamente', () => {
                    location.reload();
                });
            } else {
                showErrorModal(data.error || 'Error al eliminar la liga');
            }
        })
        .catch(error => {
            showErrorModal('Error al eliminar la liga');
        });
    });
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    const teamsHtml = data.teams && data.teams.length > 0
        ? data.teams.map(team => `
            <div class="team-item">
                ${team.logo_path
                    ? `<img src="${team.logo_path}" alt="${team.name}" class="team-logo">`
                    : `<div class="team-logo-placeholder"><i class="fas fa-shield-alt"></i></div>`
                }
                <div class="team-info">
                    <div class="team-name">${team.name}</div>
                    <div class="team-meta">
                        ${team.product_count} productos
                        ${team.is_top_team ? '<span class="badge badge-warning"><i class="fas fa-star"></i> Top</span>' : ''}
                    </div>
                </div>
            </div>
        `).join('')
        : '<p style="color: var(--gray-500); text-align: center; padding: 2rem;">No hay equipos en esta liga</p>';

    return `
        <div class="modal-liga-details">
            <div class="liga-header">
                ${data.logo_path
                    ? `<img src="${data.logo_path}" alt="${data.name}" class="liga-logo-large">`
                    : `<div class="liga-logo-large liga-logo-placeholder"><i class="fas fa-trophy"></i></div>`
                }
                <div class="liga-info-main">
                    <h3>${data.name}</h3>
                    <div class="liga-meta">
                        ${data.country ? `<span class="meta-item"><i class="fas fa-globe"></i> ${data.country}</span>` : ''}
                        <span class="meta-item"><i class="fas fa-code"></i> ${data.slug}</span>
                        <span class="badge badge-${data.is_active ? 'success' : 'warning'}">
                            ${data.is_active ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                </div>
            </div>

            <div class="liga-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Equipos</div>
                        <div class="stat-value">${data.team_count}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Productos</div>
                        <div class="stat-value">${data.product_count}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                        <i class="fas fa-sort"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Orden</div>
                        <div class="stat-value">${data.display_order}</div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4><i class="fas fa-shield-alt"></i> Equipos de esta Liga</h4>
                <div class="teams-list">
                    ${teamsHtml}
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                <button class="btn btn-primary" onclick="editLiga(${data.league_id})">
                    <i class="fas fa-edit"></i>
                    Editar Liga
                </button>
            </div>
        </div>
    `;
};

// Filtros
document.getElementById('filterCountry')?.addEventListener('change', () => {
    applyFilters();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    applyFilters();
});

function applyFilters() {
    const country = document.getElementById('filterCountry').value;
    const status = document.getElementById('filterStatus').value;

    const url = new URL(window.location);
    if (country) {
        url.searchParams.set('country', country);
    } else {
        url.searchParams.delete('country');
    }

    if (status !== '') {
        url.searchParams.set('is_active', status);
    } else {
        url.searchParams.delete('is_active');
    }

    window.location = url;
}
</script>

<style>
.liga-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.liga-logo-large {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: var(--radius-xl);
    background: white;
    padding: 0.5rem;
    border: 2px solid var(--gray-200);
}

.liga-logo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    color: var(--gray-400);
    font-size: 2.5rem;
}

.liga-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.liga-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.meta-item {
    color: var(--gray-600);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.liga-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
}

.detail-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
}

.detail-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.teams-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.team-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.team-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: var(--radius-md);
}

.team-logo-placeholder {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    color: var(--gray-400);
}

.team-name {
    font-weight: 600;
    color: var(--gray-900);
}

.team-meta {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
