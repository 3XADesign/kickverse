# ANALISIS COMPLETO - ESTRUCTURA DEL PROYECTO KICKVERSE

## 1. RESUMEN EJECUTIVO

Este es un proyecto PHP **vanilla** (sin framework) con arquitectura **MVC simplificada** personalizada. No usa Laravel, Symfony, u otro framework moderno, sino un enrutador y estructura de controladores/vistas caseros.

**Características principales:**
- Router personalizado (simple pattern matching)
- Sistema de vistas con layouts
- Sistema i18n (multiidioma: español/inglés)
- Base de datos MySQL
- API REST para frontend
- Admin CRM funcional
- Carrito y checkout completos
- Autenticación y suscripciones

---

## 2. ESTRUCTURA DE CARPETAS

```
/Users/danielgomezmartin/Desktop/3XA/kickverse/
│
├── /public                          [AQUI VA EN SERVIDOR WEB]
│   ├── index.php                    [PUNTO DE ENTRADA PRINCIPAL]
│   ├── .htaccess                    [REWRITE RULES - ENRUTAMIENTO]
│   ├── /css                         [Estilos procesados]
│   ├── /js                          [JavaScript compilado]
│   ├── /img                         [Imágenes]
│   ├── /assets                      [Otros recursos]
│   └── /uploads                     [Imágenes subidas por usuarios]
│
├── /app                             [LOGICA APLICACION - NO VA EN SERVIDOR]
│   ├── Router.php                   [Sistema de enrutamiento]
│   ├── Database.php                 [Conexión a BD]
│   ├── /controllers                 [Controladores MVC]
│   │   ├── Controller.php           [Clase base con métodos útiles]
│   │   ├── HomeController.php       [Página de inicio]
│   │   ├── ProductPageController.php
│   │   ├── CheckoutPageController.php
│   │   ├── /admin                   [Controladores admin]
│   │   │   ├── ClientesController.php
│   │   │   ├── PedidosController.php
│   │   │   └── ...
│   │   └── /api                     [Controladores API]
│   │       ├── CartController.php
│   │       ├── ProductController.php
│   │       └── ...
│   ├── /models                      [Modelos de BD]
│   │   ├── Model.php                [Clase base]
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── ...
│   ├── /views                       [TEMPLATES - NO VA EN SERVIDOR]
│   │   ├── /layouts
│   │   │   ├── main.php             [Layout principal]
│   │   │   └── admin-crm.php
│   │   ├── /partials
│   │   │   ├── header.php           [Componente header]
│   │   │   └── footer.php           [Componente footer]
│   │   ├── home.php                 [Vista home]
│   │   ├── /products
│   │   ├── /checkout
│   │   ├── /account
│   │   ├── /admin
│   │   └── ...
│   ├── /helpers
│   │   ├── i18n.php                 [Sistema multiidioma]
│   │   ├── Mailer.php
│   │   └── OxaPayAPI.php
│   ├── /middleware
│   │   └── AdminMiddleware.php
│   └── /lang
│       ├── es.php                   [Traducción español]
│       └── en.php                   [Traducción inglés]
│
├── /config                          [CONFIGURACION - NO VA EN SERVIDOR]
│   ├── app.php                      [Rutas, timezone, analytics]
│   └── database.php
│
├── /routes                          [DEFINICION DE RUTAS]
│   └── web.php                      [Todas las rutas aquí]
│
├── /database                        [MIGRACIONES/SEEDERS]
│   └── ...
│
├── /storage                         [DATOS TEMPORALES]
│   └── ...
│
├── .htaccess                        [REDIRIGE TODO A /public]
├── /css                             [ESTILOS SOURCE (raíz)]
├── /js                              [SCRIPTS SOURCE (raíz)]
├── /img                             [IMÁGENES SOURCE (raíz)]
│
└── [ARCHIVOS MD DE DOCUMENTACION]

```

---

## 3. FLOW DE SOLICITUD - COMO FUNCIONA EL ROUTING

### 3.1 REQUEST FLOW

```
Cliente → .htaccess (raíz)
         ↓
    RewriteRule ^(.*)$ public/$1 [L]
         ↓
    /public/index.php
         ↓
    index.php:
    1. Requiere /config/app.php (configuración)
    2. Requiere /app/Database.php (conexión BD)
    3. Requiere /app/helpers/i18n.php (multiidioma)
    4. Requiere /routes/web.php (devuelve $router)
    5. $router->dispatch()
         ↓
    Router.php:
    1. Lee REQUEST_METHOD ($_SERVER)
    2. Lee REQUEST_URI (path)
    3. Itera sobre rutas registradas
    4. Pattern matching con regex
    5. Si coincide → callHandler()
    6. Si NO coincide → notFound() (404)
         ↓
    callHandler():
    1. Si handler es string "ControllerName@methodName"
    2. Requiere archivo del controlador
    3. Instancia la clase
    4. Llama al método con parámetros
         ↓
    Controller->method():
    Usa $this->view('viewname', $data)
         ↓
    view():
    1. Extract($data) - variables disponibles
    2. Captura contenido vista con ob_start()
    3. Carga layout (por defecto layouts/main)
    4. Layout incluye $content
    5. Renderiza header + content + footer
```

### 3.2 ESTRUCTURAS DE RUTAS

```php
// RUTAS FRONTEND (sirven HTML)
$router->get('/', 'HomeController@index');
$router->get('/productos', 'ProductPageController@index');
$router->get('/productos/:slug', 'ProductPageController@show');

// RUTAS API (devuelven JSON)
$router->get('/api/products', 'ProductController@index');
$router->post('/api/cart/add', 'CartController@add');

// RUTAS ADMIN (requieren auth)
$router->get('/admin/dashboard', 'AdminAuthController@dashboard');
$router->get('/admin/clientes', 'ClientesController@index');
```

---

## 4. PAGINA DE INICIO (HOME) - DONDE ESTA

### Ubicación: 
`/Users/danielgomezmartin/Desktop/3XA/kickverse/app/views/home.php`

### Como se carga:

```
RUTA: GET /
    ↓
CONTROLADOR: /app/controllers/HomeController.php
    ↓
METODO: index()
    ↓
DATOS PREPARADOS:
- $featured_products (productos destacados)
- $leagues (ligas)
- $latest_products (últimos productos)
- $best_sellers (más vendidos)
- $hero_products (para banner principal)
    ↓
$this->view('home', $data, 'layouts/main')
    ↓
LAYOUT: /app/views/layouts/main.php
    ↓
CONTENIDO:
    <header> (header.php)
    <main> (contenido de home.php)
    <footer> (footer.php)
```

### Componentes de Home:

1. **Hero Banner** - Sección superior con 2 imágenes de productos aleatorios
2. **Category Grid** - 4 boxes (All jerseys, Mystery Box, Best Sellers, Top Leagues)
3. **Featured Products** - Carrusel de productos destacados
4. **Leagues Section** - Ligas disponibles
5. **Latest Products** - Últimos productos añadidos
6. **Social Media** - Enlaces a redes

---

## 5. COMPONENTES REUTILIZABLES

### Header (`/app/views/partials/header.php`)
- Logo y navegación
- Selector de idioma
- Dropdown de cuenta (si logged in)
- Botón de carrito
- Menú móvil con hamburguesa
- Modal de login

### Footer (`/app/views/partials/footer.php`)
- Links de navegación
- Info legal/términos
- Contacto (Telegram, WhatsApp, Email)
- Redes sociales
- Copyright

Ambos se incluyen automáticamente en el layout principal.

---

## 6. LAYOUTS - COMO FUNCIONAN

### Patrón de Herencia

```php
// En Controller.php
protected function view($view, $data = [], $layout = 'layouts/main') {
    extract($data);  // Variables disponibles en vista
    
    // Cargar vista específica
    ob_start();
    require $viewPath;
    $content = ob_get_clean();
    
    // Cargar layout que envuelve
    if ($layout) {
        require $layoutPath;  // El layout echo el $content
    }
}
```

### Layouts disponibles:

1. **layouts/main.php** - Layout principal (usa header + footer)
   - Incluye: header.php, footer.php
   - CSS global, Analytics, Scripts
   - Usado por: Home, Productos, Checkout, etc.

2. **layouts/admin-crm.php** - Layout admin
   - Incluye: Sidebar admin
   - CSS admin específico
   - Usado por: Dashboard, Clientes, Pedidos, etc.

### Como crear nueva página con layout:

```php
// En controlador
public function miBlog() {
    $data = ['titulo' => 'Mi Blog'];
    
    // Opción 1: Layout por defecto (main)
    $this->view('pagina-blog', $data);
    
    // Opción 2: Layout específico
    $this->view('pagina-blog', $data, 'layouts/custom');
    
    // Opción 3: Sin layout
    $this->view('pagina-blog', $data, null);
}
```

---

## 7. SISTEMA MULTIIDIOMA (i18n)

### Archivos:
- `/app/helpers/i18n.php` - Clase i18n
- `/app/lang/es.php` - Traducciones español
- `/app/lang/en.php` - Traducciones inglés

### Uso en vistas:

```php
// Función __() para traducir
<h1><?= __('hero.banner_title') ?></h1>

// Obtener idioma actual
<?= i18n::getLang() ?>  // 'es' o 'en'

// En es.php
return [
    'hero' => [
        'banner_title' => 'Camisetas de Fútbol Premium'
    ]
];

// En en.php
return [
    'hero' => [
        'banner_title' => 'Premium Football Jerseys'
    ]
];
```

---

## 8. ARCHIVOS QUE VAN AL SERVIDOR - DEPLOYMENT

### QUE VA EN RAIZ DEL SERVIDOR (/)

```
/public/index.php
/public/.htaccess
/public/css/          (archivos compilados)
/public/js/           (archivos compilados)
/public/img/          (imágenes)
/public/assets/
/public/uploads/      (para imágenes subidas)
/public/favicon.*
/public/site.webmanifest
```

### QUE NO VA EN SERVIDOR (mantener en local o en carpeta private)

```
/app/                 (controllers, models, views)
/config/              (app.php con datos sensibles)
/routes/              (definición de rutas)
/database/            (migraciones, seeds)
/storage/             (logs, archivos temporales)
/.git/                (nunca)
/node_modules/        (si existe, nunca)
.htaccess (raíz)      (solo el de /public)
```

### .htaccess Strategy

**Raíz (/Users/.../kickverse/.htaccess):**
```apache
RewriteRule ^(.*)$ public/$1 [L]
```
Este archivo se puede omitir si el servidor web apunta directamente a /public.

**Mejor práctica para production:**
1. Apuntar Document Root a `/public`
2. No necesitar .htaccess de raíz
3. Solo mantener `/public/.htaccess`

---

## 9. SISTEMA DE ERROR PAGES - DONDE CREARLAS

### Estado actual (BÁSICO):
El Router.php tiene un método notFound() que devuelve 404 HTML inline.

```php
// En Router.php, línea 151
private function notFound() {
    http_response_code(404);
    echo '<!DOCTYPE html>... 404 - Página no encontrada ...';
}
```

### MEJORA RECOMENDADA: Error Pages propias

Para crear páginas de error 400.php y 500.php con diseño de home:

**Opción 1: Crear views de error**
```
/app/views/errors/
├── 400.php
├── 404.php
├── 500.php
└── 503.php
```

**Opción 2: Rutas específicas en Router**
```php
private function notFound() {
    http_response_code(404);
    
    // Usar el view system
    $this->renderError(404);
}

private function renderError($code) {
    $viewPath = __DIR__ . '/../app/views/errors/' . $code . '.php';
    
    if (file_exists($viewPath)) {
        http_response_code($code);
        require $viewPath;
    } else {
        // Fallback genérico
        echo "Error $code";
    }
}
```

**Opción 3: Error Handler Global (MEJOR)**
```php
// En public/index.php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (strpos($errfile, 'public') === false) {
        // Error en lógica (no views)
        header("HTTP/1.1 500 Internal Server Error");
        require __DIR__ . '/../app/views/errors/500.php';
        exit;
    }
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && $error['type'] === E_ERROR) {
        // Error fatal
        header("HTTP/1.1 500 Internal Server Error");
        require __DIR__ . '/../app/views/errors/500.php';
    }
});
```

---

## 10. ARQUITECTURA DE CARPETAS - RESUMEN

### Modo DESARROLLO (local)

```
kickverse/
├── /app           ← Aquí está la LÓGICA
├── /config        ← Configuración
├── /routes        ← Rutas
├── /database      ← Migraciones
├── /public        ← Aquí se sirve
├── /css           ← Source SASS/CSS
├── /js            ← Source JavaScript
└── /img           ← Source imágenes
```

### Modo PRODUCTION (servidor)

```
/var/www/kickverse/public/    ← Document Root
├── index.php
├── .htaccess
├── /css/
├── /js/
├── /img/
└── /uploads/

/var/www/kickverse/app/       ← NO accesible por web
├── ...

/var/www/kickverse/config/    ← NO accesible por web
├── ...
```

**Ventaja:** El .htaccess de raíz redirige AUTOMATICAMENTE todo a /public.

---

## 11. FLUJO DE DEPLOYMENT

### Paso 1: Preparar servidor
```bash
# En servidor
mkdir -p /var/www/kickverse
mkdir -p /var/www/kickverse/storage/logs
mkdir -p /var/www/kickverse/public/uploads

# Permisos
chmod 755 /var/www/kickverse
chmod 755 /var/www/kickverse/public
chmod 777 /var/www/kickverse/storage
chmod 777 /var/www/kickverse/public/uploads
```

### Paso 2: Subir archivos
```bash
# Opción A: Git
git clone https://github.com/3XADesign/kickverse.git /var/www/kickverse

# Opción B: SFTP (mantener esta estructura)
# Copiar TODO el contenido (no necesita /css ni /js de raíz si están compilados)
```

### Paso 3: Configurar servidor web
```apache
# En httpd.conf o .conf del sitio
<VirtualHost *:80>
    ServerName kickverse.es
    ServerAlias www.kickverse.es
    
    # IMPORTANTE: Apuntar aquí
    DocumentRoot /var/www/kickverse/public
    
    <Directory /var/www/kickverse/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Deshabilitar acceso a /app, /config, etc.
    <Directory /var/www/kickverse/app>
        Require all denied
    </Directory>
    
    <Directory /var/www/kickverse/config>
        Require all denied
    </Directory>
</VirtualHost>
```

### Paso 4: Configurar .env / config
```php
// config/app.php
'env' => 'production',
'debug' => false,
'url' => 'https://kickverse.es',
```

### Paso 5: SSL/HTTPS
```apache
# Habilitar en .htaccess
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 12. RUTAS IMPORTANTES PARA REFERENCIA

### URLs Públicas
- `GET /` → HomeController@index
- `GET /productos` → ProductPageController@index
- `GET /carrito` → CartPageController@index
- `GET /checkout` → CheckoutPageController@index
- `GET /login` → AuthPageController@login

### URLs API (JSON)
- `GET /api/products` → ProductController@index
- `POST /api/cart/add` → CartController@add
- `POST /api/auth/login` → AuthController@login

### URLs Admin
- `GET /admin/dashboard` → AdminAuthController@dashboard
- `GET /admin/clientes` → ClientesController@index
- `GET /admin/pedidos` → PedidosController@index

Ver `/routes/web.php` para lista completa.

---

## 13. CONFIGURACION IMPORTANTE

### `/config/app.php`

```php
[
    'name' => 'Kickverse',
    'env' => 'production',
    'url' => 'https://kickverse.es',
    'timezone' => 'Europe/Madrid',
    'locale' => 'es',
    
    'views' => __DIR__ . '/../app/views',
    'public' => __DIR__ . '/../public',
    
    'analytics' => [
        'gtm_id' => 'GTM-MQFTT34L',
        'ga_id' => 'G-SD9ETEJ9TG',
    ],
    
    'oxapay' => [
        'api_key' => getenv('OXAPAY_API_KEY'),
        'merchant_id' => getenv('OXAPAY_MERCHANT_ID'),
    ],
]
```

---

## 14. ESTRUCTURA TECHNICA - SUMMARY

| Aspecto | Detalles |
|--------|----------|
| **Framework** | PHP vanilla (MVC casero) |
| **Enrutador** | Router personalizado (sin composer) |
| **BD** | MySQL/MariaDB |
| **Template Engine** | PHP nativo (sin Blade, Twig, etc.) |
| **Multiidioma** | Sistema i18n casero (es, en) |
| **Autenticación** | Sessions $_SESSION |
| **API** | REST JSON (sin GraphQL) |
| **Payments** | OxaPay API |
| **Email** | PHPMailer (Mailer.php) |
| **Frontend JS** | Vanilla JavaScript + fetch() |
| **CSS** | Sass compilado a modern.css |
| **Deployment** | Apache .htaccess rewrites |

---

## 15. PROXIMOS PASOS - CREAR ERROR PAGES

### Paso 1: Crear estructura
```bash
mkdir -p /Users/danielgomezmartin/Desktop/3XA/kickverse/app/views/errors
```

### Paso 2: Crear 400.php
```php
<!-- /app/views/errors/400.php -->
<!-- Usar mismo HTML/CSS que home, pero con mensaje de error -->
<!-- Reutilizar header.php, footer.php -->
```

### Paso 3: Crear 500.php
```php
<!-- /app/views/errors/500.php -->
```

### Paso 4: Actualizar Router.php
```php
// Modificar notFound() para usar view system
private function notFound() {
    $this->renderErrorPage(404);
}

private function renderErrorPage($code) {
    http_response_code($code);
    // render /app/views/errors/{$code}.php
}
```

---

## CHECKPOINTS CLAVE

1. **Punto de entrada:** `/public/index.php`
2. **Router principal:** `/app/Router.php`
3. **Clase base controllers:** `/app/controllers/Controller.php`
4. **Home:** `/app/controllers/HomeController.php` → `/app/views/home.php`
5. **Layouts:** `/app/views/layouts/main.php`
6. **Componentes:** `/app/views/partials/header.php`, `/app/views/partials/footer.php`
7. **Configuración:** `/config/app.php`
8. **Rutas:** `/routes/web.php`
9. **Servidor:** Solo subir `/public/` + `/app/` + `/config/` + `/routes/` + `/storage/`
10. **NO subir:** `/css/`, `/js/`, `/img/` de raíz si ya están compilados en `/public/`

