# KICKVERSE - DIAGRAMA DE RELACIONES DE BASE DE DATOS

## ESTRUCTURA VISUAL

### NÚCLEO DE PRODUCTOS
```
leagues (6)
    ├─→ teams (69)
    │   ├─→ products (200+) [FK: team_id, league_id]
    │   │   ├─→ product_images (200+) [FK: product_id]
    │   │   ├─→ product_variants (1400+) [FK: product_id]
    │   │   │   ├─→ stock_movements [FK: product_variant_id]
    │   │   │   └─→ low_stock_alerts [FK: product_variant_id]
    │   │   ├─→ product_price_history [FK: product_id]
    │   │   └─→ order_items [FK: product_id, variant_id]
    │   │       └─→ orders [FK: order_id]
    │
    ├─→ translations [FK: entity_id (para leagues)]
    └─→ size_guides
```

### CLIENTES Y AUTENTICACIÓN
```
customers (base)
    ├─→ customer_preferences [FK: customer_id, UNIQUE]
    ├─→ shipping_addresses [FK: customer_id]
    ├─→ customer_sessions [FK: customer_id]
    ├─→ orders [FK: customer_id]
    │   ├─→ order_items [FK: order_id, product_id, variant_id]
    │   ├─→ payment_transactions [FK: order_id]
    │   └─→ coupon_usage [FK: order_id, coupon_id]
    ├─→ subscriptions [FK: customer_id]
    │   ├─→ subscription_shipments [FK: subscription_id]
    │   │   └─→ mystery_box_contents [FK: (no direct, via shipment)]
    │   └─→ subscription_payments [FK: subscription_id]
    ├─→ carts [FK: customer_id]
    │   └─→ cart_items [FK: cart_id, product_id, variant_id]
    ├─→ wishlists [FK: customer_id, product_id, variant_id]
    │   └─→ wishlist_notifications_sent [FK: wishlist_id]
    ├─→ loyalty_points_history [FK: customer_id]
    ├─→ customer_messages [FK: customer_id]
    ├─→ notifications [FK: customer_id]
    ├─→ analytics_events [FK: customer_id]
    └─→ drop_results [FK: customer_id, drop_event_id, pool_item_id]
```

### SUSCRIPCIONES
```
subscription_plans (4)
    ├─→ subscriptions [FK: plan_id, customer_id]
    │   ├─→ subscription_shipments [FK: subscription_id]
    │   │   └─→ mystery_box_contents [FK: (indirect)]
    │   ├─→ subscription_payments [FK: subscription_id]
    │   └─→ payment_transactions [FK: subscription_id]
    │
    └─→ translations [FK: entity_id (para planes)]
```

### MYSTERY BOXES
```
mystery_box_types (3)
    ├─→ mystery_box_orders [FK: box_type_id, customer_id, shipping_address_id]
    │   ├─→ mystery_box_contents [FK: order_id, product_id, variant_id]
    │   └─→ payment_transactions [FK: (via order)]
    │
    └─→ leagues [FK: league_restriction (nullable)]
```

### DROP EVENTS (Gamificación)
```
drop_events (1)
    ├─→ drop_pool_items [FK: drop_event_id, product_id]
    │   └─→ drop_results [FK: pool_item_id]
    │       ├─→ customers [FK: customer_id, nullable]
    │       └─→ orders [FK: catalog_order_id, nullable]
    │
    └─→ drop_results [FK: drop_event_id]
```

### PAGOS
```
payment_transactions (base)
    ├─→ customers [FK: customer_id]
    ├─→ orders [FK: order_id, nullable]
    ├─→ subscriptions [FK: subscription_id, nullable]
    └─→ payment_webhooks [FK: transaction_id]
```

### PROMOCIONES Y DESCUENTOS
```
coupons (6)
    ├─→ coupon_usage [FK: coupon_id, customer_id, order_id]
    └─→ promotional_campaigns [FK: auto_apply_coupon_id]
        ├─→ orders [FK: (via coupon_id)]
        └─→ promotion_3x2_usage [FK: order_id, customer_id]
```

### LEALTAD
```
customers [loyalty_tier, loyalty_points]
    ├─→ loyalty_points_history [FK: customer_id, reference_order_id]
    │
    ├─→ loyalty_tier_benefits [tier: ENUM, 4 registros]
    │
    └─→ loyalty_rewards [FK: (redemptions no se trackean en DB)]
```

### ADMIN Y AUDITORÍA
```
admin_users (1+)
    ├─→ audit_log [FK: admin_id, customer_id]
    ├─→ system_settings [FK: updated_by]
    ├─→ product_price_history [FK: changed_by]
    └─→ loyalty_points_history [FK: created_by]
```

---

## FLUJOS DE DATOS PRINCIPALES

### Flujo 1: COMPRA DE CATÁLOGO
```
1. customers
2. → carts (crear/obtener)
3. → cart_items (agregar artículos)
4. → shipping_addresses (seleccionar)
5. → coupons (aplicar descuento)
6. → orders (crear)
7. → order_items (agregar items)
8. → coupon_usage (registrar uso)
9. → payment_transactions (crear)
10. → payment_webhooks (si Oxapay)
11. ← actualizar orders.status
12. → stock_movements (reservar stock)
13. → product_variants (decrementar)
14. → low_stock_alerts (si necesario)
15. ← actualizar customers (total_spent, total_orders_count)
16. → loyalty_points_history (registrar puntos)
17. ← actualizar customers (loyalty_points, loyalty_tier)
```

### Flujo 2: SUSCRIPCIÓN MENSUAL
```
1. customers
2. → subscriptions (crear)
3. → subscription_payments (pago inicial)
4. → payment_transactions
5. ← actualizar subscriptions.status → active
6. [Mensualmente]
7. → subscription_shipments (generar)
8. → mystery_box_contents (qué incluir)
9. → product_variants (decrementar stock)
10. → stock_movements (registrar)
11. → subscription_payments (renewal)
12. → loyalty_points_history (puntos mensuales)
13. [Hasta cancelación o expiración]
14. ← actualizar subscriptions.status
```

### Flujo 3: MYSTERY BOX
```
1. customers
2. → mystery_box_types (seleccionar)
3. → carts → cart_items
4. → mystery_box_orders (crear)
5. → payment_transactions
6. ← actualizar mystery_box_orders.status
7. → mystery_box_contents (al abrir)
8. ← registrar reveal_date
```

### Flujo 4: DROP EVENT
```
1. drop_events (activo)
2. ← customers (participan)
3. → drop_pool_items (weighted random)
4. → drop_results (ganador)
5. [24-48 horas]
6. Si compra:
   ← → orders (crear)
   ← → order_items
   ← → payment_transactions
   ← actualizar drop_results.purchased_at
```

---

## ÍNDICES CLAVE

### Por frecuencia de acceso

**MUY FRECUENTES:**
```
products.idx_active_featured (is_active, is_featured)
orders.idx_customer (customer_id)
orders.idx_order_status (order_status)
customers.idx_email (email)
product_variants.idx_product_size (product_id, size)
cart_items.idx_cart (cart_id)
```

**FRECUENTES:**
```
product_images.idx_product (product_id)
shipping_addresses.idx_customer (customer_id)
order_items.idx_order (order_id)
order_items.idx_product (product_id)
subscriptions.idx_customer (customer_id)
subscriptions.idx_status (status)
```

**MODERADO:**
```
products.idx_search (FULLTEXT name, description)
leagues.idx_slug (slug)
teams.idx_league (league_id)
drop_results.idx_event_customer (drop_event_id, customer_id)
analytics_events.idx_session (session_id)
```

---

## CARDINALIDADES

### ONE-TO-MANY (1:N)
```
leagues (1) → (N) teams
leagues (1) → (N) products
teams (1) → (N) products
customers (1) → (N) orders
customers (1) → (N) carts
customers (1) → (N) wishlists
customers (1) → (N) subscriptions
customers (1) → (N) shipping_addresses
customers (1) → (N) customer_preferences (UNIQUE)
customers (1) → (N) loyalty_points_history
customers (1) → (N) analytics_events
customers (1) → (N) customer_messages
customers (1) → (N) notifications
customers (1) → (N) drop_results

products (1) → (N) product_images
products (1) → (N) product_variants
products (1) → (N) order_items
products (1) → (N) product_price_history
products (1) → (N) wishlists

orders (1) → (N) order_items
orders (1) → (N) coupon_usage
orders (1) → (N) drop_results (como purchase)

carts (1) → (N) cart_items
subscriptions (1) → (N) subscription_shipments
subscriptions (1) → (N) subscription_payments

mystery_box_types (1) → (N) mystery_box_orders
drop_events (1) → (N) drop_pool_items
drop_events (1) → (N) drop_results
drop_pool_items (1) → (N) drop_results
```

### MANY-TO-ONE (N:1)
```
Todas las FK mencionadas arriba son N:1 desde la tabla secundaria
```

### MANY-TO-MANY (M:N)
```
Implementadas como tablas pivote:
customers ←(coupons)→ orders → coupon_usage
customers ←(products)→ wishlists
drop_results: customers ←(drops)→ products
```

---

## CONSTRAINTS Y VALIDACIONES

### UNIQUE CONSTRAINTS
```
customers.email
customers.telegram_username
customers.whatsapp_number
products.slug
teams.slug
leagues.slug
product_variants.sku
coupons.code
subscription_plans.slug
mystery_box_types.slug
customer_preferences (customer_id)
wishlists (customer_id, product_id, variant_id)
size_guides (category, size, language)
translations (entity_type, entity_id, translation_key, language)
loyalty_tier_benefits (tier)
```

### FOREIGN KEY CONSTRAINTS
```
ON DELETE RESTRICT (no permite eliminar si hay referencias):
- leagues: teams depende
- teams: products depende
- subscription_plans: subscriptions depende
- mystery_box_types: mystery_box_orders depende
- drop_events: drop_results depende
- products: order_items depende (restricción parcial)
- customers: orders depende
- coupons: coupon_usage depende
- coupons: promotional_campaigns depende

ON DELETE CASCADE (elimina referencias automáticamente):
- products: product_images, product_variants, product_price_history
- product_variants: stock_movements, low_stock_alerts
- customers: customer_preferences, shipping_addresses, carts, wishlists,
            loyalty_points_history, customer_messages, notifications,
            customer_sessions
- orders: order_items, coupon_usage
- carts: cart_items
- subscriptions: subscription_shipments, subscription_payments
- mystery_box_orders: mystery_box_contents
- drop_events: drop_pool_items, drop_results
- wishlists: wishlist_notifications_sent
- payment_transactions: payment_webhooks

ON DELETE SET NULL (permite nulo):
- products: league_id, team_id
- orders: coupon_id
- mystery_box_orders: selected_league_id
- mystery_box_orders: payment_id
- drop_results: customer_id
- drop_results: catalog_order_id
- shipping_addresses: customer_id (convertida a RESTRICT en algunos casos)
```

---

## TABLAS DE AUDITORÍA

### Tracks automáticos
```
customer_messages: todas las comunicaciones
- channel (telegram, whatsapp, email, instagram, internal)
- direction (inbound/outbound)
- status (sent, delivered, read, failed)

notifications: eventos del sistema
- notification_type (order_shipped, stock_alert, etc.)
- sent_via (telegram, whatsapp, email, web_push)

audit_log: acciones admin
- action_type (create, update, delete, login, etc.)
- old_values/new_values (JSON)

stock_movements: cambios de inventario
- movement_type (purchase, sale, return, adjustment, etc.)
- stock_after (snapshots de estado)

loyalty_points_history: transacciones de puntos
- transaction_type (order_purchase, refund, etc.)
- points_balance_after (snapshots de estado)

product_price_history: cambios de precio
- old_price/new_price

payment_webhooks: logs de eventos Oxapay
- payload (JSON raw)
- processed (boolean)

analytics_events: comportamiento usuario
- event_type (page_view, purchase, etc.)
```

---

## DATOS INICIALES DISTRIBUIDOS

```
Ligas: 6
├─ La Liga: 20 equipos
├─ Premier League: 11 equipos
├─ Serie A: 10 equipos
├─ Bundesliga: 18 equipos
├─ Ligue 1: 4 equipos
└─ Selecciones: 6 equipos
TOTAL: 69 equipos

Productos: 200+
├─ Jerseys: 194 (home, away, retro)
├─ Accessories: 0 (preparado para expandir)
├─ Mystery Boxes: 0 (tipos en mystery_box_types)
└─ Subscriptions: 0 (planes en subscription_plans)

Variantes por producto: 7 tallas (S-4XL)
TOTAL: 1400+ variantes

Planes suscripción: 4
├─ Plan FAN: 24.99€/mes
├─ Plan Premium Random: 29.99€/mes
├─ Plan Premium TOP: 34.99€/mes
└─ Plan Retro TOP: 39.99€/mes

Tipos Mystery Box: 3
├─ Box Clásica: 124.95€
├─ Box por Liga: 174.95€
└─ Box Premium Elite: 174.95€

Cupones: 6
├─ WELCOME5, NOTBETTING10, TOPBONUS10, KICKVERSE10, MYSTERY10, CATALOGO5

Tiers Lealtad: 4
├─ Standard, Silver, Gold, Platinum

Beneficios por Tier: 4
├─ Multiplicadores, descuentos, envío gratis, acceso drops

Admin: 1 predefinido
├─ username: admin
├─ password: admin123 (CAMBIAR!)
├─ role: super_admin

Drop Events: 1 (noviembre 2024)
├─ 100 drops disponibles
├─ Pool items: 12 (4 comunes, 4 raros, 4 legendarios)

Size Guides: 28
├─ General: 7 (S-4XL)
├─ Player: 5 (S-2XL)
├─ Kids: 7 (16-28)
└─ Tracksuit: 5 (S-2XL)

Settings: 15+
├─ Contactos, precios, idioma, Analytics IDs, etc.
```

---

## CAPACIDAD Y ESCALABILIDAD

### Límites actuales
```
INT UNSIGNED: 0 a 4,294,967,295
├─ customer_id: hasta 4B clientes
├─ product_id: hasta 4B productos
├─ order_id: hasta 4B órdenes

BIGINT UNSIGNED: para analytics
├─ event_id: hasta 18B eventos
├─ view_id: hasta 18B vistas

DECIMAL(10,2): hasta 99,999,999.99€
DECIMAL(20,8): para cripto (hasta 99,999,999.99999999)
```

### Tablas crecimiento esperado
```
RÁPIDO (requerir particionamiento después):
- orders (una por transacción)
- order_items (N por orden)
- analytics_events (una por acción usuario)
- customer_messages (una por mensaje)
- loyalty_points_history (una por transacción)
- stock_movements (una por movimiento)

MODERADO:
- customers (una por usuario)
- subscriptions (renovación/mes)
- payment_transactions (una por pago)

LENTO:
- products (nuevos jerseys ocasionalmente)
- product_variants (con nuevos productos)
```

---

## QUERIES CRÍTICAS PARA CRM

### Rendimiento esperado

**Con índices correctos (< 100ms):**
- Obtener cliente por email
- Obtener órdenes por cliente
- Obtener artículos en carrito
- Stock disponible por talla
- Órdenes pendientes de pago
- Clientes por tier de lealtad
- Suscripciones activas
- Bajos en stock

**100-500ms:**
- Análisis de ingresos por período
- Top 10 productos más vendidos
- Clientes con más órdenes
- Estadísticas de drops

**500ms+:**
- Análisis completo de cohort
- Reportes analíticos agregados
- Predicción de churn
```

