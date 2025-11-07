# Kickverse - Backend PHP MVC

## Resumen del Proyecto

Se ha migrado completamente Kickverse de un sitio estático HTML/JS con datos hardcodeados a una aplicación PHP dinámica con arquitectura MVC y base de datos MySQL.

## Estructura del Proyecto

```
kickverse/
├── app/
│   ├── controllers/
│   │   ├── api/              # API REST Controllers
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── AuthController.php
│   │   │   ├── OrderController.php
│   │   │   ├── CustomerController.php
│   │   │   └── PaymentController.php
│   │   ├── admin/            # Admin Panel Controllers
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── AdminAuthController.php
│   │   │   ├── AdminOrderController.php
│   │   │   ├── AdminProductController.php
│   │   │   └── AdminCustomerController.php
│   │   ├── Controller.php    # Base Controller
│   │   ├── HomeController.php
│   │   ├── ProductPageController.php
│   │   ├── CartPageController.php
│   │   ├── CheckoutPageController.php
│   │   ├── AuthPageController.php
│   │   ├── AccountPageController.php
│   │   ├── PageController.php
│   │   └── LeaguePageController.php
│   ├── models/
│   │   ├── Model.php         # Base Model
│   │   ├── Product.php
│   │   ├── Customer.php
│   │   ├── Order.php
│   │   ├── Cart.php
│   │   └── League.php
│   ├── views/                # Views (Por crear)
│   ├── Database.php          # Database Singleton
│   └── Router.php            # Router Class
├── config/
│   ├── database.php          # DB Config
│   └── app.php               # App Config
├── database/
│   ├── schema.sql            # Database Schema (46 tablas)
│   └── data_migration.sql    # Data Migration
├── public/
│   ├── index.php             # Entry Point
│   └── .htaccess             # URL Rewriting
└── routes/
    └── web.php               # Routes Definition

```

## Base de Datos

### Credenciales
- Host: `50.31.174.69`
- Database: `iqvfmscx_kickverse`
- Username: `iqvfmscx_kickverse`
- Password: `I,nzP1aIY4cG`

### Esquema
- **46 tablas** creadas
- **Todas las tablas fueron populadas** con datos migrados del código hardcodeado
- Sistema relacional completo con:
  - Productos con variantes (tallas)
  - Historial de precios
  - Clientes con autenticación híbrida
  - Pedidos con snapshot de precios
  - Carritos de compra
  - Sistema de puntos de lealtad
  - Cupones de descuento
  - Suscripciones
  - Mystery boxes
  - Drops
  - Pagos (Oxapay)

## Características Implementadas

### 1. Arquitectura MVC
- **Model**: Clase base con CRUD completo + modelos específicos
- **View**: Sistema de vistas con `extract()` para pasar datos
- **Controller**: Controlador base con helpers + controladores específicos
- **Router**: Sistema de routing con parámetros dinámicos

### 2. API REST Completa

#### Productos
- `GET /api/products` - Listar productos (con filtros)
- `GET /api/products/:id` - Detalle de producto
- `GET /api/products/slug/:slug` - Producto por slug
- `GET /api/products/search?q=` - Búsqueda
- `GET /api/leagues` - Ligas con equipos

#### Carrito
- `GET /api/cart` - Ver carrito
- `POST /api/cart/add` - Añadir al carrito
- `PUT /api/cart/update/:itemId` - Actualizar cantidad
- `DELETE /api/cart/remove/:itemId` - Eliminar item
- `DELETE /api/cart/clear` - Vaciar carrito

#### Autenticación
- `POST /api/auth/register` - Registro clásico
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Usuario actual
- `POST /api/auth/social/telegram` - Login con Telegram
- `POST /api/auth/social/whatsapp` - Login con WhatsApp

#### Pedidos
- `GET /api/orders` - Listar pedidos del cliente
- `GET /api/orders/:id` - Detalle de pedido
- `POST /api/orders/create` - Crear pedido
- `POST /api/orders/:id/cancel` - Cancelar pedido
- `POST /api/orders/validate-coupon` - Validar cupón

#### Cliente
- `GET /api/customer/profile` - Perfil
- `PUT /api/customer/profile` - Actualizar perfil
- `GET /api/customer/addresses` - Direcciones de envío
- `POST /api/customer/addresses` - Añadir dirección
- `PUT /api/customer/addresses/:id` - Actualizar dirección
- `DELETE /api/customer/addresses/:id` - Eliminar dirección
- `GET /api/customer/preferences` - Preferencias
- `PUT /api/customer/preferences` - Actualizar preferencias
- `GET /api/customer/loyalty` - Puntos de lealtad

#### Pagos (Oxapay)
- `POST /api/payment/create` - Crear pago
- `POST /api/payment/callback` - Webhook de Oxapay
- `GET /api/payment/status/:orderId` - Estado del pago

### 3. Panel de Administración

#### Rutas Admin
- `GET /admin` - Dashboard
- `GET /admin/login` - Login admin
- `GET /admin/orders` - Gestión de pedidos
- `GET /admin/products` - Gestión de productos
- `GET /admin/customers` - Gestión de clientes

### 4. Páginas Frontend
- `/` - Homepage
- `/productos` - Listado de productos
- `/productos/:slug` - Detalle de producto
- `/ligas/:slug` - Página de liga
- `/carrito` - Carrito
- `/checkout` - Checkout
- `/login` - Login
- `/register` - Registro
- `/mi-cuenta` - Cuenta del cliente
- `/mis-pedidos` - Pedidos
- Páginas estáticas (FAQ, contacto, etc.)

### 5. Seguridad
- Prepared statements (PDO) contra SQL injection
- Password hashing con bcrypt
- CSRF tokens
- Session management
- Input validation
- XSS protection headers
- HTTPS forzado en .htaccess

### 6. Características de Negocio

#### Precios
- **Snapshot de precios**: Los pedidos guardan el precio al momento de compra
- **Historial de precios**: Tabla `product_price_history`
- Precio base: €24.99
- Parches: +€1.99
- Personalización: +€2.99
- Envío gratis: pedidos ≥ €50

#### Sistema de Puntos
- 1 punto por cada euro gastado
- Tiers: standard, bronze, silver, gold, platinum
- Actualización automática de tier con triggers

#### Autenticación Híbrida
- Email/Password clásico
- Telegram username
- WhatsApp number
- Sin email requerido para redes sociales

#### Carritos
- Carritos por sesión (invitados)
- Carritos por customer_id (registrados)
- Expiración: 7 días
- Conversión a pedido

## Configuración

### Variables de Entorno Recomendadas

Crear archivo `.env` en la raíz:

```bash
# Admin Credentials
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=$2y$10$... # Usar password_hash('tu_password', PASSWORD_BCRYPT)

# Oxapay
OXAPAY_API_KEY=tu_api_key
OXAPAY_MERCHANT_ID=tu_merchant_id

# App
APP_ENV=production
APP_DEBUG=false
```

### Apache Configuration

El `.htaccess` ya está configurado con:
- URL rewriting a `index.php`
- Force HTTPS
- Security headers
- Compression
- Browser caching

## Próximos Pasos

### 1. Crear Vistas (Frontend)
- Adaptar los HTML existentes a PHP views
- Usar los datos de los controladores en lugar de JavaScript hardcodeado
- Implementar el nuevo diseño

### 2. Crear Vistas de Admin
- Dashboard con estadísticas
- Gestión de pedidos
- Gestión de productos
- Gestión de clientes

### 3. Testing
- Probar todas las rutas API
- Probar flujo de compra completo
- Probar integración con Oxapay
- Verificar autenticación y permisos

### 4. Optimizaciones
- Añadir caché (Redis/Memcached)
- Optimizar queries con índices
- Lazy loading de imágenes
- Minificar CSS/JS

## Convenciones de Código

### Modelos
- Extender `Model`
- Usar `$table` y `$primaryKey`
- Métodos específicos del negocio

### Controladores
- Extender `Controller`
- Métodos públicos = acciones
- Usar helpers: `json()`, `view()`, `redirect()`

### Rutas
- RESTful cuando sea posible
- Parámetros con `:nombre`
- Agrupar por funcionalidad

### Naming
- snake_case: columnas de BD
- camelCase: métodos PHP
- PascalCase: clases
- kebab-case: URLs/slugs

## Nombres Exactos de Columnas (Schema)

### Tabla `products`
```sql
product_id, product_type, name, slug, description, base_price,
original_price, stock_quantity, league_id, team_id, jersey_type,
season, version, has_patches_available, patches_price,
has_personalization_available, personalization_price, is_active,
is_featured, created_at, updated_at
```

### Tabla `product_variants`
```sql
variant_id, product_id, size, size_category, sku, stock_quantity,
low_stock_threshold, chest_width_cm, length_cm, height_cm,
weight_kg, age_range, is_active, created_at, updated_at
```

### Tabla `customers`
```sql
customer_id, email, password_hash, email_verified,
email_verification_token, telegram_username, telegram_chat_id,
whatsapp_number, full_name, phone, preferred_language,
customer_status, loyalty_tier, loyalty_points, total_orders_count,
total_spent, last_login_date, last_activity_date, newsletter_subscribed,
created_at, updated_at, deleted_at
```

### Tabla `orders`
```sql
order_id, customer_id, order_type, order_status, payment_status,
subtotal, discount_amount, coupon_id, shipping_cost, total_amount,
shipping_address_id, order_date, payment_id, tracking_number,
shipped_date, delivered_date, estimated_delivery_date, admin_notes,
created_at, updated_at
```

### Tabla `cart_items`
```sql
cart_item_id, cart_id, product_id, variant_id, quantity, unit_price,
has_patches, has_personalization, personalization_name,
personalization_number, added_at
```

## Notas Importantes

1. **SIEMPRE** revisar el schema antes de crear queries
2. **NUNCA** inventar nombres de columnas
3. **USAR** prepared statements para todas las queries
4. **VALIDAR** input del usuario
5. **PROTEGER** rutas de admin con `requireAdminAuth()`
6. **PROTEGER** rutas de cliente con `requireAuth()`
7. **SNAPSHOT** de precios en pedidos (crítico!)

## Contacto y Documentación

- Configuración Oxapay: `/config/app.php`
- Schema completo: `/database/schema.sql`
- Datos migrados: `/database/data_migration.sql`
- Rutas completas: `/routes/web.php`

---

**Estado**: Backend completo ✅
**Pendiente**: Vistas frontend y admin
**Fecha**: 2025-11-06
