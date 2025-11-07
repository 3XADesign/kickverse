# RESUMEN VISUAL - ARQUITECTURA KICKVERSE

## DIAGRAMA DE FLUJO PRINCIPAL

```
CLIENT REQUEST (HTTP GET /productos)
        |
        v
   .htaccess (raíz)
   RewriteRule ^(.*)$ public/$1
        |
        v
   /public/index.php
   |-- Requiere /config/app.php
   |-- Requiere /app/Database.php
   |-- Requiere /app/helpers/i18n.php
   |-- Requiere /routes/web.php
        |
        v
   Router::dispatch()
   |-- Lee REQUEST_METHOD y REQUEST_URI
   |-- Itera sobre rutas registradas
   |-- Pattern matching (regex)
        |
        v
   MATCH FOUND: GET /productos => ProductPageController@index
        |
        v
   callHandler("ProductPageController@index")
   |-- Carga /app/controllers/ProductPageController.php
   |-- Instancia ProductPageController
   |-- Llama $controller->index()
        |
        v
   ProductPageController::index()
   |-- Prepara datos: $products, $categories, etc.
   |-- $this->view('products/index', $data, 'layouts/main')
        |
        v
   Controller::view()
   |-- ob_start()
   |-- Carga /app/views/products/index.php
   |-- $content = ob_get_clean()
        |
        v
   Carga layout: /app/views/layouts/main.php
   |-- Layout incluye /partials/header.php
   |-- Layout echo $content (producto index)
   |-- Layout incluye /partials/footer.php
        |
        v
   RESPONSE: HTML completo con header + contenido + footer
        |
        v
   BROWSER RENDERS
```

---

## ESTRUCTURA DE CARPETAS - VISUAL

```
kickverse/ (RAIZ DEL PROYECTO)
│
├── 📄 .htaccess          <- REDIRIGE A /public
├── 📄 composer.json      (si existe)
├── 📄 .gitignore
│
├── 📂 public/            <- SERVIDOR WEB APUNTA AQUI
│   ├── 📄 index.php      <- PUNTO DE ENTRADA
│   ├── 📄 .htaccess      <- URL REWRITING
│   ├── 📂 css/
│   │   ├── modern.css
│   │   ├── modal.css
│   │   └── notifications.css
│   ├── 📂 js/
│   │   ├── main.js
│   │   └── notifications.js
│   ├── 📂 img/
│   ├── 📂 uploads/       <- IMAGENES DE USUARIOS
│   └── 📂 assets/
│
├── 📂 app/               <- LOGICA APLICACION (NO SERVIDA)
│   ├── 📄 Router.php     <- ENRUTADOR PERSONALIZADO
│   ├── 📄 Database.php   <- CONEXION BD
│   ├── 📂 controllers/
│   │   ├── 📄 Controller.php         <- CLASE BASE
│   │   ├── 📄 HomeController.php
│   │   ├── 📄 ProductPageController.php
│   │   ├── 📄 CheckoutPageController.php
│   │   ├── 📂 admin/                 <- CONTROLADORES ADMIN
│   │   │   ├── ClientesController.php
│   │   │   ├── PedidosController.php
│   │   │   ├── ProductosController.php
│   │   │   └── ...
│   │   └── 📂 api/                   <- CONTROLADORES API
│   │       ├── ProductController.php
│   │       ├── CartController.php
│   │       ├── AuthController.php
│   │       └── ...
│   ├── 📂 models/
│   │   ├── 📄 Model.php              <- CLASE BASE
│   │   ├── 📄 Product.php
│   │   ├── 📄 Order.php
│   │   ├── 📄 Customer.php
│   │   ├── 📄 Cart.php
│   │   └── ...
│   ├── 📂 views/                     <- TEMPLATES
│   │   ├── 📄 home.php               <- HOME PAGE
│   │   ├── 📂 layouts/
│   │   │   ├── 📄 main.php           <- LAYOUT PRINCIPAL
│   │   │   └── 📄 admin-crm.php      <- LAYOUT ADMIN
│   │   ├── 📂 partials/
│   │   │   ├── 📄 header.php         <- HEADER REUTILIZABLE
│   │   │   └── 📄 footer.php         <- FOOTER REUTILIZABLE
│   │   ├── 📂 errors/                <- ERROR PAGES
│   │   │   ├── 📄 400.php
│   │   │   ├── 📄 404.php
│   │   │   └── 📄 500.php
│   │   ├── 📂 products/
│   │   ├── 📂 checkout/
│   │   ├── 📂 account/
│   │   ├── 📂 cart/
│   │   ├── 📂 admin/
│   │   └── 📂 auth/
│   ├── 📂 helpers/
│   │   ├── 📄 i18n.php               <- MULTIIDIOMA
│   │   ├── 📄 Mailer.php
│   │   └── 📄 OxaPayAPI.php
│   ├── 📂 middleware/
│   │   └── 📄 AdminMiddleware.php
│   └── 📂 lang/
│       ├── 📄 es.php                 <- TRADUCCION ESPAÑOL
│       └── 📄 en.php                 <- TRADUCCION INGLES
│
├── 📂 config/            <- CONFIGURACION (NO SERVIDA)
│   ├── 📄 app.php        <- SETTINGS PRINCIPALES
│   └── 📄 database.php
│
├── 📂 routes/
│   └── 📄 web.php        <- TODAS LAS RUTAS
│
├── 📂 database/
│   ├── 📂 migrations/
│   └── 📂 seeds/
│
├── 📂 storage/
│   ├── 📂 logs/
│   └── 📂 cache/
│
├── 📂 css/               <- SOURCE STYLES (NO SERVIDA)
│   └── ...
│
├── 📂 js/                <- SOURCE SCRIPTS (NO SERVIDA)
│   └── ...
│
├── 📂 img/               <- SOURCE IMAGES (NO SERVIDA)
│   └── ...
│
└── 📚 DOCUMENTACION.md
```

---

## ARCHITECTURE PATTERN - MVC

```
REQUEST FLOW:

┌─────────────────────────────────────────┐
│         HTTP REQUEST                    │
│  (GET /productos/barcellona-2024)       │
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│  Router Pattern Matching                 │
│  /productos/:slug → ProductPageController│
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│  MODEL LAYER                            │
│  Product::getBySlug('barcellona-2024')  │
│  → Query BD                             │
│  → Return array de datos                │
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│  CONTROLLER LAYER                       │
│  ProductPageController::show($slug)     │
│  - Recibe datos de model                │
│  - Prepara variables para vista         │
│  - Llama view('products/show', $data)   │
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│  VIEW LAYER                             │
│  /app/views/products/show.php           │
│  - Renderiza HTML                       │
│  - Usa variables de controller          │
│  - Usa partials (header, footer)        │
│  - Retorna contenido                    │
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│  LAYOUT WRAPPING                        │
│  /app/views/layouts/main.php            │
│  - Envuelve contenido en HTML completo  │
│  - Incluye CSS global                   │
│  - Incluye JS global                    │
│  - Meta tags, Analytics, etc.           │
└─────────────────────────────────────────┘
            |
            v
┌─────────────────────────────────────────┐
│         HTTP RESPONSE                   │
│  (HTML completo)                        │
└─────────────────────────────────────────┘
```

---

## COMPONENTES PRINCIPALES

### 1. ROUTER (App/Router.php)

```
Entrada: REQUEST_METHOD + REQUEST_URI
Procesamiento:
  - Registra rutas: $router->get('/path', 'Controller@method')
  - Al dispatch, compara path contra todas las rutas
  - Usa regex pattern matching
  - Extrae parámetros (:id, :slug, etc.)
Salida: Llama al controlador correspondiente o 404
```

### 2. CONTROLLER (App/Controllers/Controller.php)

```
Métodos principales:
  - view($name, $data, $layout)  → Renderiza template
  - json($data, $code)           → Response JSON
  - post(), get(), input()       → Lectura de datos
  - redirect($url)               → Redirección
  - getUser()                    → Usuario actual
  - validateCSRF()               → Validación tokens
```

### 3. MODELS (App/Models/)

```
Extienden: Model.php (clase base)
Responsabilidad:
  - Conexión a BD
  - Queries SELECT, INSERT, UPDATE, DELETE
  - Retornan arrays de datos
  - Sin lógica de presentación
```

### 4. VIEWS (App/Views/)

```
Estructura:
  - /layouts/main.php    → Esqueleto HTML
  - /partials/header.php → Header reutilizable
  - /partials/footer.php → Footer reutilizable
  - /home.php            → Contenido específico
  - /errors/400.php      → Página de error
  
Características:
  - PHP puro (no Blade, Twig, etc.)
  - Acceso a variables via $variable
  - Soporte multiidioma via __('key')
```

### 5. i18n (App/Helpers/i18n.php)

```
Función: __('key.subkey')
Ejemplo:
  __('hero.banner_title')
  
Busca en:
  /app/lang/es.php → 'Camisetas de Fútbol Premium'
  /app/lang/en.php → 'Premium Football Jerseys'
```

---

## FLUJO DE HOME PAGE

```
1. USUARIO VISITA: https://kickverse.es/

2. ROUTING:
   GET / → HomeController@index

3. CONTROLLER (/app/controllers/HomeController.php):
   - $this->productModel->getFeatured()    → productos destacados
   - $this->leagueModel->getAllActive()    → ligas activas
   - $this->productModel->getActive(12)    → últimos productos
   - $this->productModel->getRandom(3)     → best sellers
   - $this->productModel->getRandom(2)     → hero products
   
   Llama: $this->view('home', $data, 'layouts/main')

4. VIEW (/app/views/home.php):
   <section class="hero-banner-slim">
   <section class="category-section">
   <section class="featured-products">
   <section class="leagues-section">
   <section class="latest-products">

5. LAYOUT (/app/views/layouts/main.php):
   <html>
     <head>
       <!-- Meta, CSS, Analytics -->
     </head>
     <body>
       <?php include 'partials/header.php' ?>
       <main>
         <!-- AQUI VÑ EL CONTENIDO DE home.php ($content) -->
       </main>
       <?php include 'partials/footer.php' ?>
       <!-- Scripts -->
     </body>
   </html>

6. RESPONSE: HTML completo renderizado
```

---

## DEPLOYMENT - ESTRUCTURA FINAL

```
En SERVIDOR (Opción recomendada):

Document Root: /var/www/kickverse/public

/var/www/kickverse/
│
├── public/                    ← ACCESIBLE POR WEB
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   ├── img/
│   └── uploads/
│
├── app/                       ← NO ACCESIBLE (fuera web)
├── config/                    ← NO ACCESIBLE (fuera web)
├── routes/                    ← NO ACCESIBLE (fuera web)
└── storage/                   ← NO ACCESIBLE (fuera web)

PERMISOS:
- 755: /var/www/kickverse
- 755: /var/www/kickverse/public
- 777: /var/www/kickverse/storage
- 777: /var/www/kickverse/public/uploads
```

---

## ARCHIVOS CLAVE PARA MODIFICAR

```
Para crear nueva página:
1. /routes/web.php
   Agregar: $router->get('/nueva', 'NuevaController@index');

2. /app/controllers/NuevaController.php
   class NuevaController extends Controller {
       public function index() {
           $this->view('nueva', [/* datos */]);
       }
   }

3. /app/views/nueva.php
   HTML de la página

4. /app/views/layouts/main.php (si necesitas layout custom)
   O crear: /app/views/layouts/custom.php
```

---

## TABLA RAPIDA DE ARCHIVOS

| Tarea | Archivo | Tipo |
|-------|---------|------|
| Añadir ruta | /routes/web.php | Routing |
| Crear controlador | /app/controllers/Nuevo.php | PHP |
| Crear modelo | /app/models/Nuevo.php | PHP |
| Crear vista | /app/views/nueva.php | PHP/HTML |
| Traducción | /app/lang/es.php | PHP |
| Configuración | /config/app.php | PHP |
| Estilos | /public/css/modern.css | CSS |
| Scripts | /public/js/main.js | JavaScript |
| Error 404 | /app/views/errors/404.php | PHP/HTML |
| Error 500 | /app/views/errors/500.php | PHP/HTML |
| Header | /app/views/partials/header.php | PHP/HTML |
| Footer | /app/views/partials/footer.php | PHP/HTML |

---

## CHECKLIST FINAL

```
DESARROLLO:
[ ] Entiendo flujo home → HomeController → home.php
[ ] Sé donde están layouts (main.php)
[ ] Sé donde están componentes (header.php, footer.php)
[ ] Entiendo multiidioma (__('key'))
[ ] Sé crear nueva página (controller + view + ruta)

DEPLOYMENT:
[ ] Sé qué subir a servidor (/public, /app, /config, /routes)
[ ] Sé qué NO subir (.git, css source, js source)
[ ] Entiendo .htaccess rewrite rules
[ ] Sé configurar permisos de carpetas
[ ] Sé donde va error 400, 404, 500

ESTRUCTURA:
[ ] PHP vanilla, no framework
[ ] Router personalizado (patrón matching)
[ ] MVC simplificado (Controllers, Models, Views)
[ ] Layouts reutilizables
[ ] Componentes parciales (header, footer)
```

