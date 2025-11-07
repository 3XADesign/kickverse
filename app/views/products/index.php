<!-- Breadcrumb -->
<div style="background: var(--gray-50); padding: var(--space-4) 0; border-bottom: 1px solid var(--gray-200);">
    <div class="container">
        <nav style="display: flex; align-items: center; gap: var(--space-2); font-size: 0.875rem; color: var(--gray-600);">
            <a href="/" style="color: var(--gray-600); transition: var(--transition);"><?= __('nav.home') ?></a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span style="color: var(--gray-900); font-weight: 500;"><?= __('product.all_products') ?></span>
        </nav>
    </div>
</div>

<!-- Catalog Section -->
<section class="section">
    <div class="container">
        <div class="catalog-layout">
            <!-- Sidebar Filters -->
            <aside class="catalog-sidebar">
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-filter"></i>
                        <?= __('product.filters') ?>
                    </h3>

                    <!-- League Filter -->
                    <div class="filter-group">
                        <h4 class="filter-group-title"><?= __('product.filter_by_league') ?></h4>
                        <div class="filter-options">
                            <a href="/productos" class="filter-option <?= empty($_GET['league']) ? 'active' : '' ?>">
                                <i class="fas fa-globe"></i>
                                <?= __('product.all_leagues') ?>
                            </a>
                            <?php if (!empty($leagues)): ?>
                                <?php foreach ($leagues as $league): ?>
                                    <a href="/productos?league=<?= htmlspecialchars($league['slug']) ?>"
                                       class="filter-option <?= ($_GET['league'] ?? '') === $league['slug'] ? 'active' : '' ?>">
                                        <?php if (!empty($league['logo_path'])): ?>
                                            <img src="<?= htmlspecialchars($league['logo_path']) ?>"
                                                 alt="<?= htmlspecialchars($league['name']) ?>"
                                                 style="width: 20px; height: 20px; object-fit: contain;">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($league['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <h4 class="filter-group-title"><?= __('product.filter_by_price') ?></h4>
                        <div class="filter-options">
                            <?php
                            $currentLeague = isset($_GET['league']) ? '&league=' . urlencode($_GET['league']) : '';
                            $priceRange = $_GET['price_range'] ?? '';
                            ?>
                            <a href="/productos?<?= ltrim($currentLeague, '&') ?>" class="filter-option <?= empty($priceRange) ? 'active' : '' ?>">
                                <i class="fas fa-globe"></i>
                                <?= __('product.all_prices') ?>
                            </a>
                            <a href="/productos?price_range=0-30<?= $currentLeague ?>" class="filter-option <?= $priceRange === '0-30' ? 'active' : '' ?>">
                                <i class="fas fa-euro-sign"></i>
                                €0 - €30
                            </a>
                            <a href="/productos?price_range=30-60<?= $currentLeague ?>" class="filter-option <?= $priceRange === '30-60' ? 'active' : '' ?>">
                                <i class="fas fa-euro-sign"></i>
                                €30 - €60
                            </a>
                            <a href="/productos?price_range=60-100<?= $currentLeague ?>" class="filter-option <?= $priceRange === '60-100' ? 'active' : '' ?>">
                                <i class="fas fa-euro-sign"></i>
                                €60 - €100
                            </a>
                            <a href="/productos?price_range=100+<?= $currentLeague ?>" class="filter-option <?= $priceRange === '100+' ? 'active' : '' ?>">
                                <i class="fas fa-euro-sign"></i>
                                €100+
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="catalog-main">
                <!-- Mobile Filters Accordion -->
                <div class="mobile-filters-card">
                    <button class="mobile-filters-header" onclick="toggleMobileFilters()">
                        <div>
                            <i class="fas fa-filter"></i>
                            <span><?= __('product.filters') ?></span>
                        </div>
                        <i class="fas fa-chevron-down mobile-filters-arrow"></i>
                    </button>
                    <div class="mobile-filters-content">
                        <!-- League Filter -->
                        <div class="mobile-filter-group">
                            <h4 class="mobile-filter-title"><?= __('product.filter_by_league') ?></h4>
                            <div class="mobile-filter-options">
                                <a href="/productos" class="mobile-filter-option <?= empty($_GET['league']) ? 'active' : '' ?>">
                                    <i class="fas fa-globe"></i>
                                    <?= __('product.all_leagues') ?>
                                </a>
                                <?php if (!empty($leagues)): ?>
                                    <?php foreach ($leagues as $league): ?>
                                        <a href="/productos?league=<?= htmlspecialchars($league['slug']) ?>"
                                           class="mobile-filter-option <?= ($_GET['league'] ?? '') === $league['slug'] ? 'active' : '' ?>">
                                            <?php if (!empty($league['logo_path'])): ?>
                                                <img src="<?= htmlspecialchars($league['logo_path']) ?>"
                                                     alt="<?= htmlspecialchars($league['name']) ?>"
                                                     style="width: 20px; height: 20px; object-fit: contain;">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($league['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="mobile-filter-group">
                            <h4 class="mobile-filter-title"><?= __('product.filter_by_price') ?></h4>
                            <div class="mobile-filter-options">
                                <?php
                                $currentLeague = isset($_GET['league']) ? '&league=' . urlencode($_GET['league']) : '';
                                $priceRange = $_GET['price_range'] ?? '';
                                ?>
                                <a href="/productos?<?= ltrim($currentLeague, '&') ?>" class="mobile-filter-option <?= empty($priceRange) ? 'active' : '' ?>">
                                    <i class="fas fa-globe"></i>
                                    <?= __('product.all_prices') ?>
                                </a>
                                <a href="/productos?price_range=0-30<?= $currentLeague ?>" class="mobile-filter-option <?= $priceRange === '0-30' ? 'active' : '' ?>">
                                    <i class="fas fa-euro-sign"></i>
                                    €0 - €30
                                </a>
                                <a href="/productos?price_range=30-60<?= $currentLeague ?>" class="mobile-filter-option <?= $priceRange === '30-60' ? 'active' : '' ?>">
                                    <i class="fas fa-euro-sign"></i>
                                    €30 - €60
                                </a>
                                <a href="/productos?price_range=60-100<?= $currentLeague ?>" class="mobile-filter-option <?= $priceRange === '60-100' ? 'active' : '' ?>">
                                    <i class="fas fa-euro-sign"></i>
                                    €60 - €100
                                </a>
                                <a href="/productos?price_range=100+<?= $currentLeague ?>" class="mobile-filter-option <?= $priceRange === '100+' ? 'active' : '' ?>">
                                    <i class="fas fa-euro-sign"></i>
                                    €100+
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header with Sort -->
                <div class="catalog-header">
                    <div>
                        <h1 class="catalog-title"><?= htmlspecialchars($page_title ?? __('product.all_products')) ?></h1>
                        <p class="catalog-count">
                            <?= $total_products ?> <?= $total_products === 1 ? __('product.product_singular') : __('product.products_plural') ?>
                        </p>
                    </div>

                    <div class="catalog-sort">
                        <label for="sort" style="font-size: 0.875rem; color: var(--gray-600); margin-right: var(--space-2);">
                            <?= __('product.sort_by') ?>:
                        </label>
                        <select id="sort" class="form-control" style="width: auto; padding: var(--space-2) var(--space-4);">
                            <option value="featured"><?= __('product.featured') ?></option>
                            <option value="newest"><?= __('product.newest') ?></option>
                            <option value="price_low"><?= __('product.price_low') ?></option>
                            <option value="price_high"><?= __('product.price_high') ?></option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (!empty($products)): ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <a href="/productos/<?= htmlspecialchars($product['slug']) ?>">
                                    <div class="product-card-image">
                                        <?php
                                        // Get main image
                                        $mainImage = '/img/logo.png';
                                        if (!empty($product['main_image'])) {
                                            $mainImage = $product['main_image'];
                                        } elseif (!empty($product['image_path'])) {
                                            $mainImage = $product['image_path'];
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($mainImage) ?>"
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             loading="lazy"
                                             onerror="this.src='/img/logo.png'">
                                        <?php if (!empty($product['original_price']) && $product['original_price'] > $product['base_price']): ?>
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
                                            <?php if (!empty($product['original_price']) && $product['original_price'] > $product['base_price']): ?>
                                                <span class="price-original">€<?= number_format($product['original_price'], 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php
                            $queryParams = [];
                            if (!empty($_GET['league'])) $queryParams[] = 'league=' . urlencode($_GET['league']);
                            if (!empty($_GET['price_range'])) $queryParams[] = 'price_range=' . urlencode($_GET['price_range']);
                            $baseUrl = '/productos?' . (!empty($queryParams) ? implode('&', $queryParams) . '&' : '');
                            ?>

                            <?php if ($current_page > 1): ?>
                                <a href="<?= $baseUrl ?>page=<?= $current_page - 1 ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $current_page - 2);
                            $end = min($total_pages, $current_page + 2);

                            if ($start > 1): ?>
                                <a href="<?= $baseUrl ?>page=1" class="pagination-number">1</a>
                                <?php if ($start > 2): ?>
                                    <span class="pagination-dots">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <a href="<?= $baseUrl ?>page=<?= $i ?>"
                                   class="pagination-number <?= $i === $current_page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($end < $total_pages): ?>
                                <?php if ($end < $total_pages - 1): ?>
                                    <span class="pagination-dots">...</span>
                                <?php endif; ?>
                                <a href="<?= $baseUrl ?>page=<?= $total_pages ?>" class="pagination-number"><?= $total_pages ?></a>
                            <?php endif; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="<?= $baseUrl ?>page=<?= $current_page + 1 ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: var(--space-20) 0;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--gray-300); margin-bottom: var(--space-4);"></i>
                        <h3 style="color: var(--gray-600); margin-bottom: var(--space-2);">No hay productos disponibles</h3>
                        <p style="color: var(--gray-500);">Intenta cambiar los filtros o vuelve más tarde</p>
                        <a href="/productos" class="btn btn-primary" style="margin-top: var(--space-4);">
                            Ver todos los productos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Catalog Layout */
.catalog-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--space-8);
    margin-top: var(--space-6);
}

/* Mobile Filters Accordion */
.mobile-filters-card {
    display: none;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-4);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.mobile-filters-header {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-3) var(--space-4);
    background: white;
    border: none;
    cursor: pointer;
    font-weight: 600;
    color: var(--gray-900);
    transition: var(--transition);
}

.mobile-filters-header:hover {
    background: var(--gray-50);
}

.mobile-filters-header > div {
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.mobile-filters-header i.fas.fa-filter {
    color: #f479d9;
}

.mobile-filters-arrow {
    transition: transform 0.3s ease;
    color: var(--gray-500);
}

.mobile-filters-card.expanded .mobile-filters-arrow {
    transform: rotate(180deg);
}

.mobile-filters-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.mobile-filters-card.expanded .mobile-filters-content {
    max-height: 1000px;
    overflow-y: auto;
}

.mobile-filter-group {
    padding: var(--space-4);
    border-top: 1px solid var(--gray-200);
}

.mobile-filter-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--space-3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.mobile-filter-options {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.mobile-filter-option {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-2) var(--space-3);
    border-radius: var(--radius-md);
    color: var(--gray-700);
    text-decoration: none;
    font-size: 0.9375rem;
    transition: var(--transition);
}

.mobile-filter-option:hover {
    background: var(--gray-100);
}

.mobile-filter-option.active {
    background: #f479d9;
    color: white;
    font-weight: 600;
}

.mobile-filter-option i {
    font-size: 0.875rem;
    opacity: 0.7;
}

/* Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    margin-top: var(--space-8);
    padding-top: var(--space-6);
    border-top: 1px solid var(--gray-200);
}

.pagination-btn,
.pagination-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 var(--space-3);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    background: white;
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.pagination-btn:hover,
.pagination-number:hover {
    border-color: var(--primary);
    background: var(--gray-50);
    color: var(--primary);
}

.pagination-number.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination-dots {
    padding: 0 var(--space-2);
    color: var(--gray-400);
}

@media (max-width: 1024px) {
    .catalog-layout {
        grid-template-columns: 1fr;
    }

    .catalog-sidebar {
        display: none;
    }

    .mobile-filters-card {
        display: block;
    }
}

@media (max-width: 640px) {
    .pagination {
        gap: var(--space-1);
    }

    .pagination-btn,
    .pagination-number {
        min-width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }
}

/* Sidebar Filters */
.catalog-sidebar {
    position: sticky;
    top: 90px;
    height: fit-content;
}

.filter-section {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
}

.filter-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: var(--space-5);
    display: flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--gray-900);
}

.filter-group {
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
}

.filter-group:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.filter-group-title {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-700);
    margin-bottom: var(--space-3);
    letter-spacing: 0.05em;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.filter-option {
    padding: var(--space-2) var(--space-3);
    border-radius: var(--radius-md);
    color: var(--gray-700);
    font-size: 0.95rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.filter-option:hover {
    background: var(--gray-50);
    color: var(--primary);
}

.filter-option.active {
    background: var(--primary);
    color: white;
    font-weight: 500;
}

/* Catalog Header */
.catalog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-8);
    flex-wrap: wrap;
    gap: var(--space-4);
}

.catalog-title {
    font-size: 2rem;
    margin-bottom: var(--space-1);
}

.catalog-count {
    color: var(--gray-600);
    font-size: 0.95rem;
}

.catalog-sort {
    display: flex;
    align-items: center;
}

@media (max-width: 640px) {
    .catalog-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .catalog-sort {
        width: 100%;
    }

    .catalog-sort select {
        flex: 1;
    }
}
</style>

<script>
// Sort functionality
document.getElementById('sort')?.addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('sort', this.value);
    window.location.href = url.toString();
});

// Set current sort value from URL
const urlParams = new URLSearchParams(window.location.search);
const sortValue = urlParams.get('sort');
if (sortValue) {
    document.getElementById('sort').value = sortValue;
}

// Mobile filters accordion toggle
function toggleMobileFilters() {
    const card = document.querySelector('.mobile-filters-card');
    card.classList.toggle('expanded');
}
</script>
