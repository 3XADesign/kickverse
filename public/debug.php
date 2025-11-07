<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Test</h1>";

// Test 1: PHP Works
echo "<p>✅ PHP funciona correctamente</p>";

// Test 2: Include paths
echo "<h2>Rutas:</h2>";
echo "<p>__DIR__: " . __DIR__ . "</p>";
echo "<p>dirname(__DIR__): " . dirname(__DIR__) . "</p>";

// Test 3: Check if files exist
$configPath = dirname(__DIR__) . '/config/app.php';
echo "<p>Config exists: " . (file_exists($configPath) ? '✅ Sí' : '❌ No') . " ($configPath)</p>";

$dbPath = dirname(__DIR__) . '/app/Database.php';
echo "<p>Database.php exists: " . (file_exists($dbPath) ? '✅ Sí' : '❌ No') . " ($dbPath)</p>";

$routesPath = dirname(__DIR__) . '/routes/web.php';
echo "<p>Routes exists: " . (file_exists($routesPath) ? '✅ Sí' : '❌ No') . " ($routesPath)</p>";

// Test 4: Try to load config
echo "<h2>Test de Config:</h2>";
try {
    $config = require $configPath;
    echo "<pre>";
    print_r($config);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
