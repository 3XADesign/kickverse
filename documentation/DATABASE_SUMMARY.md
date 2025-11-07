# KICKVERSE - RESUMEN EJECUTIVO DE BASE DE DATOS

## VISTA RÁPIDA

### Módulos Implementados

1. **PRODUCTOS** (7 tablas)
   - Ligas (6): La Liga, Premier, Serie A, Bundesliga, Ligue 1, Selecciones
   - Equipos (69): Distribuidos por liga
   - Productos (200+): Jerseys con múltiples variantes
   - Variants (1400+): Tallas por producto
   - Imágenes: Múltiples por producto
   - Precios: Historial de cambios
   - Traducciones: Multi-idioma ES/EN

2. **CLIENTES** (4 tablas)
   - Autenticación Híbrida: Email, Telegram, WhatsApp
   - Preferencias: Ligas, equipos, tallas
   - Direcciones: Múltiples por cliente
   - Sesiones: Guest y registrados

3. **SUSCRIPCIONES** (4 tablas)
   - 4 Planes: FAN, Premium Random, Premium TOP, Retro TOP
   - Suscripciones: Estados (active, paused, cancelled, expired)
   - Envíos: Historial mensual
   - Pagos: Tracking de renovaciones

4. **MYSTERY BOXES** (3 tablas)
   - 3 Tipos: Clásica, Por Liga, Premium Elite
   - Órdenes: Compras de cajas
   - Contenidos: Qué se incluye en cada caja

5. **DROPS** (3 tablas)
   - Eventos: Gamificación con rareza
   - Pool: Items comunes (62%), raros (30%), legendarios (8%)
   - Resultados: Qué ganó cada usuario

6. **ORDENES** (2 tablas)
   - Pedidos: Catalog, Mystery Box, Subscription, Drop, Upsell
   - Items: Detalles con personalización (parches, nombres)

7. **PAGOS** (2 tablas)
   - Transacciones: Oxapay, Manual, Telegram, WhatsApp
   - Webhooks: Log de notificaciones Oxapay

8. **PROMOCIONES** (4 tablas)
   - Coupons: 6 códigos preconfigurados
   - Uso: Historial de cada cupón
   - Campañas: 3x2, First Purchase, etc.
   - 3x2 Tracking: Seguimiento específico

9. **LEALTAD** (3 tablas)
   - Puntos: Transacciones (compras, reembolsos, ajustes)
   - Tiers: Standard, Silver, Gold, Platinum
   - Recompensas: Canje de puntos

10. **WISHLIST** (2 tablas)
    - Favoritos: Productos guardados
    - Notificaciones: Stock y precio drop

11. **CARRITO** (2 tablas)
    - Carts: Guest y registrados
    - Items: Productos en carrito

12. **INVENTARIO** (2 tablas)
    - Movimientos: Auditoría de stock
    - Alertas: Bajo stock automático

13. **COMUNICACIONES** (2 tablas)
    - Mensajes: Telegram, WhatsApp, Email, Instagram
    - Notificaciones: Automáticas del sistema

14. **ANALYTICS** (2 tablas)
    - Eventos: Page views, clicks, compras
    - Vistas: Interés en productos

15. **ADMIN** (3 tablas)
    - Usuarios: 6 roles de permisos
    - Auditoría: Log de todas las acciones
    - Configuración: Settings del sistema

---

## ESTADÍSTICAS

| Concepto | Cantidad | Notas |
|----------|----------|-------|
| Tablas | 35+ | Bien estructuradas |
| Foreign Keys | 45+ | Integridad referencial |
| Índices | 60+ | Performance optimizado |
| Triggers | 5 | Automáticos |
| Ligas | 6 | Completas |
| Equipos | 69 | Distribuidos |
| Productos | 200+ | Jerseys activos |
| Variantes | 1400+ | Por talla |
| Planes suscripción | 4 | Diferenciados |
| Tipos mystery box | 3 | Opciones variadas |
| Coupons | 6 | Preconfigurados |
| Tiers lealtad | 4 | Progresivos |
| Admin roles | 6 | Basados en permisos |

---

## ESTRUCTURA DE CLIENTES

### Autenticación Híbrida
```
Cliente puede ser:
├─ Email + Password
├─ Telegram Username + Chat ID
├─ WhatsApp Number
└─ Combinación de los anteriores

Estados: active, inactive, blocked
Datos: nombre, teléfono, idioma
Lealtad: tier + puntos + órdenes + gastado
Seguridad: password reset tokens, locked accounts
```

### Preferencias Personalizables
```
Ligas preferidas: JSON array
Equipos preferidos: JSON array
Equipos a excluir: JSON array
Talla jersey preferida: S-4XL
Talla niños preferida: 16-28
Notificaciones: new drops, stock alerts, price drops
```

---

## FLUJO DE COMPRA COMPLETO

### 1. Catálogo
```
Customer selecciona producto
    → Agrega a Cart (carts + cart_items)
    → Procede a checkout
    → Selecciona/crea Shipping Address
    → Aplica Coupon (opcional)
    → Crea Order
    → Payment Transaction (pending)
    → Confirma pago (Oxapay/Manual)
    → Order status → processing
    → Stock Movement (decremented)
    → Loyalty Points (awarded)
    → Order status → shipped/delivered
```

### 2. Suscripción
```
Customer elige Plan
    → Crea Subscription (status: pending)
    → Setup Payment
    → Subscription status → active
    → Genera Shipment mensualmente
    → Carga Mystery Box Contents
    → Procesa Subscription Payment
    → Repite cada mes hasta cancelled
```

### 3. Mystery Box
```
Customer selecciona Box Type
    → Elige Liga (si aplica)
    → Selecciona Preferred Size
    → Agrega a Cart
    → Procede a Checkout
    → Crea Order (type: mystery_box)
    → Payment
    → Al abrir: registra Box Contents
```

### 4. Drop Event
```
Active Drop Event
    → Customer participa
    → Pool Items definidos con rarity
    → Random result (ponderado por weight)
    → Drop Result creado
    → 24-48h para comprar
    → Si compra: crea Catalog Order
    → Status: purchased_at registrado
```

---

## SISTEMA DE PUNTOS DE LEALTAD

### Tiers y Beneficios
```
STANDARD (por defecto)
├─ 0 órdenes mínimas
├─ 0€ gastados
├─ Multiplicador: 1.0x
└─ Sin beneficios adicionales

SILVER (3 órdenes, 100€)
├─ Multiplicador: 1.25x
├─ Descuento: 5%
└─ Bonus cumpleaños: 50 puntos

GOLD (10 órdenes, 300€)
├─ Multiplicador: 1.50x
├─ Descuento: 10%
├─ Envío gratis
├─ Acceso anticipado drops
├─ Soporte prioritario
└─ Bonus cumpleaños: 100 puntos

PLATINUM (25 órdenes, 750€)
├─ Multiplicador: 2.0x
├─ Descuento: 15%
├─ Envío gratis
├─ Acceso anticipado drops
├─ VIP prioritario
└─ Bonus cumpleaños: 200 puntos
```

### Cálculo de Puntos
```
Base: 1 punto = 1 EUR gastado
Aplicado: puntos_ganados = floor(total_order * tier_multiplier)
Automático: Trigger al cambiar order_status → delivered
Historial: Registrado en loyalty_points_history

Formas de obtener:
├─ Compra (order_purchase)
├─ Reembolso (order_refund)
├─ Cumpleaños (birthday_bonus)
├─ Referral
├─ Ajuste manual (admin)
└─ Ascenso tier (tier_bonus)
```

### Recompensas Canjeables
```
500 pts → 5€ descuento
900 pts → 10€ descuento
300 pts → Envío gratis (máx 5)
1200 pts → 15% desc máx 20€ (máx 1)
```

---

## SISTEMA DE PAGOS

### Métodos Soportados
```
Oxapay Crypto:
├─ Bitcoin (BTC)
├─ Ethereum (ETH)
└─ Tether (USDT)

Manual:
├─ Telegram
├─ WhatsApp
└─ Bank Transfer

Estados: pending, processing, completed, failed, expired, refunded
```

### Flujo de Pago
```
1. Create Payment Transaction (status: pending)
2. Si Oxapay:
   ├─ Generate Oxapay payment URL
   ├─ QR code para pago
   ├─ Store wallet address
   └─ Esperar webhook
3. Si Manual:
   ├─ Generar referencia
   ├─ Admin verifica proof
   └─ Marcar como completed
4. Order status → processing (cuando completed)
```

### Webhooks Oxapay
```
Recibidos en payment_webhooks
├─ payload (JSON)
├─ signature (verificación)
├─ processed (boolean)
└─ processing_error (si aplica)

Actualiza payment_transactions status
Dispara triggers de actualización de orden
```

---

## SISTEMA DE DESCUENTOS

### Cupones Actuales
```
WELCOME5 (5€)
├─ Primera compra
├─ Mínimo 60€
└─ Por cliente: 1 vez

NOTBETTING10 (10% máx 5€)
├─ General
└─ Por cliente: 3 veces

TOPBONUS10 (10% máx 5€)
├─ General
└─ Por cliente: 3 veces

KICKVERSE10 (10% máx 5€)
├─ General
└─ Por cliente: 5 veces

MYSTERY10 (10% máx 15€)
├─ Solo Mystery Boxes
├─ Mínimo 100€
└─ Por cliente: 2 veces

CATALOGO5 (5€)
├─ Solo Jerseys
├─ Mínimo 50€
└─ Por cliente: 3 veces
```

### Campañas Configurables
```
3x2: Compra 3, paga 2
First Purchase: Descuento primera compra
Exit Intent: Retención al salir
Bundle: Combos de productos
Flash Sale: Descuentos temporales
Seasonal: Por épocas
```

---

## INVENTARIO Y STOCK

### Gestión de Stock
```
1. Crear variante con stock_quantity inicial
2. Al comprar:
   ├─ Crear Stock Movement (type: reserved)
   ├─ Decrementar stock_quantity
   └─ Si bajo umbral: crear Low Stock Alert
3. Al devolver:
   ├─ Crear Stock Movement (type: return)
   └─ Incrementar stock_quantity
4. Ajustes manuales:
   ├─ Crear Stock Movement (type: adjustment)
   └─ Admin nota razón
```

### Alertas Automáticas
```
Trigger: check_low_stock_after_movement
Condición: stock_after <= low_stock_threshold
Acción:
├─ Crear low_stock_alerts record
├─ alert_status = 'pending'
└─ Notificar a inventory manager

Estados: pending, notified, resolved, dismissed
```

---

## TRIGGERS AUTOMÁTICOS

### 1. Update Customer Stats
```
Evento: AFTER INSERT ON orders (si status IN 'delivered', 'processing')
Actualiza:
├─ total_orders_count +1
├─ total_spent += order.total_amount
└─ last_activity_date = NOW()
```

### 2. Create Stock Movement
```
Evento: AFTER INSERT ON order_items
Acciones:
├─ INSERT stock_movements (type: reserved)
└─ UPDATE product_variants stock_quantity -quantity
```

### 3. Check Low Stock
```
Evento: AFTER INSERT ON stock_movements
Si: stock_after <= low_stock_threshold
Acciones:
├─ INSERT/UPDATE low_stock_alerts
└─ alert_status = 'pending'
```

### 4. Award Loyalty Points
```
Evento: AFTER UPDATE ON orders
Si: status cambió a 'delivered'
Acciones:
├─ Calcular puntos (amount * tier_multiplier)
├─ UPDATE customers loyalty_points
├─ INSERT loyalty_points_history
└─ Verificar ascenso de tier
```

### 5. Check Subscription Expiration
```
Evento: BEFORE UPDATE ON subscriptions
Si: current_period_end < TODAY() Y status = 'active'
Acción: SET status = 'expired'
```

---

## CONFIGURACIÓN DEL SISTEMA

### Settings Almacenados
```
Contacto:
├─ telegram_contact: @esKickverse
├─ whatsapp_contact: +34 614 299 735
├─ email_contact: hola@kickverse.es
└─ instagram_handle: @kickverse.es

Precios:
├─ free_shipping_threshold: 50€
├─ base_jersey_price: 24.99€
├─ patches_price: 1.99€
└─ personalization_price: 2.99€

Moneda: EUR

Idioma: es (por defecto)

Analytics:
├─ gtm_id: GTM-MQFTT34L
└─ ga_id: G-SD9ETEJ9TG

Envío: Spain only

Devoluciones: 14 días
```

---

## MODELOS PHP EXISTENTES

```
Model.php
├─ find(id)
├─ all(orderBy, limit)
├─ where(conditions)
├─ whereOne(conditions)
├─ create(data)
├─ update(id, data)
├─ delete(id)
├─ softDelete(id)
├─ count(conditions)
└─ Transacciones

Customer.php
├─ findByEmail()
├─ findByTelegram()
├─ findByWhatsApp()
├─ register()
├─ registerSocial()
├─ verifyPassword()
├─ getPreferences()
├─ updatePreferences()
├─ getAddresses()
├─ addLoyaltyPoints()
└─ checkTierUpgrade()

Product.php
├─ getByLeague()
├─ getByTeam()
├─ getActive()
├─ getFeatured()
├─ getRandom()
├─ search()
├─ hasStock()
├─ getVariant()
├─ decreaseStock()
└─ recordPriceChange()

Order.php
├─ createOrder() [con transacción]
├─ getOrderWithItems()
├─ getCustomerOrders()
├─ updateStatus()
├─ updatePaymentStatus()
├─ getPendingOrders()
├─ getRevenueStats()
└─ cancelOrder()

League.php
├─ getAllActive()
├─ getBySlug()
└─ getWithTeams()

Admin.php
├─ findByEmail()
├─ createMagicToken()
├─ verifyToken()
├─ cleanExpiredTokens()
└─ getDashboardStats()

Cart.php
├─ getOrCreate()
├─ getItems()
├─ addItem()
├─ removeItem()
├─ clear()
└─ convertToOrder()
```

---

## PRÓXIMOS MODELOS NECESARIOS

```
Para CRM completo:

Subscription.php
├─ create()
├─ updateStatus()
├─ getActiveSubscriptions()
├─ getExpiringSubscriptions()
├─ generateShipment()
└─ processMonthlyPayments()

MysteryBox.php
├─ getTypes()
├─ createOrder()
├─ generateContents()
├─ trackReveal()
└─ getCustomerBoxes()

DropEvent.php
├─ getActive()
├─ createResult()
├─ calculateWinner()
├─ getPendingResults()
└─ markAsPurchased()

Payment.php
├─ createTransaction()
├─ updateStatus()
├─ createOxapayPayment()
├─ verifyWebhook()
└─ getTransactionHistory()

LoyaltyReward.php
├─ getAvailableRewards()
├─ redeemReward()
├─ getCustomerRedemptions()
└─ updateTierBenefits()

Notification.php
├─ create()
├─ markAsRead()
├─ sendVia()
└─ getForCustomer()

Analytics.php
├─ trackEvent()
├─ trackProductView()
├─ getEventStats()
└─ getConversionFunnel()

Report.php
├─ revenueByPeriod()
├─ topProducts()
├─ customerSegmentation()
├─ subscriptionMetrics()
└─ loyaltyMetrics()
```

---

## CONSULTAS COMUNES PARA CRM

### Clientes
```sql
-- Cliente completo
SELECT c.*, COUNT(o.order_id) total_orders, SUM(o.total_amount) total_spent
FROM customers c
LEFT JOIN orders o ON c.customer_id = o.customer_id
WHERE c.customer_id = ?
GROUP BY c.customer_id

-- Top clientes (VIP)
SELECT * FROM customers
WHERE loyalty_tier = 'platinum'
ORDER BY total_spent DESC
LIMIT 50

-- Clientes inactivos
SELECT * FROM customers
WHERE last_activity_date < DATE_SUB(NOW(), INTERVAL 30 DAY)
```

### Ordenes
```sql
-- Ordenes pendientes
SELECT o.*, c.full_name, SUM(oi.quantity) item_count
FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_status = 'pending_payment'
GROUP BY o.order_id

-- Ingresos por período
SELECT DATE(order_date) day, COUNT(*) orders, SUM(total_amount) revenue
FROM orders
WHERE order_status IN ('delivered', 'shipped')
GROUP BY DATE(order_date)

-- AOV (Average Order Value)
SELECT AVG(total_amount) avg_order_value
FROM orders
WHERE order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
```

### Productos
```sql
-- Stock bajo
SELECT p.product_id, p.name, SUM(pv.stock_quantity) total_stock
FROM products p
JOIN product_variants pv ON p.product_id = pv.product_id
GROUP BY p.product_id
HAVING total_stock < 50

-- Más vendidos
SELECT p.product_id, p.name, COUNT(*) sales_count, SUM(oi.quantity) qty_sold
FROM products p
JOIN order_items oi ON p.product_id = oi.product_id
GROUP BY p.product_id
ORDER BY sales_count DESC
LIMIT 20

-- Sin vendidas
SELECT p.* FROM products p
LEFT JOIN order_items oi ON p.product_id = oi.product_id
WHERE oi.order_item_id IS NULL
```

### Suscripciones
```sql
-- Activas por plan
SELECT sp.plan_name, COUNT(*) subscriber_count, SUM(sp.monthly_price) mrr
FROM subscriptions s
JOIN subscription_plans sp ON s.plan_id = sp.plan_id
WHERE s.status = 'active'
GROUP BY s.plan_id

-- Vencidas pronto
SELECT s.*, sp.plan_name, c.full_name, c.telegram_username, c.email
FROM subscriptions s
JOIN subscription_plans sp ON s.plan_id = sp.plan_id
JOIN customers c ON s.customer_id = c.customer_id
WHERE s.status = 'active'
AND s.current_period_end BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)

-- MRR (Monthly Recurring Revenue)
SELECT SUM(sp.monthly_price) mrr
FROM subscriptions s
JOIN subscription_plans sp ON s.plan_id = sp.plan_id
WHERE s.status = 'active'
```

---

## ARCHIVOS CRÍTICOS

```
/database/schema.sql
├─ Creación de todas las tablas
├─ Índices y constraints
└─ 5 triggers

/database/data_migration.sql
├─ 6 ligas
├─ 69 equipos
├─ 200+ productos
├─ 1400+ variantes
├─ 4 planes suscripción
├─ 3 tipos mystery box
├─ 6 cupones
└─ Datos iniciales

/config/database.php
├─ Credenciales
├─ Opciones PDO
└─ Configuración conexión

/app/models/
├─ Model.php (base)
├─ Customer.php
├─ Product.php
├─ Order.php
├─ League.php
├─ Admin.php
└─ Cart.php
```

---

## PARA CONSTRUIR CRM

### Fase 1: Modelos base (en progreso)
- Crear modelos faltantes
- Implementar métodos de consulta

### Fase 2: Rutas API
- GET /api/customers/{id}
- POST /api/customers
- GET /api/orders/{id}
- GET /api/reports/revenue
- etc.

### Fase 3: Vistas Admin
- Dashboard con KPIs
- Gestión de clientes
- Seguimiento de órdenes
- Inventario
- Reportes

### Fase 4: Automatizaciones
- Webhooks Oxapay
- Renovación suscripciones
- Alertas de inventario
- Notificaciones

---

**Documentación generada:** 2025-11-06
**Versión:** 1.0
**Status:** Completo
