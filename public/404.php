<?php
/**
 * 404 Not Found Error Page
 */

// Load environment configuration
require_once __DIR__ . '/../config/env.php';

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Load i18n system
require_once __DIR__ . '/../app/helpers/i18n.php';
i18n::init('es');

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
                    <a href="/ligas/champions" class="quick-link">
                        <i class="fas fa-trophy"></i> Champions
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Capture the buffered content
$content = ob_get_clean();

// Include the main layout
include __DIR__ . '/../app/views/layouts/main.php';
