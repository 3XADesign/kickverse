<?php
/**
 * Script para vincular logos de equipos con la base de datos
 * Este script se ejecutará una vez y luego el admin panel permitirá gestión manual
 */

require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        $config['options']
    );

    $logosBasePath = __DIR__ . '/../public/img/logos/teams';
    $webBasePath = '/img/logos/teams';

    // Mapeo de ligas
    $leagues = [
        'laliga' => 1,
        'premier' => 2,
        'seriea' => 3,
        'bundesliga' => 4,
        'ligue1' => 5,
        'selecciones' => 6
    ];

    $totalUpdated = 0;
    $totalSkipped = 0;
    $notFound = [];

    foreach ($leagues as $leagueSlug => $leagueId) {
        $leagueDir = "$logosBasePath/$leagueSlug";

        if (!is_dir($leagueDir)) {
            echo "❌ Carpeta no encontrada: $leagueDir\n";
            continue;
        }

        // Obtener equipos de esta liga
        $stmt = $pdo->prepare("SELECT team_id, name, slug FROM teams WHERE league_id = ? AND is_active = 1");
        $stmt->execute([$leagueId]);
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\n📂 Procesando liga: " . strtoupper($leagueSlug) . " (" . count($teams) . " equipos)\n";
        echo str_repeat("-", 60) . "\n";

        foreach ($teams as $team) {
            $teamName = $team['name'];
            $teamSlug = $team['slug'];
            $teamId = $team['team_id'];

            // Limpiar nombre para buscar archivo
            $cleanName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($teamName));
            $cleanName = trim($cleanName, '-');

            // Buscar posibles archivos
            $possibleFiles = [
                "$cleanName.png",
                "$teamSlug.png",
                str_replace(' ', '-', strtolower($teamName)) . '.png',
                str_replace(' ', '_', strtolower($teamName)) . '.png',
            ];

            $logoFound = false;
            foreach ($possibleFiles as $filename) {
                $fullPath = "$leagueDir/$filename";
                if (file_exists($fullPath)) {
                    $webPath = "$webBasePath/$leagueSlug/$filename";

                    // Actualizar base de datos
                    $updateStmt = $pdo->prepare("UPDATE teams SET logo_path = ? WHERE team_id = ?");
                    $updateStmt->execute([$webPath, $teamId]);

                    echo "✅ {$teamName}: $webPath\n";
                    $totalUpdated++;
                    $logoFound = true;
                    break;
                }
            }

            if (!$logoFound) {
                // Listar archivos disponibles en el directorio
                $files = scandir($leagueDir);
                $pngFiles = array_filter($files, function($f) {
                    return substr($f, -4) === '.png';
                });

                // Buscar coincidencias parciales
                foreach ($pngFiles as $file) {
                    $filenameLower = strtolower(pathinfo($file, PATHINFO_FILENAME));
                    $teamNameLower = strtolower($teamName);

                    // Coincidencia parcial
                    if (strpos($filenameLower, $teamNameLower) !== false ||
                        strpos($teamNameLower, $filenameLower) !== false) {
                        $webPath = "$webBasePath/$leagueSlug/$file";

                        $updateStmt = $pdo->prepare("UPDATE teams SET logo_path = ? WHERE team_id = ?");
                        $updateStmt->execute([$webPath, $teamId]);

                        echo "🔍 {$teamName}: $webPath (coincidencia parcial)\n";
                        $totalUpdated++;
                        $logoFound = true;
                        break;
                    }
                }

                if (!$logoFound) {
                    echo "⚠️  {$teamName}: Logo no encontrado\n";
                    $notFound[] = "{$teamName} ({$leagueSlug})";
                    $totalSkipped++;
                }
            }
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 RESUMEN:\n";
    echo "✅ Logos vinculados: $totalUpdated\n";
    echo "⚠️  Logos no encontrados: $totalSkipped\n";

    if (!empty($notFound)) {
        echo "\n❌ Equipos sin logo:\n";
        foreach ($notFound as $team) {
            echo "   - $team\n";
        }
    }

    echo "\n✨ Proceso completado!\n";

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage() . "\n");
}
