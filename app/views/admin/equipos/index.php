<?php
// Vista de Gestión de Equipos
$current_page = 'equipos';
$page_title = 'Gestión de Equipos';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-shield-alt"></i>
            Equipos
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar equipos..." class="search-input">
            </div>
            <select id="filterLeague" class="form-select" style="width: auto;">
                <option value="">Todas las ligas</option>
                <?php foreach ($leagues as $league): ?>
                    <option value="<?= $league['league_id'] ?>"
                            <?= (isset($filters['league_id']) && $filters['league_id'] == $league['league_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($league['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="filterTopTeam" class="form-select" style="width: auto;">
                <option value="">Todos</option>
                <option value="1" <?= (isset($filters['is_top_team']) && $filters['is_top_team'] === '1') ? 'selected' : '' ?>>Top Teams</option>
                <option value="0" <?= (isset($filters['is_top_team']) && $filters['is_top_team'] === '0') ? 'selected' : '' ?>>Normal</option>
            </select>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="1" <?= (isset($filters['is_active']) && $filters['is_active'] === '1') ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= (isset($filters['is_active']) && $filters['is_active'] === '0') ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i>
                Nuevo Equipo
            </button>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="equiposTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Logo</th>
                        <th>Nombre</th>
                        <th>Liga</th>
                        <th style="width: 100px;">Top Team</th>
                        <th style="width: 100px;">Productos</th>
                        <th style="width: 80px;">Orden</th>
                        <th style="width: 100px;">Estado</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipos)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-shield-alt"></i>
                                    <p class="empty-state-title">No hay equipos</p>
                                    <p class="empty-state-text">Comienza agregando tu primer equipo</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipos as $equipo): ?>
                            <tr class="table-row-clickable" onclick="openEquipoModal(<?= $equipo['team_id'] ?>)">
                                <td><strong>#<?= $equipo['team_id'] ?></strong></td>
                                <td>
                                    <?php if (!empty($equipo['logo_path'])): ?>
                                        <img src="<?= htmlspecialchars($equipo['logo_path']) ?>"
                                             alt="<?= htmlspecialchars($equipo['name']) ?>"
                                             class="table-img">
                                    <?php else: ?>
                                        <div class="table-img-placeholder">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--gray-900);">
                                        <?= htmlspecialchars($equipo['name']) ?>
                                    </strong>
                                    <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                        <?= htmlspecialchars($equipo['slug']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($equipo['league_name'])): ?>
                                        <span class="badge badge-info">
                                            <?= htmlspecialchars($equipo['league_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($equipo['is_top_team']): ?>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-star"></i> Top Team
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= $equipo['product_count'] ?> productos
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?= $equipo['display_order'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $equipo['is_active'] ? 'success' : 'warning' ?>">
                                        <?= $equipo['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="openEquipoModal(<?= $equipo['team_id'] ?>)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="editEquipo(<?= $equipo['team_id'] ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($equipo['product_count'] == 0): ?>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="deleteEquipo(<?= $equipo['team_id'] ?>)"
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
.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 1rem;
    color: var(--gray-400);
}

.search-input {
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    color: var(--gray-700);
    background: white;
    width: 250px;
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

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

    .search-input,
    .form-select {
        width: 100% !important;
    }
}
</style>

<script>
// Función para abrir modal con detalles del equipo
function openEquipoModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para crear nuevo equipo
function openCreateModal() {
    // TODO: Implementar modal de creación
    showWarningModal('Funcionalidad de creación en desarrollo');
}

// Función para editar equipo
function editEquipo(id) {
    // TODO: Implementar modal de edición
    showWarningModal('Funcionalidad de edición en desarrollo. ID: ' + id);
}

// Función para eliminar equipo
function deleteEquipo(id) {
    showConfirmModal('¿Estás seguro de eliminar este equipo?', () => {
        fetch(`/admin/equipos/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Equipo eliminado correctamente', () => {
                    location.reload();
                });
            } else {
                showErrorModal(data.error || 'Error al eliminar el equipo');
            }
        })
        .catch(error => {
            showErrorModal('Error al eliminar el equipo');
        });
    });
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    const productsHtml = data.products && data.products.length > 0
        ? data.products.map(product => `
            <div class="product-item">
                <div class="product-info">
                    <div class="product-name">${product.name}</div>
                    <div class="product-meta">
                        <span class="badge badge-${product.product_type === 'jersey' ? 'primary' : 'info'}">
                            ${product.product_type}
                        </span>
                        ${product.season ? `<span class="meta-text">${product.season}</span>` : ''}
                        ${product.jersey_type ? `<span class="meta-text">${product.jersey_type}</span>` : ''}
                    </div>
                </div>
                <div class="product-stats">
                    <div class="product-price">€${parseFloat(product.base_price).toFixed(2)}</div>
                    <div class="product-stock">
                        <span class="badge badge-${product.total_stock > 0 ? 'success' : 'warning'}">
                            ${product.total_stock} en stock
                        </span>
                    </div>
                </div>
            </div>
        `).join('')
        : '<p style="color: var(--gray-500); text-align: center; padding: 2rem;">No hay productos de este equipo</p>';

    return `
        <div class="modal-equipo-details">
            <div class="equipo-header">
                ${data.logo_path
                    ? `<img src="${data.logo_path}" alt="${data.name}" class="equipo-logo-large">`
                    : `<div class="equipo-logo-large equipo-logo-placeholder"><i class="fas fa-shield-alt"></i></div>`
                }
                <div class="equipo-info-main">
                    <h3>${data.name}</h3>
                    <div class="equipo-meta">
                        ${data.league_name ? `<span class="badge badge-info"><i class="fas fa-trophy"></i> ${data.league_name}</span>` : ''}
                        ${data.is_top_team ? '<span class="badge badge-warning"><i class="fas fa-star"></i> Top Team</span>' : ''}
                        <span class="badge badge-${data.is_active ? 'success' : 'warning'}">
                            ${data.is_active ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                    <div style="margin-top: 0.5rem; color: var(--gray-600); font-size: 0.875rem;">
                        <i class="fas fa-code"></i> ${data.slug}
                    </div>
                </div>
            </div>

            <div class="equipo-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Productos</div>
                        <div class="stat-value">${data.products ? data.products.length : 0}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Liga</div>
                        <div class="stat-value" style="font-size: 1rem;">${data.league_name || 'N/A'}</div>
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

            ${data.league_country ? `
                <div class="info-box">
                    <i class="fas fa-globe"></i>
                    <span><strong>País de la Liga:</strong> ${data.league_country}</span>
                </div>
            ` : ''}

            <div class="detail-section">
                <h4><i class="fas fa-box"></i> Productos del Equipo</h4>
                <div class="products-list">
                    ${productsHtml}
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                <button class="btn btn-primary" onclick="editEquipo(${data.team_id})">
                    <i class="fas fa-edit"></i>
                    Editar Equipo
                </button>
            </div>
        </div>
    `;
};

// Búsqueda
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    filterTable();
});

// Filtros
document.getElementById('filterLeague')?.addEventListener('change', () => {
    applyFilters();
});

document.getElementById('filterTopTeam')?.addEventListener('change', () => {
    applyFilters();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    applyFilters();
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#equiposTable tbody tr:not(:first-child)');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function applyFilters() {
    const league = document.getElementById('filterLeague').value;
    const topTeam = document.getElementById('filterTopTeam').value;
    const status = document.getElementById('filterStatus').value;

    const url = new URL(window.location);

    if (league) {
        url.searchParams.set('league_id', league);
    } else {
        url.searchParams.delete('league_id');
    }

    if (topTeam !== '') {
        url.searchParams.set('is_top_team', topTeam);
    } else {
        url.searchParams.delete('is_top_team');
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
.equipo-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.equipo-logo-large {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: var(--radius-xl);
    background: white;
    padding: 0.5rem;
    border: 2px solid var(--gray-200);
}

.equipo-logo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    color: var(--gray-400);
    font-size: 2.5rem;
}

.equipo-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.equipo-meta {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.equipo-stats {
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

.info-box {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    color: var(--gray-700);
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

.products-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.product-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.product-meta {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.meta-text {
    font-size: 0.75rem;
    color: var(--gray-600);
}

.product-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.product-price {
    font-weight: 700;
    color: var(--success);
    font-size: 1.125rem;
}

.product-stock {
    font-size: 0.75rem;
}
</style>
