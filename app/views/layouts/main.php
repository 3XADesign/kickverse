<!DOCTYPE html>
<html lang="<?= i18n::getLang() ?>">
<head>
    <!-- Google tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config['analytics']['ga_id'] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= $config['analytics']['ga_id'] ?>');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?= $config['analytics']['gtm_id'] ?>');</script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $page_description ?? __('hero.subtitle') ?>">
    <meta name="keywords" content="<?= $page_keywords ?? 'camisetas fútbol, jerseys, LaLiga, Premier League, Champions League' ?>">
    <title><?= $page_title ?? 'Kickverse - ' . __('hero.title') ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Kickverse" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Modern Design System -->
    <link rel="stylesheet" href="/css/modern.css">
    <link rel="stylesheet" href="/css/modal.css">
    <link rel="stylesheet" href="/css/notifications.css">

    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $config['analytics']['gtm_id'] ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main>
        <?php
        // Include the specific page content
        // This is where the controller's view will be inserted
        echo $content ?? '';
        ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <!-- Scripts -->
    <?php if (isset($_SESSION['notification'])): ?>
    <script>
        // Pass PHP session notification to JavaScript
        window.sessionNotification = <?= json_encode($_SESSION['notification']) ?>;
        <?php unset($_SESSION['notification']); // Clear after reading ?>
    </script>
    <?php endif; ?>
    <script src="/js/notifications.js"></script>
    <script src="/js/main.js"></script>

    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
