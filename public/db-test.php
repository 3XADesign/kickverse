<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de Conexión a Base de Datos</h1>";

$config = [
    'host' => '50.31.174.69',
    'database' => 'iqvfmscx_kickverse',
    'username' => 'iqvfmscx_kickverse',
    'password' => 'I,nzP1aIY4cG',
];

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    
    echo "<p style='color:green;'>✅ Conexión exitosa a la base de datos!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Total de productos en la BD: <strong>{$result['total']}</strong></p>";
    
    // Get some products
    $stmt = $pdo->query("SELECT name, base_price FROM products LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Productos de ejemplo:</h2><ul>";
    foreach ($products as $product) {
        echo "<li>{$product['name']} - €{$product['base_price']}</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
}
?>
