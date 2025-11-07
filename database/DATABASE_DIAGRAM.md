# KICKVERSE - Diagrama de Base de Datos

## Índice
1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Diagrama Visual](#diagrama-visual)
3. [Módulos y Tablas](#módulos-y-tablas)
4. [Relaciones Clave](#relaciones-clave)
5. [Flujos de Datos](#flujos-de-datos)

---

## Resumen Ejecutivo

La base de datos de Kickverse está diseñada para soportar una plataforma de e-commerce de camisetas de fútbol con múltiples canales de venta:

- **Catálogo Directo**: Compra de productos individuales
- **Suscripciones**: Planes mensuales con envíos recurrentes
- **Mystery Boxes**: Cajas sorpresa con múltiples productos
- **Drops Gamificados**: Sistema de sorteos limitados
- **Programa de Fidelización**: Puntos y recompensas
- **Wishlist**: Lista de deseos con alertas

**Total de Tablas**: 50+ tablas organizadas en 15 módulos funcionales

**Características Técnicas**:
- Motor: InnoDB (transaccional)
- Codificación: UTF8MB4 (soporte completo de emojis y caracteres especiales)
- Integridad Referencial: Foreign Keys con ON DELETE policies apropiadas
- Auditoría: Triggers automáticos para historial
- Soft Deletes: En tablas críticas como customers
- Timestamps automáticos: created_at/updated_at

---

## Diagrama Visual

### Vista General de Módulos

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          KICKVERSE DATABASE                              │
│                                                                          │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐            │
│  │   PRODUCTOS  │────▶│   PEDIDOS    │◀────│   CLIENTES   │            │
│  │  (7 tablas)  │     │  (4 tablas)  │     │  (4 tablas)  │            │
│  └──────────────┘     └──────────────┘     └──────────────┘            │
│         │                    │                     │                     │
│         │                    │                     │                     │
│         ▼                    ▼                     ▼                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐            │
│  │SUSCRIPCIONES │     │ MYSTERY BOX  │     │ FIDELIZACIÓN │            │
│  │  (4 tablas)  │     │  (3 tablas)  │     │  (3 tablas)  │            │
│  └──────────────┘     └──────────────┘     └──────────────┘            │
│                                                                          │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐            │
│  │    DROPS     │     │  PROMOCIONES │     │    PAGOS     │            │
│  │  (3 tablas)  │     │  (4 tablas)  │     │  (2 tablas)  │            │
│  └──────────────┘     └──────────────┘     └──────────────┘            │
│                                                                          │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐            │
│  │  INVENTARIO  │     │COMUNICACIÓN  │     │  ANALYTICS   │            │
│  │  (2 tablas)  │     │  (2 tablas)  │     │  (2 tablas)  │            │
│  └──────────────┘     └──────────────┘     └──────────────┘            │
│                                                                          │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐            │
│  │   CARRITO    │     │   WISHLIST   │     │    ADMIN     │            │
│  │  (2 tablas)  │     │  (2 tablas)  │     │  (3 tablas)  │            │
│  └──────────────┘     └──────────────┘     └──────────────┘            │
└─────────────────────────────────────────────────────────────────────────┘
```

### Diagrama Entidad-Relación Detallado

```
┌──────────────────────────────────────────────────────────────────┐
│                    MÓDULO DE PRODUCTOS                            │
└──────────────────────────────────────────────────────────────────┘

    leagues                    teams                    products
    ┌─────────────┐           ┌─────────────┐          ┌──────────────┐
    │ league_id PK│◀────┐     │ team_id  PK │◀────┐    │ product_id PK│
    │ name        │     │     │ league_id FK│     │    │ league_id  FK│
    │ slug        │     └─────│ name        │     └────│ team_id    FK│
    │ country     │           │ slug        │          │ product_type │
    │ logo_path   │           │ is_top_team │          │ name         │
    └─────────────┘           └─────────────┘          │ base_price   │
                                                        │ stock_qty    │
                                                        └──────────────┘
                                                               │
                              ┌────────────────────────────────┼──────────────┐
                              │                                │              │
                              ▼                                ▼              ▼
                    ┌──────────────────┐          ┌──────────────────┐  ┌──────────┐
                    │ product_variants │          │ product_images   │  │price_hist│
                    ├──────────────────┤          ├──────────────────┤  ├──────────┤
                    │ variant_id    PK │          │ image_id      PK │  │history_id│
                    │ product_id    FK │          │ product_id    FK │  │product_id│
                    │ size             │          │ image_path       │  │old_price │
                    │ stock_quantity   │          │ image_type       │  │new_price │
                    └──────────────────┘          └──────────────────┘  └──────────┘


┌──────────────────────────────────────────────────────────────────┐
│                    MÓDULO DE CLIENTES                             │
└──────────────────────────────────────────────────────────────────┘

           customers                 customer_preferences      shipping_addresses
    ┌────────────────────┐          ┌──────────────────┐      ┌──────────────────┐
    │ customer_id     PK │◀────┐    │ preference_id PK │      │ address_id    PK │
    │ email              │     │    │ customer_id   FK │      │ customer_id   FK │
    │ telegram_username  │     └────│ favorite_teams   │      │ street_address   │
    │ whatsapp_number    │          │ favorite_leagues │      │ city             │
    │ full_name          │          │ preferred_size   │      │ postal_code      │
    │ loyalty_tier       │          └──────────────────┘      │ is_default       │
    │ loyalty_points     │                                     └──────────────────┘
    └────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                    MÓDULO DE PEDIDOS                              │
└──────────────────────────────────────────────────────────────────┘

           orders                          order_items
    ┌──────────────────┐              ┌──────────────────────┐
    │ order_id      PK │◀─────────────│ order_item_id     PK │
    │ customer_id   FK │              │ order_id          FK │
    │ order_type       │              │ product_id        FK │
    │ order_status     │              │ variant_id        FK │
    │ payment_status   │              │ quantity             │
    │ subtotal         │              │ unit_price           │
    │ discount_amount  │              │ has_patches          │
    │ total_amount     │              │ has_personalization  │
    │ shipping_addr FK │              │ personalization_name │
    │ payment_id    FK │              │ personalization_num  │
    │ coupon_id     FK │              └──────────────────────┘
    └──────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                  MÓDULO DE SUSCRIPCIONES                          │
└──────────────────────────────────────────────────────────────────┘

    subscription_plans           subscriptions              subscription_shipments
    ┌────────────────┐          ┌───────────────────┐      ┌────────────────────┐
    │ plan_id     PK │◀────┐    │subscription_id PK │◀─────│ shipment_id     PK │
    │ plan_name      │     │    │ customer_id    FK │      │ subscription_id FK │
    │ plan_type      │     └────│ plan_id        FK │      │ shipment_date      │
    │ monthly_price  │          │ status            │      │ tracking_number    │
    │ jersey_qty     │          │ start_date        │      │ status             │
    │ jersey_quality │          │ period_end        │      │ contents (JSON)    │
    └────────────────┘          │ preferred_size    │      └────────────────────┘
                                └───────────────────┘
                                        │
                                        │
                                        ▼
                           ┌─────────────────────────┐
                           │ subscription_payments   │
                           ├─────────────────────────┤
                           │ payment_id           PK │
                           │ subscription_id      FK │
                           │ payment_date            │
                           │ amount                  │
                           │ payment_method          │
                           │ period_start/end        │
                           └─────────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                  MÓDULO DE MYSTERY BOXES                          │
└──────────────────────────────────────────────────────────────────┘

    mystery_box_types         mystery_box_orders        mystery_box_contents
    ┌──────────────┐          ┌──────────────────┐      ┌────────────────────┐
    │box_type_id PK│◀────┐    │ order_id      PK │◀─────│ content_id      PK │
    │ name         │     │    │ customer_id   FK │      │ order_id        FK │
    │ price        │     └────│ box_type_id   FK │      │ product_id      FK │
    │ jersey_qty   │          │ selected_league  │      │ variant_id      FK │
    │ tier_restr   │          │ preferred_size   │      │ quantity           │
    └──────────────┘          │ status           │      │ reveal_date        │
                              └──────────────────┘      └────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│               MÓDULO DE DROPS GAMIFICADOS                         │
└──────────────────────────────────────────────────────────────────┘

     drop_events              drop_pool_items            drop_results
    ┌──────────────┐          ┌──────────────────┐      ┌────────────────┐
    │event_id   PK │◀────┐    │ pool_item_id  PK │◀─────│ result_id   PK │
    │ event_name   │     │    │ event_id      FK │      │ event_id    FK │
    │ start_date   │     ├────│ product_id    FK │      │ customer_id FK │
    │ end_date     │     │    │ rarity           │      │ pool_item   FK │
    │ total_drops  │     │    │ weight           │      │ selected_size  │
    │ remaining    │     │    └──────────────────┘      │ was_purchased  │
    └──────────────┘     │                              │ order_id    FK │
                         │                              └────────────────┘
                         │
                         └─ Rarity: common (62%), rare (30%), legendary (8%)


┌──────────────────────────────────────────────────────────────────┐
│              MÓDULO DE PROMOCIONES Y DESCUENTOS                   │
└──────────────────────────────────────────────────────────────────┘

       coupons                coupon_usage           promotional_campaigns
    ┌──────────────┐          ┌──────────────┐      ┌────────────────────┐
    │ coupon_id PK │◀────┐    │ usage_id  PK │      │ campaign_id     PK │
    │ code         │     │    │ coupon_id FK │      │ campaign_name      │
    │ discount_type│     └────│ customer  FK │      │ campaign_type      │
    │ discount_val │          │ order_id  FK │      │ trigger_condition  │
    │ min_purchase │          │ used_at      │      │ start_date         │
    │ usage_limit  │          └──────────────┘      │ end_date           │
    │ valid_until  │                                 └────────────────────┘
    └──────────────┘
                              promotion_3x2_usage
                              ┌─────────────────────┐
                              │ usage_id         PK │
                              │ order_id         FK │
                              │ paid_item_1_id   FK │
                              │ paid_item_2_id   FK │
                              │ free_item_id     FK │
                              └─────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                   MÓDULO DE FIDELIZACIÓN                          │
└──────────────────────────────────────────────────────────────────┘

    loyalty_points_history    loyalty_tier_benefits    loyalty_rewards
    ┌────────────────────┐    ┌──────────────────┐    ┌──────────────────┐
    │ transaction_id  PK │    │ benefit_id    PK │    │ reward_id     PK │
    │ customer_id     FK │    │ tier             │    │ reward_name      │
    │ points_change      │    │ min_orders_req   │    │ points_required  │
    │ balance_after      │    │ min_spent        │    │ reward_type      │
    │ transaction_type   │    │ points_multiplier│    │ reward_value     │
    │ reference_order FK │    │ discount_%       │    │ max_redemptions  │
    │ expires_at         │    │ free_shipping    │    └──────────────────┘
    └────────────────────┘    │ early_access     │
                              └──────────────────┘

    Tiers: standard → silver → gold → platinum
    Multipliers: 1.0x → 1.25x → 1.5x → 2.0x


┌──────────────────────────────────────────────────────────────────┐
│                      MÓDULO DE PAGOS                              │
└──────────────────────────────────────────────────────────────────┘

      payment_transactions              payment_webhooks
    ┌──────────────────────────┐       ┌─────────────────────┐
    │ transaction_id        PK │◀──────│ webhook_id       PK │
    │ customer_id           FK │       │ transaction_id   FK │
    │ order_id              FK │       │ webhook_type        │
    │ subscription_id       FK │       │ payload (JSON)      │
    │ payment_method           │       │ signature           │
    │ amount                   │       │ processed           │
    │ status                   │       │ received_at         │
    │ oxapay_transaction_id    │       └─────────────────────┘
    │ oxapay_payment_url       │
    │ oxapay_crypto_amount     │
    │ manual_payment_proof     │
    │ verified_by           FK │
    └──────────────────────────┘

    Payment Methods: oxapay_btc, oxapay_eth, oxapay_usdt,
                     telegram_manual, whatsapp_manual, bank_transfer


┌──────────────────────────────────────────────────────────────────┐
│                     MÓDULO DE CARRITO                             │
└──────────────────────────────────────────────────────────────────┘

         carts                       cart_items
    ┌──────────────────┐            ┌────────────────────────┐
    │ cart_id       PK │◀───────────│ cart_item_id        PK │
    │ customer_id   FK │            │ cart_id             FK │
    │ session_id       │            │ product_id          FK │
    │ cart_status      │            │ variant_id          FK │
    │ expires_at       │            │ quantity               │
    │ converted_to  FK │            │ has_patches            │
    └──────────────────┘            │ has_personalization    │
                                    │ unit_price (snapshot)  │
                                    └────────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                    MÓDULO DE WISHLIST                             │
└──────────────────────────────────────────────────────────────────┘

         wishlists                wishlist_notifications_sent
    ┌──────────────────────┐     ┌────────────────────────────┐
    │ wishlist_id       PK │◀────│ notification_id         PK │
    │ customer_id       FK │     │ wishlist_id             FK │
    │ product_id        FK │     │ notification_type          │
    │ variant_id        FK │     │ sent_at                    │
    │ notify_on_stock      │     └────────────────────────────┘
    │ notify_on_price_drop │
    │ price_when_added     │
    └──────────────────────┘

    Notification Types: back_in_stock, price_drop, low_stock_alert


┌──────────────────────────────────────────────────────────────────┐
│                  MÓDULO DE INVENTARIO                             │
└──────────────────────────────────────────────────────────────────┘

       stock_movements              low_stock_alerts
    ┌──────────────────────┐       ┌─────────────────────┐
    │ movement_id       PK │       │ alert_id         PK │
    │ variant_id        FK │       │ variant_id       FK │
    │ movement_type        │       │ threshold_qty       │
    │ quantity (±)         │       │ current_qty         │
    │ stock_after          │       │ alert_status        │
    │ reference_order   FK │       │ resolved_at         │
    │ created_by        FK │       └─────────────────────┘
    └──────────────────────┘

    Movement Types: purchase, sale, return, adjustment,
                    reserved, unreserved, damaged, lost


┌──────────────────────────────────────────────────────────────────┐
│                 MÓDULO DE COMUNICACIÓN                            │
└──────────────────────────────────────────────────────────────────┘

     customer_messages              notifications
    ┌──────────────────────┐       ┌─────────────────────────┐
    │ message_id        PK │       │ notification_id      PK │
    │ customer_id       FK │       │ customer_id          FK │
    │ channel              │       │ notification_type       │
    │ direction            │       │ title                   │
    │ message_content      │       │ message                 │
    │ related_order_id  FK │       │ sent_via                │
    │ status               │       │ sent_at                 │
    │ sent_at              │       │ read_at                 │
    │ handled_by        FK │       │ related_order_id     FK │
    └──────────────────────┘       │ action_url              │
                                   └─────────────────────────┘

    Channels: telegram, whatsapp, email, instagram
    Directions: inbound, outbound


┌──────────────────────────────────────────────────────────────────┐
│                   MÓDULO DE ANALYTICS                             │
└──────────────────────────────────────────────────────────────────┘

      analytics_events               product_views
    ┌──────────────────────┐        ┌────────────────────────┐
    │ event_id          PK │        │ view_id             PK │
    │ customer_id       FK │        │ product_id          FK │
    │ session_id           │        │ customer_id         FK │
    │ event_type           │        │ session_id             │
    │ event_category       │        │ time_spent_seconds     │
    │ event_value          │        │ scrolled_to_desc       │
    │ page_url             │        │ clicked_add_to_cart    │
    │ device_type          │        │ viewed_at              │
    │ browser              │        └────────────────────────┘
    │ ip_address           │
    └──────────────────────┘

    Event Types: page_view, cta_click, product_view, add_to_cart,
                 checkout_start, purchase, exit_intent, scroll_depth


┌──────────────────────────────────────────────────────────────────┐
│                 MÓDULO DE ADMINISTRACIÓN                          │
└──────────────────────────────────────────────────────────────────┘

       admin_users                  audit_log              system_settings
    ┌──────────────────┐       ┌──────────────────┐      ┌────────────────┐
    │ admin_id      PK │◀──┐   │ log_id        PK │      │ setting_id  PK │
    │ username         │   │   │ admin_id      FK │      │ setting_key    │
    │ password_hash    │   └───│ customer_id   FK │      │ setting_value  │
    │ email            │       │ action_type      │      │ setting_type   │
    │ role             │       │ entity_type      │      │ is_public      │
    │ is_active        │       │ entity_id        │      │ updated_by  FK │
    │ last_login       │       │ old_values (JSON)│      └────────────────┘
    └──────────────────┘       │ new_values (JSON)│
                               │ ip_address       │
    Roles: super_admin         └──────────────────┘
           admin
           inventory_manager
           customer_service
           marketing
           readonly
```

---

## Módulos y Tablas

### 1. CORE PRODUCT (7 tablas)
- **leagues**: Ligas de fútbol (La Liga, Premier, Serie A, etc.)
- **teams**: Equipos de fútbol
- **products**: Productos principales (camisetas, accesorios, etc.)
- **product_images**: Imágenes de productos
- **product_variants**: Variantes de tallas con stock individual
- **product_price_history**: Historial de cambios de precio
- **size_guides**: Guías de tallas con medidas

### 2. CUSTOMERS (4 tablas)
- **customers**: Clientes con autenticación híbrida
- **customer_preferences**: Preferencias de equipos, tallas, notificaciones
- **shipping_addresses**: Direcciones de envío múltiples
- **customer_sessions**: Sesiones para carritos guest

### 3. ORDERS (4 tablas)
- **orders**: Pedidos del catálogo
- **order_items**: Items de cada pedido con precios snapshot
- **shipping_addresses**: (compartida con customers)
- **payment_transactions**: (compartida con payments)

### 4. SUBSCRIPTIONS (4 tablas)
- **subscription_plans**: Planes disponibles (Fan, Premium, Retro)
- **subscriptions**: Suscripciones activas de clientes
- **subscription_shipments**: Historial de envíos mensuales
- **subscription_payments**: Pagos y renovaciones manuales

### 5. MYSTERY BOXES (3 tablas)
- **mystery_box_types**: Tipos de cajas (Clásica, Liga, Elite)
- **mystery_box_orders**: Pedidos de mystery boxes
- **mystery_box_contents**: Contenido real de cada caja

### 6. DROPS GAMIFICADOS (3 tablas)
- **drop_events**: Eventos de drops con límites
- **drop_pool_items**: Pool de items con raridades
- **drop_results**: Resultados de drops por cliente

### 7. PROMOTIONS (4 tablas)
- **coupons**: Cupones de descuento
- **coupon_usage**: Uso de cupones por cliente
- **promotional_campaigns**: Campañas promocionales
- **promotion_3x2_usage**: Tracking de promociones 3x2

### 8. LOYALTY (3 tablas)
- **loyalty_points_history**: Transacciones de puntos
- **loyalty_tier_benefits**: Beneficios por tier
- **loyalty_rewards**: Catálogo de recompensas

### 9. WISHLIST (2 tablas)
- **wishlists**: Lista de deseos por cliente
- **wishlist_notifications_sent**: Notificaciones enviadas

### 10. PAYMENTS (2 tablas)
- **payment_transactions**: Transacciones Oxapay y manuales
- **payment_webhooks**: Log de webhooks de pago

### 11. CART (2 tablas)
- **carts**: Carritos de compra
- **cart_items**: Items del carrito

### 12. INVENTORY (2 tablas)
- **stock_movements**: Movimientos de inventario
- **low_stock_alerts**: Alertas de stock bajo

### 13. COMMUNICATION (2 tablas)
- **customer_messages**: Mensajes Telegram/WhatsApp
- **notifications**: Notificaciones del sistema

### 14. ANALYTICS (2 tablas)
- **analytics_events**: Eventos de usuario (GTM)
- **product_views**: Vistas de productos

### 15. ADMIN (3 tablas)
- **admin_users**: Usuarios administradores
- **audit_log**: Log de auditoría de cambios
- **system_settings**: Configuración del sistema

---

## Relaciones Clave

### Relaciones de Productos

```
leagues (1) ──< teams (N)
teams (1) ──< products (N)
products (1) ──< product_variants (N)
products (1) ──< product_images (N)
products (1) ──< product_price_history (N)
```

**Significado**: Una liga tiene muchos equipos, un equipo tiene muchos productos (camisetas), y cada producto tiene múltiples variantes (tallas) e imágenes.

### Relaciones de Clientes

```
customers (1) ──< orders (N)
customers (1) ──< subscriptions (N)
customers (1) ──< shipping_addresses (N)
customers (1) ─── customer_preferences (1)
customers (1) ──< wishlists (N)
customers (1) ──< carts (N)
```

**Significado**: Un cliente puede tener múltiples pedidos, suscripciones, direcciones, carritos y items en wishlist, pero solo un registro de preferencias.

### Relaciones de Pedidos

```
orders (1) ──< order_items (N)
order_items (N) ──> products (1)
order_items (N) ──> product_variants (1)
orders (N) ──> coupons (1) [opcional]
orders (N) ──> shipping_addresses (1)
orders (N) ──> payment_transactions (1) [opcional]
```

**Significado**: Un pedido contiene múltiples items, cada item referencia un producto y variante específicos. Un pedido puede tener un cupón aplicado.

### Relaciones de Suscripciones

```
subscription_plans (1) ──< subscriptions (N)
customers (1) ──< subscriptions (N)
subscriptions (1) ──< subscription_shipments (N)
subscriptions (1) ──< subscription_payments (N)
```

**Significado**: Un plan de suscripción puede tener muchas suscripciones activas. Cada suscripción tiene historial de envíos y pagos.

### Relaciones de Mystery Boxes

```
mystery_box_types (1) ──< mystery_box_orders (N)
mystery_box_orders (1) ──< mystery_box_contents (N)
mystery_box_contents (N) ──> products (1)
mystery_box_contents (N) ──> product_variants (1)
```

**Significado**: Un tipo de caja puede ser pedido muchas veces. Cada pedido contiene múltiples productos específicos.

### Relaciones de Drops

```
drop_events (1) ──< drop_pool_items (N)
drop_events (1) ──< drop_results (N)
drop_pool_items (N) ──> products (1)
drop_results (N) ──> drop_pool_items (1)
drop_results (N) ──> customers (1) [opcional]
drop_results (N) ──> orders (1) [opcional si comprado]
```

**Significado**: Un evento de drop tiene un pool de items posibles. Cada resultado del drop referencia a qué item ganó el cliente.

### Relaciones de Fidelización

```
customers (1) ──< loyalty_points_history (N)
loyalty_points_history (N) ──> orders (1) [opcional]
customers.loyalty_tier ──> loyalty_tier_benefits (1)
```

**Significado**: Cada transacción de puntos está vinculada a un cliente y opcionalmente a un pedido que generó los puntos.

---

## Flujos de Datos

### Flujo 1: Compra de Catálogo

```
1. Cliente navega catálogo
   └─> product_views registrado

2. Cliente añade item al carrito
   ├─> cart_items creado
   └─> analytics_events (add_to_cart)

3. Cliente procede al checkout
   ├─> order creado (status: pending_payment)
   ├─> order_items creados con precios snapshot
   └─> stock_movements (reserved)

4. Cliente paga
   ├─> payment_transaction creado
   └─> order.payment_status = completed

5. Pedido se envía
   ├─> order.order_status = shipped
   ├─> tracking_number asignado
   └─> notification enviada

6. Pedido entregado
   ├─> order.order_status = delivered
   ├─> loyalty_points_history creado (puntos ganados)
   └─> customer.total_spent actualizado
```

### Flujo 2: Nueva Suscripción

```
1. Cliente solicita suscripción (Telegram)
   └─> customer_messages creado

2. Admin procesa solicitud
   ├─> subscription creado (status: pending)
   └─> audit_log registrado

3. Cliente realiza primer pago
   ├─> subscription_payment creado
   └─> subscription.status = active

4. Cada mes:
   ├─> Admin prepara envío
   ├─> subscription_shipment creado
   ├─> stock_movements para productos incluidos
   └─> tracking asignado

5. Al llegar period_end:
   ├─> subscription.status = expired (si no renovado)
   └─> notification de renovación enviada
```

### Flujo 3: Mystery Box

```
1. Cliente selecciona tipo de caja
   └─> product_views registrado

2. Cliente completa pedido
   ├─> mystery_box_order creado
   ├─> payment_transaction creado
   └─> order.status = pending_payment

3. Pago confirmado
   └─> order.status = processing

4. Admin prepara caja
   ├─> mystery_box_contents creados (5 items)
   ├─> stock_movements para cada item
   └─> order.status = shipped

5. Cliente recibe y revela
   ├─> mystery_box_contents.reveal_date actualizado
   └─> order.status = delivered
```

### Flujo 4: Drop Gamificado

```
1. Drop event activo
   └─> drop_events.is_active = TRUE

2. Cliente juega drop
   ├─> drop_results creado
   ├─> drop_events.remaining_drops decrementado
   └─> algoritmo weighted random selecciona item

3. Cliente decide comprar
   ├─> order creado
   ├─> drop_results.was_purchased = TRUE
   ├─> drop_results.catalog_order_id asignado
   └─> payment_transaction creado

4. Drop expira sin compra
   └─> drop_results.purchase_deadline pasado
```

### Flujo 5: Loyalty Points

```
1. Cliente completa pedido
   └─> Trigger: award_loyalty_points_on_order

2. Calcular puntos
   ├─> base_points = FLOOR(total_amount)
   ├─> multiplier según loyalty_tier
   └─> points_earned = base_points * multiplier

3. Actualizar cliente
   ├─> customers.loyalty_points += points_earned
   └─> loyalty_points_history creado

4. Verificar upgrade de tier
   ├─> Si total_spent > threshold
   └─> customers.loyalty_tier actualizado

5. Cliente canjea puntos
   ├─> loyalty_points_history (negative)
   ├─> coupon generado o beneficio aplicado
   └─> customers.loyalty_points decrementado
```

### Flujo 6: Wishlist con Alertas

```
1. Cliente añade item a wishlist
   ├─> wishlists creado
   └─> price_when_added guardado

2. Producto vuelve a tener stock
   ├─> stock_movements (purchase/adjustment)
   ├─> Verificar wishlists con notify_on_stock = TRUE
   ├─> notification creada
   └─> wishlist_notifications_sent registrado

3. Precio del producto baja
   ├─> product_price_history creado
   ├─> Comparar con wishlist.price_when_added
   ├─> Si price_drop > threshold
   ├─> notification creada
   └─> wishlist_notifications_sent registrado
```

### Flujo 7: Pago con Oxapay

```
1. Cliente procede a checkout
   └─> order creado (pending_payment)

2. Sistema crea transacción Oxapay
   ├─> payment_transaction creado
   ├─> API call a Oxapay
   ├─> oxapay_payment_url recibida
   └─> Cliente redirigido

3. Oxapay envía webhook
   ├─> payment_webhooks creado (processed = FALSE)
   └─> Webhook procesado

4. Verificar firma webhook
   ├─> Si válido: processed = TRUE
   ├─> payment_transaction.status = completed
   └─> order.payment_status = completed

5. Si falla
   ├─> payment_transaction.status = failed
   └─> notification enviada a cliente
```

### Flujo 8: Promoción 3x2

```
1. Cliente tiene 2 items en carrito
   └─> Sistema detecta oportunidad 3x2

2. Cliente selecciona 3er item gratis
   ├─> order_items creado con is_free_item = TRUE
   └─> order.discount_amount = precio del 3er item

3. Registrar promoción
   ├─> promotion_3x2_usage creado
   ├─> paid_item_1_id, paid_item_2_id, free_item_id
   └─> audit_log registrado

4. Analytics tracking
   └─> analytics_events (promotion_used)
```

---

## Índices y Optimizaciones

### Índices Principales

**Products**:
- `idx_product_type` - Filtrar por tipo de producto
- `idx_active_featured` - Productos activos y destacados
- `FULLTEXT idx_search` - Búsqueda de texto completo

**Orders**:
- `idx_customer` - Pedidos por cliente
- `idx_order_status` - Filtrar por estado
- `idx_order_date` - Ordenar cronológicamente
- `idx_orders_customer_status` - Compuesto para queries frecuentes

**Customers**:
- `idx_email` - Login por email
- `idx_telegram` - Login por Telegram
- `idx_loyalty` - Ranking de fidelización

**Analytics**:
- `idx_event_type` - Filtrar por tipo de evento
- `idx_created` - Queries por rango de fechas
- `idx_session` - Tracking de sesiones

### Triggers Automáticos

1. **update_customer_stats_after_order**: Actualiza estadísticas del cliente
2. **create_stock_movement_on_order**: Crea movimiento de stock automáticamente
3. **check_low_stock_after_movement**: Genera alertas de stock bajo
4. **award_loyalty_points_on_order**: Otorga puntos de fidelización
5. **check_subscription_expiration**: Marca suscripciones expiradas

---

## Constraints y Reglas de Negocio

### Integridad Referencial

**ON DELETE CASCADE** (elimina dependientes):
- `product_images` → `products`
- `product_variants` → `products`
- `order_items` → `orders`
- `cart_items` → `carts`

**ON DELETE RESTRICT** (previene eliminación):
- `orders` → `customers`
- `subscriptions` → `customers`
- `order_items` → `products`

**ON DELETE SET NULL** (permite eliminación, pone NULL):
- `orders` → `coupons`
- `drop_results` → `customers` (para drops guest)

### Reglas de Negocio

**Precios**:
- Base jersey: 24.99 EUR
- Parches: +1.99 EUR
- Personalización: +2.99 EUR
- Precio en order_items es snapshot (no cambia)

**Stock**:
- Stock por variante (talla)
- Reserva automática al crear order
- Alertas cuando stock <= low_stock_threshold

**Suscripciones**:
- Expiración manual basada en current_period_end
- No hay auto-renovación (gestionado manualmente)
- Trigger verifica expiración en cada UPDATE

**Loyalty**:
- 1 punto por 1 EUR gastado
- Multiplicador según tier (1.0x - 2.0x)
- Puntos pueden expirar (expires_at en loyalty_points_history)

**Drops**:
- Límite de drops por evento
- Límite por cliente
- Sistema de raridades con pesos probabilísticos

---

## Consideraciones de Rendimiento

### Queries Frecuentes Optimizados

1. **Listado de productos activos por liga**:
```sql
SELECT p.*, t.name as team_name, l.name as league_name
FROM products p
JOIN teams t ON p.team_id = t.team_id
JOIN leagues l ON p.league_id = l.league_id
WHERE p.is_active = TRUE AND l.slug = 'laliga'
ORDER BY p.is_featured DESC, p.name;
-- Usa: idx_active_featured, idx_slug
```

2. **Pedidos pendientes de un cliente**:
```sql
SELECT * FROM orders
WHERE customer_id = ? AND order_status IN ('pending_payment', 'processing')
ORDER BY order_date DESC;
-- Usa: idx_customer, idx_order_status
```

3. **Verificar stock disponible**:
```sql
SELECT stock_quantity FROM product_variants
WHERE product_id = ? AND size = ?;
-- Usa: idx_product_size
```

### Particionamiento Recomendado (Futuro)

Para tablas grandes (>1M registros):
- `analytics_events`: Particionar por mes
- `loyalty_points_history`: Particionar por año
- `audit_log`: Particionar por trimestre

---

## Migración y Mantenimiento

### Scripts de Migración

1. **schema.sql**: Crea todas las tablas, índices y triggers
2. **data_migration.sql**: Migra datos hardcodeados
3. **Verificación**:
```sql
SELECT COUNT(*) as total_products FROM products;
SELECT COUNT(*) as total_teams FROM teams;
SELECT COUNT(*) as total_variants FROM product_variants;
```

### Mantenimiento Regular

**Diario**:
- Verificar `low_stock_alerts` pendientes
- Procesar `payment_webhooks` no procesados
- Limpiar `carts` expirados

**Semanal**:
- Actualizar `drop_events` activos
- Verificar `subscriptions` próximas a expirar
- Review de `audit_log` para cambios críticos

**Mensual**:
- Archivar `analytics_events` antiguos
- Limpiar `customer_sessions` expiradas
- Optimizar tablas grandes (OPTIMIZE TABLE)

---

## Seguridad

### Datos Sensibles

**Nunca en logs**:
- `customers.password_hash`
- `payment_transactions.oxapay_response` (puede contener claves)
- `admin_users.password_hash`

**Encriptados**:
- Contraseñas con bcrypt (cost factor 10)
- Payment proofs (archivos, no en DB directamente)

### Auditoría

Todas las acciones críticas en `audit_log`:
- Cambios de precio
- Modificaciones de pedidos
- Verificaciones de pago manual
- Cambios de roles de admin

---

**Fin del Diagrama de Base de Datos**

Para implementación práctica, consultar:
- `schema.sql` - Estructura completa
- `data_migration.sql` - Datos iniciales
- `DATABASE_DOCUMENTATION.md` - Documentación técnica detallada
