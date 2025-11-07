<?php
/**
 * Kickverse Application Entry Point
 * Main bootstrap file
 */

// FORCE ERROR DISPLAY FOR DEBUGGING
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start session
session_start();

// Error reporting based on environment
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
