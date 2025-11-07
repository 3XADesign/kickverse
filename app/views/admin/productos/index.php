<?php
// Vista de Gestión de Productos
$current_page = 'productos';
$page_title = 'Gestión de Productos';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-tshirt"></i>
            Productos
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar productos..." class="search-input">
            </div>
            <select id="filterType" class="form-select" style="width: auto;">
                <option value="">Todos los tipos</option>
                <option value="jersey">Camisetas</option>
                <option value="accessory">Accesorios</option>
                <option value="mystery_box">Mystery Box</option>
                <option value="subscription">Suscripción</option>
            </select>
            <select id="filterLeague" class="form-select" style="width: auto;">
                <option value="">Todas las ligas</option>
                <?php if (!empty($leagues)): ?>
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?= $league['league_id'] ?>"><?= htmlspecialchars($league['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
            <button class="btn btn-primary" onclick="window.location.href='/admin/productos/crear'">
                <i class="fas fa-plus"></i>
                Nuevo Producto
            </button>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="productosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Equipo/Liga</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-tshirt"></i>
                                    <p class="empty-state-title">No hay productos</p>
                                    <p class="empty-state-text">Comienza agregando tu primer producto</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr class="table-row-clickable"
                                onclick="openProductoModal(<?= $producto['product_id'] ?>)"
                                data-type="<?= $producto['product_type'] ?>"
                                data-league="<?= $producto['league_id'] ?? '' ?>"
                                data-status="<?= $producto['is_active'] ? 'active' : 'inactive' ?>">
                                <td><strong>#<?= $producto['product_id'] ?></strong></td>
                                <td>
                                    <?php if (!empty($producto['main_image'])): ?>
                                        <img src="<?= htmlspecialchars($producto['main_image']) ?>"
                                             alt="<?= htmlspecialchars($producto['name']) ?>"
                                             class="table-img">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: var(--gray-200); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-tshirt" style="color: var(--gray-400);"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="max-width: 250px;">
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?= htmlspecialchars($producto['name']) ?>
                                        </div>
                                        <?php if ($producto['season']): ?>
                                            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                                <i class="fas fa-calendar"></i> <?= htmlspecialchars($producto['season']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($producto['is_featured']): ?>
                                            <span class="badge badge-warning" style="margin-top: 0.25rem; font-size: 0.65rem;">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($producto['team_name']): ?>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <?php if ($producto['team_logo']): ?>
                                                <img src="<?= htmlspecialchars($producto['team_logo']) ?>"
                                                     alt="<?= htmlspecialchars($producto['team_name']) ?>"
                                                     style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php endif; ?>
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.875rem;">
                                                    <?= htmlspecialchars($producto['team_name']) ?>
                                                </div>
                                                <?php if ($producto['league_name']): ?>
                                                    <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                        <?= htmlspecialchars($producto['league_name']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($producto['league_name']): ?>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <?php if ($producto['league_logo']): ?>
                                                <img src="<?= htmlspecialchars($producto['league_logo']) ?>"
                                                     alt="<?= htmlspecialchars($producto['league_name']) ?>"
                                                     style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php endif; ?>
                                            <span style="font-weight: 600; font-size: 0.875rem;">
                                                <?= htmlspecialchars($producto['league_name']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $typeColors = [
                                        'jersey' => 'info',
                                        'accessory' => 'secondary',
                                        'mystery_box' => 'purple',
                                        'subscription' => 'warning'
                                    ];
                                    $typeLabels = [
                                        'jersey' => 'Camiseta',
                                        'accessory' => 'Accesorio',
                                        'mystery_box' => 'Mystery Box',
                                        'subscription' => 'Suscripción'
                                    ];
                                    $typeIcons = [
                                        'jersey' => 'fa-tshirt',
                                        'accessory' => 'fa-box',
                                        'mystery_box' => 'fa-gift',
                                        'subscription' => 'fa-calendar-check'
                                    ];
                                    $type = $producto['product_type'];
                                    ?>
                                    <span class="badge badge-<?= $typeColors[$type] ?? 'secondary' ?>">
                                        <i class="fas <?= $typeIcons[$type] ?? 'fa-box' ?>"></i>
                                        <?= $typeLabels[$type] ?? ucfirst($type) ?>
                                    </span>
                                    <?php if ($producto['jersey_type']): ?>
                                        <div style="font-size: 0.7rem; color: var(--gray-500); margin-top: 0.25rem;">
                                            <?= ucfirst($producto['jersey_type']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <div style="font-weight: 700; color: var(--success); font-size: 1.1rem;">
                                            €<?= number_format($producto['base_price'], 2) ?>
                                        </div>
                                        <?php if ($producto['original_price'] && $producto['original_price'] > $producto['base_price']): ?>
                                            <div style="font-size: 0.75rem; color: var(--gray-400); text-decoration: line-through;">
                                                €<?= number_format($producto['original_price'], 2) ?>
                                            </div>
                                            <div style="font-size: 0.7rem; color: var(--danger); font-weight: 600;">
                                                -<?= round((($producto['original_price'] - $producto['base_price']) / $producto['original_price']) * 100) ?>%
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $stock = $producto['stock_quantity'];
                                    $stockColor = $stock > 20 ? 'success' : ($stock > 5 ? 'warning' : 'danger');
                                    ?>
                                    <div>
                                        <span class="badge badge-<?= $stockColor ?>">
                                            <?= $stock ?> uds.
                                        </span>
                                        <?php if ($producto['total_variants'] > 0): ?>
                                            <div style="font-size: 0.7rem; color: var(--gray-500); margin-top: 0.25rem;">
                                                <?= $producto['total_variants'] ?> tallas
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $producto['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $producto['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="openProductoModal(<?= $producto['product_id'] ?>)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary"
                                                onclick="editProducto(<?= $producto['product_id'] ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($productos) && $total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-link <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
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
    padding-left: 2.5rem;
    width: 250px;
}

.table-row-clickable {
    cursor: pointer;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.pagination-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--gray-700);
    font-weight: 500;
    transition: var(--transition);
}

.pagination-link:hover {
    background: var(--gray-100);
    border-color: var(--primary);
}

.pagination-link.active {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    border-color: transparent;
}

.badge-secondary {
    background: #e2e8f0;
    color: #475569;
}

@media (max-width: 768px) {
    .crm-card-actions {
        width: 100%;
        flex-direction: column;
    }

    .search-input {
        width: 100%;
    }
}
</style>

<script>
// Función para abrir modal con detalles del producto
function openProductoModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para editar producto
function editProducto(id) {
    window.location.href = `/admin/productos/editar/${id}`;
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    // Parse images
    const images = data.images || [];
    const mainImage = images.find(img => img.image_type === 'main') || images[0];

    // Parse variants
    const variants = data.variants || [];
    const variantsByCategory = {};
    variants.forEach(variant => {
        const cat = variant.size_category || 'general';
        if (!variantsByCategory[cat]) variantsByCategory[cat] = [];
        variantsByCategory[cat].push(variant);
    });

    // Type labels and colors
    const typeLabels = {
        'jersey': 'Camiseta',
        'accessory': 'Accesorio',
        'mystery_box': 'Mystery Box',
        'subscription': 'Suscripción'
    };

    const typeColors = {
        'jersey': 'info',
        'accessory': 'secondary',
        'mystery_box': 'purple',
        'subscription': 'warning'
    };

    const jerseyTypeLabels = {
        'home': 'Local',
        'away': 'Visitante',
        'third': 'Tercera',
        'goalkeeper': 'Portero',
        'retro': 'Retro'
    };

    return `
        <div class="modal-producto-details">
            <div class="producto-header">
                ${mainImage ? `
                    <div class="producto-image-main">
                        <img src="${mainImage.image_path}" alt="${data.name}">
                    </div>
                ` : `
                    <div class="producto-image-placeholder">
                        <i class="fas fa-tshirt"></i>
                    </div>
                `}
                <div class="producto-info-main">
                    <div class="producto-meta-badges">
                        <span class="badge badge-${typeColors[data.product_type] || 'secondary'}">
                            ${typeLabels[data.product_type] || data.product_type}
                        </span>
                        ${data.is_active ?
                            '<span class="badge badge-success">Activo</span>' :
                            '<span class="badge badge-danger">Inactivo</span>'
                        }
                        ${data.is_featured ?
                            '<span class="badge badge-warning"><i class="fas fa-star"></i> Destacado</span>' :
                            ''
                        }
                    </div>
                    <h3>${data.name}</h3>
                    ${data.team_name ? `
                        <div class="producto-team-info">
                            ${data.team_logo ? `<img src="${data.team_logo}" alt="${data.team_name}">` : ''}
                            <div>
                                <div class="team-name">${data.team_name}</div>
                                ${data.league_name ? `<div class="league-name">${data.league_name}</div>` : ''}
                            </div>
                        </div>
                    ` : ''}
                    ${data.jersey_type ? `
                        <p><strong>Tipo:</strong> ${jerseyTypeLabels[data.jersey_type] || data.jersey_type}</p>
                    ` : ''}
                    ${data.season ? `<p><strong>Temporada:</strong> ${data.season}</p>` : ''}
                    ${data.version ? `<p><strong>Versión:</strong> ${data.version === 'player' ? 'Jugador' : 'Fan'}</p>` : ''}
                </div>
            </div>

            ${images.length > 1 ? `
                <div class="producto-gallery">
                    <h4><i class="fas fa-images"></i> Galería de Imágenes</h4>
                    <div class="gallery-grid">
                        ${images.map(img => `
                            <div class="gallery-item">
                                <img src="${img.image_path}" alt="${img.alt_text || data.name}">
                                <div class="gallery-type">${img.image_type}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            <div class="producto-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Precio</div>
                        <div class="stat-value">€${parseFloat(data.base_price).toFixed(2)}</div>
                        ${data.original_price && data.original_price > data.base_price ? `
                            <div class="stat-detail">
                                <span style="text-decoration: line-through; color: var(--gray-400);">€${parseFloat(data.original_price).toFixed(2)}</span>
                                <span style="color: var(--danger); font-weight: 600; margin-left: 0.5rem;">
                                    -${Math.round(((data.original_price - data.base_price) / data.original_price) * 100)}%
                                </span>
                            </div>
                        ` : ''}
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Stock Total</div>
                        <div class="stat-value">${data.stock_quantity}</div>
                        ${variants.length > 0 ? `<div class="stat-detail">${variants.length} variantes</div>` : ''}
                    </div>
                </div>
                ${data.has_personalization_available ? `
                    <div class="stat-item">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Personalización</div>
                            <div class="stat-value">+€${parseFloat(data.personalization_price).toFixed(2)}</div>
                        </div>
                    </div>
                ` : ''}
                ${data.has_patches_available ? `
                    <div class="stat-item">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Parches</div>
                            <div class="stat-value">+€${parseFloat(data.patches_price).toFixed(2)}</div>
                        </div>
                    </div>
                ` : ''}
            </div>

            ${data.description ? `
                <div class="detail-section">
                    <h4><i class="fas fa-align-left"></i> Descripción</h4>
                    <p>${data.description}</p>
                </div>
            ` : ''}

            ${variants.length > 0 ? `
                <div class="detail-section">
                    <h4><i class="fas fa-ruler"></i> Tallas Disponibles</h4>
                    ${Object.keys(variantsByCategory).map(category => `
                        <div class="variants-category">
                            <h5>${category === 'general' ? 'Tallas Generales' :
                                  category === 'player' ? 'Tallas Jugador' :
                                  category === 'kids' ? 'Tallas Niño' :
                                  category === 'tracksuit' ? 'Chandal' : category}</h5>
                            <div class="variants-grid">
                                ${variantsByCategory[category].map(variant => {
                                    const stockColor = variant.stock_quantity > 20 ? 'success' :
                                                      variant.stock_quantity > 5 ? 'warning' : 'danger';
                                    return `
                                        <div class="variant-item ${variant.stock_quantity === 0 ? 'out-of-stock' : ''}">
                                            <div class="variant-size">${variant.size}</div>
                                            <div class="variant-stock badge badge-${stockColor}">
                                                ${variant.stock_quantity} uds.
                                            </div>
                                            ${variant.sku ? `<div class="variant-sku">SKU: ${variant.sku}</div>` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}

            <div class="producto-details-grid">
                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Información General</h4>
                    <p><strong>ID:</strong> #${data.product_id}</p>
                    <p><strong>Slug:</strong> ${data.slug}</p>
                    <p><strong>Creado:</strong> ${new Date(data.created_at).toLocaleDateString('es-ES')}</p>
                    ${data.updated_at ? `<p><strong>Actualizado:</strong> ${new Date(data.updated_at).toLocaleDateString('es-ES')}</p>` : ''}
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                <button class="btn btn-primary" onclick="editProducto(${data.product_id})">
                    <i class="fas fa-edit"></i>
                    Editar Producto
                </button>
            </div>
        </div>
    `;
};

// Search and filter functionality
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    filterTable();
});

document.getElementById('filterType')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterLeague')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('filterType').value;
    const leagueFilter = document.getElementById('filterLeague').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#productosTable tbody tr:not(:first-child)');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowType = row.getAttribute('data-type');
        const rowLeague = row.getAttribute('data-league');
        const rowStatus = row.getAttribute('data-status');

        const matchesSearch = text.includes(searchTerm);
        const matchesType = !typeFilter || rowType === typeFilter;
        const matchesLeague = !leagueFilter || rowLeague === leagueFilter;
        const matchesStatus = !statusFilter || rowStatus === statusFilter;

        row.style.display = matchesSearch && matchesType && matchesLeague && matchesStatus ? '' : 'none';
    });
}
</script>

<style>
/* Modal Producto Details */
.producto-header {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--gray-200);
}

.producto-image-main {
    flex-shrink: 0;
}

.producto-image-main img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: var(--radius-xl);
    border: 2px solid var(--gray-200);
}

.producto-image-placeholder {
    width: 200px;
    height: 200px;
    background: var(--gray-100);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--gray-400);
}

.producto-info-main {
    flex: 1;
}

.producto-meta-badges {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.producto-info-main h3 {
    margin: 0 0 1rem 0;
    font-size: 1.75rem;
    color: var(--gray-900);
}

.producto-team-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.producto-team-info img {
    width: 48px;
    height: 48px;
    object-fit: contain;
}

.team-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--gray-900);
}

.league-name {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.producto-gallery {
    margin-bottom: 2rem;
}

.producto-gallery h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.gallery-item {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 2px solid var(--gray-200);
}

.gallery-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.gallery-type {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
    text-align: center;
}

.producto-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    align-items: flex-start;
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
    flex-shrink: 0;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stat-detail {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
}

.producto-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
}

.detail-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section h5 {
    font-size: 0.9rem;
    margin: 1rem 0 0.5rem 0;
    color: var(--gray-700);
}

.detail-section p {
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.variants-category {
    margin-bottom: 1.5rem;
}

.variants-category:last-child {
    margin-bottom: 0;
}

.variants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.75rem;
}

.variant-item {
    padding: 0.75rem;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    text-align: center;
    transition: var(--transition);
}

.variant-item:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
}

.variant-item.out-of-stock {
    opacity: 0.5;
    background: var(--gray-100);
}

.variant-size {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.variant-stock {
    margin-bottom: 0.25rem;
}

.variant-sku {
    font-size: 0.7rem;
    color: var(--gray-500);
}

@media (max-width: 768px) {
    .producto-header {
        flex-direction: column;
    }

    .producto-image-main img,
    .producto-image-placeholder {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }

    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }

    .variants-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
}
</style>
