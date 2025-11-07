# KICKVERSE - ESTRUCTURA COMPLETA DE BASE DE DATOS

**Documentación generada:** 2025-11-06  
**Versión de esquema:** 1.0  
**Base de datos:** kickverse (MySQL UTF8MB4)

---

## RESUMEN EJECUTIVO

### Estadísticas de la Base de Datos:

| Concepto | Cantidad |
|----------|----------|
| **Tablas principales** | 35+ tablas |
| **Relaciones** | 45+ Foreign Keys |
| **Triggers** | 5 triggers automáticos |
| **Índices** | 60+ índices para performance |
| **Datos iniciales** | 6 ligas, 69 equipos, 200+ productos |

### Módulos principales implementados:

1. **Gestión de Productos** (Jerseys, Accesorios, Mystery Boxes)
2. **Sistema de Clientes/Usuarios** (Autenticación híbrida)
3. **Gestión de Pedidos** (Catalog, Subscriptions, Mystery Boxes, Drops)
4. **Sistema de Suscripciones** (4 planes con diferentes características)
5. **Mystery Boxes** (Cajas sorpresa personalizables)
6. **Drop Events** (Sistema de gamificación con rareza)
7. **Sistema de Pagos** (Oxapay + Manual + Telegram/WhatsApp)
8. **Sistema de Lealtad** (Puntos, Tiers, Recompensas)
9. **Carrito de Compras** (Guest + Registrados)
10. **Gestión de Inventario** (Stock, Movimientos, Alertas)
11. **Comunicaciones** (Mensajes, Notificaciones, Historial)
12. **Analytics** (Eventos de usuario, Vistas de productos)
13. **Admin** (Usuarios, Roles, Auditoría)

---

## ARQUITECTURA DE TABLAS POR SECCIÓN

### 1. TABLAS DE PRODUCTOS Y CONTENIDO (7 tablas)

#### Tabla: `leagues` (6 registros)
**Propósito:** Almacenar ligas de fútbol disponibles
```
Campos principales:
- league_id (PK, INT UNSIGNED)
- name (VARCHAR 100) - Nombre de la liga
- slug (VARCHAR 100, UNIQUE) - URL-friendly identifier
- country (VARCHAR 100) - País
- logo_path (VARCHAR 255) - Ruta del logo
- display_order (INT UNSIGNED) - Orden de visualización
- is_active (BOOLEAN) - Activa/Inactiva
- created_at, updated_at (TIMESTAMPS)

Índices:
- idx_slug (slug)
- idx_display_order (display_order)

Datos iniciales:
1. La Liga (España)
2. Premier League (Inglaterra)
3. Serie A (Italia)
4. Bundesliga (Alemania)
5. Ligue 1 (Francia)
6. Selecciones (Internacional)
```

#### Tabla: `teams` (69 registros)
**Propósito:** Almacenar equipos dentro de cada liga
```
Campos principales:
- team_id (PK, INT UNSIGNED)
- league_id (FK → leagues) - Liga a la que pertenece
- name (VARCHAR 100) - Nombre del equipo
- slug (VARCHAR 100, UNIQUE) - URL-friendly
- logo_path (VARCHAR 255) - Logo del equipo
- is_top_team (BOOLEAN) - Equipo de élite (para planes Premium)
- display_order (INT UNSIGNED) - Orden en la lista
- is_active (BOOLEAN)
- created_at, updated_at

Índices:
- idx_league (league_id)
- idx_slug (slug)
- idx_top_team (is_top_team)

Relaciones:
- FOREIGN KEY (league_id) → leagues(league_id) ON DELETE RESTRICT

Ejemplos de equipos:
- La Liga: Barcelona, Real Madrid, Atlético Madrid (TOP), etc.
- Premier League: Man City, Man United, Liverpool, Arsenal (TOP)
- Serie A: Juventus, Inter, Milan, Napoli (TOP)
- Bundesliga: Bayern, Dortmund, Leverkusen (TOP)
- Ligue 1: PSG (TOP), Marseille, Monaco
- Selecciones: Argentina, Brasil (TOP), Uruguay, etc.
```

#### Tabla: `products` (200+ registros)
**Propósito:** Almacenar todos los productos disponibles
```
Campos principales:
- product_id (PK, INT UNSIGNED)
- product_type (ENUM) - 'jersey', 'accessory', 'mystery_box', 'subscription'
- name (VARCHAR 255) - Nombre del producto
- slug (VARCHAR 255, UNIQUE) - URL-friendly
- description (TEXT) - Descripción detallada
- base_price (DECIMAL 10,2) - Precio base (24.99 EUR por defecto)
- original_price (DECIMAL 10,2) - Precio tachado (79.99 EUR)
- stock_quantity (INT UNSIGNED) - Stock total

Jersey-specific fields:
- league_id (FK → leagues, NULL) - Liga del equipo
- team_id (FK → teams, NULL) - Equipo
- jersey_type (ENUM) - 'home', 'away', 'third', 'goalkeeper', 'retro'
- season (VARCHAR 20) - Temporada (ej. '2024/25')
- version (ENUM) - 'fan', 'player'

Opcionales:
- has_patches_available (BOOLEAN) - Si permite parches
- patches_price (DECIMAL 10,2) - Precio de parches (1.99 EUR)
- has_personalization_available (BOOLEAN) - Si permite personalización
- personalization_price (DECIMAL 10,2) - Precio de personalización (2.99 EUR)

Estado:
- is_active (BOOLEAN) - Activo/Inactivo
- is_featured (BOOLEAN) - Destacado en inicio
- created_at, updated_at

Índices:
- idx_product_type, idx_slug, idx_team, idx_league
- idx_active_featured, idx_search (FULLTEXT)

Relaciones:
- FOREIGN KEY (league_id) → leagues(league_id) ON DELETE SET NULL
- FOREIGN KEY (team_id) → teams(team_id) ON DELETE SET NULL

Datos iniciales:
- 40 jerseys La Liga (2 por equipo: home/away)
- 22 jerseys Premier League
- 20 jerseys Serie A
- 36 jerseys Bundesliga
- 12 jerseys Ligue 1
- 14 jerseys Selecciones (incluyendo retros)
```

#### Tabla: `product_images` (200+ registros)
**Propósito:** Almacenar múltiples imágenes por producto
```
Campos principales:
- image_id (PK, INT UNSIGNED)
- product_id (FK → products) - Producto relacionado
- image_path (VARCHAR 255) - Ruta de la imagen
- image_type (ENUM) - 'main', 'detail', 'hover', 'gallery'
- display_order (INT UNSIGNED) - Orden de aparición
- alt_text (VARCHAR 255) - Texto alternativo
- created_at

Índices:
- idx_product (product_id)
- idx_type_order (image_type, display_order)

Relaciones:
- FOREIGN KEY (product_id) → products(product_id) ON DELETE CASCADE
```

#### Tabla: `product_variants` (1400+ registros)
**Propósito:** Gestionar tallas y stock por talla
```
Campos principales:
- variant_id (PK, INT UNSIGNED)
- product_id (FK → products) - Producto padre
- size (ENUM) - 'S','M','L','XL','2XL','3XL','4XL','16','18','20','22','24','26','28'
- size_category (ENUM) - 'general', 'player', 'kids', 'tracksuit'
- sku (VARCHAR 100, UNIQUE) - Stock Keeping Unit
- stock_quantity (INT UNSIGNED) - Stock disponible
- low_stock_threshold (INT UNSIGNED) - Umbral para alertas (default 10)

Medidas (para guía de tallas):
- chest_width_cm (DECIMAL 5,2)
- length_cm (DECIMAL 5,2)
- height_cm (DECIMAL 5,2) - Para niños
- weight_kg (DECIMAL 5,2) - Para niños
- age_range (VARCHAR 50) - Para niños

Estado:
- is_active (BOOLEAN)
- created_at, updated_at

Índices:
- idx_product_size (product_id, size)
- idx_sku (sku)
- idx_stock (stock_quantity)

Relaciones:
- FOREIGN KEY (product_id) → products(product_id) ON DELETE CASCADE
```

#### Tabla: `product_price_history` (histórico)
**Propósito:** Registrar cambios de precio
```
Campos principales:
- history_id (PK)
- product_id (FK → products)
- old_price, new_price (DECIMAL 10,2)
- change_reason (VARCHAR 255)
- changed_by (INT UNSIGNED) → admin_users
- changed_at (TIMESTAMP)

Índices:
- idx_product (product_id)
- idx_date (changed_at)
```

#### Tabla: `translations` (multiidioma)
**Propósito:** Soporte para ES/EN
```
Campos principales:
- translation_id (PK)
- entity_type (ENUM) - 'product', 'league', 'team', 'page', etc.
- entity_id (INT UNSIGNED) - ID de la entidad
- translation_key (VARCHAR 255) - Clave de traducción
- language (ENUM) - 'es', 'en'
- translation_value (TEXT) - Texto traducido
- created_at, updated_at

UNIQUE KEY: (entity_type, entity_id, translation_key, language)
```

#### Tabla: `size_guides` (28 registros)
**Propósito:** Guías de tallas reutilizables
```
Campos principales:
- guide_id (PK)
- category (ENUM) - 'general', 'player', 'kids', 'tracksuit'
- size (VARCHAR 10) - S, M, L, etc.
- chest_width_cm, length_cm, height_cm, weight_kg
- age_range (VARCHAR 50)
- language (ENUM) - 'es', 'en'
- created_at

UNIQUE KEY: (category, size, language)
```

---

### 2. TABLAS DE USUARIOS Y CLIENTES (4 tablas)

#### Tabla: `customers` (registro de clientes)
**Propósito:** Almacenar todos los clientes con autenticación híbrida
```
Campos principales - Autenticación clásica:
- customer_id (PK, INT UNSIGNED)
- email (VARCHAR 255, UNIQUE) - Opcional (NULL si usa Telegram/WhatsApp)
- password_hash (VARCHAR 255) - Bcrypt hash
- email_verified (BOOLEAN)
- email_verification_token (VARCHAR 100)

Autenticación social:
- telegram_username (VARCHAR 100, UNIQUE)
- telegram_chat_id (VARCHAR 100, UNIQUE)
- whatsapp_number (VARCHAR 20)

Información básica:
- full_name (VARCHAR 255) - REQUERIDO
- phone (VARCHAR 20)
- preferred_language (ENUM) - 'es', 'en' (default 'es')

Estado de cuenta:
- customer_status (ENUM) - 'active', 'inactive', 'blocked'
- registration_date (TIMESTAMP)
- last_activity_date (TIMESTAMP)
- last_login_date (TIMESTAMP)

Marketing:
- marketing_consent (BOOLEAN)
- newsletter_subscribed (BOOLEAN)

Lealtad:
- loyalty_tier (ENUM) - 'standard', 'silver', 'gold', 'platinum'
- loyalty_points (INT UNSIGNED) - Puntos acumulados
- total_orders_count (INT UNSIGNED) - Contador de pedidos
- total_spent (DECIMAL 10,2) - Dinero total gastado

Seguridad:
- password_reset_token (VARCHAR 100)
- password_reset_expires (TIMESTAMP)
- failed_login_attempts (INT UNSIGNED)
- locked_until (TIMESTAMP)

Soft delete:
- deleted_at (TIMESTAMP) - Para eliminación lógica

Timestamps:
- created_at, updated_at

Índices:
- idx_email (email)
- idx_telegram (telegram_username)
- idx_whatsapp (whatsapp_number)
- idx_status (customer_status)
- idx_loyalty (loyalty_tier, loyalty_points)

Sistema híbrido:
- Cliente puede tener SOLO email
- Cliente puede tener SOLO Telegram
- Cliente puede tener SOLO WhatsApp
- O una combinación de ellos
```

#### Tabla: `customer_preferences` (preferencias personalizadas)
**Propósito:** Almacenar preferencias por cliente
```
Campos principales:
- preference_id (PK)
- customer_id (FK → customers, UNIQUE) - Una preferencia por cliente
- favorite_teams (JSON) - Array de team_ids
- favorite_leagues (JSON) - Array de league_ids
- excluded_teams (JSON) - Equipos a excluir
- preferred_size_jersey (ENUM) - S a 4XL
- preferred_size_kids (ENUM) - 16 a 28
- prefers_fan_version (BOOLEAN)

Preferencias de notificación:
- notify_new_drops (BOOLEAN)
- notify_stock_alerts (BOOLEAN)
- notify_price_drops (BOOLEAN)
- notify_subscription_renewal (BOOLEAN)

Timestamps:
- created_at, updated_at

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE CASCADE
```

#### Tabla: `shipping_addresses` (direcciones de envío)
**Propósito:** Almacenar direcciones de entrega
```
Campos principales:
- address_id (PK, INT UNSIGNED)
- customer_id (FK → customers)
- is_default (BOOLEAN) - Dirección por defecto
- recipient_name (VARCHAR 255) - Nombre del destinatario
- phone (VARCHAR 20) - Teléfono de contacto
- email (VARCHAR 255) - Email del destinatario
- street_address (VARCHAR 255) - Calle
- additional_address (VARCHAR 255) - Piso, puerta, etc.
- city (VARCHAR 100) - Ciudad
- province (VARCHAR 100) - Comunidad autónoma
- postal_code (VARCHAR 20) - Código postal
- country (VARCHAR 100) - País (default 'España')
- additional_notes (TEXT) - Instrucciones especiales
- is_active (BOOLEAN) - Activa/Inactiva
- created_at, updated_at

Índices:
- idx_customer (customer_id)
- idx_default (is_default)

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE CASCADE
```

#### Tabla: `customer_sessions` (sesiones)
**Propósito:** Rastrear sesiones de usuario
```
Campos principales:
- session_id (VARCHAR 100, PK) - Identificador de sesión
- customer_id (FK → customers, NULL) - Null si es guest
- session_data (JSON) - Datos de sesión
- ip_address (VARCHAR 45)
- user_agent (TEXT)
- created_at, expires_at
- last_activity (TIMESTAMP)

Índices:
- idx_customer (customer_id)
- idx_expires (expires_at)
```

---

### 3. TABLAS DE SUSCRIPCIONES (4 tablas)

#### Tabla: `subscription_plans` (4 planes)
**Propósito:** Almacenar planes de suscripción disponibles
```
Planes configurados:
1. Plan FAN (24.99 EUR/mes)
   - 1 camiseta FAN aleatoria/mes
   - Badge: "MÁS POPULAR"
   
2. Plan Premium Random (29.99 EUR/mes)
   - 1 camiseta PLAYER aleatorio de clubs TOP
   - Acceso anticipado a drops
   - 10% descuento en tienda
   
3. Plan Premium TOP (34.99 EUR/mes)
   - 1 camiseta PLAYER de clubs ELITE (Madrid, Barça, PSG, Bayern, City, etc.)
   - Envío prioritario
   - Pin de coleccionista
   - Certificado de autenticidad
   - 10% descuento
   - Badge: "TOP"
   
4. Plan Retro TOP (39.99 EUR/mes)
   - 1 camiseta retro mítica
   - Acceso anticipado
   - Certificado
   - Badge: "👑 LEGEND"

Campos principales:
- plan_id (PK, INT UNSIGNED)
- plan_name (VARCHAR 100)
- plan_slug (VARCHAR 100, UNIQUE)
- plan_type (ENUM) - 'fan', 'premium_random', 'premium_top', 'retro_top'
- monthly_price (DECIMAL 10,2)
- setup_fee (DECIMAL 10,2) - Opcional (default 0)
- description (TEXT)
- jersey_quantity (INT UNSIGNED) - Camisetas por mes
- jersey_quality (ENUM) - 'fan', 'player', 'retro'
- includes_* (BOOLEAN) - Características incluidas
- store_discount_percentage (INT UNSIGNED)
- badge_text, badge_color
- display_order (INT UNSIGNED)
- is_active (BOOLEAN)
- created_at, updated_at

Índices:
- idx_slug (plan_slug)
- idx_active_order (is_active, display_order)
```

#### Tabla: `subscriptions` (suscripciones activas)
**Propósito:** Gestionar suscripciones de clientes
```
Campos principales:
- subscription_id (PK, INT UNSIGNED)
- customer_id (FK → customers)
- plan_id (FK → subscription_plans)
- status (ENUM) - 'active', 'pending', 'cancelled', 'paused', 'expired'
- start_date (DATE)
- current_period_start, current_period_end (DATE)
- next_billing_date (DATE) - Próxima renovación
- cancellation_date (DATE)
- cancellation_reason (TEXT)
- pause_date (DATE)
- pause_reason (TEXT)
- preferred_size (ENUM) - S a 4XL
- league_preferences (JSON) - Ligas preferidas
- team_preferences (JSON) - Equipos preferidos
- teams_to_exclude (JSON) - Equipos a evitar
- total_months_paid (INT UNSIGNED)
- last_payment_date (DATE)
- last_payment_amount (DECIMAL 10,2)
- created_at, updated_at

Índices:
- idx_customer (customer_id)
- idx_status (status)
- idx_next_billing (next_billing_date)
- idx_period_end (current_period_end)

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE RESTRICT
- FOREIGN KEY (plan_id) → subscription_plans(plan_id) ON DELETE RESTRICT

Ciclo de vida:
- pending → active (después de primer pago)
- active → paused (pausa temporal)
- paused → active (reactivación)
- active → cancelled (final)
- active → expired (fecha vencimiento)
```

#### Tabla: `subscription_shipments` (envíos de suscripción)
**Propósito:** Historial de envíos para cada suscripción
```
Campos principales:
- shipment_id (PK, INT UNSIGNED)
- subscription_id (FK → subscriptions)
- shipment_date (DATE)
- expected_delivery_date (DATE)
- actual_delivery_date (DATE)
- tracking_number (VARCHAR 100)
- carrier (VARCHAR 100) - Empresa de envíos
- status (ENUM) - 'pending', 'preparing', 'shipped', 'in_transit', 'delivered', 'returned', 'failed'
- contents (JSON) - [{product_id, variant_id, quantity}, ...]
- notes (TEXT)
- created_at, updated_at

Índices:
- idx_subscription (subscription_id)
- idx_status (status)
- idx_shipment_date (shipment_date)
```

#### Tabla: `subscription_payments` (pagos de suscripción)
**Propósito:** Historial de pagos por suscripción
```
Campos principales:
- payment_id (PK, INT UNSIGNED)
- subscription_id (FK → subscriptions)
- payment_date (DATE)
- amount (DECIMAL 10,2)
- payment_method (ENUM) - 'oxapay', 'telegram', 'whatsapp', 'manual'
- payment_reference (VARCHAR 255)
- period_start, period_end (DATE)
- status (ENUM) - 'pending', 'completed', 'failed', 'refunded'
- transaction_id (VARCHAR 255) - ID de Oxapay
- transaction_data (JSON)
- notes (TEXT)
- processed_by (INT UNSIGNED) → admin_users
- created_at

Índices:
- idx_subscription (subscription_id)
- idx_payment_date (payment_date)
- idx_status (status)
```

---

### 4. TABLAS DE MYSTERY BOXES (3 tablas)

#### Tabla: `mystery_box_types` (3 tipos)
**Propósito:** Almacenar tipos de cajas sorpresa disponibles

**Tipos configurados:**
```
1. Box Clásica (124.95 EUR)
   - 5 camisetas FAN variadas
   - Stock disponible inicialmente

2. Box por Liga (174.95 EUR)
   - 5 camisetas PLAYER de una liga elegida
   - Cliente elige: La Liga, Premier, Serie A, Bundesliga o Ligue 1

3. Box Premium Elite (174.95 EUR)
   - 5 camisetas PLAYER de equipos TOP
   - Con empaque premium
   - Con certificado
   - Envío express incluido
```

Campos principales:
- box_type_id (PK, INT UNSIGNED)
- name (VARCHAR 100) - Nombre de la caja
- slug (VARCHAR 100, UNIQUE)
- description (TEXT) - Descripción detallada
- price (DECIMAL 10,2)
- original_price (DECIMAL 10,2) - Precio tachado
- jersey_quantity (INT UNSIGNED) - Camisetas incluidas
- jersey_quality (ENUM) - 'fan', 'player', 'mixed'
- league_restriction (INT UNSIGNED) → leagues - NULL = cualquier liga
- team_tier_restriction (ENUM) - 'any', 'top_only'
- includes_premium_packaging, includes_certificate, includes_express_shipping (BOOLEAN)
- badge_text, badge_color
- display_order (INT UNSIGNED)
- is_active (BOOLEAN)
- created_at, updated_at

#### Tabla: `mystery_box_orders` (pedidos de caja)
**Propósito:** Registrar compras de mystery boxes
```
Campos principales:
- order_id (PK, INT UNSIGNED)
- customer_id (FK → customers)
- box_type_id (FK → mystery_box_types)
- order_date (TIMESTAMP)
- status (ENUM) - 'pending', 'preparing', 'shipped', 'delivered', 'cancelled'
- selected_league_id (FK → leagues, NULL) - Para "Box por Liga"
- preferred_size (ENUM)
- special_instructions (TEXT)
- total_price (DECIMAL 10,2)
- shipping_address_id (FK → shipping_addresses)
- tracking_number (VARCHAR 100)
- shipped_date, delivered_date (DATE)
- payment_id (INT UNSIGNED) → payment_transactions
- created_at, updated_at

Índices:
- idx_customer, idx_status, idx_order_date
```

#### Tabla: `mystery_box_contents` (contenido de caja)
**Propósito:** Registrar qué contenía cada caja (cuando se abre)
```
Campos principales:
- content_id (PK)
- order_id (FK → mystery_box_orders)
- product_id (FK → products)
- variant_id (FK → product_variants)
- quantity (INT UNSIGNED)
- reveal_date (TIMESTAMP) - Cuándo se abrió la caja
- created_at

Relaciones:
- FOREIGN KEY (order_id) → mystery_box_orders(order_id) ON DELETE CASCADE
- FOREIGN KEY (product_id) → products(product_id) ON DELETE RESTRICT
- FOREIGN KEY (variant_id) → product_variants(variant_id) ON DELETE RESTRICT
```

---

### 5. TABLAS DE DROP EVENTS (3 tablas)

#### Tabla: `drop_events` (eventos de drop)
**Propósito:** Gestionar eventos de gamificación con drops

```
Evento configurado:
- Drop Semanal - Noviembre 2024
  - Fecha: 2024-11-01 a 2024-11-30
  - 100 drops disponibles
  - Máximo 1 drop por cliente
  - Precio: 24.99 EUR
  - Estado: Activo

Campos principales:
- drop_event_id (PK, INT UNSIGNED)
- event_name (VARCHAR 255)
- description (TEXT)
- start_date, end_date (TIMESTAMP) - Rango de evento
- total_drops_available (INT UNSIGNED)
- remaining_drops (INT UNSIGNED)
- max_drops_per_customer (INT UNSIGNED)
- drop_price (DECIMAL 10,2)
- is_active (BOOLEAN)
- created_at, updated_at

Índices:
- idx_dates (start_date, end_date)
- idx_active (is_active)
```

#### Tabla: `drop_pool_items` (items disponibles en drop)
**Propósito:** Pool de camisetas que se pueden ganar

```
Items configurados:
COMUNES (probabilidad 62%):
- Real Sociedad Home
- Aston Villa Home
- Olympique Lyon Home
- Benfica Home

RAROS (probabilidad 30%):
- Atlético Madrid Home
- Arsenal Home
- Juventus Retro 1997
- Inter Home

LEGENDARIOS (probabilidad 8%):
- Real Madrid Home
- Barcelona Home
- Argentina 1986 Retro
- Brasil 2002 Retro

Campos principales:
- pool_item_id (PK)
- drop_event_id (FK → drop_events)
- product_id (FK → products)
- rarity (ENUM) - 'common', 'rare', 'legendary'
- weight (INT UNSIGNED) - Probabilidad (mayor = más probable)
- display_order (INT UNSIGNED)
- is_active (BOOLEAN)
- created_at

Total: 62 + 30 + 8 = 100 (pesos suman 100%)

Índices:
- idx_event (drop_event_id)
- idx_rarity (rarity)
- idx_weight (weight)
```

#### Tabla: `drop_results` (resultados de drops)
**Propósito:** Registrar qué ganó cada usuario

```
Campos principales:
- result_id (PK)
- drop_event_id (FK → drop_events)
- customer_id (FK → customers, NULL) - NULL para guests
- session_id (VARCHAR 100) - Para rastrear guests
- pool_item_id (FK → drop_pool_items)
- selected_size (ENUM)
- was_purchased (BOOLEAN) - Si finalmente compró
- catalog_order_id (INT UNSIGNED) → orders - Order si compró
- result_date (TIMESTAMP)
- purchase_deadline (TIMESTAMP) - 24-48h para comprar
- purchased_at (TIMESTAMP)

Índices:
- idx_event_customer (drop_event_id, customer_id)
- idx_purchased (was_purchased)
- idx_result_date (result_date)
```

---

### 6. TABLAS DE PEDIDOS (2 tablas)

#### Tabla: `orders` (pedidos principales)
**Propósito:** Almacenar todos los pedidos del catálogo

```
Campos principales:
- order_id (PK, INT UNSIGNED)
- customer_id (FK → customers)
- order_type (ENUM) - 'catalog', 'mystery_box', 'subscription_initial', 'drop', 'upsell'
- order_source (ENUM) - 'web', 'telegram', 'whatsapp', 'instagram'
- order_status (ENUM) - 'pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'
- payment_status (ENUM) - 'pending', 'completed', 'failed', 'refunded', 'partially_refunded'

Precios:
- subtotal (DECIMAL 10,2)
- discount_amount (DECIMAL 10,2) - Descuento aplicado
- coupon_id (INT UNSIGNED) → coupons
- shipping_cost (DECIMAL 10,2)
- total_amount (DECIMAL 10,2)

Pago:
- payment_method (ENUM) - 'oxapay', 'telegram', 'whatsapp', 'manual'
- payment_id (INT UNSIGNED) → payment_transactions

Envío:
- shipping_address_id (FK → shipping_addresses)
- tracking_number (VARCHAR 100)
- carrier (VARCHAR 100)
- shipped_date, delivered_date (DATE)

Notas:
- customer_notes (TEXT)
- admin_notes (TEXT)

Timestamps:
- order_date (TIMESTAMP)
- updated_at (TIMESTAMP)

Índices:
- idx_customer (customer_id)
- idx_order_status (order_status)
- idx_payment_status (payment_status)
- idx_order_date (order_date)
- idx_type (order_type)

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE RESTRICT
- FOREIGN KEY (shipping_address_id) → shipping_addresses(address_id) ON DELETE RESTRICT
```

#### Tabla: `order_items` (líneas de pedido)
**Propósito:** Detalles de cada artículo en el pedido

```
Campos principales:
- order_item_id (PK, INT UNSIGNED)
- order_id (FK → orders)
- product_id (FK → products)
- variant_id (FK → product_variants)
- quantity (INT UNSIGNED)
- unit_price (DECIMAL 10,2) - Precio en momento de compra

Opcionales (parches y personalización):
- has_patches (BOOLEAN)
- patches_price (DECIMAL 10,2)
- has_personalization (BOOLEAN)
- personalization_name (VARCHAR 50) - Nombre a personalizar
- personalization_number (VARCHAR 5) - Número de jugador
- personalization_price (DECIMAL 10,2)

Cálculos:
- subtotal (DECIMAL 10,2) - unit_price * quantity + opcionales

Promociones:
- is_free_item (BOOLEAN) - Para promociones 3x2
- promotion_id (INT UNSIGNED)

Timestamps:
- created_at

Índices:
- idx_order (order_id)
- idx_product (product_id)
```

---

### 7. TABLAS DE PAGOS (2 tablas)

#### Tabla: `payment_transactions` (transacciones)
**Propósito:** Registrar todas las transacciones de pago

```
Campos principales:
- transaction_id (PK, INT UNSIGNED)
- customer_id (FK → customers)
- order_id (FK → orders, NULL)
- subscription_id (FK → subscriptions, NULL)

Método de pago:
- payment_method (ENUM) - 'oxapay_btc', 'oxapay_eth', 'oxapay_usdt', 
                           'telegram_manual', 'whatsapp_manual', 'bank_transfer'
- amount (DECIMAL 10,2)
- currency (VARCHAR 10) - EUR

Estado:
- status (ENUM) - 'pending', 'processing', 'completed', 'failed', 'expired', 'refunded'

Oxapay específico:
- oxapay_transaction_id (VARCHAR 255)
- oxapay_payment_url (VARCHAR 500)
- oxapay_qr_code (VARCHAR 500)
- oxapay_crypto_amount (DECIMAL 20,8)
- oxapay_crypto_currency (VARCHAR 10) - BTC, ETH, USDT
- oxapay_network (VARCHAR 50) - Red blockchain
- oxapay_wallet_address (VARCHAR 255)
- oxapay_response (JSON) - Respuesta completa

Pago manual:
- manual_payment_reference (VARCHAR 255)
- manual_payment_proof (VARCHAR 500) - Ruta de comprobante
- verified_by (INT UNSIGNED) → admin_users
- verified_at (TIMESTAMP)

Timestamps:
- initiated_at (TIMESTAMP)
- completed_at (TIMESTAMP)
- expires_at (TIMESTAMP)

Notas:
- notes (TEXT)

Índices:
- idx_customer, idx_order, idx_status
- idx_oxapay_id (oxapay_transaction_id)
- idx_initiated (initiated_at)
```

#### Tabla: `payment_webhooks` (webhooks de Oxapay)
**Propósito:** Log de webhooks recibidos

```
Campos principales:
- webhook_id (PK)
- transaction_id (FK → payment_transactions, NULL)
- webhook_type (VARCHAR 50)
- payload (JSON) - Datos del webhook
- signature (VARCHAR 255) - Firma de verificación
- processed (BOOLEAN)
- processed_at (TIMESTAMP)
- processing_error (TEXT)
- received_at (TIMESTAMP)

Índices:
- idx_transaction (transaction_id)
- idx_processed (processed)
- idx_received (received_at)
```

---

### 8. TABLAS DE PROMOCIONES (4 tablas)

#### Tabla: `coupons` (cupones de descuento)
**Propósito:** Gestionar códigos de promoción

```
Cupones configurados:
1. WELCOME5 - 5€ descuento (primera compra, compra mín. 60€)
2. NOTBETTING10 - 10% desc. máx 5€
3. TOPBONUS10 - 10% desc. máx 5€
4. KICKVERSE10 - 10% desc. general máx 5€
5. MYSTERY10 - 10% desc. en Mystery Boxes máx 15€
6. CATALOGO5 - 5€ desc. en catálogo

Campos principales:
- coupon_id (PK, INT UNSIGNED)
- code (VARCHAR 50, UNIQUE) - Código del cupón
- description (VARCHAR 255)
- discount_type (ENUM) - 'fixed', 'percentage'
- discount_value (DECIMAL 10,2)
- max_discount_amount (DECIMAL 10,2) - Para % coupons
- min_purchase_amount (DECIMAL 10,2)
- applies_to_product_type (ENUM) - 'all', 'jersey', 'mystery_box', 'subscription'
- applies_to_first_order_only (BOOLEAN)
- usage_limit_total (INT UNSIGNED, NULL = ilimitado)
- usage_limit_per_customer (INT UNSIGNED)
- times_used (INT UNSIGNED)
- valid_from, valid_until (TIMESTAMP)
- is_active (BOOLEAN)
- created_by (INT UNSIGNED) → admin_users
- created_at, updated_at

Índices:
- idx_code (code)
- idx_active (is_active)
- idx_validity (valid_from, valid_until)
```

#### Tabla: `coupon_usage` (historial de uso)
**Propósito:** Rastrear cada uso de cupón

```
Campos principales:
- usage_id (PK)
- coupon_id (FK → coupons)
- customer_id (FK → customers)
- order_id (FK → orders)
- discount_applied (DECIMAL 10,2)
- used_at (TIMESTAMP)

Índices:
- idx_coupon, idx_customer, idx_order

Relaciones:
- FOREIGN KEY (coupon_id) → coupons(coupon_id) ON DELETE CASCADE
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE CASCADE
- FOREIGN KEY (order_id) → orders(order_id) ON DELETE CASCADE
```

#### Tabla: `promotional_campaigns` (campañas)
**Propósito:** Gestionar campañas de marketing

```
Campos principales:
- campaign_id (PK)
- campaign_name (VARCHAR 255)
- campaign_type (ENUM) - '3x2', 'first_purchase', 'exit_intent', 'bundle', 'flash_sale', 'seasonal'
- description (TEXT)
- trigger_condition (JSON) - Condiciones para mostrar
- discount_description (VARCHAR 255)
- auto_apply_coupon_id (FK → coupons)
- start_date, end_date (TIMESTAMP)
- is_active (BOOLEAN)
- impression_count, conversion_count (INT UNSIGNED)
- created_at, updated_at

Índices:
- idx_active, idx_dates, idx_type
```

#### Tabla: `promotion_3x2_usage` (seguimiento 3x2)
**Propósito:** Registrar uso de promoción 3x2

```
Campos principales:
- usage_id (PK)
- order_id (FK → orders)
- customer_id (FK → customers)
- paid_item_1_id (INT UNSIGNED) → order_items
- paid_item_2_id (INT UNSIGNED) → order_items
- free_item_id (INT UNSIGNED) → order_items
- discount_amount (DECIMAL 10,2)
- used_at (TIMESTAMP)
```

---

### 9. TABLAS DE LEALTAD (3 tablas)

#### Tabla: `loyalty_points_history` (historial de puntos)
**Propósito:** Registrar todas las transacciones de puntos

```
Campos principales:
- transaction_id (PK)
- customer_id (FK → customers)
- points_change (INT) - Positivo (ganado), negativo (gastado)
- points_balance_after (INT UNSIGNED) - Saldo después
- transaction_type (ENUM) - 'order_purchase', 'order_refund', 'points_redemption', 
                             'birthday_bonus', 'referral', 'manual_adjustment', 'tier_bonus'
- reference_order_id (FK → orders, NULL)
- description (VARCHAR 255)
- expires_at (DATE) - Si aplica
- created_by (INT UNSIGNED) → admin_users
- created_at

Cálculo de puntos:
- 1 punto = 1 EUR gastado
- Multiplicador según tier de lealtad
- Automatizado por trigger al completar orden

Índices:
- idx_customer, idx_type, idx_created
```

#### Tabla: `loyalty_tier_benefits` (beneficios por tier)
**Propósito:** Configuración de tiers de lealtad

```
Tiers configurados:
1. STANDARD (por defecto)
   - 0 órdenes mínimas, 0€ gastados
   - Multiplicador: 1.0x
   - Descuento: 0%
   - Sin envío gratis
   - Sin acceso anticipado
   - Sin soporte prioritario
   - Bonus cumpleaños: 0 puntos

2. SILVER
   - 3 órdenes mínimas, 100€ mínimo gastado
   - Multiplicador: 1.25x (+25% puntos)
   - Descuento: 5%
   - Sin envío gratis
   - Bonus cumpleaños: 50 puntos

3. GOLD
   - 10 órdenes mínimas, 300€ mínimo gastado
   - Multiplicador: 1.50x (+50% puntos)
   - Descuento: 10%
   - Envío gratis activado
   - Acceso anticipado a drops
   - Soporte prioritario
   - Bonus cumpleaños: 100 puntos

4. PLATINUM
   - 25 órdenes mínimas, 750€ mínimo gastado
   - Multiplicador: 2.0x (doble puntos)
   - Descuento: 15%
   - Envío gratis activado
   - Acceso anticipado a drops
   - Soporte prioritario VIP
   - Bonus cumpleaños: 200 puntos

Campos principales:
- benefit_id (PK)
- tier (ENUM, UNIQUE) - 'standard', 'silver', 'gold', 'platinum'
- min_orders_required (INT UNSIGNED)
- min_total_spent (DECIMAL 10,2)
- points_multiplier (DECIMAL 3,2)
- discount_percentage (INT UNSIGNED)
- free_shipping, early_drop_access, priority_support (BOOLEAN)
- birthday_bonus_points (INT UNSIGNED)
- created_at, updated_at
```

#### Tabla: `loyalty_rewards` (catálogo de recompensas)
**Propósito:** Canjear puntos por recompensas

```
Recompensas configuradas:
1. Descuento 5€ (500 puntos)
2. Descuento 10€ (900 puntos)
3. Envío Gratis (300 puntos, máx 5 canjes)
4. 15% Descuento (1200 puntos, máx 1 canje)

Campos principales:
- reward_id (PK)
- reward_name (VARCHAR 255)
- description (TEXT)
- points_required (INT UNSIGNED)
- reward_type (ENUM) - 'discount_coupon', 'free_shipping', 'free_product', 'percentage_off'
- reward_value (VARCHAR 255) - Código, product_id, o porcentaje
- max_redemptions_total (INT UNSIGNED, NULL = ilimitado)
- max_redemptions_per_customer (INT UNSIGNED)
- times_redeemed (INT UNSIGNED)
- is_active (BOOLEAN)
- created_at, updated_at

Índices:
- idx_points (points_required)
- idx_active (is_active)
```

---

### 10. TABLAS DE WISHLIST (2 tablas)

#### Tabla: `wishlists` (lista de deseos)
**Propósito:** Guardar productos favoritos

```
Campos principales:
- wishlist_id (PK)
- customer_id (FK → customers)
- product_id (FK → products)
- variant_id (FK → product_variants, NULL) - Talla específica si aplica
- notify_on_stock (BOOLEAN) - Alertar si vuelve a estar disponible
- notify_on_price_drop (BOOLEAN) - Alertar si baja de precio
- price_when_added (DECIMAL 10,2)
- added_at (TIMESTAMP)

UNIQUE KEY: (customer_id, product_id, variant_id)

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE CASCADE
- FOREIGN KEY (product_id) → products(product_id) ON DELETE CASCADE
- FOREIGN KEY (variant_id) → product_variants(variant_id) ON DELETE CASCADE
```

#### Tabla: `wishlist_notifications_sent` (historial)
**Propósito:** Rastrear notificaciones enviadas

```
Campos principales:
- notification_id (PK)
- wishlist_id (FK → wishlists)
- notification_type (ENUM) - 'back_in_stock', 'price_drop', 'low_stock_alert'
- sent_at (TIMESTAMP)

Índices:
- idx_wishlist, idx_sent
```

---

### 11. TABLAS DE CARRITO (2 tablas)

#### Tabla: `carts` (carritos de compra)
**Propósito:** Gestionar carritos de compra

```
Campos principales:
- cart_id (PK, INT UNSIGNED)
- customer_id (FK → customers, NULL) - NULL para guests
- session_id (VARCHAR 100, NULL) - Para guest carts
- cart_status (ENUM) - 'active', 'abandoned', 'converted', 'expired'
- created_at, updated_at
- expires_at (TIMESTAMP) - 7 días por defecto
- converted_to_order_id (FK → orders, NULL)

Índices:
- idx_customer, idx_session, idx_status, idx_expires

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE CASCADE
- FOREIGN KEY (converted_to_order_id) → orders(order_id) ON DELETE SET NULL
```

#### Tabla: `cart_items` (artículos del carrito)
**Propósito:** Detalles de cada artículo en carrito

```
Campos principales:
- cart_item_id (PK, INT UNSIGNED)
- cart_id (FK → carts)
- product_id (FK → products)
- variant_id (FK → product_variants)
- quantity (INT UNSIGNED)
- has_patches (BOOLEAN)
- has_personalization (BOOLEAN)
- personalization_name, personalization_number (VARCHAR)
- unit_price (DECIMAL 10,2) - Precio en momento de agregar
- added_at, updated_at

Índices:
- idx_cart, idx_product

Relaciones:
- FOREIGN KEY (cart_id) → carts(cart_id) ON DELETE CASCADE
- FOREIGN KEY (product_id) → products(product_id) ON DELETE CASCADE
- FOREIGN KEY (variant_id) → product_variants(variant_id) ON DELETE CASCADE
```

---

### 12. TABLAS DE INVENTARIO (2 tablas)

#### Tabla: `stock_movements` (historial de movimientos)
**Propósito:** Auditar todos los cambios de stock

```
Campos principales:
- movement_id (PK)
- product_variant_id (FK → product_variants)
- movement_type (ENUM) - 'purchase', 'sale', 'return', 'adjustment', 
                          'reserved', 'unreserved', 'damaged', 'lost'
- quantity (INT) - Puede ser negativo
- stock_after (INT UNSIGNED) - Stock después del movimiento
- reference_order_id (FK → orders, NULL)
- reference_subscription_shipment_id (FK → subscription_shipments, NULL)
- reference_mystery_box_order_id (FK → mystery_box_orders, NULL)
- notes (TEXT)
- created_by (INT UNSIGNED) → admin_users
- created_at

Índices:
- idx_variant, idx_type, idx_created

Relaciones:
- FOREIGN KEY (product_variant_id) → product_variants(variant_id) ON DELETE CASCADE
```

#### Tabla: `low_stock_alerts` (alertas de bajo stock)
**Propósito:** Monitorear productos con bajo stock

```
Campos principales:
- alert_id (PK)
- product_variant_id (FK → product_variants)
- threshold_quantity (INT UNSIGNED) - Umbral configurado
- current_quantity (INT UNSIGNED) - Cantidad actual
- alert_status (ENUM) - 'pending', 'notified', 'resolved', 'dismissed'
- resolved_at (TIMESTAMP)
- resolved_by (INT UNSIGNED) → admin_users
- resolution_notes (TEXT)
- created_at, updated_at

Índices:
- idx_variant, idx_status

Trigger: Crea automáticamente cuando stock cae bajo umbral
```

---

### 13. TABLAS DE COMUNICACIONES (2 tablas)

#### Tabla: `customer_messages` (mensajes)
**Propósito:** Historial de comunicaciones

```
Campos principales:
- message_id (PK)
- customer_id (FK → customers)
- channel (ENUM) - 'telegram', 'whatsapp', 'email', 'instagram', 'internal'
- direction (ENUM) - 'inbound', 'outbound'
- message_subject (VARCHAR 255, NULL)
- message_content (TEXT)
- message_data (JSON) - Datos raw de plataforma
- related_order_id (FK → orders, NULL)
- related_subscription_id (FK → subscriptions, NULL)
- status (ENUM) - 'sent', 'delivered', 'read', 'failed'
- sent_at (TIMESTAMP)
- delivered_at (TIMESTAMP)
- read_at (TIMESTAMP)
- handled_by (INT UNSIGNED) → admin_users
- is_resolved (BOOLEAN)

Índices:
- idx_customer, idx_channel, idx_status, idx_sent
```

#### Tabla: `notifications` (notificaciones del sistema)
**Propósito:** Notificaciones automáticas

```
Campos principales:
- notification_id (PK)
- customer_id (FK → customers)
- notification_type (ENUM) - 'order_shipped', 'order_delivered', 'subscription_renewal', 
                              'subscription_expiring', 'drop_available', 'stock_alert', 
                              'price_drop', 'payment_reminder'
- title (VARCHAR 255)
- message (TEXT)
- sent_via (ENUM) - 'telegram', 'whatsapp', 'email', 'web_push'
- sent_at (TIMESTAMP)
- read_at (TIMESTAMP)
- related_order_id (FK → orders, NULL)
- related_subscription_id (FK → subscriptions, NULL)
- action_url (VARCHAR 500) - URL de acción

Índices:
- idx_customer, idx_type, idx_read, idx_sent
```

---

### 14. TABLAS DE ANALYTICS (2 tablas)

#### Tabla: `analytics_events` (eventos de usuario)
**Propósito:** Rastrear comportamiento de usuario

```
Campos principales:
- event_id (PK, BIGINT UNSIGNED)
- customer_id (FK → customers, NULL)
- session_id (VARCHAR 100)
- event_type (ENUM) - 'page_view', 'cta_click', 'product_view', 'add_to_cart', 
                       'remove_from_cart', 'checkout_start', 'purchase', 'exit_intent', 
                       'scroll_depth', 'time_on_page', 'form_submit', 'drop_play', 'video_play'
- event_category (VARCHAR 100)
- event_label (VARCHAR 255)
- event_value (DECIMAL 10,2)
- page_url (VARCHAR 500)
- page_title (VARCHAR 255)
- referrer_url (VARCHAR 500)
- device_type (ENUM) - 'desktop', 'mobile', 'tablet'
- browser, os (VARCHAR 100)
- screen_resolution (VARCHAR 20)
- ip_address (VARCHAR 45)
- country, city (VARCHAR 100)
- event_data (JSON)
- created_at

Índices:
- idx_customer, idx_session, idx_event_type, idx_created, idx_page_url

Relaciones:
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE SET NULL
```

#### Tabla: `product_views` (vistas de productos)
**Propósito:** Rastrear interés en productos

```
Campos principales:
- view_id (PK, BIGINT UNSIGNED)
- product_id (FK → products)
- customer_id (FK → customers, NULL)
- session_id (VARCHAR 100)
- time_spent_seconds (INT UNSIGNED)
- scrolled_to_description, scrolled_to_reviews (BOOLEAN)
- clicked_add_to_cart (BOOLEAN)
- viewed_at (TIMESTAMP)

Índices:
- idx_product, idx_customer, idx_session, idx_viewed
```

---

### 15. TABLAS DE ADMIN Y SISTEMA (3 tablas)

#### Tabla: `admin_users` (usuarios administrativos)
**Propósito:** Control de acceso del panel admin

```
Campos principales:
- admin_id (PK, INT UNSIGNED)
- username (VARCHAR 100, UNIQUE)
- password_hash (VARCHAR 255) - Bcrypt
- email (VARCHAR 255, UNIQUE)
- full_name (VARCHAR 255)
- role (ENUM) - 'super_admin', 'admin', 'inventory_manager', 'customer_service', 'marketing', 'readonly'
- permissions (JSON) - Permisos específicos
- is_active (BOOLEAN)
- last_login, last_login_ip (TIMESTAMP, VARCHAR)
- failed_login_attempts (INT UNSIGNED)
- locked_until (TIMESTAMP)
- two_factor_enabled (BOOLEAN)
- two_factor_secret (VARCHAR 255)
- created_at, updated_at

Usuario predeterminado:
- Username: admin
- Password: admin123 (CAMBIAR EN PRODUCCIÓN!)
- Email: admin@kickverse.es
- Role: super_admin

Índices:
- idx_username, idx_email, idx_active
```

#### Tabla: `audit_log` (registro de auditoría)
**Propósito:** Rastrear todas las acciones

```
Campos principales:
- log_id (PK, BIGINT UNSIGNED)
- admin_id (FK → admin_users, NULL)
- customer_id (FK → customers, NULL)
- action_type (ENUM) - 'create', 'update', 'delete', 'login', 'logout', 
                        'password_change', 'status_change', 'payment_verify'
- entity_type (VARCHAR 100) - Tipo de entidad afectada
- entity_id (INT UNSIGNED)
- old_values, new_values (JSON) - Cambios
- description (TEXT)
- ip_address (VARCHAR 45)
- user_agent (TEXT)
- created_at

Índices:
- idx_admin, idx_customer, idx_entity, idx_action, idx_created

Relaciones:
- FOREIGN KEY (admin_id) → admin_users(admin_id) ON DELETE SET NULL
- FOREIGN KEY (customer_id) → customers(customer_id) ON DELETE SET NULL
```

#### Tabla: `system_settings` (configuración)
**Propósito:** Almacenar parámetros del sistema

```
Configuración actual:
- site_name: "Kickverse"
- telegram_contact: "@esKickverse"
- whatsapp_contact: "+34 614 299 735"
- email_contact: "hola@kickverse.es"
- instagram_handle: "@kickverse.es"
- twitter_handle: "@kickverse_es"
- tiktok_handle: "@kickverse_es"
- free_shipping_threshold: "50.00" EUR
- base_jersey_price: "24.99" EUR
- patches_price: "1.99" EUR
- personalization_price: "2.99" EUR
- gtm_id: "GTM-MQFTT34L"
- ga_id: "G-SD9ETEJ9TG"
- currency: "EUR"
- default_language: "es"
- shipping_countries: JSON array ["España"]
- return_policy_days: "14"

Campos principales:
- setting_id (PK)
- setting_key (VARCHAR 100, UNIQUE)
- setting_value (TEXT)
- setting_type (ENUM) - 'string', 'number', 'boolean', 'json'
- description (TEXT)
- is_public (BOOLEAN)
- updated_by (INT UNSIGNED) → admin_users
- updated_at

Índices:
- idx_key (setting_key)
- idx_public (is_public)
```

---

## TRIGGERS AUTOMÁTICOS

### 1. `update_customer_stats_after_order`
**Evento:** AFTER INSERT ON orders
```sql
Actualiza automáticamente:
- customers.total_orders_count (incrementa 1)
- customers.total_spent (suma order.total_amount)
- customers.last_activity_date = NOW()

Condición: Solo si order_status IN ('delivered', 'processing')
```

### 2. `create_stock_movement_on_order`
**Evento:** AFTER INSERT ON order_items
```sql
Automáticamente:
- Crea registro en stock_movements (movement_type='reserved')
- Actualiza product_variants.stock_quantity (decrementa)
- Registra reference_order_id para auditoría
```

### 3. `check_low_stock_after_movement`
**Evento:** AFTER INSERT ON stock_movements
```sql
Si stock_after <= low_stock_threshold:
- Crea/actualiza registro en low_stock_alerts
- alert_status = 'pending'
- Notifica al equipo de inventario
```

### 4. `award_loyalty_points_on_order`
**Evento:** AFTER UPDATE ON orders
```sql
Condición: OLD.order_status != 'delivered' AND NEW.order_status = 'delivered'

Acciones:
- Calcula puntos (floor(total_amount * tier_multiplier))
- Actualiza customers.loyalty_points
- Inserta en loyalty_points_history
- Verifica posible ascenso de tier
```

### 5. `check_subscription_expiration`
**Evento:** BEFORE UPDATE ON subscriptions
```sql
Si NEW.current_period_end < CURDATE() Y NEW.status = 'active':
- Cambia status a 'expired'
```

---

## ÍNDICES CLAVE PARA PERFORMANCE

```sql
Índices compuestos importantes:
- orders: (customer_id, order_status)
- orders: (order_date, order_status)
- products: (league_id, team_id)
- products: (is_active, product_type)
- subscriptions: (status, next_billing_date)
- drop_results: (drop_event_id, customer_id)
- product_variants: (product_id, size)
- analytics_events: (customer_id, created_at)
```

---

## CONFIGURACIÓN DE BASE DE DATOS

### Credenciales (config/database.php)
```
Host: 50.31.174.69
Base de datos: iqvfmscx_kickverse
Usuario: iqvfmscx_kickverse
Charset: utf8mb4
Collation: utf8mb4_unicode_ci
```

### PDO Options
```
- ERRMODE: EXCEPTION (lanza excepciones)
- FETCH_MODE: ASSOC (devuelve arrays asociativos)
- EMULATE_PREPARES: false (prepared statements verdaderos)
```

---

## FLUJOS DE DATOS PRINCIPALES

### 1. Flujo de Compra (Catálogo)
```
Customer → Cart Items → Orders → Order Items → Payment Transactions
         ↓                    ↓
    Wishlist         Shipping Address
                          ↓
                    Stock Movements
                          ↓
                    Loyalty Points
```

### 2. Flujo de Suscripción
```
Customer → Subscription Plan → Subscriptions → Subscription Payments
                                    ↓
                          Subscription Shipments
                                    ↓
                               Mystery Box Contents
                                    ↓
                          Loyalty Points (monthly)
```

### 3. Flujo de Mystery Box
```
Customer → Mystery Box Type → Mystery Box Orders → Mystery Box Contents
                                      ↓
                            Shipping Address
                                      ↓
                            Payment Transactions
```

### 4. Flujo de Drop Event
```
Drop Event → Drop Pool Items → Drop Results → Catalog Order (optional)
                                                      ↓
                                          Stock Movements
```

---

## QUERIES COMUNES PARA CRM

### Obtener cliente completo
```sql
SELECT c.*, 
       COUNT(o.order_id) as total_orders,
       SUM(o.total_amount) as total_spent
FROM customers c
LEFT JOIN orders o ON c.customer_id = o.customer_id
WHERE c.customer_id = ?
GROUP BY c.customer_id
```

### Productos más vendidos
```sql
SELECT p.product_id, p.name, COUNT(oi.order_item_id) as sales_count
FROM products p
JOIN order_items oi ON p.product_id = oi.product_id
WHERE p.is_active = 1
GROUP BY p.product_id
ORDER BY sales_count DESC
LIMIT 10
```

### Clientes VIP (platinum tier)
```sql
SELECT c.* FROM customers c
WHERE c.loyalty_tier = 'platinum'
  AND c.customer_status = 'active'
  AND c.deleted_at IS NULL
ORDER BY c.total_spent DESC
```

### Ordenes pendientes de pago
```sql
SELECT o.* FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_status = 'pending_payment'
  AND o.order_date > DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY o.order_date DESC
```

### Stock bajo
```sql
SELECT pv.variant_id, p.name, pv.size, pv.stock_quantity
FROM product_variants pv
JOIN products p ON pv.product_id = p.product_id
WHERE pv.stock_quantity < pv.low_stock_threshold
ORDER BY pv.stock_quantity ASC
```

---

## RESUMEN DE MODELOS PHP EXISTENTES

### 1. Model.php (Base)
- Métodos CRUD básicos
- Soporte para transacciones
- Queries flexibles con WHERE
- Soft delete

### 2. Customer.php
- Autenticación múltiple (email, Telegram, WhatsApp)
- Gestión de preferencias
- Direcciones de envío
- Sistema de puntos de lealtad
- Historial de compras

### 3. Product.php
- Búsqueda por liga/equipo
- Filtrado de productos
- Gestión de variantes (tallas)
- Historial de precios
- Imágenes por producto

### 4. Order.php
- Crear órdenes con cálculo de totales
- Aplicar cupones
- Actualizar estados
- Obtener detalles con items
- Estadísticas de ingresos

### 5. League.php
- Obtener ligas activas
- Obtener equipos de liga
- Búsqueda por slug

### 6. Admin.php
- Tokens de autenticación magic link
- Estadísticas del dashboard
- Gestión de usuarios admin

### 7. Cart.php
- Carrito para guests y registrados
- Agregar/remover items
- Conversión a orden
- Persistencia de datos

---

## PRÓXIMOS PASOS PARA CRM

1. **Crear modelos faltantes:**
   - Subscription.php
   - MysteryBox.php
   - DropEvent.php
   - Payment.php
   - LoyaltyReward.php

2. **Rutas de API para:**
   - Gestión de clientes
   - Reportes de ventas
   - Seguimiento de órdenes
   - Gestión de suscripciones
   - Alertas de inventario

3. **Vistas del panel admin:**
   - Dashboard con KPIs
   - Gestión de clientes
   - Ordenes y seguimiento
   - Inventario
   - Reportes

4. **Automatizaciones:**
   - Envío de notificaciones
   - Carrito abandonado
   - Renovación de suscripciones
   - Generación de shipments

---

## ARCHIVOS RELACIONADOS

- Schema SQL: `/database/schema.sql`
- Migración de datos: `/database/data_migration.sql`
- Configuración: `/config/database.php`
- Modelos: `/app/models/*.php`

