<?php
/**
 * Database Configuration
 * Kickverse - MySQL Database Connection Settings
 */

// Load environment helper
require_once __DIR__ . '/env.php';

// Determine database host based on environment
$isProduction = env('APP_ENV') === 'production';
$dbHost = $isProduction ? 'localhost' : env('DB_HOST', '50.31.174.69');

return [
    'host' => $dbHost,
    'database' => env('DB_DATABASE', 'iqvfmscx_kickverse'),
    'username' => env('DB_USERNAME', 'iqvfmscx_kickverse'),
    'password' => env('DB_PASSWORD', 'I,nzP1aIY4cG'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
