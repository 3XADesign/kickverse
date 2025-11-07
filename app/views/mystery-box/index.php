<!-- Hero Section -->
<section class="hero" style="padding: var(--space-20) 0 var(--space-16); position: relative; overflow: hidden;">
    <!-- Background Image with Blur -->
    <div style="position: absolute; inset: 0; background-image: url('/img/hero-mystery-box.png'); background-size: cover; background-position: center; filter: blur(0.5px); transform: scale(1.01); z-index: 0;"></div>

    <!-- Gradient Overlay -->
    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(176, 84, 233, 0.3) 0%, rgba(193, 81, 212, 0.25) 100%); z-index: 1;"></div>

    <div class="container" style="position: relative; z-index: 2;">
        <div class="hero-content">
            <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: var(--space-2) var(--space-4); border-radius: var(--radius-full); margin-bottom: var(--space-4); text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6), 0 2px 8px rgba(0, 0, 0, 0.4);">
                <i class="fas fa-gift"></i> Mystery Box
            </div>
            <h1 style="font-size: 3.5rem; margin-bottom: var(--space-4); text-shadow: 0 2px 6px rgba(0, 0, 0, 0.7), 0 4px 12px rgba(0, 0, 0, 0.5);"><?= __('mystery_box.title') ?></h1>
            <p style="font-size: 1.25rem; max-width: 700px; margin: 0 auto var(--space-8); text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6), 0 2px 8px rgba(0, 0, 0, 0.4);">
                <?= __('mystery_box.subtitle') ?>
            </p>

            <div class="hero-actions">
                <a href="#boxes" class="btn btn-secondary btn-lg" style="background: white; color: var(--primary);">
                    <i class="fas fa-shopping-bag"></i>
                    <?= __('mystery_box.one_time') ?>
                </a>
                <a href="/suscripciones" class="btn btn-lg" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid white;">
                    <i class="fas fa-star"></i>
                    <?= __('mystery_box.subscribe') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= __('mystery_box.how_it_works') ?></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-8); margin-top: var(--space-12);">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4); color: white; font-size: 2rem; font-weight: 700;">
                    1
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: var(--space-3);"><?= __('mystery_box.step1_title') ?></h3>
                <p style="color: var(--gray-600);"><?= __('mystery_box.step1_desc') ?></p>
            </div>

            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4); color: white; font-size: 2rem; font-weight: 700;">
                    2
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: var(--space-3);"><?= __('mystery_box.step2_title') ?></h3>
                <p style="color: var(--gray-600);"><?= __('mystery_box.step2_desc') ?></p>
            </div>

            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4); color: white; font-size: 2rem; font-weight: 700;">
                    3
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: var(--space-3);"><?= __('mystery_box.step3_title') ?></h3>
                <p style="color: var(--gray-600);"><?= __('mystery_box.step3_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Mystery Boxes -->
<section id="boxes" class="section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Elige tu Mystery Box</h2>
            <p class="section-subtitle">Disponibles en compra única o suscripción mensual</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-8); margin-top: var(--space-12);">
            <!-- Fan Edition -->
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Fan Edition</h3>
                    <div class="pricing-price">
                        <span class="price-amount">€24.99</span>
                    </div>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.authentic_jersey') ?></li>
                    <li><i class="fas fa-check"></i> Equipos de ligas europeas</li>
                    <li><i class="fas fa-check"></i> Temporada actual</li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.premium_packaging') ?></li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.collector_card') ?></li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;" onclick="addMysteryBoxToCart('fan')">
                    <i class="fas fa-shopping-cart"></i>
                    Comprar Ahora
                </button>
                <a href="/suscripciones" class="btn btn-secondary" style="width: 100%; margin-top: var(--space-3);">
                    <i class="fas fa-star"></i>
                    Suscribirme
                </a>
            </div>

            <!-- Premium Edition -->
            <div class="pricing-card featured">
                <div class="featured-badge">Más Popular</div>
                <div class="pricing-header">
                    <h3>Premium Edition</h3>
                    <div class="pricing-price">
                        <span class="price-amount">€49.99</span>
                    </div>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.authentic_jersey') ?></li>
                    <li><i class="fas fa-check"></i> Equipos top de Europa</li>
                    <li><i class="fas fa-check"></i> Ediciones especiales</li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.premium_packaging') ?></li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.collector_card') ?></li>
                    <li><i class="fas fa-check"></i> Regalo sorpresa</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;" onclick="addMysteryBoxToCart('premium')">
                    <i class="fas fa-shopping-cart"></i>
                    Comprar Ahora
                </button>
                <a href="/suscripciones" class="btn btn-secondary" style="width: 100%; margin-top: var(--space-3);">
                    <i class="fas fa-star"></i>
                    Suscribirme
                </a>
            </div>

            <!-- Retro Edition -->
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3>Retro Edition</h3>
                    <div class="pricing-price">
                        <span class="price-amount">€39.99</span>
                    </div>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Camisetas vintage</li>
                    <li><i class="fas fa-check"></i> Equipos legendarios</li>
                    <li><i class="fas fa-check"></i> Ediciones históricas</li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.premium_packaging') ?></li>
                    <li><i class="fas fa-check"></i> <?= __('mystery_box.collector_card') ?></li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;" onclick="addMysteryBoxToCart('retro')">
                    <i class="fas fa-shopping-cart"></i>
                    Comprar Ahora
                </button>
                <a href="/suscripciones" class="btn btn-secondary" style="width: 100%; margin-top: var(--space-3);">
                    <i class="fas fa-star"></i>
                    Suscribirme
                </a>
            </div>
        </div>
    </div>
</section>

<!-- What's Included -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= __('mystery_box.whats_included') ?></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-6); margin-top: var(--space-12);">
            <div class="feature-box">
                <i class="fas fa-certificate" style="font-size: 2.5rem; color: var(--primary); margin-bottom: var(--space-4);"></i>
                <h4 style="margin-bottom: var(--space-2);"><?= __('mystery_box.authentic_jersey') ?></h4>
                <p style="color: var(--gray-600); font-size: 0.95rem;">Camisetas 100% oficiales y auténticas de los mejores clubes</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-globe-europe" style="font-size: 2.5rem; color: var(--primary); margin-bottom: var(--space-4);"></i>
                <h4 style="margin-bottom: var(--space-2);"><?= __('mystery_box.surprise_team') ?></h4>
                <p style="color: var(--gray-600); font-size: 0.95rem;">Equipos de LaLiga, Premier, Serie A, Bundesliga y más</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-box-open" style="font-size: 2.5rem; color: var(--primary); margin-bottom: var(--space-4);"></i>
                <h4 style="margin-bottom: var(--space-2);"><?= __('mystery_box.premium_packaging') ?></h4>
                <p style="color: var(--gray-600); font-size: 0.95rem;">Embalaje exclusivo perfecto para regalar o coleccionar</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-id-card" style="font-size: 2.5rem; color: var(--primary); margin-bottom: var(--space-4);"></i>
                <h4 style="margin-bottom: var(--space-2);"><?= __('mystery_box.collector_card') ?></h4>
                <p style="color: var(--gray-600); font-size: 0.95rem;">Tarjeta coleccionable con información del equipo</p>
            </div>
        </div>
    </div>
</section>

<style>
.pricing-card {
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    position: relative;
    transition: var(--transition);
}

.pricing-card:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.pricing-card.featured {
    border-color: var(--primary);
    border-width: 3px;
    box-shadow: 0 0 0 4px rgba(176, 84, 233, 0.1);
}

.featured-badge {
    position: absolute;
    top: -12px;
    right: var(--space-6);
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    font-weight: 600;
}

.pricing-header {
    text-align: center;
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-6);
    border-bottom: 2px solid var(--gray-100);
}

.pricing-header h3 {
    font-size: 1.5rem;
    margin-bottom: var(--space-4);
}

.pricing-price {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: var(--space-2);
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--space-6);
}

.pricing-features li {
    padding: var(--space-3) 0;
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: var(--gray-700);
}

.pricing-features i {
    color: var(--success);
    font-size: 1.125rem;
}

.feature-box {
    text-align: center;
    padding: var(--space-6);
    background: white;
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
}
</style>

<script>
function addMysteryBoxToCart(type) {
    const prices = {
        'fan': 24.99,
        'premium': 49.99,
        'retro': 39.99
    };

    // TODO: Implement add to cart API call
    showNotification('success', <?= json_encode(__('common.mystery_box_added')) ?>, `Mystery Box ${type.toUpperCase()} agregada al carrito por €${prices[type]}`);
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

<script>
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

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNotification();
    }
});
</script>
