<?php
// Vista de Gestión de Clientes
$current_page = 'clientes';
$page_title = 'Gestión de Clientes';
?>

<div class="crm-card">
    <div class="crm-card-header">
        <h2 class="crm-card-title">
            <i class="fas fa-users"></i>
            Clientes
        </h2>
        <div class="crm-card-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar clientes..." class="search-input">
            </div>
            <select id="filterTier" class="form-select" style="width: auto;">
                <option value="">Todos los niveles</option>
                <option value="standard">Standard</option>
                <option value="silver">Silver</option>
                <option value="gold">Gold</option>
                <option value="platinum">Platinum</option>
            </select>
            <select id="filterStatus" class="form-select" style="width: auto;">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
                <option value="blocked">Bloqueado</option>
            </select>
            <button class="btn btn-primary" onclick="window.location.href='/admin/clientes/crear'">
                <i class="fas fa-plus"></i>
                Nuevo Cliente
            </button>
        </div>
    </div>

    <div class="crm-card-body">
        <div class="crm-table-container">
            <table class="crm-table" id="clientesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Email / Contacto</th>
                        <th>Nivel</th>
                        <th>Puntos</th>
                        <th>Pedidos</th>
                        <th>Total Gastado</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="empty-state-title">No hay clientes</p>
                                    <p class="empty-state-text">Comienza agregando tu primer cliente</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="table-row-clickable" onclick="openClienteModal(<?= $cliente['customer_id'] ?>)">
                                <td><strong>#<?= $cliente['customer_id'] ?></strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            <?= strtoupper(substr($cliente['full_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-900);">
                                                <?= htmlspecialchars($cliente['full_name']) ?>
                                            </div>
                                            <?php if ($cliente['telegram_username']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fab fa-telegram"></i> @<?= htmlspecialchars($cliente['telegram_username']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.875rem;">
                                        <?php if ($cliente['email']): ?>
                                            <div style="color: var(--gray-700);">
                                                <i class="fas fa-envelope"></i> <?= htmlspecialchars($cliente['email']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($cliente['phone']): ?>
                                            <div style="color: var(--gray-600); margin-top: 0.25rem;">
                                                <i class="fas fa-phone"></i> <?= htmlspecialchars($cliente['phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($cliente['whatsapp_number']): ?>
                                            <div style="color: var(--gray-600); margin-top: 0.25rem;">
                                                <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($cliente['whatsapp_number']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $tierColors = [
                                        'standard' => 'secondary',
                                        'silver' => 'info',
                                        'gold' => 'warning',
                                        'platinum' => 'purple'
                                    ];
                                    $tierIcons = [
                                        'standard' => 'fa-user',
                                        'silver' => 'fa-medal',
                                        'gold' => 'fa-crown',
                                        'platinum' => 'fa-gem'
                                    ];
                                    $tier = $cliente['loyalty_tier'];
                                    ?>
                                    <span class="badge badge-<?= $tierColors[$tier] ?? 'secondary' ?>">
                                        <i class="fas <?= $tierIcons[$tier] ?? 'fa-user' ?>"></i>
                                        <?= ucfirst($tier) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--primary);"><?= number_format($cliente['loyalty_points']) ?></strong> pts
                                </td>
                                <td>
                                    <strong><?= $cliente['total_orders_count'] ?></strong> pedidos
                                </td>
                                <td>
                                    <strong style="color: var(--success);">€<?= number_format($cliente['total_spent'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'warning',
                                        'blocked' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Activo',
                                        'inactive' => 'Inactivo',
                                        'blocked' => 'Bloqueado'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusColors[$cliente['customer_status']] ?? 'secondary' ?>">
                                        <?= $statusLabels[$cliente['customer_status']] ?? $cliente['customer_status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($cliente['registration_date'])) ?></td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-sm btn-secondary" onclick="openClienteModal(<?= $cliente['customer_id'] ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary" onclick="editCliente(<?= $cliente['customer_id'] ?>)" title="Editar">
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

        <?php if (!empty($clientes) && $total_pages > 1): ?>
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
// Función para abrir modal con detalles del cliente
function openClienteModal(id) {
    const url = new URL(window.location);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
    crmAdmin.checkURLParams();
}

// Función para editar cliente
function editCliente(id) {
    window.location.href = `/admin/clientes/editar/${id}`;
}

// Renderizar contenido del modal
window.renderModalContent = function(data) {
    return `
        <div class="modal-cliente-details">
            <div class="cliente-header">
                <div class="cliente-avatar-large">
                    ${data.full_name.substring(0, 2).toUpperCase()}
                </div>
                <div class="cliente-info-main">
                    <h3>${data.full_name}</h3>
                    <div class="cliente-meta">
                        <span class="badge badge-${data.customer_status === 'active' ? 'success' : 'warning'}">
                            ${data.customer_status === 'active' ? 'Activo' : data.customer_status}
                        </span>
                        <span class="badge badge-purple">
                            <i class="fas fa-crown"></i> ${data.loyalty_tier}
                        </span>
                    </div>
                </div>
            </div>

            <div class="cliente-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Pedidos</div>
                        <div class="stat-value">${data.total_orders_count}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Gastado</div>
                        <div class="stat-value">€${parseFloat(data.total_spent).toFixed(2)}</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Puntos Lealtad</div>
                        <div class="stat-value">${data.loyalty_points}</div>
                    </div>
                </div>
            </div>

            <div class="cliente-details-grid">
                <div class="detail-section">
                    <h4><i class="fas fa-envelope"></i> Información de Contacto</h4>
                    ${data.email ? `<p><strong>Email:</strong> ${data.email}</p>` : ''}
                    ${data.phone ? `<p><strong>Teléfono:</strong> ${data.phone}</p>` : ''}
                    ${data.whatsapp_number ? `<p><strong>WhatsApp:</strong> ${data.whatsapp_number}</p>` : ''}
                    ${data.telegram_username ? `<p><strong>Telegram:</strong> @${data.telegram_username}</p>` : ''}
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Información General</h4>
                    <p><strong>ID:</strong> #${data.customer_id}</p>
                    <p><strong>Registro:</strong> ${new Date(data.registration_date).toLocaleDateString('es-ES')}</p>
                    <p><strong>Idioma:</strong> ${data.preferred_language.toUpperCase()}</p>
                    <p><strong>Newsletter:</strong> ${data.newsletter_subscribed ? 'Sí' : 'No'}</p>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="crmAdmin.closeModal()">
                    Cerrar
                </button>
                <button class="btn btn-primary" onclick="editCliente(${data.customer_id})">
                    <i class="fas fa-edit"></i>
                    Editar Cliente
                </button>
            </div>
        </div>
    `;
};

// Search and filter functionality
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    filterTable();
});

document.getElementById('filterTier')?.addEventListener('change', () => {
    filterTable();
});

document.getElementById('filterStatus')?.addEventListener('change', () => {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const tierFilter = document.getElementById('filterTier').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#clientesTable tbody tr:not(:first-child)');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = text.includes(searchTerm);
        const matchesTier = !tierFilter || text.includes(tierFilter);
        const matchesStatus = !statusFilter || text.includes(statusFilter);

        row.style.display = matchesSearch && matchesTier && matchesStatus ? '' : 'none';
    });
}
</script>

<style>
.cliente-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.cliente-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: 700;
}

.cliente-info-main h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.cliente-meta {
    display: flex;
    gap: 0.5rem;
}

.cliente-stats {
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

.cliente-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
}

.detail-section h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section p {
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}
</style>
