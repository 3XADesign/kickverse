<!-- League Hero Section -->
<section class="league-hero" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); padding: var(--space-20) 0 var(--space-12); color: white; position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: center; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: var(--space-4); color: white;">
                <?= htmlspecialchars($league['name']) ?>
            </h1>
            <?php if (!empty($league['description'])): ?>
                <p style="font-size: 1.25rem; opacity: 0.95; margin-bottom: var(--space-6);">
                    <?= htmlspecialchars($league['description']) ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($league['country'])): ?>
                <div style="display: inline-flex; align-items: center; gap: var(--space-2); background: rgba(255,255,255,0.2); padding: var(--space-2) var(--space-4); border-radius: var(--radius-full);">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($league['country']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Background decoration -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; pointer-events: none;">
        <div style="position: absolute; width: 500px; height: 500px; border-radius: 50%; background: white; top: -250px; right: -100px;"></div>
        <div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background: white; bottom: -150px; left: -50px;"></div>
    </div>
</section>

<!-- Teams Section (if available) -->
<?php if (!empty($league['teams'])): ?>
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-shield-alt"></i>
                Equipos
            </h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: var(--space-6); margin-top: var(--space-12);">
            <?php foreach ($league['teams'] as $team): ?>
                <a href="/productos?team=<?= urlencode($team['slug']) ?>" class="team-card">
                    <?php if (!empty($team['logo_path'])): ?>
                        <img src="<?= htmlspecialchars($team['logo_path']) ?>" alt="<?= htmlspecialchars($team['name']) ?>" class="team-logo">
                    <?php else: ?>
                        <div class="team-logo-placeholder" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 50%; margin-bottom: var(--space-4); position: relative; overflow: hidden;">
                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: #9ca3af; letter-spacing: -2px;">
                                <?= strtoupper(substr($team['name'], 0, 2)) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <h3 class="team-name"><?= htmlspecialchars($team['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Products Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-tshirt"></i>
                Camisetas Disponibles
            </h2>
            <p class="section-subtitle">
                <?= count($products) ?> <?= count($products) === 1 ? 'producto disponible' : 'productos disponibles' ?>
            </p>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: var(--space-16) var(--space-4); background: white; border-radius: var(--radius-2xl); margin-top: var(--space-12);">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--gray-300); margin-bottom: var(--space-4);"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: var(--space-3);">No hay productos disponibles</h3>
                <p style="color: var(--gray-600); margin-bottom: var(--space-6);">Pronto tendremos camisetas de esta liga.</p>
                <a href="/productos" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Ver todos los productos
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid" style="margin-top: var(--space-12);">
                <?php foreach ($products as $product): ?>
                    <a href="/productos/<?= htmlspecialchars($product['slug']) ?>" class="product-card">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($product['image_url'] ?? '/img/logo.png') ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 onerror="this.src='/img/logo.png'">
                            <?php if ($product['stock_status'] === 'low'): ?>
                                <span class="product-badge badge-warning">Pocas unidades</span>
                            <?php elseif ($product['stock_status'] === 'out'): ?>
                                <span class="product-badge badge-danger">Agotado</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <p class="product-team"><?= htmlspecialchars($product['team_name']) ?></p>
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-footer">
                                <p class="product-price">€<?= number_format($product['price'] ?? 0, 2) ?></p>
                                <?php if (($product['stock_status'] ?? '') !== 'out'): ?>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-shopping-cart"></i>
                                        Añadir
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.league-hero {
    position: relative;
}

.team-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: var(--space-6);
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-xl);
    text-decoration: none;
    transition: all 0.3s;
}

.team-card:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(176, 84, 233, 0.2);
}

.team-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: var(--space-4);
}

.team-logo-placeholder {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: 50%;
    margin-bottom: var(--space-4);
    font-size: 2rem;
    color: var(--gray-400);
}

.team-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    text-align: center;
    transition: color 0.3s;
}

.team-card:hover .team-name {
    color: var(--primary);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-6);
}

.product-card {
    background: white;
    border-radius: var(--radius-xl);
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid var(--gray-100);
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

.product-image {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    background: var(--gray-50);
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-badge {
    position: absolute;
    top: var(--space-3);
    right: var(--space-3);
    padding: var(--space-2) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.product-info {
    padding: var(--space-4);
}

.product-team {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: var(--space-2);
}

.product-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    line-height: 1.4;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

@media (max-width: 768px) {
    .league-hero h1 {
        font-size: 2.5rem;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: var(--space-4);
    }
}
</style>
