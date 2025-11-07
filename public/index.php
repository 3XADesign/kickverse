<?php
/**
 * Kickverse Application Entry Point
 * Main bootstrap file
 */

// Load environment configuration
require_once __DIR__ . '/../config/env.php';

// Configure error reporting based on environment
if (env('APP_ENV') === 'production') {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

// Start session
session_start();

// Load app configuration
$config = require __DIR__ . '/../config/app.php';

// Set timezone
date_default_timezone_set($config['timezone']);

// Set default character encoding
mb_internal_encoding('UTF-8');

// Load database connection
require_once __DIR__ . '/../app/Database.php';

// Initialize database
Database::getInstance();

// Load i18n system
require_once __DIR__ . '/../app/helpers/i18n.php';
i18n::init('es');

// Load and dispatch router
$router = require __DIR__ . '/../routes/web.php';
$router->dispatch();
