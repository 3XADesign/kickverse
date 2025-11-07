# KICKVERSE - Documentación Técnica de Base de Datos

## Resumen Ejecutivo

Esta documentación describe la estructura completa de la base de datos de Kickverse, una plataforma e-commerce de camisetas de fútbol con características avanzadas de suscripción, gamificación y fidelización.

**Base de Datos**: `iqvfmscx_kickverse`
**Motor**: MySQL 8.4 / InnoDB
**Codificación**: UTF8MB4
**Total de Tablas**: 46
**Estado**: ✅ Desplegada y poblada con datos iniciales

---

## Índice

1. [Arquitectura General](#arquitectura-general)
2. [Tablas por Módulo](#tablas-por-módulo)
3. [Queries Comunes](#queries-comunes)
4. [Procedimientos y Triggers](#procedimientos-y-triggers)
5. [Seguridad y Permisos](#seguridad-y-permisos)
6. [Mantenimiento](#mantenimiento)
7. [APIs y Endpoints](#apis-y-endpoints)
8. [Troubleshooting](#troubleshooting)

---

## Arquitectura General

### Principios de Diseño

1. **Normalización**: 3NF (Third Normal Form) para evitar redundancia
2. **Integridad Referencial**: Foreign Keys con políticas apropiadas
3. **Auditoría**: Timestamps automáticos y triggers de auditoría
4. **Escalabilidad**: Índices optimizados para queries frecuentes
5. **Flexibilidad**: Campos JSON para datos variables
6. **Seguridad**: Soft deletes en tablas críticas

### Tecnologías

- **Motor de Almacenamiento**: InnoDB (transaccional, soporta FK)
- **Codificación**: `utf8mb4_unicode_ci` (emojis, caracteres especiales)
- **Timestamps**: Automáticos con `DEFAULT CURRENT_TIMESTAMP`
- **Triggers**: Para mantener integridad y auditoría

---

## Tablas por Módulo

### 1. PRODUCTOS (7 tablas)

#### `leagues`
Liga de fútbol (La Liga, Premier League, etc.)

**Campos principales**:
- `league_id` (PK): ID único
- `name`: Nombre completo
- `slug`: URL-friendly identifier
- `logo_path`: Ruta al logo SVG
- `display_order`: Orden de visualización

**Ejemplo de Query**:
```sql
SELECT * FROM leagues WHERE is_active = TRUE ORDER BY display_order;
```

#### `teams`
Equipos de fútbol

**Campos principales**:
- `team_id` (PK)
- `league_id` (FK → leagues)
- `name`: Nombre del equipo
- `slug`: URL-friendly
- `is_top_team`: TRUE para equipos Premium TOP

**Relaciones**:
- Un equipo pertenece a una liga
- Un equipo puede tener múltiples productos

**Ejemplo de Query**:
```sql
SELECT t.*, l.name as league_name
FROM teams t
JOIN leagues l ON t.league_id = l.league_id
WHERE l.slug = 'laliga' AND t.is_active = TRUE;
```

#### `products`
Productos principales (camisetas, accesorios)

**Campos principales**:
- `product_id` (PK)
- `product_type`: ENUM('jersey', 'accessory', 'mystery_box', 'subscription')
- `team_id` (FK → teams, nullable)
- `league_id` (FK → leagues, nullable)
- `jersey_type`: ENUM('home', 'away', 'third', 'goalkeeper', 'retro')
- `season`: e.g., '2024/25'
- `version`: ENUM('fan', 'player')
- `base_price`: DECIMAL(10,2) - Precio base actual
- `stock_quantity`: Stock total (suma de variantes)
- `is_featured`: Destacado en portada

**Índices**:
- `idx_product_type`: Filtrar por tipo
- `idx_team`, `idx_league`: Búsqueda por equipo/liga
- `FULLTEXT idx_search`: Búsqueda de texto

**Ejemplo de Query**:
```sql
-- Productos destacados de La Liga
SELECT p.*, t.name as team_name
FROM products p
JOIN teams t ON p.team_id = t.team_id
JOIN leagues l ON p.league_id = l.league_id
WHERE p.is_active = TRUE
  AND p.is_featured = TRUE
  AND l.slug = 'laliga'
ORDER BY p.display_order;
```

#### `product_variants`
Variantes de talla con stock individual

**Campos principales**:
- `variant_id` (PK)
- `product_id` (FK → products)
- `size`: ENUM('S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', ...)
- `size_category`: ENUM('general', 'player', 'kids', 'tracksuit')
- `sku`: Stock Keeping Unit único
- `stock_quantity`: Stock de esta talla específica
- `low_stock_threshold`: Alerta cuando stock <= threshold

**Caso de Uso**: Cada producto tiene 7 variantes (tallas S-4XL)

**Ejemplo de Query**:
```sql
-- Verificar stock de una talla específica
SELECT pv.stock_quantity, pv.low_stock_threshold
FROM product_variants pv
WHERE pv.product_id = 123 AND pv.size = 'M';

-- Tallas disponibles de un producto
SELECT size, stock_quantity
FROM product_variants
WHERE product_id = 123 AND stock_quantity > 0;
```

#### `product_images`
Imágenes de productos

**Campos principales**:
- `image_id` (PK)
- `product_id` (FK → products)
- `image_path`: Ruta a la imagen
- `image_type`: ENUM('main', 'detail', 'hover', 'gallery')
- `display_order`: Orden de visualización

**Patrón de rutas**: `./img/camisetas/{league_slug}_{team_slug}_{type}.png`

**Ejemplo**: `./img/camisetas/laliga_madrid_local.png`

#### `product_price_history`
Historial de cambios de precio

**Campos principales**:
- `history_id` (PK)
- `product_id` (FK → products)
- `old_price`, `new_price`: DECIMAL(10,2)
- `change_reason`: Motivo del cambio
- `changed_by` (FK → admin_users)
- `changed_at`: TIMESTAMP

**Importancia**: Los precios en `order_items` son snapshots, nunca cambian retroactivamente.

**Ejemplo de Query**:
```sql
-- Ver historial de precios de un producto
SELECT old_price, new_price, change_reason, changed_at
FROM product_price_history
WHERE product_id = 123
ORDER BY changed_at DESC;
```

#### `size_guides`
Guías de tallas con medidas

**Categorías**: general, player, kids, tracksuit
**Idiomas**: es, en

**Ejemplo de Query**:
```sql
SELECT size, chest_width_cm, length_cm, age_range
FROM size_guides
WHERE category = 'kids' AND language = 'es'
ORDER BY CAST(size AS UNSIGNED);
```

---

### 2. CLIENTES (4 tablas)

#### `customers`
Clientes con autenticación híbrida

**Sistema de Auth Híbrido**:
1. **Clásico**: `email` + `password_hash`
2. **Telegram**: `telegram_username` + `telegram_chat_id`
3. **WhatsApp**: `whatsapp_number`

**Campos de Fidelización**:
- `loyalty_tier`: ENUM('standard', 'silver', 'gold', 'platinum')
- `loyalty_points`: INT UNSIGNED
- `total_orders_count`: Actualizado por trigger
- `total_spent`: DECIMAL(10,2), actualizado por trigger

**Soft Delete**: `deleted_at` TIMESTAMP NULL

**Ejemplo de Query**:
```sql
-- Login por email
SELECT * FROM customers
WHERE email = 'user@example.com'
  AND deleted_at IS NULL;

-- Login por Telegram
SELECT * FROM customers
WHERE telegram_username = 'username'
  AND deleted_at IS NULL;

-- Top clientes VIP
SELECT full_name, loyalty_tier, total_spent, loyalty_points
FROM customers
WHERE customer_status = 'active'
ORDER BY total_spent DESC
LIMIT 10;
```

#### `customer_preferences`
Preferencias del cliente

**Campos JSON**:
- `favorite_teams`: Array de `team_id`
- `favorite_leagues`: Array de `league_id`
- `excluded_teams`: Array de `team_id`

**Campos de Notificación**:
- `notify_new_drops`: BOOLEAN
- `notify_stock_alerts`: BOOLEAN
- `notify_price_drops`: BOOLEAN

**Ejemplo**:
```sql
SELECT
  c.full_name,
  cp.favorite_teams,
  cp.preferred_size_jersey
FROM customers c
LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
WHERE c.customer_id = 123;
```

#### `shipping_addresses`
Direcciones de envío múltiples

**Campos**:
- `address_id` (PK)
- `customer_id` (FK)
- `is_default`: Una sola puede ser default por cliente
- `street_address`, `city`, `province`, `postal_code`, `country`

**Restricción**: España por defecto

**Ejemplo**:
```sql
-- Dirección por defecto del cliente
SELECT * FROM shipping_addresses
WHERE customer_id = 123 AND is_default = TRUE;

-- Todas las direcciones
SELECT * FROM shipping_addresses
WHERE customer_id = 123 AND is_active = TRUE;
```

---

### 3. PEDIDOS (4 tablas)

#### `orders`
Pedidos del catálogo

**Estados del Pedido**:
- `pending_payment`: Creado, esperando pago
- `processing`: Pago confirmado, preparando envío
- `shipped`: Enviado
- `delivered`: Entregado
- `cancelled`: Cancelado
- `refunded`: Reembolsado

**Estados de Pago**:
- `pending`, `completed`, `failed`, `refunded`, `partially_refunded`

**Campos de Precio** (IMPORTANTE):
- `subtotal`: Suma de items
- `discount_amount`: Descuento total (cupones + 3x2)
- `shipping_cost`: Coste de envío (0 si > 50 EUR)
- `total_amount`: `subtotal - discount + shipping`

**Ejemplo de Query**:
```sql
-- Pedidos pendientes de pago
SELECT o.order_id, c.full_name, o.total_amount, o.order_date
FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_status = 'pending_payment'
ORDER BY o.order_date DESC;

-- Revenue del mes
SELECT
  SUM(total_amount) as total_revenue,
  COUNT(*) as total_orders
FROM orders
WHERE order_status IN ('delivered', 'shipped')
  AND MONTH(order_date) = MONTH(CURRENT_DATE);
```

#### `order_items`
Items del pedido con precio snapshot

**CRÍTICO**: `unit_price` es un snapshot del precio en el momento de la compra. NO cambio aunque `products.base_price` cambie.

**Personalizaciones**:
- `has_patches`: +1.99 EUR
- `has_personalization`: +2.99 EUR
  - `personalization_name`: VARCHAR(50)
  - `personalization_number`: VARCHAR(5)

**Promociones**:
- `is_free_item`: TRUE para 3x2
- `promotion_id` (FK): Referencia a la promoción aplicada

**Cálculo de Subtotal**:
```sql
subtotal = (unit_price + patches_price + personalization_price) * quantity
```

**Ejemplo de Query**:
```sql
-- Detalle completo del pedido
SELECT
  oi.quantity,
  p.name as product_name,
  pv.size,
  oi.unit_price,
  oi.has_patches,
  oi.has_personalization,
  oi.personalization_name,
  oi.personalization_number,
  oi.subtotal
FROM order_items oi
JOIN products p ON oi.product_id = p.product_id
JOIN product_variants pv ON oi.variant_id = pv.variant_id
WHERE oi.order_id = 456;
```

---

### 4. SUSCRIPCIONES (4 tablas)

#### `subscription_plans`
Planes disponibles

**Datos Migrados**:
1. **Plan FAN** (24.99 EUR/mes)
   - 1 camiseta FAN mensual
   - Equipos aleatorios

2. **Plan Premium Random** (29.99 EUR/mes)
   - 1 camiseta PLAYER mensual
   - Cualquier club top
   - 10% descuento en tienda
   - Acceso early a drops

3. **Plan Premium TOP** (34.99 EUR/mes)
   - 1 camiseta PLAYER de clubs TOP
   - Solo Madrid, Barça, PSG, City, Bayern, etc.
   - Envío express
   - Certificado de autenticidad
   - Pin de coleccionista

4. **Plan Retro TOP** (39.99 EUR/mes)
   - 1 camiseta RETRO legendaria
   - Selecciones y clubs míticos

**Ejemplo de Query**:
```sql
SELECT * FROM subscription_plans
WHERE is_active = TRUE
ORDER BY display_order;
```

#### `subscriptions`
Suscripciones activas de clientes

**Gestión Manual de Renovación**:
- `current_period_end`: Fecha de fin del periodo actual
- `next_billing_date`: Cuándo debe renovarse (manual)
- Trigger `check_subscription_expiration` marca como expirado si `current_period_end` < hoy

**Preferencias**:
- `preferred_size`: Talla preferida
- `league_preferences` (JSON): Ligas preferidas
- `team_preferences` (JSON): Equipos preferidos
- `teams_to_exclude` (JSON): Equipos que NO quiere recibir

**Ejemplo de Query**:
```sql
-- Suscripciones próximas a expirar (próximos 7 días)
SELECT
  s.subscription_id,
  c.full_name,
  c.telegram_username,
  sp.plan_name,
  s.current_period_end
FROM subscriptions s
JOIN customers c ON s.customer_id = c.customer_id
JOIN subscription_plans sp ON s.plan_id = sp.plan_id
WHERE s.status = 'active'
  AND s.current_period_end BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY);
```

#### `subscription_shipments`
Historial de envíos mensuales

**Campos**:
- `contents` (JSON): Array de `{product_id, variant_id, quantity}`
- `tracking_number`: Número de seguimiento
- `status`: pending, preparing, shipped, delivered

**Ejemplo de Query**:
```sql
-- Historial de envíos de una suscripción
SELECT
  shipment_date,
  expected_delivery_date,
  tracking_number,
  status,
  contents
FROM subscription_shipments
WHERE subscription_id = 789
ORDER BY shipment_date DESC;
```

#### `subscription_payments`
Pagos manuales de suscripciones

**Tracking Manual**:
- `payment_date`: Cuándo pagó
- `period_start`, `period_end`: Periodo cubierto
- `payment_method`: ENUM('oxapay', 'telegram', 'whatsapp', 'manual')
- `verified_by` (FK → admin_users): Quién verificó el pago

**Ejemplo de Query**:
```sql
-- Pagos pendientes de verificación
SELECT
  sp.payment_id,
  c.full_name,
  sp.amount,
  sp.payment_date,
  sp.payment_method,
  sp.manual_payment_reference
FROM subscription_payments sp
JOIN subscriptions s ON sp.subscription_id = s.subscription_id
JOIN customers c ON s.customer_id = c.customer_id
WHERE sp.status = 'pending';
```

---

### 5. MYSTERY BOXES (3 tablas)

#### `mystery_box_types`
Tipos de cajas disponibles

**Datos Migrados**:
1. **Box Clásica** (124.95 EUR)
   - 5 camisetas FAN
   - Equipos variados

2. **Box por Liga** (174.95 EUR)
   - 5 camisetas PLAYER
   - De una liga específica (cliente elige)
   - `league_restriction` (FK)

3. **Box Premium Elite** (174.95 EUR)
   - 5 camisetas PLAYER
   - Solo equipos TOP (`team_tier_restriction = 'top_only'`)
   - Packaging premium + certificado + express

#### `mystery_box_orders`
Pedidos de mystery boxes

**Campos**:
- `selected_league_id` (FK): Para Box por Liga
- `preferred_size`: Talla deseada (todas las camisetas)
- `special_instructions`: Notas del cliente

#### `mystery_box_contents`
Contenido real de cada caja

**Importante**: Se crea después de preparar la caja

**Campos**:
- `reveal_date`: Cuándo el cliente abrió la caja
- Cada caja tiene 5 registros (5 productos)

**Ejemplo de Query**:
```sql
-- Preparar contenido de caja (admin)
INSERT INTO mystery_box_contents (order_id, product_id, variant_id, quantity)
VALUES
  (ORDER_ID, PRODUCT_1, VARIANT_1, 1),
  (ORDER_ID, PRODUCT_2, VARIANT_2, 1),
  (ORDER_ID, PRODUCT_3, VARIANT_3, 1),
  (ORDER_ID, PRODUCT_4, VARIANT_4, 1),
  (ORDER_ID, PRODUCT_5, VARIANT_5, 1);

-- Ver contenido de una caja
SELECT
  p.name as product_name,
  pv.size,
  mbc.reveal_date
FROM mystery_box_contents mbc
JOIN products p ON mbc.product_id = p.product_id
JOIN product_variants pv ON mbc.variant_id = pv.variant_id
WHERE mbc.order_id = 999;
```

---

### 6. DROPS GAMIFICADOS (3 tablas)

#### `drop_events`
Eventos de drops semanales

**Límites**:
- `total_drops_available`: 100 (ejemplo)
- `remaining_drops`: Contador decreciente
- `max_drops_per_customer`: 1 (límite por cliente)

**Ejemplo de Query**:
```sql
-- Drop activo actual
SELECT * FROM drop_events
WHERE is_active = TRUE
  AND start_date <= NOW()
  AND end_date >= NOW()
LIMIT 1;
```

#### `drop_pool_items`
Pool de items posibles

**Sistema de Raridades** (Weighted Random):
- **Common** (peso 62): Real Sociedad, Aston Villa, Lyon, Benfica
- **Rare** (peso 30): Atlético, Arsenal, Juventus Retro, Inter
- **Legendary** (peso 8): Madrid, Barça, Argentina '86, Brasil '02

**Algoritmo de Selección**:
```sql
-- Simplified weighted random (en app)
SELECT pool_item_id, product_id, rarity
FROM drop_pool_items
WHERE drop_event_id = EVENT_ID
ORDER BY RAND() * weight DESC
LIMIT 1;
```

#### `drop_results`
Resultados por cliente

**Campos**:
- `customer_id` (FK, nullable): NULL si guest
- `session_id`: Para tracking de guests
- `pool_item_id` (FK): Qué ganó
- `selected_size`: Talla elegida
- `was_purchased`: TRUE si compró el drop
- `catalog_order_id` (FK): Si compró, referencia al pedido
- `purchase_deadline`: 24-48h para comprar

**Ejemplo de Query**:
```sql
-- Resultados de un cliente
SELECT
  dr.result_date,
  p.name as product_won,
  dpi.rarity,
  dr.was_purchased,
  dr.selected_size
FROM drop_results dr
JOIN drop_pool_items dpi ON dr.pool_item_id = dpi.pool_item_id
JOIN products p ON dpi.product_id = p.product_id
WHERE dr.customer_id = 123
ORDER BY dr.result_date DESC;

-- Estadísticas del drop
SELECT
  dpi.rarity,
  COUNT(*) as times_won
FROM drop_results dr
JOIN drop_pool_items dpi ON dr.pool_item_id = dpi.pool_item_id
WHERE dr.drop_event_id = 1
GROUP BY dpi.rarity;
```

---

### 7. PROMOCIONES (4 tablas)

#### `coupons`
Cupones de descuento

**Tipos**:
- `fixed`: Descuento fijo en EUR (ej: 5 EUR)
- `percentage`: Porcentaje con límite opcional (ej: 10% máx 5 EUR)

**Restricciones**:
- `min_purchase_amount`: Compra mínima
- `applies_to_product_type`: 'all', 'jersey', 'mystery_box', 'subscription'
- `applies_to_first_order_only`: Solo primer pedido

**Límites de Uso**:
- `usage_limit_total`: NULL = ilimitado
- `usage_limit_per_customer`: Veces que puede usar cada cliente
- `times_used`: Contador actual

**Cupones Migrados**:
```
WELCOME5: 5 EUR, min 60 EUR, solo primer pedido
NOTBETTING10: 10%, máx 5 EUR
TOPBONUS10: 10%, máx 5 EUR
KICKVERSE10: 10%, máx 5 EUR
MYSTERY10: 10%, máx 15 EUR, solo mystery boxes
CATALOGO5: 5 EUR, min 50 EUR, solo jerseys
```

**Ejemplo de Query**:
```sql
-- Validar cupón
SELECT
  code,
  discount_type,
  discount_value,
  max_discount_amount,
  min_purchase_amount,
  usage_limit_per_customer,
  (SELECT COUNT(*) FROM coupon_usage WHERE coupon_id = c.coupon_id AND customer_id = CUSTOMER_ID) as times_used_by_customer
FROM coupons c
WHERE code = 'WELCOME5'
  AND is_active = TRUE
  AND (valid_from IS NULL OR valid_from <= NOW())
  AND (valid_until IS NULL OR valid_until >= NOW());
```

#### `coupon_usage`
Tracking de uso por cliente

**Ejemplo**:
```sql
INSERT INTO coupon_usage (coupon_id, customer_id, order_id, discount_applied)
VALUES (COUPON_ID, CUSTOMER_ID, ORDER_ID, 5.00);
```

#### `promotion_3x2_usage`
Tracking de promociones 3x2

**Campos**:
- `paid_item_1_id`, `paid_item_2_id` (FK → order_items): Items pagados
- `free_item_id` (FK → order_items): Item gratis
- `discount_amount`: Precio del item gratis

---

### 8. FIDELIZACIÓN (3 tablas)

#### `loyalty_points_history`
Transacciones de puntos

**Tipos de Transacción**:
- `order_purchase`: Puntos ganados por compra
- `order_refund`: Puntos devueltos por reembolso
- `points_redemption`: Puntos canjeados
- `birthday_bonus`: Bonus de cumpleaños
- `referral`: Referir amigo
- `manual_adjustment`: Ajuste manual por admin

**Cálculo de Puntos**:
```sql
base_points = FLOOR(order.total_amount)  -- 1 punto por EUR
multiplier = loyalty_tier_benefits.points_multiplier  -- 1.0x - 2.0x
points_earned = base_points * multiplier
```

**Trigger**: `award_loyalty_points_on_order` ejecuta automáticamente al marcar pedido como `delivered`

**Ejemplo de Query**:
```sql
-- Historial de un cliente
SELECT
  transaction_type,
  points_change,
  points_balance_after,
  description,
  created_at
FROM loyalty_points_history
WHERE customer_id = 123
ORDER BY created_at DESC;
```

#### `loyalty_tier_benefits`
Beneficios por tier

**Tiers y Requisitos**:
```
Standard: 0 pedidos, 0 EUR
Silver:   3 pedidos, 100 EUR
Gold:     10 pedidos, 300 EUR
Platinum: 25 pedidos, 750 EUR
```

**Beneficios**:
- `points_multiplier`: 1.0x → 1.25x → 1.5x → 2.0x
- `discount_percentage`: 0% → 5% → 10% → 15%
- `free_shipping`: Solo Gold y Platinum
- `early_drop_access`: Solo Gold y Platinum
- `birthday_bonus_points`: 0 → 50 → 100 → 200

#### `loyalty_rewards`
Catálogo de recompensas

**Ejemplos**:
```
500 puntos: Cupón 5 EUR
900 puntos: Cupón 10 EUR
300 puntos: Envío gratis
1200 puntos: 15% descuento
```

**Ejemplo de Query**:
```sql
-- Recompensas disponibles para un cliente
SELECT
  r.reward_name,
  r.points_required,
  r.description,
  c.loyalty_points,
  (r.points_required <= c.loyalty_points) as can_redeem
FROM loyalty_rewards r
CROSS JOIN customers c
WHERE c.customer_id = 123 AND r.is_active = TRUE
ORDER BY r.points_required;
```

---

### 9. WISHLIST (2 tablas)

#### `wishlists`
Lista de deseos por cliente

**Campos**:
- `product_id` (FK)
- `variant_id` (FK, nullable): Talla específica opcional
- `notify_on_stock`: Notificar cuando vuelva stock
- `notify_on_price_drop`: Notificar cuando baje precio
- `price_when_added`: Para comparar bajadas

**Ejemplo de Query**:
```sql
-- Wishlist de un cliente
SELECT
  p.name as product_name,
  pv.size,
  w.price_when_added,
  p.base_price as current_price,
  (w.price_when_added - p.base_price) as price_difference,
  pv.stock_quantity
FROM wishlists w
JOIN products p ON w.product_id = p.product_id
LEFT JOIN product_variants pv ON w.variant_id = pv.variant_id
WHERE w.customer_id = 123;
```

#### `wishlist_notifications_sent`
Tracking de notificaciones enviadas

**Tipos**:
- `back_in_stock`: Producto volvió a stock
- `price_drop`: Precio bajó
- `low_stock_alert`: Últimas unidades

---

### 10. PAGOS (2 tablas)

#### `payment_transactions`
Transacciones de pago (Oxapay + Manual)

**Métodos de Pago**:
- `oxapay_btc`, `oxapay_eth`, `oxapay_usdt`: Crypto vía Oxapay
- `telegram_manual`: Confirmación manual por Telegram
- `whatsapp_manual`: Confirmación manual por WhatsApp
- `bank_transfer`: Transferencia bancaria

**Campos Oxapay**:
- `oxapay_transaction_id`: ID de transacción Oxapay
- `oxapay_payment_url`: URL de pago generada
- `oxapay_crypto_amount`: Cantidad en crypto
- `oxapay_wallet_address`: Dirección de wallet
- `oxapay_response` (JSON): Respuesta completa de API

**Campos Manual**:
- `manual_payment_reference`: Referencia del pago
- `manual_payment_proof`: Ruta a captura/comprobante
- `verified_by` (FK → admin_users): Admin que verificó
- `verified_at`: Timestamp de verificación

**Estados**:
- `pending`: Iniciado, esperando
- `processing`: En proceso (Oxapay)
- `completed`: Completado
- `failed`: Fallido
- `expired`: Expirado
- `refunded`: Reembolsado

**Ejemplo de Query**:
```sql
-- Pagos pendientes de verificación manual
SELECT
  pt.transaction_id,
  c.full_name,
  pt.amount,
  pt.payment_method,
  pt.manual_payment_reference,
  pt.manual_payment_proof,
  pt.initiated_at
FROM payment_transactions pt
JOIN customers c ON pt.customer_id = c.customer_id
WHERE pt.payment_method IN ('telegram_manual', 'whatsapp_manual', 'bank_transfer')
  AND pt.status = 'pending'
ORDER BY pt.initiated_at;
```

#### `payment_webhooks`
Log de webhooks de Oxapay

**Campos**:
- `webhook_type`: Tipo de webhook
- `payload` (JSON): Datos completos del webhook
- `signature`: Firma para validación
- `processed`: TRUE cuando se procesó
- `processing_error`: Mensaje si falló

**Ejemplo de Query**:
```sql
-- Webhooks pendientes de procesar
SELECT * FROM payment_webhooks
WHERE processed = FALSE
ORDER BY received_at;
```

---

### 11. CARRITO (2 tablas)

#### `carts`
Carritos de compra

**Tipos**:
- **Logged-in**: `customer_id` != NULL, `session_id` = NULL
- **Guest**: `customer_id` = NULL, `session_id` != NULL

**Estados**:
- `active`: En uso
- `abandoned`: Abandonado (no comprado en X tiempo)
- `converted`: Convertido a pedido
- `expired`: Expirado

**Conversión**:
- `converted_to_order_id` (FK → orders): Referencia al pedido creado

**Ejemplo de Query**:
```sql
-- Carrito del cliente
SELECT * FROM carts
WHERE customer_id = 123 AND cart_status = 'active';

-- Carritos abandonados (últimas 24h)
SELECT c.cart_id, cu.email, c.updated_at
FROM carts c
LEFT JOIN customers cu ON c.customer_id = cu.customer_id
WHERE c.cart_status = 'active'
  AND c.updated_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

#### `cart_items`
Items del carrito

**Snapshot de Precio**:
- `unit_price`: Precio actual (puede cambiar)
- En `order_items` el precio es snapshot final

**Ejemplo de Query**:
```sql
-- Carrito completo con totales
SELECT
  ci.quantity,
  p.name as product_name,
  pv.size,
  ci.unit_price,
  ci.has_patches,
  ci.has_personalization,
  (ci.unit_price +
   IF(ci.has_patches, 1.99, 0) +
   IF(ci.has_personalization, 2.99, 0)) * ci.quantity as item_total
FROM cart_items ci
JOIN products p ON ci.product_id = p.product_id
JOIN product_variants pv ON ci.variant_id = pv.variant_id
WHERE ci.cart_id = (
  SELECT cart_id FROM carts WHERE customer_id = 123 AND cart_status = 'active'
);
```

---

### 12. INVENTARIO (2 tablas)

#### `stock_movements`
Movimientos de inventario

**Tipos de Movimiento**:
- `purchase`: Compra de stock (entrada)
- `sale`: Venta (salida)
- `return`: Devolución (entrada)
- `adjustment`: Ajuste manual
- `reserved`: Reservado para pedido
- `unreserved`: Liberado de reserva
- `damaged`: Dañado (salida)
- `lost`: Perdido (salida)

**Trigger**: `create_stock_movement_on_order` crea automáticamente al crear `order_items`

**Ejemplo de Query**:
```sql
-- Historial de movimientos de una variante
SELECT
  movement_type,
  quantity,
  stock_after,
  notes,
  created_at
FROM stock_movements
WHERE product_variant_id = 456
ORDER BY created_at DESC;
```

#### `low_stock_alerts`
Alertas de stock bajo

**Trigger**: `check_low_stock_after_movement` crea/actualiza alertas automáticamente

**Estados**:
- `pending`: Pendiente de acción
- `notified`: Notificado a admin
- `resolved`: Resuelto (reabastecido)
- `dismissed`: Descartado

**Ejemplo de Query**:
```sql
-- Alertas pendientes
SELECT
  p.name as product_name,
  pv.size,
  lsa.current_quantity,
  lsa.threshold_quantity,
  lsa.created_at
FROM low_stock_alerts lsa
JOIN product_variants pv ON lsa.product_variant_id = pv.variant_id
JOIN products p ON pv.product_id = p.product_id
WHERE lsa.alert_status = 'pending'
ORDER BY lsa.current_quantity;
```

---

### 13. COMUNICACIÓN (2 tablas)

#### `customer_messages`
Mensajes de/hacia clientes

**Canales**:
- `telegram`, `whatsapp`, `email`, `instagram`, `internal`

**Direcciones**:
- `inbound`: De cliente a empresa
- `outbound`: De empresa a cliente

**Estados**:
- `sent`, `delivered`, `read`, `failed`

**Ejemplo de Query**:
```sql
-- Conversación con un cliente
SELECT
  direction,
  message_content,
  sent_at,
  read_at,
  status
FROM customer_messages
WHERE customer_id = 123
ORDER BY sent_at DESC;

-- Mensajes no resueltos
SELECT * FROM customer_messages
WHERE direction = 'inbound'
  AND is_resolved = FALSE
ORDER BY sent_at;
```

#### `notifications`
Notificaciones del sistema a clientes

**Tipos**:
- `order_shipped`, `order_delivered`
- `subscription_renewal`, `subscription_expiring`
- `drop_available`
- `stock_alert`, `price_drop`
- `payment_reminder`

**Canales de Envío**:
- `telegram`, `whatsapp`, `email`, `web_push`

**Ejemplo de Query**:
```sql
-- Notificaciones no leídas
SELECT
  notification_type,
  title,
  message,
  sent_at
FROM notifications
WHERE customer_id = 123
  AND read_at IS NULL
ORDER BY sent_at DESC;
```

---

### 14. ANALYTICS (2 tablas)

#### `analytics_events`
Eventos de usuario (GTM compatible)

**Tipos de Evento**:
- `page_view`, `cta_click`, `product_view`
- `add_to_cart`, `remove_from_cart`
- `checkout_start`, `purchase`
- `exit_intent`, `scroll_depth`, `time_on_page`
- `drop_play`, `form_submit`

**Datos del Dispositivo**:
- `device_type`: desktop, mobile, tablet
- `browser`, `os`, `screen_resolution`
- `ip_address`, `country`, `city`

**Ejemplo de Query**:
```sql
-- Conversión funnel
SELECT
  event_type,
  COUNT(*) as events,
  COUNT(DISTINCT session_id) as unique_sessions
FROM analytics_events
WHERE DATE(created_at) = CURDATE()
  AND event_type IN ('product_view', 'add_to_cart', 'checkout_start', 'purchase')
GROUP BY event_type;
```

#### `product_views`
Vistas detalladas de productos

**Métricas**:
- `time_spent_seconds`: Tiempo en página
- `scrolled_to_description`: Scrolleó hasta descripción
- `clicked_add_to_cart`: Clickeó añadir al carrito

**Ejemplo de Query**:
```sql
-- Productos más vistos
SELECT
  p.name,
  COUNT(*) as total_views,
  AVG(pv.time_spent_seconds) as avg_time_spent,
  SUM(pv.clicked_add_to_cart) as add_to_cart_clicks
FROM product_views pv
JOIN products p ON pv.product_id = p.product_id
WHERE pv.viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY p.product_id
ORDER BY total_views DESC
LIMIT 10;
```

---

### 15. ADMINISTRACIÓN (3 tablas)

#### `admin_users`
Usuarios administradores

**Roles**:
- `super_admin`: Acceso total
- `admin`: Acceso completo excepto config crítica
- `inventory_manager`: Gestión de stock
- `customer_service`: Atención al cliente
- `marketing`: Promociones, cupones, analytics
- `readonly`: Solo lectura

**Seguridad**:
- `password_hash`: bcrypt con cost factor 10
- `failed_login_attempts`: Contador de intentos fallidos
- `locked_until`: Bloqueo temporal tras X intentos
- `two_factor_secret`: Para 2FA (futuro)

**Ejemplo de Query**:
```sql
-- Login admin
SELECT * FROM admin_users
WHERE username = 'admin' AND is_active = TRUE;

-- Verificar intentos fallidos
UPDATE admin_users
SET failed_login_attempts = failed_login_attempts + 1
WHERE username = 'admin';
```

#### `audit_log`
Log de auditoría de cambios

**Acciones Auditadas**:
- `create`, `update`, `delete`: Operaciones CRUD
- `login`, `logout`, `password_change`: Acciones de auth
- `status_change`: Cambios de estado críticos
- `payment_verify`: Verificaciones de pago

**Campos JSON**:
- `old_values`: Estado anterior
- `new_values`: Estado nuevo

**Ejemplo de Query**:
```sql
-- Cambios recientes en un pedido
SELECT
  au.username as admin_name,
  al.action_type,
  al.old_values,
  al.new_values,
  al.created_at
FROM audit_log al
LEFT JOIN admin_users au ON al.admin_id = au.admin_id
WHERE al.entity_type = 'order' AND al.entity_id = 456
ORDER BY al.created_at DESC;
```

#### `system_settings`
Configuración del sistema

**Settings Migrados**:
```
site_name: Kickverse
telegram_contact: @esKickverse
whatsapp_contact: +34 614 299 735
free_shipping_threshold: 50.00 EUR
base_jersey_price: 24.99 EUR
patches_price: 1.99 EUR
personalization_price: 2.99 EUR
gtm_id: GTM-MQFTT34L
```

**Ejemplo de Query**:
```sql
-- Obtener configuración pública
SELECT setting_key, setting_value
FROM system_settings
WHERE is_public = TRUE;

-- Actualizar setting
UPDATE system_settings
SET setting_value = '29.99', updated_by = ADMIN_ID
WHERE setting_key = 'base_jersey_price';
```

---

## Queries Comunes

### Dashboard de Admin

```sql
-- KPIs del día
SELECT
  (SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()) as orders_today,
  (SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = CURDATE() AND order_status IN ('delivered', 'shipped')) as revenue_today,
  (SELECT COUNT(*) FROM customers WHERE DATE(registration_date) = CURDATE()) as new_customers_today,
  (SELECT COUNT(*) FROM subscriptions WHERE status = 'active') as active_subscriptions,
  (SELECT COUNT(*) FROM low_stock_alerts WHERE alert_status = 'pending') as low_stock_alerts;
```

### Productos Bestsellers

```sql
SELECT
  p.name,
  COUNT(oi.order_item_id) as times_sold,
  SUM(oi.quantity) as units_sold,
  SUM(oi.subtotal) as total_revenue
FROM order_items oi
JOIN products p ON oi.product_id = p.product_id
JOIN orders o ON oi.order_id = o.order_id
WHERE o.order_status IN ('delivered', 'shipped')
  AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY p.product_id
ORDER BY times_sold DESC
LIMIT 20;
```

### Clientes VIP

```sql
SELECT
  c.customer_id,
  c.full_name,
  c.email,
  c.loyalty_tier,
  c.loyalty_points,
  c.total_orders_count,
  c.total_spent,
  COUNT(DISTINCT s.subscription_id) as active_subscriptions
FROM customers c
LEFT JOIN subscriptions s ON c.customer_id = s.customer_id AND s.status = 'active'
WHERE c.customer_status = 'active'
GROUP BY c.customer_id
ORDER BY c.total_spent DESC
LIMIT 50;
```

---

## Procedimientos y Triggers

### Triggers Implementados

#### 1. `update_customer_stats_after_order`
**Acción**: AFTER INSERT ON orders
**Función**: Actualiza `customers.total_orders_count` y `customers.total_spent`

#### 2. `create_stock_movement_on_order`
**Acción**: AFTER INSERT ON order_items
**Función**: Crea movimiento de stock y actualiza `product_variants.stock_quantity`

#### 3. `check_low_stock_after_movement`
**Acción**: AFTER INSERT ON stock_movements
**Función**: Crea/actualiza alertas de stock bajo

#### 4. `award_loyalty_points_on_order`
**Acción**: AFTER UPDATE ON orders
**Función**: Otorga puntos cuando order_status cambia a 'delivered'

#### 5. `check_subscription_expiration`
**Acción**: BEFORE UPDATE ON subscriptions
**Función**: Marca como expirado si `current_period_end` < hoy

---

## Seguridad y Permisos

### Datos Sensibles

**NUNCA en logs o respuestas de API**:
- `customers.password_hash`
- `admin_users.password_hash`
- `payment_transactions.oxapay_response` (puede contener claves)

### Encriptación

- **Contraseñas**: bcrypt con cost factor 10
- **Tokens**: Random 100 chars
- **PII**: Considerar encriptar `customers.email`, `phone` si regulación lo requiere

### Permisos de Base de Datos

Usuario: `iqvfmscx_kickverse`
Permisos: SELECT, INSERT, UPDATE, DELETE en `iqvfmscx_kickverse.*`

---

## Mantenimiento

### Tareas Diarias

```sql
-- Limpiar carritos expirados
DELETE FROM carts
WHERE cart_status = 'active'
  AND expires_at < NOW();

-- Procesar webhooks pendientes
SELECT * FROM payment_webhooks
WHERE processed = FALSE;

-- Verificar subscripciones próximas a expirar
SELECT * FROM subscriptions
WHERE status = 'active'
  AND current_period_end BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY);
```

### Tareas Semanales

```sql
-- Actualizar drops activos
UPDATE drop_events SET is_active = FALSE
WHERE end_date < NOW();

-- Limpiar sesiones expiradas
DELETE FROM customer_sessions
WHERE expires_at < NOW();
```

### Tareas Mensuales

```sql
-- Archivar analytics antiguos (opcional, particionar)
-- Optimizar tablas grandes
OPTIMIZE TABLE analytics_events;
OPTIMIZE TABLE loyalty_points_history;
OPTIMIZE TABLE audit_log;
```

---

## APIs y Endpoints

### Endpoints Recomendados (a implementar en PHP)

**Productos**:
- `GET /api/products` - Listado con filtros
- `GET /api/products/:id` - Detalle
- `GET /api/products/:id/variants` - Variantes disponibles

**Carrito**:
- `POST /api/cart/add` - Añadir item
- `DELETE /api/cart/items/:id` - Eliminar item
- `GET /api/cart` - Ver carrito actual

**Checkout**:
- `POST /api/checkout` - Crear pedido
- `POST /api/payment/oxapay` - Iniciar pago Oxapay
- `POST /api/webhooks/oxapay` - Webhook de Oxapay

**Cuenta**:
- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Registro
- `GET /api/account/orders` - Historial de pedidos
- `GET /api/account/subscriptions` - Suscripciones

**Drops**:
- `POST /api/drops/play` - Jugar drop
- `GET /api/drops/results` - Resultados del cliente

---

## Troubleshooting

### Problema: Stock negativo

**Diagnóstico**:
```sql
SELECT * FROM product_variants WHERE stock_quantity < 0;
```

**Solución**:
```sql
UPDATE product_variants SET stock_quantity = 0 WHERE stock_quantity < 0;
-- Investigar en stock_movements por qué ocurrió
```

### Problema: Pedido sin items

**Diagnóstico**:
```sql
SELECT o.* FROM orders o
LEFT JOIN order_items oi ON o.order_id = oi.order_id
WHERE oi.order_item_id IS NULL;
```

**Solución**: Cancelar pedidos huérfanos o añadir constraint para prevenir.

### Problema: Puntos de fidelización incorrectos

**Diagnóstico**:
```sql
-- Recalcular puntos
SELECT
  customer_id,
  SUM(points_change) as calculated_points,
  (SELECT loyalty_points FROM customers c WHERE c.customer_id = lph.customer_id) as current_points
FROM loyalty_points_history lph
GROUP BY customer_id
HAVING calculated_points != current_points;
```

---

**Fin de la Documentación Técnica**

Para más información, consultar:
- `schema.sql` - Estructura completa
- `data_migration.sql` - Datos iniciales
- `DATABASE_DIAGRAM.md` - Diagramas visuales
