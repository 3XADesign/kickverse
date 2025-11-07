<?php
/**
 * Database Configuration
 * Kickverse - MySQL Database Connection Settings
 */

return [
    'host' => 'localhost',
    'database' => 'iqvfmscx_kickverse',
    'username' => 'iqvfmscx_kickverse',
    'password' => 'I,nzP1aIY4cG',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
