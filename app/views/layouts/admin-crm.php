<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin CRM' ?> - Kickverse</title>

    <!-- Admin CRM Styles -->
    <link rel="stylesheet" href="/css/admin/admin-crm.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts: Inter & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <?php
    $active_page = $active_page ?? '';
    $admin_name = $_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? 'Admin';
    include __DIR__ . '/../admin/partials/sidebar.php';
    ?>

    <!-- Main Content Area -->
    <main class="admin-main" id="adminMain">
        <div class="admin-container">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?? 'info' ?>" role="alert">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : ($_SESSION['flash']['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $content ?>
        </div>
    </main>

    <!-- Modal Container -->
    <div id="modalOverlay" class="modal-overlay">
        <div id="modalContainer" class="modal-container"></div>
    </div>

    <!-- Admin CRM JavaScript -->
    <script src="/js/admin/admin-crm.js"></script>

    <!-- Initialize CRM Admin -->
    <script>
        // Inicializar sistema CRM
        const crmAdmin = new CRMAdmin();

        // Exponer funciones globales para compatibilidad
        window.crmAdmin = crmAdmin;
    </script>

    <!-- Page Specific Scripts -->
    <?php if (isset($page_scripts)): ?>
        <?= $page_scripts ?>
    <?php endif; ?>
</body>
</html>
