<!-- Hero Section -->
<section class="leagues-hero" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); padding: var(--space-20) 0 var(--space-12); color: white; position: relative; overflow: hidden;">
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: center; max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: var(--space-4); color: white;">
                <i class="fas fa-trophy"></i>
                Todas las Ligas
            </h1>
            <p style="font-size: 1.25rem; opacity: 0.95; margin-bottom: var(--space-6); color: white;">
                Descubre las mejores camisetas de las principales competiciones de fútbol del mundo
            </p>
        </div>
    </div>

    <!-- Background decoration -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; pointer-events: none;">
        <div style="position: absolute; width: 500px; height: 500px; border-radius: 50%; background: white; top: -250px; right: -100px;"></div>
        <div style="position: absolute; width: 300px; height: 300px; border-radius: 50%; background: white; bottom: -150px; left: -50px;"></div>
    </div>
</section>

<!-- Leagues Grid -->
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <?php if (empty($leagues)): ?>
            <div style="text-align: center; padding: var(--space-16) var(--space-4); background: white; border-radius: var(--radius-2xl);">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--gray-300); margin-bottom: var(--space-4);"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: var(--space-3);">No hay ligas disponibles</h3>
                <p style="color: var(--gray-600);">Pronto añadiremos más competiciones.</p>
            </div>
        <?php else: ?>
            <div class="leagues-grid">
                <?php foreach ($leagues as $league): ?>
                    <a href="/ligas/<?= urlencode($league['slug']) ?>" class="league-card">
                        <div class="league-card-header">
                            <?php if (!empty($league['logo_path'])): ?>
                                <img src="<?= htmlspecialchars($league['logo_path']) ?>"
                                     alt="<?= htmlspecialchars($league['name']) ?>"
                                     class="league-logo">
                            <?php else: ?>
                                <div class="league-logo-placeholder">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            <?php endif; ?>
                            <h2 class="league-name"><?= htmlspecialchars($league['name']) ?></h2>
                            <?php if (!empty($league['country'])): ?>
                                <p class="league-country">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($league['country']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="league-card-body">
                            <?php if (!empty($league['description'])): ?>
                                <p class="league-description"><?= htmlspecialchars($league['description']) ?></p>
                            <?php endif; ?>

                            <div class="league-stats">
                                <div class="stat-item">
                                    <i class="fas fa-shield-alt"></i>
                                    <span><?= count($league['teams'] ?? []) ?> equipos</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-tshirt"></i>
                                    <span><?= $league['product_count'] ?? 0 ?> productos</span>
                                </div>
                            </div>

                            <!-- Preview of top teams -->
                            <?php if (!empty($league['teams'])): ?>
                                <div class="league-teams-preview">
                                    <?php foreach (array_slice($league['teams'], 0, 6) as $team): ?>
                                        <div class="team-preview-item" title="<?= htmlspecialchars($team['name']) ?>">
                                            <?php if (!empty($team['logo_path'])): ?>
                                                <img src="<?= htmlspecialchars($team['logo_path']) ?>"
                                                     alt="<?= htmlspecialchars($team['name']) ?>">
                                            <?php else: ?>
                                                <div class="team-initial">
                                                    <?= strtoupper(substr($team['name'], 0, 2)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($league['teams']) > 6): ?>
                                        <div class="team-preview-more">
                                            +<?= count($league['teams']) - 6 ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="league-card-footer">
                            <span class="btn-link">
                                Ver productos
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.leagues-hero {
    position: relative;
}

.leagues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--space-6);
}

.league-card {
    background: white;
    border-radius: var(--radius-2xl);
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid var(--gray-100);
    display: flex;
    flex-direction: column;
}

.league-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(176, 84, 233, 0.2);
    border-color: var(--primary);
}

.league-card-header {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    padding: var(--space-8);
    text-align: center;
    border-bottom: 2px solid var(--gray-200);
}

.league-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin: 0 auto var(--space-4);
    display: block;
}

.league-logo-placeholder {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--space-4);
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.league-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
    transition: color 0.3s;
}

.league-card:hover .league-name {
    color: var(--primary);
}

.league-country {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-2);
    justify-content: center;
}

.league-card-body {
    padding: var(--space-6);
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.league-description {
    color: var(--gray-700);
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.league-stats {
    display: flex;
    gap: var(--space-4);
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--gray-700);
    font-size: 0.875rem;
    font-weight: 500;
}

.stat-item i {
    color: var(--primary);
}

.league-teams-preview {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
    align-items: center;
}

.team-preview-item {
    width: 40px;
    height: 40px;
    border: 2px solid var(--gray-200);
    border-radius: 50%;
    overflow: hidden;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.team-preview-item:hover {
    transform: scale(1.1);
    border-color: var(--primary);
    z-index: 10;
}

.team-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 4px;
}

.team-initial {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--gray-500);
}

.team-preview-more {
    width: 40px;
    height: 40px;
    border: 2px dashed var(--gray-300);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
}

.league-card-footer {
    padding: var(--space-6);
    border-top: 1px solid var(--gray-200);
    text-align: center;
}

.btn-link {
    color: var(--primary);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    transition: all 0.3s;
}

.league-card:hover .btn-link {
    gap: var(--space-3);
}

.btn-link i {
    transition: transform 0.3s;
}

.league-card:hover .btn-link i {
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .leagues-hero h1 {
        font-size: 2.5rem;
    }

    .leagues-grid {
        grid-template-columns: 1fr;
        gap: var(--space-4);
    }

    .league-stats {
        flex-direction: column;
        gap: var(--space-2);
    }
}
</style>
