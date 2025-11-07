<!-- Breadcrumb -->
<div style="background: var(--gray-50); padding: var(--space-4) 0; border-bottom: 1px solid var(--gray-200);">
    <div class="container">
        <nav style="display: flex; align-items: center; gap: var(--space-2); font-size: 0.875rem; color: var(--gray-600); flex-wrap: wrap;">
            <a href="/" style="color: var(--gray-600); transition: var(--transition);"><?= __('nav.home') ?></a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <a href="/productos" style="color: var(--gray-600); transition: var(--transition);"><?= __('product.all_products') ?></a>
            <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
            <span style="color: var(--gray-900); font-weight: 500;"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<!-- Product Detail Section -->
<section class="section">
    <div class="container">
        <div class="product-detail-layout">
            <!-- Product Images -->
            <div class="product-images">
                <div class="product-main-image">
                    <?php
                    // Try to get main image from multiple sources
                    $mainImage = '/img/logo.png';
                    if (!empty($product['images'][0]['image_path'])) {
                        $mainImage = $product['images'][0]['image_path'];
                    } elseif (!empty($product['main_image'])) {
                        $mainImage = $product['main_image'];
                    } elseif (!empty($product['image_path'])) {
                        $mainImage = $product['image_path'];
                    }
                    ?>
                    <img id="mainImage" src="<?= htmlspecialchars($mainImage) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         onerror="this.src='/img/logo.png'">
                </div>

                <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                    <div class="product-thumbnail-grid">
                        <?php foreach (array_slice($product['images'], 0, 4) as $index => $image): ?>
                            <div class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                 onclick="changeMainImage('<?= htmlspecialchars($image['image_path']) ?>', this)">
                                <img src="<?= htmlspecialchars($image['image_path']) ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     onerror="this.src='/img/logo.png'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <?php if (!empty($product['team_name'])): ?>
                    <p class="product-category">
                        <i class="fas fa-shield-alt"></i>
                        <?= htmlspecialchars($product['team_name']) ?>
                    </p>
                <?php endif; ?>

                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="product-price-section">
                    <div class="product-price">
                        <span class="price-current">€<?= number_format($product['base_price'], 2) ?></span>
                        <?php if (!empty($product['original_price']) && $product['original_price'] > $product['base_price']): ?>
                            <span class="price-original">€<?= number_format($product['original_price'], 2) ?></span>
                            <span class="price-badge">
                                <?= round((($product['original_price'] - $product['base_price']) / $product['original_price']) * 100) ?>% OFF
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Add to Cart Form -->
                <form id="addToCartForm" class="product-form">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Size Selection -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-ruler"></i>
                            <?= __('product.size') ?>
                        </label>
                        <div class="size-options">
                            <?php if (!empty($product['variants'])): ?>
                                <?php foreach ($product['variants'] as $variant): ?>
                                    <label class="size-option">
                                        <input type="radio" name="size" value="<?= htmlspecialchars($variant['size']) ?>" onchange="hideSizeError()">
                                        <span><?= htmlspecialchars($variant['size']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--error);"><?= __('product.out_of_stock') ?></p>
                            <?php endif; ?>
                        </div>
                        <div id="sizeError" class="error-message" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= __('product.size_required') ?>
                        </div>
                    </div>

                    <!-- Quantity Selection -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-shopping-bag"></i>
                            <?= __('product.quantity') ?>
                        </label>
                        <div class="quantity-selector">
                            <button type="button" onclick="changeQuantity(-1)" class="quantity-btn">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" readonly>
                            <button type="button" onclick="changeQuantity(1)" class="quantity-btn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="button" id="addToCartBtn" class="btn btn-primary btn-lg" style="width: 100%;" onclick="document.getElementById('addToCartForm').dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));">
                        <span id="btnContent">
                            <i class="fas fa-shopping-cart"></i>
                            <?= __('product.add_to_cart') ?>
                        </span>
                        <span id="btnSpinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                            <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] === 'es'): ?>
                                Añadiendo...
                            <?php else: ?>
                                Adding...
                            <?php endif; ?>
                        </span>
                    </button>
                </form>

                <!-- Product Features -->
                <div class="product-features">
                    <div class="feature-item">
                        <i class="fas fa-certificate"></i>
                        <span><?= __('product.official_product') ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shipping-fast"></i>
                        <span><?= __('product.free_shipping') ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span><?= __('product.delivery_time') ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span><?= __('product.secure_payment') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="product-tabs" style="margin-top: var(--space-16);">
            <div class="tabs-header">
                <button class="tab-btn active" onclick="switchTab('description')">
                    <?= __('product.description') ?>
                </button>
                <button class="tab-btn" onclick="switchTab('shipping')">
                    <?= __('product.shipping_info') ?>
                </button>
                <button class="tab-btn" onclick="switchTab('size-guide')">
                    <?= __('size_guide.tab_title') ?>
                </button>
            </div>

            <div class="tabs-content">
                <div id="tab-description" class="tab-panel active">
                    <h3><?= __('product.product_info') ?></h3>
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Producto de alta calidad.')) ?></p>
                    <?php if (!empty($product['specifications'])): ?>
                        <div style="margin-top: var(--space-6);">
                            <h4><?= __('product.specifications') ?></h4>
                            <p><?= nl2br(htmlspecialchars($product['specifications'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="tab-shipping" class="tab-panel">
                    <h3><?= __('product.shipping_info') ?></h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: var(--space-3) 0; border-bottom: 1px solid var(--gray-200);">
                            <i class="fas fa-check" style="color: var(--success); margin-right: var(--space-2);"></i>
                            <?= __('product.free_shipping') ?>
                        </li>
                        <li style="padding: var(--space-3) 0; border-bottom: 1px solid var(--gray-200);">
                            <i class="fas fa-check" style="color: var(--success); margin-right: var(--space-2);"></i>
                            <?= __('product.delivery_time') ?>
                        </li>
                        <li style="padding: var(--space-3) 0; border-bottom: 1px solid var(--gray-200);">
                            <i class="fas fa-check" style="color: var(--success); margin-right: var(--space-2);"></i>
                            Seguimiento de pedido incluido
                        </li>
                        <li style="padding: var(--space-3) 0;">
                            <i class="fas fa-check" style="color: var(--success); margin-right: var(--space-2);"></i>
                            Devoluciones gratuitas en 30 días
                        </li>
                    </ul>
                </div>

                <div id="tab-size-guide" class="tab-panel">
                    <div class="size-guide-header">
                        <h3><?= __('size_guide.title') ?></h3>
                        <p class="size-guide-subtitle">
                            <?= __('size_guide.subtitle') ?>
                        </p>
                    </div>

                    <!-- Size Tips -->
                    <div class="size-tips">
                        <h4><?= __('size_guide.tips_title') ?></h4>
                        <div class="size-tips-grid">
                            <div class="size-tip">
                                <i class="fas fa-ruler-combined"></i>
                                <div>
                                    <strong><?= __('size_guide.tip_1_title') ?>:</strong>
                                    <span><?= __('size_guide.tip_1_desc') ?></span>
                                </div>
                            </div>
                            <div class="size-tip">
                                <i class="fas fa-arrows-alt-h"></i>
                                <div>
                                    <strong><?= __('size_guide.tip_2_title') ?>:</strong>
                                    <span><?= __('size_guide.tip_2_desc') ?></span>
                                </div>
                            </div>
                            <div class="size-tip">
                                <i class="fas fa-arrows-alt-v"></i>
                                <div>
                                    <strong><?= __('size_guide.tip_3_title') ?>:</strong>
                                    <span><?= __('size_guide.tip_3_desc') ?></span>
                                </div>
                            </div>
                            <div class="size-tip">
                                <i class="fas fa-exchange-alt"></i>
                                <div>
                                    <strong><?= __('size_guide.tip_4_title') ?>:</strong>
                                    <span><?= __('size_guide.tip_4_desc') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Size Categories Accordion -->
                    <div class="size-accordion">
                        <!-- General -->
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>
                                    <i class="fas fa-tshirt"></i>
                                    <?= __('size_guide.category_general') ?>
                                </span>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="accordion-description">
                                    <?= __('size_guide.category_general_desc') ?>
                                </p>
                                <div class="size-table-wrapper">
                                    <table class="size-table">
                                        <thead>
                                            <tr>
                                                <th><?= __('size_guide.size') ?></th>
                                                <th><?= __('size_guide.chest_width') ?> (CM)</th>
                                                <th><?= __('size_guide.length') ?> (CM)</th>
                                                <th><?= __('size_guide.height') ?> (CM)</th>
                                                <th><?= __('size_guide.weight') ?> (KG)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>S</td><td>52-54</td><td>70-74</td><td>165-173</td><td>50-60</td></tr>
                                            <tr><td>M</td><td>54-56</td><td>74-76</td><td>170-178</td><td>55-70</td></tr>
                                            <tr><td>L</td><td>56-58</td><td>76-78</td><td>175-182</td><td>70-80</td></tr>
                                            <tr><td>XL</td><td>58-60</td><td>78-80</td><td>178-185</td><td>80-90</td></tr>
                                            <tr><td>2XL</td><td>60-62</td><td>80-82</td><td>182-190</td><td>85-95</td></tr>
                                            <tr><td>3XL</td><td>62-64</td><td>82-84</td><td>188-195</td><td>90-100</td></tr>
                                            <tr><td>4XL</td><td>64-66</td><td>84-86</td><td>195-200</td><td>100-120</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="size-note">
                                    <i class="fas fa-info-circle"></i>
                                    <?= __('size_guide.note') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Player Version -->
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>
                                    <i class="fas fa-medal"></i>
                                    <?= __('size_guide.category_player') ?>
                                </span>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="accordion-description">
                                    <?= __('size_guide.category_player_desc') ?>
                                </p>
                                <div class="size-table-wrapper">
                                    <table class="size-table">
                                        <thead>
                                            <tr>
                                                <th><?= __('size_guide.size') ?></th>
                                                <th><?= __('size_guide.chest_width') ?> (CM)</th>
                                                <th><?= __('size_guide.length') ?> (CM)</th>
                                                <th><?= __('size_guide.height') ?> (CM)</th>
                                                <th><?= __('size_guide.weight') ?> (KG)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>S</td><td>50-52</td><td>68-72</td><td>165-173</td><td>50-60</td></tr>
                                            <tr><td>M</td><td>52-54</td><td>72-74</td><td>170-178</td><td>55-70</td></tr>
                                            <tr><td>L</td><td>54-56</td><td>74-76</td><td>175-182</td><td>70-80</td></tr>
                                            <tr><td>XL</td><td>56-58</td><td>76-78</td><td>178-185</td><td>80-90</td></tr>
                                            <tr><td>2XL</td><td>58-60</td><td>78-80</td><td>182-190</td><td>85-95</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Kids -->
                        <div class="accordion-item">
                            <button class="accordion-header" onclick="toggleAccordion(this)">
                                <span>
                                    <i class="fas fa-child"></i>
                                    <?= __('size_guide.category_kids') ?>
                                </span>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="accordion-description">
                                    <?= __('size_guide.category_kids_desc') ?>
                                </p>
                                <div class="size-table-wrapper">
                                    <table class="size-table">
                                        <thead>
                                            <tr>
                                                <th><?= __('size_guide.size') ?></th>
                                                <th><?= __('size_guide.age') ?></th>
                                                <th><?= __('size_guide.height') ?> (CM)</th>
                                                <th><?= __('size_guide.weight') ?> (KG)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>16</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '2-3 años' : '2-3 years' ?></td><td>92-98</td><td>13-15</td></tr>
                                            <tr><td>18</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '3-4 años' : '3-4 years' ?></td><td>98-104</td><td>15-17</td></tr>
                                            <tr><td>20</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '4-5 años' : '4-5 years' ?></td><td>104-110</td><td>17-19</td></tr>
                                            <tr><td>22</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '6-7 años' : '6-7 years' ?></td><td>116-122</td><td>21-24</td></tr>
                                            <tr><td>24</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '8-9 años' : '8-9 years' ?></td><td>128-134</td><td>26-30</td></tr>
                                            <tr><td>26</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '10-11 años' : '10-11 years' ?></td><td>140-146</td><td>32-38</td></tr>
                                            <tr><td>28</td><td><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '12-13 años' : '12-13 years' ?></td><td>152-158</td><td>42-50</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact CTA -->
                    <div class="size-guide-cta">
                        <div class="size-guide-cta-content">
                            <i class="fas fa-question-circle"></i>
                            <div>
                                <h4 style="color: white;"><?= __('size_guide.cta_title') ?></h4>
                                <p style="color: white;"><?= __('size_guide.cta_description') ?></p>
                            </div>
                        </div>
                        <div class="size-guide-cta-actions">
                            <a href="https://wa.me/34614299735" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                <i class="fab fa-whatsapp"></i>
                                <?= __('size_guide.btn_whatsapp') ?>
                            </a>
                            <a href="https://t.me/esKickverse" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                <i class="fab fa-telegram"></i>
                                <?= __('size_guide.btn_telegram') ?>
                            </a>
                            <a href="/productos" class="btn btn-sm btn-secondary">
                                <i class="fas fa-tshirt"></i>
                                <?= __('size_guide.btn_catalog') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div style="margin-top: var(--space-20);">
                <h2 class="section-title"><?= __('product.related_products') ?></h2>
                <div class="product-grid" style="margin-top: var(--space-8);">
                    <?php foreach (array_slice($related_products, 0, 4) as $relatedProduct): ?>
                        <div class="product-card">
                            <a href="/productos/<?= htmlspecialchars($relatedProduct['slug']) ?>">
                                <div class="product-card-image">
                                    <img src="<?= htmlspecialchars($relatedProduct['image_path'] ?? '/img/logo.png') ?>"
                                         alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                                         onerror="this.src='/img/logo.png'"
                                         loading="lazy">
                                </div>
                                <div class="product-card-content">
                                    <h3 class="product-card-title"><?= htmlspecialchars($relatedProduct['name']) ?></h3>
                                    <div class="product-card-price">
                                        <span class="price-current">€<?= number_format($relatedProduct['base_price'], 2) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Product Detail Layout */
.product-detail-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-12);
    margin-top: var(--space-8);
}

@media (max-width: 1024px) {
    .product-detail-layout {
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
}

/* Product Images */
.product-images {
    position: sticky;
    top: 90px;
    height: fit-content;
}

@media (max-width: 1024px) {
    .product-images {
        position: static;
    }
}

.product-main-image {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    overflow: hidden;
    margin-bottom: var(--space-4);
}

.product-main-image img {
    width: 100%;
    height: auto;
    aspect-ratio: 1;
    object-fit: cover;
}

.product-thumbnail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-3);
}

.product-thumbnail {
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    cursor: pointer;
    transition: var(--transition);
}

.product-thumbnail:hover,
.product-thumbnail.active {
    border-color: var(--primary);
}

.product-thumbnail img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
}

/* Product Info */
.product-category {
    color: var(--primary);
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: var(--space-2);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.product-title {
    font-size: 2rem;
    margin-bottom: var(--space-4);
    line-height: 1.2;
}

.product-price-section {
    margin-bottom: var(--space-6);
}

.product-price {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    flex-wrap: wrap;
}

.price-badge {
    background: var(--danger);
    color: white;
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    font-weight: 600;
}

.product-description {
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
    line-height: 1.8;
}

/* Product Form */
.product-form {
    margin-bottom: var(--space-6);
    padding-top: var(--space-6);
    border-top: 1px solid var(--gray-200);
}

.form-group {
    margin-bottom: var(--space-5);
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: var(--space-3);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

/* Size Options */
.size-options {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.size-option {
    cursor: pointer;
}

.size-option input {
    display: none;
}

.size-option span {
    display: block;
    padding: var(--space-3) var(--space-4);
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: var(--transition);
    min-width: 50px;
    text-align: center;
}

.size-option:hover span {
    border-color: var(--primary);
}

.size-option input:checked + span {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* Quantity Selector */
.quantity-selector {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    width: fit-content;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border: 2px solid var(--gray-300);
    background: white;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.quantity-selector input {
    width: 60px;
    height: 40px;
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-lg);
    text-align: center;
    font-weight: 600;
}

/* Product Features */
.product-features {
    margin-top: var(--space-6);
    padding-top: var(--space-6);
    border-top: 1px solid var(--gray-200);
}

.feature-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3) 0;
    color: var(--gray-700);
    font-size: 0.95rem;
}

.feature-item i {
    color: var(--primary);
    font-size: 1.125rem;
}

/* Product Tabs */
.tabs-header {
    display: flex;
    gap: var(--space-2);
    border-bottom: 2px solid var(--gray-200);
}

.tab-btn {
    padding: var(--space-4) var(--space-6);
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    font-weight: 600;
    color: var(--gray-600);
    transition: var(--transition);
}

.tab-btn:hover {
    color: var(--primary);
}

.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tabs-content {
    margin-top: var(--space-6);
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.tab-panel h3 {
    margin-bottom: var(--space-4);
}

.tab-panel h4 {
    margin-bottom: var(--space-3);
    font-size: 1.125rem;
}

/* Error message */
.error-message {
    margin-top: var(--space-3);
    padding: var(--space-3) var(--space-4);
    background: #fee2e2;
    border: 1px solid #ef4444;
    border-radius: var(--radius-lg);
    color: #991b1b;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.error-message i {
    color: #ef4444;
}

/* Size Guide Styles */
.size-guide-header {
    margin-bottom: var(--space-8);
}

.size-guide-subtitle {
    color: var(--gray-600);
    font-size: 1rem;
    line-height: 1.6;
    margin-top: var(--space-2);
}

/* Size Tips */
.size-tips {
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    margin-bottom: var(--space-8);
}

.size-tips h4 {
    font-size: 1.125rem;
    margin-bottom: var(--space-4);
    color: var(--gray-900);
}

.size-tips-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-4);
}

@media (max-width: 768px) {
    .size-tips-grid {
        grid-template-columns: 1fr;
        gap: var(--space-3);
    }
}

.size-tip {
    display: flex;
    gap: var(--space-3);
    align-items: flex-start;
}

.size-tip i {
    color: var(--primary);
    font-size: 1.25rem;
    margin-top: 2px;
    flex-shrink: 0;
}

.size-tip strong {
    display: block;
    color: var(--gray-900);
    margin-bottom: 4px;
    font-size: 0.9375rem;
}

.size-tip span {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Accordion */
.size-accordion {
    margin-bottom: var(--space-8);
}

.accordion-item {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-3);
    overflow: hidden;
    transition: var(--transition);
}

.accordion-item:hover {
    border-color: var(--primary);
}

.accordion-header {
    width: 100%;
    padding: var(--space-4) var(--space-5);
    background: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
    color: var(--gray-900);
    transition: var(--transition);
}

.accordion-header:hover {
    background: var(--gray-50);
}

.accordion-header span {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.accordion-header i.fas {
    color: var(--primary);
}

.accordion-icon {
    transition: transform 0.3s ease;
    color: var(--gray-400) !important;
}

.accordion-item.active .accordion-icon {
    transform: rotate(180deg);
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.accordion-item.active .accordion-content {
    max-height: 1000px;
}

.accordion-description {
    padding: 0 var(--space-5) var(--space-4);
    color: var(--gray-600);
    font-size: 0.9375rem;
    line-height: 1.6;
}

/* Size Table */
.size-table-wrapper {
    padding: 0 var(--space-5) var(--space-4);
    overflow-x: auto;
}

.size-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.size-table thead th {
    background: var(--gray-100);
    padding: var(--space-3) var(--space-4);
    text-align: left;
    font-weight: 600;
    color: var(--gray-700);
    border-bottom: 2px solid var(--gray-300);
    white-space: nowrap;
}

.size-table tbody td {
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
}

.size-table tbody tr:last-child td {
    border-bottom: none;
}

.size-table tbody tr:hover {
    background: var(--gray-50);
}

.size-table tbody td:first-child {
    font-weight: 600;
    color: var(--primary);
}

.size-note {
    padding: var(--space-4) var(--space-5);
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: var(--radius-lg);
    color: #92400e;
    font-size: 0.875rem;
    line-height: 1.5;
    display: flex;
    align-items: flex-start;
    gap: var(--space-2);
    margin: var(--space-4) var(--space-5) var(--space-4);
}

.size-note i {
    color: #f59e0b;
    margin-top: 2px;
    flex-shrink: 0;
}

/* Size Guide CTA */
.size-guide-cta {
    background: linear-gradient(135deg, #FF69B4 0%, #8A2BE2 100%);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-6);
    flex-wrap: wrap;
}

.size-guide-cta-content {
    display: flex;
    align-items: flex-start;
    gap: var(--space-4);
    flex: 1;
    min-width: 250px;
}

.size-guide-cta-content i {
    font-size: 2rem;
    opacity: 0.9;
    flex-shrink: 0;
}

.size-guide-cta-content h4 {
    font-size: 1.125rem;
    margin: 0 0 var(--space-2);
    font-weight: 600;
}

.size-guide-cta-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9375rem;
}

.size-guide-cta-actions {
    display: flex;
    gap: var(--space-3);
    flex-wrap: wrap;
}

.size-guide-cta-actions .btn {
    white-space: nowrap;
}

.size-guide-cta-actions .btn-primary {
    background: white;
    color: var(--primary);
}

.size-guide-cta-actions .btn-primary:hover {
    background: var(--gray-50);
    transform: translateY(-2px);
}

.size-guide-cta-actions .btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-color: white;
}

.size-guide-cta-actions .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
    .size-guide-cta {
        flex-direction: column;
        align-items: flex-start;
    }

    .size-guide-cta-content {
        min-width: 100%;
    }

    .size-guide-cta-actions {
        width: 100%;
    }

    .size-guide-cta-actions .btn {
        flex: 1;
    }
}
</style>

<script data-timestamp="<?= time() ?>">
console.log('=== PRODUCT SCRIPT LOADED v<?= time() ?> ===', new Date().toISOString());

// Change main image
function changeMainImage(src, element) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.product-thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    element.classList.add('active');
}

// Hide size error message
function hideSizeError() {
    document.getElementById('sizeError').style.display = 'none';
}

// Show size error message
function showSizeError() {
    const errorMsg = document.getElementById('sizeError');
    errorMsg.style.display = 'flex';
    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Quantity controls with stock limit
let maxStock = 10; // Default stock
function changeQuantity(delta) {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    const newValue = current + delta;
    if (newValue >= 1 && newValue <= maxStock) {
        input.value = newValue;
    }
}

// Update max stock when size is selected
function updateStockForSize(variantData) {
    if (variantData && variantData.stock_quantity !== undefined) {
        maxStock = Math.max(1, parseInt(variantData.stock_quantity) || 10);
        const quantityInput = document.getElementById('quantity');
        quantityInput.max = maxStock;
        if (parseInt(quantityInput.value) > maxStock) {
            quantityInput.value = maxStock;
        }
    }
}

// Switch tabs
function switchTab(tabName) {
    // Update buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Update panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Toggle accordion
function toggleAccordion(button) {
    const item = button.closest('.accordion-item');
    const wasActive = item.classList.contains('active');

    // Close all accordion items
    document.querySelectorAll('.accordion-item').forEach(el => {
        el.classList.remove('active');
    });

    // Open clicked item if it wasn't active
    if (!wasActive) {
        item.classList.add('active');
    }
}

// Add to cart
function addToCart(event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    console.log('🛒 addToCart called - STOPPED PROPAGATION');

    const formData = new FormData(event.target);
    const productId = formData.get('product_id');
    const size = formData.get('size');
    const quantity = parseInt(formData.get('quantity'));

    console.log('📦 Product ID:', productId, 'Size:', size, 'Quantity:', quantity);

    if (!size) {
        console.log('❌ No size selected');
        showSizeError();
        return false;
    }

    console.log('Fetching variant...');

    // Get variant_id from backend based on product_id and size
    fetch(`/api/products/${productId}/variant?size=${encodeURIComponent(size)}`)
        .then(res => {
            console.log('Variant response:', res);
            return res.json();
        })
        .then(variantData => {
            console.log('Variant data:', variantData);

            if (!variantData.success || !variantData.data) {
                showNotification('error', 'Error', 'La talla seleccionada no está disponible');
                return Promise.reject('Size not available');
            }

            const variant = variantData.data;
            const variantId = variant.variant_id;

            // Update stock limit
            updateStockForSize(variant);

            // Check stock availability
            const availableStock = variant.stock_quantity || 10;
            if (availableStock === 0) {
                showNotification('error', 'Agotado', 'Esta talla está agotada');
                return Promise.reject('Out of stock');
            }

            if (quantity > availableStock) {
                showNotification('error', 'Stock insuficiente', `Solo quedan ${availableStock} unidades disponibles`);
                return Promise.reject('Insufficient stock');
            }

            console.log('Adding to cart...');

            // Now add to cart with variant_id
            return fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity,
                    csrf_token: formData.get('csrf_token')
                })
            });
        })
        .then(res => {
            if (!res) return;
            console.log('Cart add response:', res);
            return res.json();
        })
        .then(data => {
            if (!data) return;
            console.log('Cart add data:', data);

            if (data.success) {
                showNotification('success', <?= json_encode(__('common.product_added')) ?>, <?= json_encode(__('common.product_added_msg')) ?>);
                // Update cart count
                if (typeof fetchCartCount === 'function') {
                    fetchCartCount();
                }
            } else {
                showNotification('error', <?= json_encode(__('common.cart_add_error')) ?>, data.message || <?= json_encode(__('common.cart_add_error_msg')) ?>);
            }
        })
        .catch(err => {
            if (err !== 'Size not available' && err !== 'Out of stock' && err !== 'Insufficient stock') {
                console.error('Error:', err);
                showNotification('error', <?= json_encode(__('common.cart_add_error')) ?>, <?= json_encode(__('common.unexpected_error')) ?>);
            }
        });

    return false;
}

// Initialize form - Direct approach
console.log('🔧 Initializing form handler...');

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initForm);
} else {
    initForm();
}

function initForm() {
    const form = document.getElementById('addToCartForm');

    if (!form) {
        return;
    }

    // Attach listener
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const formData = new FormData(e.target);
        const productId = formData.get('product_id');
        const size = formData.get('size');
        const quantity = parseInt(formData.get('quantity')) || 1;
        const csrfToken = formData.get('csrf_token');

        if (!size) {
            showSizeError();
            return false;
        }

        // Show loading spinner
        const btn = document.getElementById('addToCartBtn');
        const btnContent = document.getElementById('btnContent');
        const btnSpinner = document.getElementById('btnSpinner');

        if (btn && btnContent && btnSpinner) {
            btn.disabled = true;
            btnContent.style.display = 'none';
            btnSpinner.style.display = 'inline';
        }

        // Get variant and add to cart
        fetch(`/api/products/${productId}/variant?size=${encodeURIComponent(size)}`)
            .then(res => {
                // Check if response is ok (status 200-299)
                if (!res.ok) {
                    throw new Error('HTTP error: ' + res.status);
                }
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server error: Invalid response');
                }
                return res.json();
            })
            .then(variantData => {
                if (!variantData.success || !variantData.data) {
                    showNotification('error', '<?= __('common.size_unavailable') ?>', '<?= __('common.size_unavailable_msg') ?>');
                    return Promise.reject('Size not available');
                }

                const variant = variantData.data;
                const variantId = variant.variant_id;

                // Check stock availability
                if (variant.stock_quantity < quantity) {
                    showNotification('error', '<?= __('common.out_of_stock_title') ?>', '<?= __('common.out_of_stock_msg') ?>');
                    return Promise.reject('Not enough stock');
                }

                // Add to cart
                return fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        variant_id: variantId,
                        quantity: quantity,
                        csrf_token: csrfToken
                    })
                });
            })
            .then(res => {
                if (!res) return; // Already rejected

                // Check if response is ok
                if (!res.ok) {
                    throw new Error('HTTP error: ' + res.status);
                }
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server error: Invalid response');
                }
                return res.json();
            })
            .then(cartData => {
                // Reset button state
                if (btn && btnContent && btnSpinner) {
                    btn.disabled = false;
                    btnContent.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }

                if (!cartData) return; // Already rejected

                console.log('Cart response:', cartData);

                if (cartData.success) {
                    // Get product info
                    const productName = '<?= addslashes($product['name']) ?>';
                    const productImage = document.getElementById('mainImage').src;
                    const selectedSize = size;

                    showProductAddedModal(productName, productImage, selectedSize, quantity);

                    // Update cart count using global function
                    if (cartData.cart_count && typeof updateCartCount === 'function') {
                        updateCartCount(cartData.cart_count);
                    }
                } else {
                    showNotification('error', '<?= __('common.cart_add_error') ?>', cartData.message || '<?= __('common.cart_add_error_msg') ?>');
                }
            })
            .catch(err => {
                // Reset button state on error
                if (btn && btnContent && btnSpinner) {
                    btn.disabled = false;
                    btnContent.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }

                if (err !== 'Size not available' && err !== 'Not enough stock') {
                    showNotification('error', '<?= __('common.error') ?>', '<?= __('common.unexpected_error') ?>');
                }
            });

        return false;
    }, true);
}
</script>

<!-- Product Added Modal -->
<div id="productAddedModal" class="product-added-modal" style="display: none;">
    <div class="product-added-backdrop" onclick="closeProductModal()"></div>
    <div class="product-added-container">
        <button onclick="closeProductModal()" class="product-added-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="product-added-content">
            <div class="product-added-success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="product-added-title">
                <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? '¡Producto añadido al carrito!' : 'Product added to cart!' ?>
            </h3>
            <div class="product-added-item">
                <img id="modalProductImage" src="" alt="" class="product-added-image">
                <div class="product-added-info">
                    <p id="modalProductName" class="product-added-name"></p>
                    <p class="product-added-details">
                        <span id="modalProductSize"></span>
                        <span class="product-added-separator">•</span>
                        <span><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Cantidad' : 'Quantity' ?>: <strong id="modalProductQty"></strong></span>
                    </p>
                </div>
            </div>
            <div class="product-added-actions">
                <a href="/carrito" class="product-added-btn product-added-btn-primary">
                    <i class="fas fa-shopping-cart"></i>
                    <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Ver Carrito' : 'View Cart' ?>
                </a>
                <button onclick="closeProductModal()" class="product-added-btn product-added-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Seguir Comprando' : 'Continue Shopping' ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Notification Modal -->
<div id="notificationModal" class="notification-modal" style="display: none;">
    <div class="notification-modal-backdrop" onclick="closeNotification()"></div>
    <div class="notification-modal-container">
        <button onclick="closeNotification()" class="notification-modal-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="notification-modal-content">
            <div id="notificationIcon" class="notification-modal-icon"></div>
            <h3 id="notificationTitle" class="notification-modal-title"></h3>
            <p id="notificationMessage" class="notification-modal-message"></p>
            <button onclick="closeNotification()" class="notification-modal-btn">
                <?= __('common.accept') ?>
            </button>
        </div>
    </div>
</div>

<style>
/* Notification Modal */
.notification-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-4);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.notification-modal.active {
    opacity: 1;
    visibility: visible;
}

.notification-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.notification-modal-container {
    position: relative;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    max-width: 440px;
    width: 100%;
    transform: scale(0.95);
    transition: transform 0.2s ease;
}

.notification-modal.active .notification-modal-container {
    transform: scale(1);
}

.notification-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: var(--gray-400);
    font-size: 1.25rem;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 1;
}

.notification-modal-close:hover {
    background: var(--gray-100);
    color: var(--gray-700);
}

.notification-modal-content {
    padding: 48px 32px 32px;
    text-align: center;
}

.notification-modal-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.notification-modal-icon.success {
    background: #dcfce7;
    color: #16a34a;
}

.notification-modal-icon.error {
    background: #fee2e2;
    color: #dc2626;
}

.notification-modal-icon.info {
    background: #dbeafe;
    color: #2563eb;
}

.notification-modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 12px;
    line-height: 1.3;
}

.notification-modal-message {
    font-size: 1rem;
    color: var(--gray-600);
    margin: 0 0 32px;
    line-height: 1.5;
}

.notification-modal-btn {
    width: 100%;
    padding: 14px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notification-modal-btn:hover {
    background: #7c3aed;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);
}

.notification-modal-btn:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .notification-modal-content {
        padding: 40px 24px 24px;
    }

    .notification-modal-icon {
        width: 56px;
        height: 56px;
        font-size: 1.75rem;
        margin-bottom: 20px;
    }

    .notification-modal-title {
        font-size: 1.25rem;
    }

    .notification-modal-message {
        font-size: 0.9375rem;
    }
}

/* Product Added Modal */
.product-added-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-4);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.product-added-modal.active {
    opacity: 1;
    visibility: visible;
}

.product-added-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.product-added-container {
    position: relative;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    max-width: 480px;
    width: 100%;
    transform: scale(0.95);
    transition: transform 0.2s ease;
}

.product-added-modal.active .product-added-container {
    transform: scale(1);
}

.product-added-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: var(--gray-400);
    font-size: 1.25rem;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 1;
}

.product-added-close:hover {
    background: var(--gray-100);
    color: var(--gray-700);
}

.product-added-content {
    padding: 40px 32px 32px;
}

.product-added-success-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 20px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #16a34a;
    font-size: 1.75rem;
    animation: scaleIn 0.3s ease;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.product-added-title {
    font-size: 1.375rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 24px;
    text-align: center;
    line-height: 1.3;
}

.product-added-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: var(--gray-50);
    border-radius: 12px;
    margin-bottom: 24px;
    align-items: center;
}

.product-added-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    background: white;
    flex-shrink: 0;
}

.product-added-info {
    flex: 1;
    min-width: 0;
}

.product-added-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 8px;
    line-height: 1.3;
}

.product-added-details {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.product-added-separator {
    color: var(--gray-400);
}

.product-added-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.product-added-btn {
    width: 100%;
    padding: 14px 24px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.product-added-btn-primary {
    background: var(--primary);
    color: white;
}

.product-added-btn-primary:hover {
    background: #7c3aed;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);
}

.product-added-btn-secondary {
    background: transparent;
    color: var(--gray-700);
    border: 2px solid var(--gray-200);
}

.product-added-btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

.product-added-btn:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .product-added-content {
        padding: 32px 24px 24px;
    }

    .product-added-title {
        font-size: 1.25rem;
    }

    .product-added-item {
        gap: 12px;
        padding: 12px;
    }

    .product-added-image {
        width: 64px;
        height: 64px;
    }

    .product-added-name {
        font-size: 0.9375rem;
    }
}
</style>

<script>
// Product Added Modal functions
function showProductAddedModal(productName, productImage, size, quantity) {
    const modal = document.getElementById('productAddedModal');
    const imageEl = document.getElementById('modalProductImage');
    const nameEl = document.getElementById('modalProductName');
    const sizeEl = document.getElementById('modalProductSize');
    const qtyEl = document.getElementById('modalProductQty');

    if (!modal || !imageEl || !nameEl || !sizeEl || !qtyEl) {
        console.error('Product modal elements not found');
        return;
    }

    imageEl.src = productImage;
    imageEl.alt = productName;
    nameEl.textContent = productName;
    sizeEl.textContent = '<?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'es' ? 'Talla' : 'Size' ?>: ' + size;
    qtyEl.textContent = quantity;

    // Show modal with animation
    modal.style.display = 'flex';
    modal.offsetHeight;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    const modal = document.getElementById('productAddedModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }
    document.body.style.overflow = 'auto';
}

// Notification modal functions
function showNotification(type, title, message) {
    const modal = document.getElementById('notificationModal');
    const icon = document.getElementById('notificationIcon');
    const titleEl = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');

    // Check if all elements exist
    if (!modal || !icon || !titleEl || !messageEl) {
        console.error('Modal elements not found');
        alert(title + '\n' + message);
        document.body.style.overflow = 'auto';
        return;
    }

    // Reset icon classes
    icon.className = 'notification-modal-icon';

    // Set icon based on type
    if (type === 'success') {
        icon.classList.add('success');
        icon.innerHTML = '<i class="fas fa-check-circle"></i>';
    } else if (type === 'error') {
        icon.classList.add('error');
        icon.innerHTML = '<i class="fas fa-times-circle"></i>';
    } else if (type === 'info') {
        icon.classList.add('info');
        icon.innerHTML = '<i class="fas fa-info-circle"></i>';
    }

    titleEl.textContent = title;
    messageEl.textContent = message;

    // Show modal with proper animation
    modal.style.display = 'flex';
    // Force reflow
    modal.offsetHeight;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeNotification() {
    const modal = document.getElementById('notificationModal');
    if (modal) {
        modal.classList.remove('active');
        // Wait for animation before hiding
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNotification();
    }
});
</script>
