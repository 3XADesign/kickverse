<?php
/**
 * Database Configuration
 * Kickverse - MySQL Database Connection Settings
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }

    return $env;
}

$env = loadEnv(__DIR__ . '/../.env');

// Determine database host based on environment
$isProduction = isset($env['APP_ENV']) && $env['APP_ENV'] === 'production';
$dbHost = $isProduction ? 'localhost' : ($env['DB_HOST'] ?? '50.31.174.69');

return [
    'host' => $dbHost,
    'database' => $env['DB_DATABASE'] ?? 'iqvfmscx_kickverse',
    'username' => $env['DB_USERNAME'] ?? 'iqvfmscx_kickverse',
    'password' => $env['DB_PASSWORD'] ?? 'I,nzP1aIY4cG',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
