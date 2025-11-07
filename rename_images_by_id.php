#!/usr/bin/env php
<?php
/**
 * Script para renombrar imágenes por ID y actualizar la base de datos
 * Ejecutar desde la raíz del proyecto: php rename_images_by_id.php
 */

// Cargar configuración de base de datos
$dbConfig = require __DIR__ . '/config/database.php';

// Conectar a la base de datos
try {
    $db = new PDO(
        "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['database'] . ";charset=" . $dbConfig['charset'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    echo "✓ Conexión a base de datos exitosa\n\n";
} catch (PDOException $e) {
    die("✗ Error de conexión: " . $e->getMessage() . "\n");
}

// Contadores
$stats = [
    'leagues_renamed' => 0,
    'teams_renamed' => 0,
    'products_renamed' => 0,
    'errors' => []
];

echo "========================================\n";
echo "  RENOMBRADO DE IMÁGENES POR ID\n";
echo "========================================\n\n";

// ============================================
// 1. RENOMBRAR LOGOS DE LIGAS
// ============================================
echo "1. Procesando LIGAS...\n";
echo "----------------------------------------\n";

$leagues = $db->query("SELECT league_id, name, logo_path FROM leagues WHERE logo_path IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

foreach ($leagues as $league) {
    $oldPath = __DIR__ . '/public' . $league['logo_path'];

    if (!file_exists($oldPath)) {
        echo "  ⚠ Liga #{$league['league_id']} ({$league['name']}): archivo no existe: {$oldPath}\n";
        $stats['errors'][] = "Liga #{$league['league_id']}: archivo no encontrado";
        continue;
    }

    // Obtener extensión del archivo original
    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);

    // Nueva ruta con ID
    $newFilename = $league['league_id'] . '.' . $extension;
    $newPath = __DIR__ . '/public/img/leagues/' . $newFilename;
    $newDbPath = '/img/leagues/' . $newFilename;

    // Crear directorio si no existe
    $dir = dirname($newPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Renombrar archivo
    if (rename($oldPath, $newPath)) {
        // Actualizar base de datos
        $stmt = $db->prepare("UPDATE leagues SET logo_path = ? WHERE league_id = ?");
        $stmt->execute([$newDbPath, $league['league_id']]);

        echo "  ✓ Liga #{$league['league_id']} ({$league['name']}): {$newFilename}\n";
        $stats['leagues_renamed']++;
    } else {
        echo "  ✗ Error al renombrar: {$oldPath}\n";
        $stats['errors'][] = "Liga #{$league['league_id']}: error al renombrar";
    }
}

echo "\n";

// ============================================
// 2. RENOMBRAR LOGOS DE EQUIPOS
// ============================================
echo "2. Procesando EQUIPOS...\n";
echo "----------------------------------------\n";

$teams = $db->query("SELECT team_id, name, logo_path FROM teams WHERE logo_path IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

foreach ($teams as $team) {
    $oldPath = __DIR__ . '/public' . $team['logo_path'];

    if (!file_exists($oldPath)) {
        echo "  ⚠ Equipo #{$team['team_id']} ({$team['name']}): archivo no existe: {$oldPath}\n";
        $stats['errors'][] = "Equipo #{$team['team_id']}: archivo no encontrado";
        continue;
    }

    // Obtener extensión del archivo original
    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);

    // Nueva ruta con ID
    $newFilename = $team['team_id'] . '.' . $extension;
    $newPath = __DIR__ . '/public/img/logos/teams/' . $newFilename;
    $newDbPath = '/img/logos/teams/' . $newFilename;

    // Crear directorio si no existe
    $dir = dirname($newPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Renombrar archivo
    if (rename($oldPath, $newPath)) {
        // Actualizar base de datos
        $stmt = $db->prepare("UPDATE teams SET logo_path = ? WHERE team_id = ?");
        $stmt->execute([$newDbPath, $team['team_id']]);

        echo "  ✓ Equipo #{$team['team_id']} ({$team['name']}): {$newFilename}\n";
        $stats['teams_renamed']++;
    } else {
        echo "  ✗ Error al renombrar: {$oldPath}\n";
        $stats['errors'][] = "Equipo #{$team['team_id']}: error al renombrar";
    }
}

echo "\n";

// ============================================
// 3. RENOMBRAR IMÁGENES DE PRODUCTOS
// ============================================
echo "3. Procesando PRODUCTOS...\n";
echo "----------------------------------------\n";

$productImages = $db->query("
    SELECT pi.image_id, pi.product_id, pi.image_path, pi.image_type, pi.display_order, p.name
    FROM product_images pi
    JOIN products p ON pi.product_id = p.product_id
    ORDER BY pi.product_id, pi.image_type, pi.display_order
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($productImages as $image) {
    $oldPath = __DIR__ . '/public' . $image['image_path'];

    if (!file_exists($oldPath)) {
        echo "  ⚠ Producto #{$image['product_id']} ({$image['name']}): archivo no existe: {$oldPath}\n";
        $stats['errors'][] = "Producto #{$image['product_id']}: imagen #{$image['image_id']} no encontrada";
        continue;
    }

    // Obtener extensión del archivo original
    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);

    // Nueva ruta con ID: product_id-image_type-display_order.ext
    // Ejemplo: 1-main-0.png, 1-gallery-1.png
    $newFilename = $image['product_id'] . '-' . $image['image_type'] . '-' . $image['display_order'] . '.' . $extension;
    $newPath = __DIR__ . '/public/img/products/' . $newFilename;
    $newDbPath = '/img/products/' . $newFilename;

    // Crear directorio si no existe
    $dir = dirname($newPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Renombrar archivo
    if (rename($oldPath, $newPath)) {
        // Actualizar base de datos
        $stmt = $db->prepare("UPDATE product_images SET image_path = ? WHERE image_id = ?");
        $stmt->execute([$newDbPath, $image['image_id']]);

        echo "  ✓ Producto #{$image['product_id']} ({$image['name']}): {$newFilename}\n";
        $stats['products_renamed']++;
    } else {
        echo "  ✗ Error al renombrar: {$oldPath}\n";
        $stats['errors'][] = "Producto #{$image['product_id']}: error al renombrar imagen #{$image['image_id']}";
    }
}

echo "\n";

// ============================================
// RESUMEN FINAL
// ============================================
echo "========================================\n";
echo "  RESUMEN\n";
echo "========================================\n";
echo "✓ Ligas renombradas:    {$stats['leagues_renamed']}\n";
echo "✓ Equipos renombrados:  {$stats['teams_renamed']}\n";
echo "✓ Productos renombrados: {$stats['products_renamed']}\n";
echo "✗ Errores:              " . count($stats['errors']) . "\n";

if (!empty($stats['errors'])) {
    echo "\nErrores encontrados:\n";
    foreach ($stats['errors'] as $error) {
        echo "  - $error\n";
    }
}

echo "\n✓ Proceso completado!\n";
