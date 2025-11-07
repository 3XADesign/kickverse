<?php
/**
 * 500 Internal Server Error Page
 */

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Load i18n system
require_once __DIR__ . '/../app/helpers/i18n.php';
i18n::init('es');

// Set page variables
$page_title = '500 - Error del Servidor | Kickverse';
$page_description = 'Ha ocurrido un error en el servidor';

// Start output buffering to capture content
ob_start();
?>

<!-- Error Hero Section -->
<section class="error-hero">
    <div class="container">
        <div class="error-content">
            <div class="error-code">500</div>
            <h1 class="error-title">Error del Servidor</h1>
            <p class="error-description">
                Lo sentimos, algo salió mal en nuestro servidor.
                Nuestro equipo ha sido notificado y estamos trabajando para solucionarlo.
                Por favor, inténtalo de nuevo en unos minutos.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
                <a href="javascript:location.reload()" class="btn btn-outline">
                    <i class="fas fa-sync-alt"></i> Reintentar
                </a>
            </div>
        </div>
        <div class="error-illustration">
            <i class="fas fa-server"></i>
        </div>
    </div>
</section>

<style>
.error-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 0;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.error-hero .container {
    display: flex;
    align-items: center;
    gap: 4rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.error-content {
    flex: 1;
}

.error-code {
    font-size: 8rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 1rem;
    opacity: 0.9;
    font-family: 'Poppins', sans-serif;
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
}

.error-description {
    font-size: 1.2rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.95;
}

.error-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: white;
    color: #f5576c;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.btn-outline {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-outline:hover {
    background: white;
    color: #f5576c;
}

.error-illustration {
    flex: 0 0 300px;
    text-align: center;
}

.error-illustration i {
    font-size: 15rem;
    opacity: 0.3;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@media (max-width: 768px) {
    .error-hero .container {
        flex-direction: column;
        text-align: center;
    }

    .error-code {
        font-size: 5rem;
    }

    .error-title {
        font-size: 2rem;
    }

    .error-illustration {
        display: none;
    }

    .error-actions {
        justify-content: center;
    }
}
</style>

<?php
// Capture the buffered content
$content = ob_get_clean();

// Include the main layout
include __DIR__ . '/../app/views/layouts/main.php';
