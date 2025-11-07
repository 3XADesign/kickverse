-- ============================================================================
-- KICKVERSE DATABASE SCHEMA
-- ============================================================================
-- Complete relational database design for Kickverse e-commerce platform
-- Features: Products, Subscriptions, Mystery Boxes, Drops, Orders, Loyalty
-- Version: 1.0
-- Date: 2025-11-01
-- ============================================================================

-- Drop existing database if exists and create new one
DROP DATABASE IF EXISTS kickverse;
CREATE DATABASE kickverse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kickverse;

-- ============================================================================
-- 1. CORE PRODUCT ENTITIES
-- ============================================================================

-- Leagues table (LaLiga, Premier League, etc.)
CREATE TABLE leagues (
    league_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    country VARCHAR(100),
    logo_path VARCHAR(255),
    display_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB;

-- Teams table
CREATE TABLE teams (
    team_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    league_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo_path VARCHAR(255),
    is_top_team BOOLEAN DEFAULT FALSE COMMENT 'For Premium TOP subscription plans',
    display_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES leagues(league_id) ON DELETE RESTRICT,
    INDEX idx_league (league_id),
    INDEX idx_slug (slug),
    INDEX idx_top_team (is_top_team)
) ENGINE=InnoDB;

-- Main products table
CREATE TABLE products (
    product_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_type ENUM('jersey', 'accessory', 'mystery_box', 'subscription') NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 24.99,
    original_price DECIMAL(10,2) DEFAULT 79.99 COMMENT 'Display price (crossed out)',
    stock_quantity INT UNSIGNED DEFAULT 0,

    -- Jersey-specific fields (nullable for other product types)
    league_id INT UNSIGNED NULL,
    team_id INT UNSIGNED NULL,
    jersey_type ENUM('home', 'away', 'third', 'goalkeeper', 'retro') NULL,
    season VARCHAR(20) NULL COMMENT 'e.g., 2024/25',
    version ENUM('fan', 'player') NULL,

    -- Features
    has_patches_available BOOLEAN DEFAULT TRUE,
    patches_price DECIMAL(10,2) DEFAULT 1.99,
    has_personalization_available BOOLEAN DEFAULT TRUE,
    personalization_price DECIMAL(10,2) DEFAULT 2.99,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (league_id) REFERENCES leagues(league_id) ON DELETE SET NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL,
    INDEX idx_product_type (product_type),
    INDEX idx_slug (slug),
    INDEX idx_team (team_id),
    INDEX idx_league (league_id),
    INDEX idx_active_featured (is_active, is_featured),
    FULLTEXT idx_search (name, description)
) ENGINE=InnoDB;

-- Product images
CREATE TABLE product_images (
    image_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('main', 'detail', 'hover', 'gallery') DEFAULT 'main',
    display_order INT UNSIGNED DEFAULT 0,
    alt_text VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_type_order (image_type, display_order)
) ENGINE=InnoDB;

-- Product variants (sizes with stock)
CREATE TABLE product_variants (
    variant_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    size ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '16', '18', '20', '22', '24', '26', '28') NOT NULL,
    size_category ENUM('general', 'player', 'kids', 'tracksuit') DEFAULT 'general',
    sku VARCHAR(100) UNIQUE,
    stock_quantity INT UNSIGNED DEFAULT 0,
    low_stock_threshold INT UNSIGNED DEFAULT 10,

    -- Size measurements
    chest_width_cm DECIMAL(5,2) NULL,
    length_cm DECIMAL(5,2) NULL,
    height_cm DECIMAL(5,2) NULL COMMENT 'For kids sizes',
    weight_kg DECIMAL(5,2) NULL COMMENT 'For kids sizes',
    age_range VARCHAR(50) NULL COMMENT 'For kids sizes, e.g., 4-5 years',

    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product_size (product_id, size),
    INDEX idx_sku (sku),
    INDEX idx_stock (stock_quantity)
) ENGINE=InnoDB;

-- Product price history (for tracking price changes)
CREATE TABLE product_price_history (
    history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    old_price DECIMAL(10,2) NOT NULL,
    new_price DECIMAL(10,2) NOT NULL,
    change_reason VARCHAR(255),
    changed_by INT UNSIGNED NULL COMMENT 'FK to admin_users',
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_date (changed_at)
) ENGINE=InnoDB;

-- Translations for multi-language support
CREATE TABLE translations (
    translation_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('product', 'league', 'team', 'page', 'ui_element', 'email_template') NOT NULL,
    entity_id INT UNSIGNED NULL COMMENT 'FK to specific entity',
    translation_key VARCHAR(255) NOT NULL,
    language ENUM('es', 'en') NOT NULL DEFAULT 'es',
    translation_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_translation (entity_type, entity_id, translation_key, language),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_language (language)
) ENGINE=InnoDB;

-- Size guide data
CREATE TABLE size_guides (
    guide_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category ENUM('general', 'player', 'kids', 'tracksuit') NOT NULL,
    size VARCHAR(10) NOT NULL,
    chest_width_cm DECIMAL(5,2),
    length_cm DECIMAL(5,2),
    height_cm DECIMAL(5,2),
    weight_kg DECIMAL(5,2),
    age_range VARCHAR(50),
    language ENUM('es', 'en') DEFAULT 'es',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_size_guide (category, size, language),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- ============================================================================
-- 2. USER & CUSTOMER ENTITIES
-- ============================================================================

-- Customers table (hybrid authentication system)
CREATE TABLE customers (
    customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Classic authentication (optional)
    email VARCHAR(255) UNIQUE NULL,
    password_hash VARCHAR(255) NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(100) NULL,

    -- Telegram/WhatsApp identification
    telegram_username VARCHAR(100) UNIQUE NULL,
    telegram_chat_id VARCHAR(100) UNIQUE NULL,
    whatsapp_number VARCHAR(20) NULL,

    -- Basic info
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    preferred_language ENUM('es', 'en') DEFAULT 'es',

    -- Account status
    customer_status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity_date TIMESTAMP NULL,
    last_login_date TIMESTAMP NULL,

    -- Marketing
    marketing_consent BOOLEAN DEFAULT FALSE,
    newsletter_subscribed BOOLEAN DEFAULT FALSE,

    -- Loyalty system
    loyalty_tier ENUM('standard', 'silver', 'gold', 'platinum') DEFAULT 'standard',
    loyalty_points INT UNSIGNED DEFAULT 0,
    total_orders_count INT UNSIGNED DEFAULT 0,
    total_spent DECIMAL(10,2) DEFAULT 0.00,

    -- Security
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires TIMESTAMP NULL,
    failed_login_attempts INT UNSIGNED DEFAULT 0,
    locked_until TIMESTAMP NULL,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete',

    INDEX idx_email (email),
    INDEX idx_telegram (telegram_username),
    INDEX idx_whatsapp (whatsapp_number),
    INDEX idx_status (customer_status),
    INDEX idx_loyalty (loyalty_tier, loyalty_points)
) ENGINE=InnoDB;

-- Customer preferences
CREATE TABLE customer_preferences (
    preference_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL UNIQUE,

    -- Team/League preferences (JSON arrays of IDs)
    favorite_teams JSON NULL COMMENT 'Array of team_ids',
    favorite_leagues JSON NULL COMMENT 'Array of league_ids',
    excluded_teams JSON NULL COMMENT 'Array of team_ids to never receive',

    -- Size preferences
    preferred_size_jersey ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL') NULL,
    preferred_size_kids ENUM('16', '18', '20', '22', '24', '26', '28') NULL,
    prefers_fan_version BOOLEAN DEFAULT TRUE,

    -- Notification preferences
    notify_new_drops BOOLEAN DEFAULT TRUE,
    notify_stock_alerts BOOLEAN DEFAULT TRUE,
    notify_price_drops BOOLEAN DEFAULT TRUE,
    notify_subscription_renewal BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Shipping addresses
CREATE TABLE shipping_addresses (
    address_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,

    -- Address details
    recipient_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NULL,
    street_address VARCHAR(255) NOT NULL,
    additional_address VARCHAR(255) NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) DEFAULT 'España',
    additional_notes TEXT NULL,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_default (is_default)
) ENGINE=InnoDB;

-- Customer sessions (for guest carts and tracking)
CREATE TABLE customer_sessions (
    session_id VARCHAR(100) PRIMARY KEY,
    customer_id INT UNSIGNED NULL,
    session_data JSON NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- ============================================================================
-- 3. SUBSCRIPTION ENTITIES
-- ============================================================================

-- Subscription plans
CREATE TABLE subscription_plans (
    plan_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    plan_slug VARCHAR(100) NOT NULL UNIQUE,
    plan_type ENUM('fan', 'premium_random', 'premium_top', 'retro_top') NOT NULL,

    -- Pricing
    monthly_price DECIMAL(10,2) NOT NULL,
    setup_fee DECIMAL(10,2) DEFAULT 0.00,

    -- Plan details
    description TEXT,
    jersey_quantity INT UNSIGNED DEFAULT 1 COMMENT 'Jerseys per month',
    jersey_quality ENUM('fan', 'player', 'retro') DEFAULT 'fan',

    -- Features (boolean flags)
    includes_early_access BOOLEAN DEFAULT FALSE,
    includes_priority_shipping BOOLEAN DEFAULT FALSE,
    includes_collector_pin BOOLEAN DEFAULT FALSE,
    includes_store_discount BOOLEAN DEFAULT FALSE,
    includes_certificate BOOLEAN DEFAULT FALSE,
    store_discount_percentage INT UNSIGNED NULL COMMENT 'e.g., 10 for 10%',

    -- Display
    badge_text VARCHAR(50) NULL COMMENT 'e.g., MÁS POPULAR, PREMIUM',
    badge_color VARCHAR(7) NULL COMMENT 'Hex color',
    display_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (plan_slug),
    INDEX idx_active_order (is_active, display_order)
) ENGINE=InnoDB;

-- Customer subscriptions
CREATE TABLE subscriptions (
    subscription_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,

    -- Subscription status
    status ENUM('active', 'pending', 'cancelled', 'paused', 'expired') DEFAULT 'pending',

    -- Dates
    start_date DATE NOT NULL,
    current_period_start DATE NOT NULL,
    current_period_end DATE NOT NULL,
    next_billing_date DATE NULL,
    cancellation_date DATE NULL,
    cancellation_reason TEXT NULL,
    pause_date DATE NULL,
    pause_reason TEXT NULL,

    -- Preferences
    preferred_size ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL') NOT NULL,
    league_preferences JSON NULL COMMENT 'Array of league_ids',
    team_preferences JSON NULL COMMENT 'Array of team_ids',
    teams_to_exclude JSON NULL COMMENT 'Array of team_ids',

    -- Payment tracking (manual management)
    total_months_paid INT UNSIGNED DEFAULT 0,
    last_payment_date DATE NULL,
    last_payment_amount DECIMAL(10,2) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE RESTRICT,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(plan_id) ON DELETE RESTRICT,
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_next_billing (next_billing_date),
    INDEX idx_period_end (current_period_end)
) ENGINE=InnoDB;

-- Subscription shipments history
CREATE TABLE subscription_shipments (
    shipment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT UNSIGNED NOT NULL,

    -- Shipment details
    shipment_date DATE NOT NULL,
    expected_delivery_date DATE NULL,
    actual_delivery_date DATE NULL,
    tracking_number VARCHAR(100) NULL,
    carrier VARCHAR(100) NULL,

    -- Status
    status ENUM('pending', 'preparing', 'shipped', 'in_transit', 'delivered', 'returned', 'failed') DEFAULT 'pending',

    -- Contents (what was sent)
    contents JSON NOT NULL COMMENT 'Array of {product_id, variant_id, quantity}',

    -- Notes
    notes TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE RESTRICT,
    INDEX idx_subscription (subscription_id),
    INDEX idx_status (status),
    INDEX idx_shipment_date (shipment_date)
) ENGINE=InnoDB;

-- Subscription payment history (manual tracking)
CREATE TABLE subscription_payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT UNSIGNED NOT NULL,

    -- Payment details
    payment_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('oxapay', 'telegram', 'whatsapp', 'manual') NOT NULL,
    payment_reference VARCHAR(255) NULL,

    -- Period covered
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,

    -- Status
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',

    -- Transaction details
    transaction_id VARCHAR(255) NULL COMMENT 'Oxapay transaction ID',
    transaction_data JSON NULL,

    -- Notes
    notes TEXT NULL,
    processed_by INT UNSIGNED NULL COMMENT 'Admin user who processed',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE RESTRICT,
    INDEX idx_subscription (subscription_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================================
-- 4. MYSTERY BOX ENTITIES
-- ============================================================================

-- Mystery box types
CREATE TABLE mystery_box_types (
    box_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    -- Pricing
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) NULL COMMENT 'Display price (crossed out)',

    -- Box contents
    jersey_quantity INT UNSIGNED DEFAULT 5,
    jersey_quality ENUM('fan', 'player', 'mixed') DEFAULT 'fan',

    -- Restrictions
    league_restriction INT UNSIGNED NULL COMMENT 'FK to leagues, NULL = any league',
    team_tier_restriction ENUM('any', 'top_only') DEFAULT 'any',

    -- Features
    includes_premium_packaging BOOLEAN DEFAULT FALSE,
    includes_certificate BOOLEAN DEFAULT FALSE,
    includes_express_shipping BOOLEAN DEFAULT FALSE,

    -- Display
    badge_text VARCHAR(50) NULL,
    badge_color VARCHAR(7) NULL,
    display_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (league_restriction) REFERENCES leagues(league_id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_active_order (is_active, display_order)
) ENGINE=InnoDB;

-- Mystery box orders
CREATE TABLE mystery_box_orders (
    order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    box_type_id INT UNSIGNED NOT NULL,

    -- Order details
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'preparing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',

    -- Box preferences
    selected_league_id INT UNSIGNED NULL COMMENT 'For "Box por Liga"',
    preferred_size ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL') NOT NULL,
    special_instructions TEXT NULL,

    -- Pricing
    total_price DECIMAL(10,2) NOT NULL,

    -- Shipping
    shipping_address_id INT UNSIGNED NOT NULL,
    tracking_number VARCHAR(100) NULL,
    shipped_date DATE NULL,
    delivered_date DATE NULL,

    -- Payment
    payment_id INT UNSIGNED NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE RESTRICT,
    FOREIGN KEY (box_type_id) REFERENCES mystery_box_types(box_type_id) ON DELETE RESTRICT,
    FOREIGN KEY (selected_league_id) REFERENCES leagues(league_id) ON DELETE SET NULL,
    FOREIGN KEY (shipping_address_id) REFERENCES shipping_addresses(address_id) ON DELETE RESTRICT,
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB;

-- Mystery box contents (what was actually in each box)
CREATE TABLE mystery_box_contents (
    content_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED DEFAULT 1,
    reveal_date TIMESTAMP NULL COMMENT 'When customer opened/revealed',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES mystery_box_orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE RESTRICT,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- ============================================================================
-- 5. DROP EVENT ENTITIES (Gamification)
-- ============================================================================

-- Drop events
CREATE TABLE drop_events (
    drop_event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    description TEXT,

    -- Event timing
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,

    -- Drop limits
    total_drops_available INT UNSIGNED DEFAULT 100,
    remaining_drops INT UNSIGNED DEFAULT 100,
    max_drops_per_customer INT UNSIGNED DEFAULT 1,

    -- Pricing
    drop_price DECIMAL(10,2) NOT NULL DEFAULT 24.99,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_dates (start_date, end_date),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Drop pool items (what can be won)
CREATE TABLE drop_pool_items (
    pool_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    drop_event_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,

    -- Rarity system
    rarity ENUM('common', 'rare', 'legendary') NOT NULL,
    weight INT UNSIGNED NOT NULL COMMENT 'Probability weight (higher = more likely)',

    -- Display
    display_order INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (drop_event_id) REFERENCES drop_events(drop_event_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_event (drop_event_id),
    INDEX idx_rarity (rarity),
    INDEX idx_weight (weight)
) ENGINE=InnoDB;

-- Drop results (what each customer won)
CREATE TABLE drop_results (
    result_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    drop_event_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NULL COMMENT 'NULL for guest drops',
    session_id VARCHAR(100) NULL COMMENT 'For guest tracking',

    -- Result
    pool_item_id INT UNSIGNED NOT NULL,
    selected_size ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL') NOT NULL,

    -- Purchase status
    was_purchased BOOLEAN DEFAULT FALSE,
    catalog_order_id INT UNSIGNED NULL,

    -- Timing
    result_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    purchase_deadline TIMESTAMP NULL COMMENT '24h or 48h after drop',
    purchased_at TIMESTAMP NULL,

    FOREIGN KEY (drop_event_id) REFERENCES drop_events(drop_event_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    FOREIGN KEY (pool_item_id) REFERENCES drop_pool_items(pool_item_id) ON DELETE RESTRICT,
    INDEX idx_event_customer (drop_event_id, customer_id),
    INDEX idx_purchased (was_purchased),
    INDEX idx_result_date (result_date)
) ENGINE=InnoDB;

-- ============================================================================
-- 6. ORDER ENTITIES (Catalog orders)
-- ============================================================================

-- Main orders table
CREATE TABLE orders (
    order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,

    -- Order type and source
    order_type ENUM('catalog', 'mystery_box', 'subscription_initial', 'drop', 'upsell') DEFAULT 'catalog',
    order_source ENUM('web', 'telegram', 'whatsapp', 'instagram') DEFAULT 'web',

    -- Status
    order_status ENUM('pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending_payment',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded', 'partially_refunded') DEFAULT 'pending',

    -- Pricing
    subtotal DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    coupon_id INT UNSIGNED NULL,
    shipping_cost DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,

    -- Payment
    payment_method ENUM('oxapay', 'telegram', 'whatsapp', 'manual') NULL,
    payment_id INT UNSIGNED NULL,

    -- Shipping
    shipping_address_id INT UNSIGNED NOT NULL,
    tracking_number VARCHAR(100) NULL,
    carrier VARCHAR(100) NULL,
    shipped_date DATE NULL,
    delivered_date DATE NULL,

    -- Notes
    customer_notes TEXT NULL,
    admin_notes TEXT NULL,

    -- Timestamps
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE RESTRICT,
    FOREIGN KEY (shipping_address_id) REFERENCES shipping_addresses(address_id) ON DELETE RESTRICT,
    INDEX idx_customer (customer_id),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_order_date (order_date),
    INDEX idx_type (order_type)
) ENGINE=InnoDB;

-- Order items
CREATE TABLE order_items (
    order_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NOT NULL,

    -- Quantity and pricing
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,

    -- Customizations
    has_patches BOOLEAN DEFAULT FALSE,
    patches_price DECIMAL(10,2) DEFAULT 0.00,
    has_personalization BOOLEAN DEFAULT FALSE,
    personalization_name VARCHAR(50) NULL,
    personalization_number VARCHAR(5) NULL,
    personalization_price DECIMAL(10,2) DEFAULT 0.00,

    -- Calculated
    subtotal DECIMAL(10,2) NOT NULL,

    -- Special offers
    is_free_item BOOLEAN DEFAULT FALSE COMMENT 'For 3x2 promotions',
    promotion_id INT UNSIGNED NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ============================================================================
-- 7. PAYMENT ENTITIES
-- ============================================================================

-- Payment transactions (Oxapay and manual)
CREATE TABLE payment_transactions (
    transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Related entities
    customer_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NULL,
    subscription_id INT UNSIGNED NULL,

    -- Payment details
    payment_method ENUM('oxapay_btc', 'oxapay_eth', 'oxapay_usdt', 'telegram_manual', 'whatsapp_manual', 'bank_transfer') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'EUR',

    -- Status
    status ENUM('pending', 'processing', 'completed', 'failed', 'expired', 'refunded') DEFAULT 'pending',

    -- Oxapay specific
    oxapay_transaction_id VARCHAR(255) NULL,
    oxapay_payment_url VARCHAR(500) NULL,
    oxapay_qr_code VARCHAR(500) NULL,
    oxapay_crypto_amount DECIMAL(20,8) NULL,
    oxapay_crypto_currency VARCHAR(10) NULL,
    oxapay_network VARCHAR(50) NULL,
    oxapay_wallet_address VARCHAR(255) NULL,
    oxapay_response JSON NULL,

    -- Manual payment tracking
    manual_payment_reference VARCHAR(255) NULL,
    manual_payment_proof VARCHAR(500) NULL COMMENT 'Screenshot/proof upload path',
    verified_by INT UNSIGNED NULL COMMENT 'Admin who verified',
    verified_at TIMESTAMP NULL,

    -- Timestamps
    initiated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,

    -- Notes
    notes TEXT NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE RESTRICT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_order (order_id),
    INDEX idx_status (status),
    INDEX idx_oxapay_id (oxapay_transaction_id),
    INDEX idx_initiated (initiated_at)
) ENGINE=InnoDB;

-- Payment webhooks log (for Oxapay callbacks)
CREATE TABLE payment_webhooks (
    webhook_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NULL,

    -- Webhook data
    webhook_type VARCHAR(50) NOT NULL,
    payload JSON NOT NULL,
    signature VARCHAR(255) NULL,

    -- Processing
    processed BOOLEAN DEFAULT FALSE,
    processed_at TIMESTAMP NULL,
    processing_error TEXT NULL,

    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (transaction_id) REFERENCES payment_transactions(transaction_id) ON DELETE SET NULL,
    INDEX idx_transaction (transaction_id),
    INDEX idx_processed (processed),
    INDEX idx_received (received_at)
) ENGINE=InnoDB;

-- ============================================================================
-- 8. PROMOTION & DISCOUNT ENTITIES
-- ============================================================================

-- Coupons
CREATE TABLE coupons (
    coupon_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),

    -- Discount details
    discount_type ENUM('fixed', 'percentage') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    max_discount_amount DECIMAL(10,2) NULL COMMENT 'For percentage coupons',

    -- Restrictions
    min_purchase_amount DECIMAL(10,2) DEFAULT 0.00,
    applies_to_product_type ENUM('all', 'jersey', 'mystery_box', 'subscription') DEFAULT 'all',
    applies_to_first_order_only BOOLEAN DEFAULT FALSE,

    -- Usage limits
    usage_limit_total INT UNSIGNED NULL COMMENT 'NULL = unlimited',
    usage_limit_per_customer INT UNSIGNED DEFAULT 1,
    times_used INT UNSIGNED DEFAULT 0,

    -- Validity
    valid_from TIMESTAMP NULL,
    valid_until TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,

    -- Tracking
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_validity (valid_from, valid_until)
) ENGINE=InnoDB;

-- Coupon usage tracking
CREATE TABLE coupon_usage (
    usage_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL,

    discount_applied DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (coupon_id) REFERENCES coupons(coupon_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_coupon (coupon_id),
    INDEX idx_customer (customer_id),
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- Promotional campaigns
CREATE TABLE promotional_campaigns (
    campaign_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_name VARCHAR(255) NOT NULL,
    campaign_type ENUM('3x2', 'first_purchase', 'exit_intent', 'bundle', 'flash_sale', 'seasonal') NOT NULL,

    -- Campaign details
    description TEXT,
    trigger_condition JSON COMMENT 'Conditions for when to show campaign',
    discount_description VARCHAR(255),

    -- Associated coupon (optional)
    auto_apply_coupon_id INT UNSIGNED NULL,

    -- Validity
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,

    -- Analytics
    impression_count INT UNSIGNED DEFAULT 0,
    conversion_count INT UNSIGNED DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (auto_apply_coupon_id) REFERENCES coupons(coupon_id) ON DELETE SET NULL,
    INDEX idx_active (is_active),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_type (campaign_type)
) ENGINE=InnoDB;

-- 3x2 Promotion tracking
CREATE TABLE promotion_3x2_usage (
    usage_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,

    -- Items involved
    paid_item_1_id INT UNSIGNED NOT NULL COMMENT 'FK to order_items',
    paid_item_2_id INT UNSIGNED NOT NULL COMMENT 'FK to order_items',
    free_item_id INT UNSIGNED NOT NULL COMMENT 'FK to order_items',

    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_customer (customer_id)
) ENGINE=InnoDB;

-- ============================================================================
-- 9. LOYALTY & REWARDS ENTITIES (NEW)
-- ============================================================================

-- Loyalty points transactions
CREATE TABLE loyalty_points_history (
    transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,

    -- Transaction details
    points_change INT NOT NULL COMMENT 'Positive for earned, negative for spent',
    points_balance_after INT UNSIGNED NOT NULL,

    -- Source
    transaction_type ENUM('order_purchase', 'order_refund', 'points_redemption', 'birthday_bonus', 'referral', 'manual_adjustment', 'tier_bonus') NOT NULL,
    reference_order_id INT UNSIGNED NULL,
    description VARCHAR(255),

    -- Expiration (if applicable)
    expires_at DATE NULL,

    -- Admin
    created_by INT UNSIGNED NULL COMMENT 'Admin user for manual adjustments',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (reference_order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_type (transaction_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Loyalty tier benefits configuration
CREATE TABLE loyalty_tier_benefits (
    benefit_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tier ENUM('standard', 'silver', 'gold', 'platinum') NOT NULL,

    -- Requirements
    min_orders_required INT UNSIGNED DEFAULT 0,
    min_total_spent DECIMAL(10,2) DEFAULT 0.00,

    -- Benefits
    points_multiplier DECIMAL(3,2) DEFAULT 1.00 COMMENT 'e.g., 1.5 = 50% more points',
    discount_percentage INT UNSIGNED DEFAULT 0,
    free_shipping BOOLEAN DEFAULT FALSE,
    early_drop_access BOOLEAN DEFAULT FALSE,
    priority_support BOOLEAN DEFAULT FALSE,
    birthday_bonus_points INT UNSIGNED DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_tier (tier)
) ENGINE=InnoDB;

-- Loyalty rewards catalog
CREATE TABLE loyalty_rewards (
    reward_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reward_name VARCHAR(255) NOT NULL,
    description TEXT,

    -- Cost
    points_required INT UNSIGNED NOT NULL,

    -- Reward type
    reward_type ENUM('discount_coupon', 'free_shipping', 'free_product', 'percentage_off') NOT NULL,
    reward_value VARCHAR(255) COMMENT 'Coupon code, product_id, or percentage',

    -- Limits
    max_redemptions_total INT UNSIGNED NULL,
    max_redemptions_per_customer INT UNSIGNED DEFAULT 1,
    times_redeemed INT UNSIGNED DEFAULT 0,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_points (points_required),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================================
-- 10. WISHLIST ENTITIES (NEW)
-- ============================================================================

-- Customer wishlists
CREATE TABLE wishlists (
    wishlist_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NULL COMMENT 'Specific size if selected',

    -- Preferences
    notify_on_stock BOOLEAN DEFAULT TRUE,
    notify_on_price_drop BOOLEAN DEFAULT TRUE,
    price_when_added DECIMAL(10,2) NULL,

    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (customer_id, product_id, variant_id),
    INDEX idx_customer (customer_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- Wishlist notifications sent
CREATE TABLE wishlist_notifications_sent (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wishlist_id INT UNSIGNED NOT NULL,
    notification_type ENUM('back_in_stock', 'price_drop', 'low_stock_alert') NOT NULL,

    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (wishlist_id) REFERENCES wishlists(wishlist_id) ON DELETE CASCADE,
    INDEX idx_wishlist (wishlist_id),
    INDEX idx_sent (sent_at)
) ENGINE=InnoDB;

-- ============================================================================
-- 11. CART ENTITIES
-- ============================================================================

-- Shopping carts
CREATE TABLE carts (
    cart_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NULL COMMENT 'NULL for guest carts',
    session_id VARCHAR(100) NULL COMMENT 'For guest carts',

    -- Status
    cart_status ENUM('active', 'abandoned', 'converted', 'expired') DEFAULT 'active',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    converted_to_order_id INT UNSIGNED NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (converted_to_order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_session (session_id),
    INDEX idx_status (cart_status),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- Cart items
CREATE TABLE cart_items (
    cart_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NOT NULL,

    -- Quantity
    quantity INT UNSIGNED NOT NULL DEFAULT 1,

    -- Customizations
    has_patches BOOLEAN DEFAULT FALSE,
    has_personalization BOOLEAN DEFAULT FALSE,
    personalization_name VARCHAR(50) NULL,
    personalization_number VARCHAR(5) NULL,

    -- Pricing snapshot
    unit_price DECIMAL(10,2) NOT NULL,

    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    INDEX idx_cart (cart_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ============================================================================
-- 12. INVENTORY MANAGEMENT ENTITIES
-- ============================================================================

-- Stock movements log
CREATE TABLE stock_movements (
    movement_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_variant_id INT UNSIGNED NOT NULL,

    -- Movement details
    movement_type ENUM('purchase', 'sale', 'return', 'adjustment', 'reserved', 'unreserved', 'damaged', 'lost') NOT NULL,
    quantity INT NOT NULL COMMENT 'Can be negative for outbound movements',
    stock_after INT UNSIGNED NOT NULL,

    -- Reference
    reference_order_id INT UNSIGNED NULL,
    reference_subscription_shipment_id INT UNSIGNED NULL,
    reference_mystery_box_order_id INT UNSIGNED NULL,

    -- Notes and admin
    notes TEXT NULL,
    created_by INT UNSIGNED NULL COMMENT 'Admin user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    FOREIGN KEY (reference_order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    FOREIGN KEY (reference_subscription_shipment_id) REFERENCES subscription_shipments(shipment_id) ON DELETE SET NULL,
    FOREIGN KEY (reference_mystery_box_order_id) REFERENCES mystery_box_orders(order_id) ON DELETE SET NULL,
    INDEX idx_variant (product_variant_id),
    INDEX idx_type (movement_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Low stock alerts
CREATE TABLE low_stock_alerts (
    alert_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_variant_id INT UNSIGNED NOT NULL,

    -- Alert details
    threshold_quantity INT UNSIGNED NOT NULL,
    current_quantity INT UNSIGNED NOT NULL,
    alert_status ENUM('pending', 'notified', 'resolved', 'dismissed') DEFAULT 'pending',

    -- Resolution
    resolved_at TIMESTAMP NULL,
    resolved_by INT UNSIGNED NULL,
    resolution_notes TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_variant_id) REFERENCES product_variants(variant_id) ON DELETE CASCADE,
    INDEX idx_variant (product_variant_id),
    INDEX idx_status (alert_status)
) ENGINE=InnoDB;

-- ============================================================================
-- 13. COMMUNICATION ENTITIES
-- ============================================================================

-- Customer messages (Telegram/WhatsApp)
CREATE TABLE customer_messages (
    message_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,

    -- Channel and direction
    channel ENUM('telegram', 'whatsapp', 'email', 'instagram', 'internal') NOT NULL,
    direction ENUM('inbound', 'outbound') NOT NULL,

    -- Message content
    message_subject VARCHAR(255) NULL,
    message_content TEXT NOT NULL,
    message_data JSON NULL COMMENT 'Raw message data from platform',

    -- Context
    related_order_id INT UNSIGNED NULL,
    related_subscription_id INT UNSIGNED NULL,

    -- Status
    status ENUM('sent', 'delivered', 'read', 'failed') DEFAULT 'sent',

    -- Timestamps
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,

    -- Admin handling
    handled_by INT UNSIGNED NULL,
    is_resolved BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (related_order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    FOREIGN KEY (related_subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_channel (channel),
    INDEX idx_status (status),
    INDEX idx_sent (sent_at)
) ENGINE=InnoDB;

-- System notifications to customers
CREATE TABLE notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,

    -- Notification details
    notification_type ENUM('order_shipped', 'order_delivered', 'subscription_renewal', 'subscription_expiring', 'drop_available', 'stock_alert', 'price_drop', 'payment_reminder') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,

    -- Delivery
    sent_via ENUM('telegram', 'whatsapp', 'email', 'web_push') NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,

    -- Context
    related_order_id INT UNSIGNED NULL,
    related_subscription_id INT UNSIGNED NULL,
    action_url VARCHAR(500) NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (related_order_id) REFERENCES orders(order_id) ON DELETE SET NULL,
    FOREIGN KEY (related_subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_type (notification_type),
    INDEX idx_read (read_at),
    INDEX idx_sent (sent_at)
) ENGINE=InnoDB;

-- ============================================================================
-- 14. ANALYTICS ENTITIES
-- ============================================================================

-- Analytics events
CREATE TABLE analytics_events (
    event_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- User identification
    customer_id INT UNSIGNED NULL,
    session_id VARCHAR(100) NOT NULL,

    -- Event details
    event_type ENUM('page_view', 'cta_click', 'product_view', 'add_to_cart', 'remove_from_cart', 'checkout_start', 'purchase', 'exit_intent', 'scroll_depth', 'time_on_page', 'form_submit', 'drop_play', 'video_play') NOT NULL,
    event_category VARCHAR(100),
    event_label VARCHAR(255),
    event_value DECIMAL(10,2) NULL,

    -- Page context
    page_url VARCHAR(500),
    page_title VARCHAR(255),
    referrer_url VARCHAR(500),

    -- Device/Browser
    device_type ENUM('desktop', 'mobile', 'tablet') NULL,
    browser VARCHAR(100),
    os VARCHAR(100),
    screen_resolution VARCHAR(20),

    -- Location (if available)
    ip_address VARCHAR(45),
    country VARCHAR(100),
    city VARCHAR(100),

    -- Event data
    event_data JSON NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    INDEX idx_customer (customer_id),
    INDEX idx_session (session_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created (created_at),
    INDEX idx_page_url (page_url(255))
) ENGINE=InnoDB;

-- Product views tracking
CREATE TABLE product_views (
    view_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NULL,
    session_id VARCHAR(100) NOT NULL,

    -- View details
    time_spent_seconds INT UNSIGNED DEFAULT 0,
    scrolled_to_description BOOLEAN DEFAULT FALSE,
    scrolled_to_reviews BOOLEAN DEFAULT FALSE,
    clicked_add_to_cart BOOLEAN DEFAULT FALSE,

    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_customer (customer_id),
    INDEX idx_session (session_id),
    INDEX idx_viewed (viewed_at)
) ENGINE=InnoDB;

-- ============================================================================
-- 15. ADMIN & SYSTEM ENTITIES
-- ============================================================================

-- Admin users
CREATE TABLE admin_users (
    admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,

    -- Role and permissions
    role ENUM('super_admin', 'admin', 'inventory_manager', 'customer_service', 'marketing', 'readonly') NOT NULL,
    permissions JSON NULL COMMENT 'Specific permissions array',

    -- Status
    is_active BOOLEAN DEFAULT TRUE,

    -- Security
    last_login TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    failed_login_attempts INT UNSIGNED DEFAULT 0,
    locked_until TIMESTAMP NULL,

    -- 2FA (future)
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Audit log
CREATE TABLE audit_log (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Who
    admin_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NULL COMMENT 'If customer action',

    -- What
    action_type ENUM('create', 'update', 'delete', 'login', 'logout', 'password_change', 'status_change', 'payment_verify') NOT NULL,
    entity_type VARCHAR(100) NOT NULL COMMENT 'e.g., product, order, customer',
    entity_id INT UNSIGNED NULL,

    -- Details
    old_values JSON NULL,
    new_values JSON NULL,
    description TEXT NULL,

    -- Context
    ip_address VARCHAR(45),
    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (admin_id) REFERENCES admin_users(admin_id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    INDEX idx_admin (admin_id),
    INDEX idx_customer (customer_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- System settings
CREATE TABLE system_settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Can be accessed by frontend',

    updated_by INT UNSIGNED NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (updated_by) REFERENCES admin_users(admin_id) ON DELETE SET NULL,
    INDEX idx_key (setting_key),
    INDEX idx_public (is_public)
) ENGINE=InnoDB;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger to update customer stats on order
DELIMITER //
CREATE TRIGGER update_customer_stats_after_order
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_status IN ('delivered', 'processing') THEN
        UPDATE customers
        SET total_orders_count = total_orders_count + 1,
            total_spent = total_spent + NEW.total_amount,
            last_activity_date = NOW()
        WHERE customer_id = NEW.customer_id;
    END IF;
END//

-- Trigger to create stock movement on order
CREATE TRIGGER create_stock_movement_on_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    INSERT INTO stock_movements (
        product_variant_id,
        movement_type,
        quantity,
        stock_after,
        reference_order_id,
        created_at
    )
    SELECT
        NEW.variant_id,
        'reserved',
        -NEW.quantity,
        pv.stock_quantity - NEW.quantity,
        NEW.order_id,
        NOW()
    FROM product_variants pv
    WHERE pv.variant_id = NEW.variant_id;

    UPDATE product_variants
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE variant_id = NEW.variant_id;
END//

-- Trigger to check low stock after movement
CREATE TRIGGER check_low_stock_after_movement
AFTER INSERT ON stock_movements
FOR EACH ROW
BEGIN
    DECLARE v_low_stock_threshold INT;

    SELECT low_stock_threshold INTO v_low_stock_threshold
    FROM product_variants
    WHERE variant_id = NEW.product_variant_id;

    IF NEW.stock_after <= v_low_stock_threshold THEN
        INSERT INTO low_stock_alerts (
            product_variant_id,
            threshold_quantity,
            current_quantity,
            alert_status
        )
        VALUES (
            NEW.product_variant_id,
            v_low_stock_threshold,
            NEW.stock_after,
            'pending'
        )
        ON DUPLICATE KEY UPDATE
            current_quantity = NEW.stock_after,
            alert_status = 'pending',
            updated_at = NOW();
    END IF;
END//

-- Trigger to update loyalty points on order completion
CREATE TRIGGER award_loyalty_points_on_order
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    DECLARE points_earned INT;
    DECLARE multiplier DECIMAL(3,2);

    IF OLD.order_status != 'delivered' AND NEW.order_status = 'delivered' THEN
        -- Get customer's loyalty multiplier
        SELECT COALESCE(ltb.points_multiplier, 1.00) INTO multiplier
        FROM customers c
        LEFT JOIN loyalty_tier_benefits ltb ON c.loyalty_tier = ltb.tier
        WHERE c.customer_id = NEW.customer_id;

        -- Calculate points (1 point per EUR spent)
        SET points_earned = FLOOR(NEW.total_amount * multiplier);

        -- Add points to customer
        UPDATE customers
        SET loyalty_points = loyalty_points + points_earned
        WHERE customer_id = NEW.customer_id;

        -- Log transaction
        INSERT INTO loyalty_points_history (
            customer_id,
            points_change,
            points_balance_after,
            transaction_type,
            reference_order_id,
            description
        )
        SELECT
            NEW.customer_id,
            points_earned,
            loyalty_points,
            'order_purchase',
            NEW.order_id,
            CONCAT('Order #', NEW.order_id, ' - ', FORMAT(NEW.total_amount, 2), ' EUR')
        FROM customers
        WHERE customer_id = NEW.customer_id;
    END IF;
END//

-- Trigger to update subscription expiration
CREATE TRIGGER check_subscription_expiration
BEFORE UPDATE ON subscriptions
FOR EACH ROW
BEGIN
    IF NEW.current_period_end < CURDATE() AND NEW.status = 'active' THEN
        SET NEW.status = 'expired';
    END IF;
END//

DELIMITER ;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Additional composite indexes for common queries
CREATE INDEX idx_orders_customer_status ON orders(customer_id, order_status);
CREATE INDEX idx_orders_date_status ON orders(order_date, order_status);
CREATE INDEX idx_products_league_team ON products(league_id, team_id);
CREATE INDEX idx_products_active_type ON products(is_active, product_type);
CREATE INDEX idx_subscriptions_status_next_billing ON subscriptions(status, next_billing_date);
CREATE INDEX idx_drop_results_event_customer ON drop_results(drop_event_id, customer_id);

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
