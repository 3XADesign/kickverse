<?php
/**
 * 404 Not Found Error Page
 */

// Prevent infinite loops
if (basename($_SERVER['SCRIPT_NAME']) !== '404.php') {
    http_response_code(404);
}

// Load environment configuration safely
$envPath = __DIR__ . '/../config/env.php';
if (file_exists($envPath)) {
    require_once $envPath;
}

// Load configuration safely
$configPath = __DIR__ . '/../config/app.php';
$config = file_exists($configPath) ? require $configPath : ['timezone' => 'Europe/Madrid'];

// Load i18n system safely
$i18nPath = __DIR__ . '/../app/helpers/i18n.php';
if (file_exists($i18nPath)) {
    require_once $i18nPath;
    i18n::init('es');
}

// Set page variables
$page_title = '404 - Página No Encontrada | Kickverse';
$page_description = 'La página que buscas no existe';
$additional_css = ['/css/error-pages.css'];

// Start output buffering to capture content
ob_start();
?>

<!-- Error Hero Section -->
<section class="error-hero error-404">
    <div class="container">
        <div class="error-content">
            <div class="error-animation">
                <div class="jersey-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div class="error-code">404</div>
            </div>
            <h1 class="error-title">¡Ups! Esta camiseta no existe</h1>
            <p class="error-description">
                Parece que la página que buscas se ha ido al banquillo.
                No te preocupes, tenemos un montón de camisetas increíbles esperándote.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Volver al Inicio
                </a>
                <a href="/productos" class="btn btn-secondary">
                    <i class="fas fa-tshirt"></i>
                    Ver Camisetas
                </a>
                <a href="/mystery-box" class="btn btn-special">
                    <i class="fas fa-gift"></i>
                    Mystery Box
                </a>
            </div>

            <!-- Popular Links -->
            <div class="popular-links">
                <p class="popular-title">Enlaces populares:</p>
                <div class="quick-links">
                    <a href="/ligas/laliga" class="quick-link">
                        <i class="fas fa-shield-alt"></i> LaLiga
                    </a>
                    <a href="/ligas/premier" class="quick-link">
                        <i class="fas fa-shield-alt"></i> Premier League
                    </a>
                    <a href="/mystery-box" class="quick-link">
                        <i class="fas fa-gift"></i> Mystery Box
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Capture the buffered content
$content = ob_get_clean();

// Include the main layout if exists, otherwise render standalone
$layoutPath = __DIR__ . '/../app/views/layouts/main.php';
if (file_exists($layoutPath)) {
    include $layoutPath;
} else {
    // Fallback standalone render
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $page_title ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/css/modern.css">
        <link rel="stylesheet" href="/css/error-pages.css">
    </head>
    <body>
        <?= $content ?>
    </body>
    </html>
    <?php
}
