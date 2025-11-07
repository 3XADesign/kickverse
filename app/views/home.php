<!-- Main Hero Banner -->
<section class="hero-banner-slim">
    <div class="container">
        <a href="/productos" class="banner-slide">
            <!-- Hero Product Images (Decorative) -->
            <?php if (!empty($hero_products) && count($hero_products) >= 2): ?>
                <div class="hero-product-images">
                    <img src="<?= htmlspecialchars($hero_products[0]['main_image']) ?>"
                         alt="Product 1"
                         class="hero-product-left"
                         onerror="this.style.display='none'">
                    <img src="<?= htmlspecialchars($hero_products[1]['main_image']) ?>"
                         alt="Product 2"
                         class="hero-product-right"
                         onerror="this.style.display='none'">
                </div>
            <?php endif; ?>

            <div class="banner-content">
                <div class="banner-text">
                    <span class="banner-tag"><?= __('hero.banner_tag') ?></span>
                    <h1><?= __('hero.banner_title') ?></h1>
                    <p><?= __('hero.banner_subtitle') ?></p>
                </div>
                <div class="banner-cta">
                    <span class="btn-shop-now"><?= __('hero.banner_cta') ?></span>
                </div>
            </div>
        </a>
    </div>
</section>

<!-- Category Grid -->
<section class="category-section">
    <div class="container">
        <div class="category-grid-main">
            <!-- Todas las Camisetas -->
            <a href="/productos" class="cat-box cat-box-primary">
                <div class="cat-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <span><?= __('nav.all_jerseys') ?></span>
            </a>

            <!-- Mystery Box -->
            <a href="/mystery-box" class="cat-box cat-box-special">
                <div class="cat-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <span>Mystery Box</span>
                <span class="cat-tag">NUEVO</span>
            </a>

            <!-- Ligas desde BDD -->
            <?php if (!empty($leagues)): ?>
                <?php foreach ($leagues as $league): ?>
                    <a href="/ligas/<?= htmlspecialchars($league['slug']) ?>" class="cat-box">
                        <?php if (!empty($league['logo_path'])): ?>
                            <div class="cat-icon cat-logo">
                                <img src="<?= htmlspecialchars($league['logo_path']) ?>" alt="<?= htmlspecialchars($league['name']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="cat-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($league['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Hero Banner Slim */
.hero-banner-slim {
    padding: var(--space-4) 0;
    background: white;
}

.banner-slide {
    display: block;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    border-radius: var(--radius-xl);
    padding: var(--space-8) var(--space-10);
    text-decoration: none;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease;
}

/* Animated gradient background */
.banner-slide::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 255, 255, 0.08) 50%,
        transparent 70%
    );
    animation: shimmer 8s infinite linear;
    pointer-events: none;
}

/* Floating particles */
.banner-slide::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-image:
        radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.06) 0%, transparent 50%);
    animation: float 15s infinite ease-in-out;
    pointer-events: none;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-20px) scale(1.05);
    }
}

/* Hero Product Images */
.hero-product-images {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.hero-product-left,
.hero-product-right {
    position: absolute;
    height: 130%;
    width: auto;
    object-fit: contain;
    opacity: 1;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
    transition: transform 0.3s ease;
}

.hero-product-left {
    right: 120px;
    top: 50%;
    transform: translateY(-35%);
    z-index: 1;
    animation: floatLeft 6s infinite ease-in-out;
}

.hero-product-right {
    right: 40px;
    top: 50%;
    transform: translateY(-45%);
    z-index: 2;
    animation: floatRight 7s infinite ease-in-out;
}

@keyframes floatLeft {
    0%, 100% {
        transform: translateY(-35%) translateX(0);
    }
    50% {
        transform: translateY(-40%) translateX(-5px);
    }
}

@keyframes floatRight {
    0%, 100% {
        transform: translateY(-45%) translateX(0);
    }
    50% {
        transform: translateY(-50%) translateX(5px);
    }
}

.banner-slide:hover .hero-product-left {
    animation-play-state: paused;
    transform: translateY(-38%) translateX(-8px) rotate(-3deg);
}

.banner-slide:hover .hero-product-right {
    animation-play-state: paused;
    transform: translateY(-48%) translateX(8px) rotate(3deg);
}

.banner-slide:hover {
    transform: scale(1.01);
}

.banner-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
}

.banner-text {
    color: white;
}

.banner-tag {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: var(--space-2);
}

.banner-text h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: var(--space-2);
    color: white;
}

.banner-text p {
    font-size: 1.125rem;
    opacity: 1;
    margin: 0;
    font-weight: 600;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.btn-shop-now {
    display: inline-block;
    background: white;
    color: var(--primary);
    padding: var(--space-3) var(--space-8);
    border-radius: var(--radius-lg);
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.2s;
}

.banner-slide:hover .btn-shop-now {
    transform: scale(1.05);
}

/* Category Grid */
.category-section {
    padding: var(--space-6) 0;
    background: var(--gray-50);
}

.category-grid-main {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: var(--space-3);
}

.cat-box {
    position: relative;
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--space-5) var(--space-3);
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    transition: all 0.2s;
    border: 2px solid transparent;
    text-align: center;
}

.cat-box:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.cat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-lg);
    background: var(--gray-50);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cat-icon i {
    font-size: 1.75rem;
    color: var(--primary);
}

.cat-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: var(--space-2);
}

.cat-box span {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-900);
}

.cat-box-primary .cat-icon {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
}

.cat-box-primary .cat-icon i {
    color: white;
}

.cat-box-special {
    position: relative;
}

.cat-tag {
    position: absolute;
    top: var(--space-2);
    right: var(--space-2);
    background: var(--primary);
    color: white;
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-md);
    font-size: 0.625rem;
    font-weight: 700;
}

/* Responsive */
@media (max-width: 1024px) {
    .hero-product-left,
    .hero-product-right {
        height: 130%; /* Mantener mismo tamaño */
    }

    .hero-product-left {
        right: 80px;
    }

    .hero-product-right {
        right: 10px;
    }
}

@media (max-width: 768px) {
    .banner-slide {
        padding: var(--space-6);
        min-height: 150px;
    }

    .banner-content {
        flex-direction: column;
        text-align: center;
        gap: var(--space-4);
        position: relative;
        z-index: 3;
    }

    .banner-text h1 {
        font-size: 1.75rem;
    }

    /* Adapt hero product images for mobile - MUCHO MAS GRANDES */
    .hero-product-images {
        display: block;
    }

    .hero-product-left,
    .hero-product-right {
        height: 320px; /* Mucho más grandes! */
        opacity: 0.35; /* Un poco más visibles */
        filter: blur(0.3px);
    }

    .hero-product-left {
        right: auto;
        left: -60px; /* Más adentro */
        top: 65%; /* Más abajo */
        transform: translateY(-50%) rotate(-15deg);
        animation: floatLeftMobile 5s infinite ease-in-out;
    }

    .hero-product-right {
        right: -60px; /* Más adentro */
        left: auto;
        top: 65%; /* Más abajo */
        transform: translateY(-50%) rotate(15deg);
        animation: floatRightMobile 5s infinite ease-in-out;
    }

    @keyframes floatLeftMobile {
        0%, 100% {
            transform: translateY(-50%) rotate(-15deg) translateX(0) scale(1);
        }
        50% {
            transform: translateY(-45%) rotate(-12deg) translateX(-8px) scale(1.05);
        }
    }

    @keyframes floatRightMobile {
        0%, 100% {
            transform: translateY(-50%) rotate(15deg) translateX(0) scale(1);
        }
        50% {
            transform: translateY(-45%) rotate(12deg) translateX(8px) scale(1.05);
        }
    }

    .banner-slide:hover .hero-product-left,
    .banner-slide:hover .hero-product-right {
        animation-play-state: running;
    }

    .category-grid-main {
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: var(--space-2);
    }

    .cat-box {
        padding: var(--space-4) var(--space-2);
    }

    .cat-icon {
        width: 50px;
        height: 50px;
    }

    .cat-icon i {
        font-size: 1.5rem;
    }
}

/* Mystery Box Section */
.mystery-box-section {
    padding: var(--space-12) 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    position: relative;
    overflow: hidden;
}

.mystery-box-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-12);
    align-items: center;
    position: relative;
    z-index: 2;
}

.mystery-box-main {
    color: white;
}

.mystery-box-header {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
}

.mystery-badge-new {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: white;
}

.mystery-icon-large {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
}

.mystery-icon-large i {
    font-size: 2rem;
    color: white;
}

.mystery-box-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: var(--space-2);
    color: white;
    line-height: 1.1;
}

.mystery-box-subtitle {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: var(--space-4);
    color: white;
    opacity: 0.95;
}

.mystery-box-description {
    font-size: 1.125rem;
    line-height: 1.6;
    margin-bottom: var(--space-6);
    color: white;
    opacity: 0.9;
}

.mystery-box-features {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
    margin-bottom: var(--space-8);
}

.mystery-feature {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: white;
    font-size: 1.125rem;
}

.mystery-feature i {
    color: white;
    font-size: 1.25rem;
}

.mystery-box-cta {
    display: inline-flex;
    align-items: center;
    gap: var(--space-3);
    background: white;
    color: var(--primary);
    padding: var(--space-4) var(--space-8);
    border-radius: var(--radius-lg);
    font-weight: 700;
    font-size: 1.125rem;
    text-decoration: none;
    transition: all 0.3s;
}

.mystery-box-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.mystery-box-visual {
    position: relative;
    height: 400px;
}

.mystery-box-decoration {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 350px;
    height: 350px;
    background-image: url('/img/mystery-box.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: var(--radius-2xl);
    filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
    animation: floatAnimation 3s ease-in-out infinite;
    overflow: hidden;
}

@keyframes floatAnimation {
    0%, 100% { transform: translate(-50%, -50%) translateY(0px); }
    50% { transform: translate(-50%, -50%) translateY(-20px); }
}

@media (max-width: 1024px) {
    .mystery-box-content {
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }

    .mystery-box-visual {
        height: 300px;
    }

    .mystery-box-decoration {
        width: 280px;
        height: 280px;
    }
}

@media (max-width: 768px) {
    .mystery-box-section {
        padding: var(--space-8) 0;
    }

    .mystery-box-title {
        font-size: 2rem;
    }

    .mystery-box-subtitle {
        font-size: 1.25rem;
    }

    .mystery-box-description {
        font-size: 1rem;
    }

    .mystery-feature {
        font-size: 1rem;
    }

    .mystery-box-visual {
        height: 250px;
    }

    .mystery-box-decoration {
        width: 220px;
        height: 220px;
    }
}
</style>

<script>
// Animar números
document.addEventListener('DOMContentLoaded', function() {
    const stats = document.querySelectorAll('.hero-stat-number');

    const animateNumber = (element) => {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    };

    // Intersection Observer para animar cuando sea visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumber(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });

    stats.forEach(stat => observer.observe(stat));
});
</script>

<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= __('home.featured_title') ?></h2>
            <p class="section-subtitle"><?= __('home.featured_subtitle') ?></p>
        </div>

        <div class="product-grid">
            <?php foreach (array_slice($featured_products, 0, 8) as $product): ?>
                <div class="product-card">
                    <a href="/productos/<?= htmlspecialchars($product['slug']) ?>">
                        <div class="product-card-image">
                            <?php
                            $mainImage = '/img/logo.png';
                            if (!empty($product['images'][0]['image_path'])) {
                                $mainImage = $product['images'][0]['image_path'];
                            } elseif (!empty($product['main_image'])) {
                                $mainImage = $product['main_image'];
                            }
                            ?>
                            <img src="<?= htmlspecialchars($mainImage) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy"
                                 onerror="this.src='/img/logo.png'">
                            <?php if ($product['original_price'] && $product['original_price'] > $product['base_price']): ?>
                                <span class="product-badge">
                                    -<?= round((($product['original_price'] - $product['base_price']) / $product['original_price']) * 100) ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-content">
                            <h3 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h3>
                            <?php if (!empty($product['team_name'])): ?>
                                <p class="product-card-team"><?= htmlspecialchars($product['team_name']) ?></p>
                            <?php endif; ?>
                            <div class="product-card-price">
                                <span class="price-current">€<?= number_format($product['base_price'], 2) ?></span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['base_price']): ?>
                                    <span class="price-original">€<?= number_format($product['original_price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="/productos" class="btn btn-primary">
                <?= __('nav.jerseys') ?>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Mystery Box Section -->
<section class="mystery-box-section">
    <div class="container">
        <div class="mystery-box-content">
            <div class="mystery-box-main">
                <div class="mystery-box-header">
                    <span class="mystery-badge-new"><?= __('mystery_box.badge_new') ?></span>
                    <div class="mystery-icon-large">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
                <h2 class="mystery-box-title"><?= __('mystery_box.main_title') ?></h2>
                <p class="mystery-box-subtitle"><?= __('mystery_box.section_subtitle') ?></p>
                <p class="mystery-box-description">
                    <?= __('mystery_box.section_description') ?>
                </p>
                <div class="mystery-box-features">
                    <div class="mystery-feature">
                        <i class="fas fa-check-circle"></i>
                        <span><?= __('mystery_box.feature_original') ?></span>
                    </div>
                    <div class="mystery-feature">
                        <i class="fas fa-check-circle"></i>
                        <span><?= __('mystery_box.feature_shipping') ?></span>
                    </div>
                    <div class="mystery-feature">
                        <i class="fas fa-check-circle"></i>
                        <span><?= __('mystery_box.feature_from_price') ?></span>
                    </div>
                </div>
                <a href="/mystery-box" class="mystery-box-cta">
                    <span><?= __('mystery_box.cta_discover') ?></span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="mystery-box-visual">
                <div class="mystery-box-decoration"></div>
            </div>
        </div>
    </div>
</section>

<!-- Leagues Section -->
<?php if (!empty($leagues)): ?>
<section class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= __('nav.leagues') ?></h2>
            <p class="section-subtitle"><?= __('home.leagues_subtitle') ?></p>
        </div>

        <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
            <?php foreach ($leagues as $league): ?>
                <a href="/ligas/<?= htmlspecialchars($league['slug']) ?>"
                   class="product-card"
                   style="text-decoration: none;">
                    <div class="product-card-image" style="aspect-ratio: 1; background: white;">
                        <?php if (!empty($league['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($league['logo_path']) ?>"
                                 alt="<?= htmlspecialchars($league['name']) ?>"
                                 style="object-fit: contain; padding: var(--space-6);"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="product-card-content" style="text-align: center;">
                        <h3 class="product-card-title" style="font-size: 1rem;">
                            <?= htmlspecialchars($league['name']) ?>
                        </h3>
                        <?php if (!empty($league['country'])): ?>
                            <p class="product-card-team" style="font-size: 0.875rem;">
                                <?= htmlspecialchars($league['country']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products -->
<?php if (!empty($latest_products)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= __('home.new_title') ?></h2>
            <p class="section-subtitle"><?= __('home.new_subtitle') ?></p>
        </div>

        <div class="product-grid">
            <?php foreach (array_slice($latest_products, 0, 8) as $product): ?>
                <div class="product-card">
                    <a href="/productos/<?= htmlspecialchars($product['slug']) ?>">
                        <div class="product-card-image">
                            <?php
                            $mainImage = '/img/logo.png';
                            if (!empty($product['images'][0]['image_path'])) {
                                $mainImage = $product['images'][0]['image_path'];
                            } elseif (!empty($product['main_image'])) {
                                $mainImage = $product['main_image'];
                            }
                            ?>
                            <img src="<?= htmlspecialchars($mainImage) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy"
                                 onerror="this.src='/img/logo.png'">
                            <?php if ($product['is_featured']): ?>
                                <span class="product-badge" style="background: var(--accent);">NEW</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-content">
                            <h3 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h3>
                            <?php if (!empty($product['team_name'])): ?>
                                <p class="product-card-team"><?= htmlspecialchars($product['team_name']) ?></p>
                            <?php endif; ?>
                            <div class="product-card-price">
                                <span class="price-current">€<?= number_format($product['base_price'], 2) ?></span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['base_price']): ?>
                                    <span class="price-original">€<?= number_format($product['original_price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="/productos" class="btn btn-primary">
                <?= __('home.view_all_products') ?>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Login Modal -->
<style>
/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: var(--radius-2xl);
    max-width: 450px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-xl);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.5rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gray-400);
    cursor: pointer;
    padding: var(--space-2);
    line-height: 1;
    transition: var(--transition);
}

.modal-close:hover {
    color: var(--gray-900);
}

.modal-body {
    padding: var(--space-6);
}

.form-group {
    margin-bottom: var(--space-4);
}

.form-group label {
    display: block;
    margin-bottom: var(--space-2);
    font-weight: 500;
    color: var(--gray-700);
}

.form-control {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
}
</style>

<script>
// Update cart count
document.addEventListener('DOMContentLoaded', function() {
    fetchCartCount();
});

function fetchCartCount() {
    fetch('/api/cart')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data.items) {
                const count = data.data.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = count;
                    cartCountEl.style.display = count > 0 ? 'flex' : 'none';
                }
            }
        })
        .catch(err => console.error('Error fetching cart:', err));
}

// Notification modal functions
function showNotification(type, title, message) {
    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const titleEl = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');

    // Set icon based on type
    if (type === 'success') {
        icon.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i>';
    } else if (type === 'error') {
        icon.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i>';
    } else if (type === 'info') {
        icon.innerHTML = '<i class="fas fa-info-circle" style="color: var(--primary);"></i>';
    }

    titleEl.textContent = title;
    messageEl.textContent = message;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeNotification() {
    document.getElementById('notificationModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>

<!-- Notification Modal -->
<div id="notificationModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeNotification()"></div>
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
            <button onclick="closeNotification()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" style="text-align: center; padding-top: 0;">
            <div id="notificationIcon" style="font-size: 3rem; margin-bottom: var(--space-4);"></div>
            <h3 id="notificationTitle" style="margin-bottom: var(--space-3);"></h3>
            <p id="notificationMessage" style="color: var(--gray-600); margin-bottom: var(--space-6);"></p>
            <button onclick="closeNotification()" class="btn btn-primary" style="width: 100%;">
                <?= __('common.accept') ?>
            </button>
        </div>
    </div>
</div>
