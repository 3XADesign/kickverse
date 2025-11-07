# GUIA DEPLOYMENT Y ERROR PAGES - KICKVERSE

## DEPLOYMENT - ESTRUCTURA PARA SERVIDOR

### Opcion A: MEJOR (Apuntar DocumentRoot a /public)

```
Tu dominio kickverse.es apunta a: /var/www/kickverse/public

Estructura en servidor:
/var/www/kickverse/
├── public/                    ← DocumentRoot
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── uploads/
│   └── favicon.*
│
├── app/                       ← No accesible web (protegido por permisos)
├── config/                    ← No accesible web
├── routes/                    ← No accesible web
├── storage/                   ← No accesible web
└── database/                  ← No accesible web (opcional)
```

**Configurar Apache:**
```apache
<VirtualHost *:80>
    ServerName kickverse.es
    DocumentRoot /var/www/kickverse/public
    
    <Directory /var/www/kickverse/public>
        AllowOverride All
        Options -Indexes
        Require all granted
    </Directory>
    
    # Bloquear acceso directo a carpetas sensibles
    <Directory /var/www/kickverse/app>
        Require all denied
    </Directory>
    
    <Directory /var/www/kickverse/config>
        Require all denied
    </Directory>
</VirtualHost>
```

### Opcion B: Alternativa (DocumentRoot a raíz, usar .htaccess)

```
Tu dominio apunta a: /var/www/kickverse

Estructura en servidor:
/var/www/kickverse/
├── .htaccess              ← Redirige todo a /public
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   └── ...
├── app/
├── config/
└── ...
```

**Contenido .htaccess (raíz):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## CHECKLIST DE DEPLOYMENT

### Pre-Deployment (Local)

```
[ ] git commit -m "Listo para deploy"
[ ] Revisar config/app.php:
    - 'env' => 'production'
    - 'debug' => false
    - 'url' => 'https://kickverse.es'
[ ] Revisar public/.htaccess:
    - HTTPS redirect habilitado
    - Rewrite rules correctas
[ ] Verificar permisos de storage/
[ ] Probar BD connection localmente
```

### En Servidor

```
[ ] SSH al servidor
[ ] Crear directorios:
    mkdir -p /var/www/kickverse/{app,config,routes,storage,public}
    mkdir -p /var/www/kickverse/public/{css,js,img,uploads}

[ ] Subir archivos (via SFTP/Git/rsync)

[ ] Permisos:
    chmod 755 /var/www/kickverse
    chmod 755 /var/www/kickverse/public
    chmod 777 /var/www/kickverse/storage
    chmod 777 /var/www/kickverse/public/uploads

[ ] Verificar archivo config/app.php existe

[ ] Revisar .htaccess en /public/

[ ] SSL Certificate:
    - Usar Let's Encrypt (certbot)
    - Configurar HTTPS en Apache

[ ] Test:
    - curl https://kickverse.es/
    - Debe cargar página home
```

### Archivos críticos

```
SUBIR SIEMPRE:
- /app/              (controllers, models, views)
- /config/           (app.php, database.php)
- /routes/           (web.php)
- /public/           (todo)
- /storage/          (crear vacío)
- /database/         (si tienes migraciones)

NO SUBIR:
- /.git/
- /.DS_Store
- /node_modules/     (si existe)
- .env (usar copy-on-server en su lugar)
```

---

## CREACION DE ERROR PAGES

### Paso 1: Crear carpeta

```bash
mkdir -p /Users/danielgomezmartin/Desktop/3XA/kickverse/app/views/errors
```

### Paso 2: Crear 400.php

**Archivo:** `/app/views/errors/400.php`

```php
<?php
/**
 * Error 400 - Bad Request
 */
?>
<!DOCTYPE html>
<html lang="<?= i18n::getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Solicitud Incorrecta | Kickverse</title>
    <link rel="stylesheet" href="/css/modern.css">
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            padding: 2rem;
            text-align: center;
            color: var(--gray-900);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--gray-600);
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-error {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-home {
            background-color: var(--primary);
            color: white;
        }

        .btn-home:hover {
            background-color: var(--primary-hover);
        }

        .btn-contact {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-contact:hover {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main>
        <div class="container">
            <div class="error-page">
                <div class="error-code">400</div>
                <h1 class="error-title">
                    <?= i18n::getLang() === 'es' ? 'Solicitud Incorrecta' : 'Bad Request' ?>
                </h1>
                <p class="error-message">
                    <?= i18n::getLang() === 'es' 
                        ? 'Lo siento, la solicitud enviada al servidor no es válida.' 
                        : 'Sorry, the request sent to the server is invalid.' 
                    ?>
                </p>
                <div class="error-buttons">
                    <a href="/" class="btn-error btn-home">
                        <?= i18n::getLang() === 'es' ? 'Ir a Inicio' : 'Go Home' ?>
                    </a>
                    <a href="/contacto" class="btn-error btn-contact">
                        <?= i18n::getLang() === 'es' ? 'Contacto' : 'Contact' ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="/js/main.js"></script>
</body>
</html>
```

### Paso 3: Crear 500.php

**Archivo:** `/app/views/errors/500.php`

```php
<?php
/**
 * Error 500 - Internal Server Error
 */
?>
<!DOCTYPE html>
<html lang="<?= i18n::getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor | Kickverse</title>
    <link rel="stylesheet" href="/css/modern.css">
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            padding: 2rem;
            text-align: center;
            color: var(--gray-900);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            line-height: 1;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--gray-600);
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .error-details {
            background-color: var(--gray-50);
            border-left: 4px solid var(--danger);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            text-align: left;
            font-size: 0.9rem;
            color: var(--gray-700);
            max-width: 100%;
            overflow-x: auto;
        }

        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-error {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-home {
            background-color: var(--primary);
            color: white;
        }

        .btn-home:hover {
            background-color: var(--primary-hover);
        }

        .btn-contact {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-contact:hover {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main>
        <div class="container">
            <div class="error-page">
                <div class="error-code">500</div>
                <h1 class="error-title">
                    <?= i18n::getLang() === 'es' ? 'Error del Servidor' : 'Server Error' ?>
                </h1>
                <p class="error-message">
                    <?= i18n::getLang() === 'es' 
                        ? 'Algo salió mal en nuestro servidor. Estamos trabajando para solucionarlo.' 
                        : 'Something went wrong on our server. We are working to fix it.' 
                    ?>
                </p>
                
                <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="error-details">
                    <strong><?= i18n::getLang() === 'es' ? 'Detalles del Error:' : 'Error Details:' ?></strong><br>
                    <?= htmlspecialchars($error_message) ?>
                </div>
                <?php endif; ?>

                <div class="error-buttons">
                    <a href="/" class="btn-error btn-home">
                        <?= i18n::getLang() === 'es' ? 'Ir a Inicio' : 'Go Home' ?>
                    </a>
                    <a href="/contacto" class="btn-error btn-contact">
                        <?= i18n::getLang() === 'es' ? 'Reportar Problema' : 'Report Issue' ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="/js/main.js"></script>
</body>
</html>
```

### Paso 4: Actualizar Router.php

**Modificar:** `/app/Router.php`

```php
// Reemplazar método notFound() (línea 150-218) con:

private function notFound() {
    http_response_code(404);
    
    if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint no encontrado'
        ]);
    } else {
        $this->renderErrorPage(404);
    }
    exit;
}

private function renderErrorPage($code) {
    http_response_code($code);
    
    $errorViewPath = __DIR__ . '/../app/views/errors/' . $code . '.php';
    
    if (file_exists($errorViewPath)) {
        require $errorViewPath;
    } else {
        // Fallback genérico
        $this->renderGenericError($code);
    }
    exit;
}

private function renderGenericError($code) {
    $messages = [
        400 => ['Bad Request', 'Solicitud Incorrecta'],
        404 => ['Not Found', 'Página no encontrada'],
        500 => ['Internal Server Error', 'Error del Servidor'],
        503 => ['Service Unavailable', 'Servicio No Disponible'],
    ];
    
    $title = $messages[$code][1] ?? 'Error';
    
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$code - $title | Kickverse</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                background: linear-gradient(135deg, #b054e9 0%, #c151d4 100%);
            }
            .container {
                text-align: center;
                color: white;
                padding: 2rem;
            }
            h1 { font-size: 5rem; margin-bottom: 1rem; }
            p { font-size: 1.2rem; margin-bottom: 2rem; }
            a {
                color: white;
                text-decoration: none;
                border: 2px solid white;
                padding: 0.75rem 2rem;
                border-radius: 8px;
                display: inline-block;
                transition: all 0.3s;
            }
            a:hover {
                background: white;
                color: #b054e9;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>$code</h1>
            <p>$title</p>
            <a href="/">Volver al inicio</a>
        </div>
    </body>
    </html>
    HTML;
}
```

### Paso 5: Crear 404.php (Bonus)

**Archivo:** `/app/views/errors/404.php`

```php
<?php
/**
 * Error 404 - Not Found
 */
?>
<!DOCTYPE html>
<html lang="<?= i18n::getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Kickverse</title>
    <link rel="stylesheet" href="/css/modern.css">
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            padding: 2rem;
            text-align: center;
            color: var(--gray-900);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--danger);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--gray-600);
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-error {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-home {
            background-color: var(--danger);
            color: white;
        }

        .btn-home:hover {
            background-color: #d63031;
        }

        .btn-browse {
            border: 2px solid var(--danger);
            color: var(--danger);
        }

        .btn-browse:hover {
            background-color: var(--danger);
            color: white;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main>
        <div class="container">
            <div class="error-page">
                <div class="error-code">404</div>
                <h1 class="error-title">
                    <?= i18n::getLang() === 'es' ? 'Página no encontrada' : 'Page Not Found' ?>
                </h1>
                <p class="error-message">
                    <?= i18n::getLang() === 'es' 
                        ? 'La página que buscas no existe o ha sido movida.' 
                        : 'The page you are looking for does not exist or has been moved.' 
                    ?>
                </p>
                <div class="error-buttons">
                    <a href="/" class="btn-error btn-home">
                        <?= i18n::getLang() === 'es' ? 'Ir a Inicio' : 'Go Home' ?>
                    </a>
                    <a href="/productos" class="btn-error btn-browse">
                        <?= i18n::getLang() === 'es' ? 'Ver Productos' : 'Browse Products' ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="/js/main.js"></script>
</body>
</html>
```

---

## PRUEBAS DE ERROR PAGES

### Local

```bash
# Test 404
curl http://localhost/pagina-inexistente

# Test 500 (simular error)
# Crear ruta que lance exception en Router.php
```

### Production

```bash
# Test 404
curl https://kickverse.es/pagina-inexistente

# Test 500
# Ver logs de apache en /var/log/apache2/error.log
```

---

## SCRIPT DEPLOYMENT AUTOMATIZADO (Opcional)

**Archivo:** `scripts/deploy.sh`

```bash
#!/bin/bash

# Deploy script para Kickverse

REMOTE_HOST="user@kickverse.es"
REMOTE_PATH="/var/www/kickverse"
LOCAL_PATH="/Users/danielgomezmartin/Desktop/3XA/kickverse"

echo "Iniciando deployment..."

# 1. Push a git
cd $LOCAL_PATH
git add .
git commit -m "Deploy update"
git push origin main

# 2. Pull en servidor
ssh $REMOTE_HOST "cd $REMOTE_PATH && git pull origin main"

# 3. Permisos
ssh $REMOTE_HOST "chmod 755 $REMOTE_PATH"
ssh $REMOTE_HOST "chmod 755 $REMOTE_PATH/public"
ssh $REMOTE_HOST "chmod 777 $REMOTE_PATH/storage"
ssh $REMOTE_HOST "chmod 777 $REMOTE_PATH/public/uploads"

# 4. Clear cache (si existe)
ssh $REMOTE_HOST "rm -rf $REMOTE_PATH/storage/cache/*"

echo "Deployment completado!"
```

---

## REFERENCIAS RAPIDAS

| Elemento | Ubicación |
|----------|-----------|
| Error 400 | `/app/views/errors/400.php` |
| Error 404 | `/app/views/errors/404.php` |
| Error 500 | `/app/views/errors/500.php` |
| Router (actualizar) | `/app/Router.php` |
| Config Production | `/config/app.php` |
| .htaccess | `/public/.htaccess` |
| Header | `/app/views/partials/header.php` |
| Footer | `/app/views/partials/footer.php` |
| CSS | `/public/css/modern.css` |

