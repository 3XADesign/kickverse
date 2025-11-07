<?php
/**
 * Application Configuration
 * Kickverse - Main App Settings
 */

return [
    // App Settings
    'name' => 'Kickverse',
    'env' => 'production', // development, production
    'debug' => false,
    'url' => 'https://kickverse.es',
    'timezone' => 'Europe/Madrid',
    'locale' => 'es',
    'fallback_locale' => 'en',

    // Paths
    'root' => dirname(__DIR__),
    'app' => dirname(__DIR__) . '/app',
    'public' => dirname(__DIR__) . '/public',
    'storage' => dirname(__DIR__) . '/storage',
    'views' => dirname(__DIR__) . '/app/views',

    // Security
    'session_lifetime' => 120, // minutes
    'session_name' => 'kickverse_session',
    'csrf_token_name' => 'csrf_token',

    // Contact
    'contacts' => [
        'telegram' => '@esKickverse',
        'whatsapp' => '+34 614 299 735',
        'email' => 'hola@kickverse.es',
        'instagram' => '@kickverse.es',
        'twitter' => '@kickverse_es',
        'tiktok' => '@kickverse_es',
    ],

    // Business Rules
    'pricing' => [
        'base_jersey_price' => 24.99,
        'patches_price' => 1.99,
        'personalization_price' => 2.99,
        'free_shipping_threshold' => 50.00,
        'currency' => 'EUR',
    ],

    // Analytics
    'analytics' => [
        'gtm_id' => 'GTM-MQFTT34L',
        'ga_id' => 'G-SD9ETEJ9TG',
    ],

    // Payments
    'oxapay' => [
        'api_key' => getenv('OXAPAY_API_KEY') ?: '',
        'merchant_id' => getenv('OXAPAY_MERCHANT_ID') ?: '',
        'webhook_url' => 'https://kickverse.es/api/webhooks/oxapay',
    ],
];
