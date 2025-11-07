# KICKVERSE CRM - GUÍA RÁPIDA DE REFERENCIA

## ARCHIVOS DE DOCUMENTACIÓN

Hemos creado 3 documentos comprensivos sobre la base de datos:

1. **DATABASE_STRUCTURE.md** (1,684 líneas)
   - Descripción detallada de cada tabla
   - Campos, tipos, índices
   - Relaciones y constraints
   - Datos iniciales configurados
   - Triggers automáticos
   - Modelos PHP existentes

2. **DATABASE_SUMMARY.md** (739 líneas)
   - Vista ejecutiva resumida
   - Estadísticas por módulo
   - Flujos de compra
   - Sistema de puntos de lealtad
   - Sistema de pagos
   - Próximos modelos a crear

3. **DATABASE_DIAGRAM.md** (501 líneas)
   - Diagrama visual de relaciones
   - Flujos de datos principales
   - Índices clave
   - Cardinalidades (1:N, M:N)
   - Constraints
   - Tablas de auditoría
   - Escalabilidad

---

## TABLA DE REFERENCIA RÁPIDA

### MÓDULOS PRINCIPALES

| Módulo | Tablas | Descripción | Estado |
|--------|--------|-------------|--------|
| **Productos** | 7 | Ligas, equipos, jerseys, variantes, imágenes | ✅ Completo |
| **Clientes** | 4 | Autenticación híbrida, preferencias, direcciones | ✅ Completo |
| **Suscripciones** | 4 | Planes, suscripciones, envíos, pagos | ✅ Esquema |
| **Mystery Boxes** | 3 | Tipos, órdenes, contenidos | ✅ Esquema |
| **Drop Events** | 3 | Eventos, pool, resultados | ✅ Esquema |
| **Órdenes** | 2 | Órdenes, items con personalización | ✅ Modelo |
| **Pagos** | 2 | Transacciones, webhooks | ✅ Esquema |
| **Promociones** | 4 | Coupons, campañas, 3x2 | ✅ Esquema |
| **Lealtad** | 3 | Puntos, tiers, recompensas | ✅ Esquema |
| **Wishlist** | 2 | Favoritos, notificaciones | ✅ Esquema |
| **Carrito** | 2 | Carritos, items | ✅ Modelo |
| **Inventario** | 2 | Movimientos, alertas | ✅ Esquema |
| **Comunicaciones** | 2 | Mensajes, notificaciones | ✅ Esquema |
| **Analytics** | 2 | Eventos, vistas de productos | ✅ Esquema |
| **Admin** | 3 | Usuarios, auditoría, settings | ✅ Esquema |

---

## ESTADÍSTICAS DE BD

### Datos iniciales

```
Ligas:              6
Equipos:           69
Productos:        200+
Variantes:      1400+
Imágenes:        200+

Planes suscripción: 4
Mystery Box tipos:  3
Cupones:            6
Tiers de lealtad:   4
Drop Events:        1
Size Guides:       28
Admin users:        1
Settings:          15+
```

### Estructura DB

```
Tablas:           35+
Foreign Keys:     45+
Índices:          60+
Triggers:          5
Unique Keys:      15+
```

---

## AUTENTICACIÓN DE CLIENTES

### Tipos soportados

```
1. Email + Password
   └─ findByEmail()
   └─ verifyPassword()
   └─ register()

2. Telegram
   └─ findByTelegram()
   └─ registerSocial()

3. WhatsApp
   └─ findByWhatsApp()
   └─ registerSocial()

4. Híbrida (combinación)
   └─ Puede tener múltiples métodos simultáneamente
```

---

## FLUJOS PRINCIPALES

### 1. COMPRA (Carrito → Orden → Pago)

```
1. Crear/obtener carrito
   Cart.getOrCreate($customerId) → carts
   
2. Agregar productos
   Cart.addItem($cartId, $productId, $variantId)
   → cart_items + product_variants stock check
   
3. Checkout
   Order.createOrder($customerId, $items, $shippingAddressId)
   → orders + order_items
   → stock_movements (reserved)
   → product_variants stock -quantity
   → coupons aplicar si existe
   → coupon_usage registrar
   
4. Pago
   Payment.createTransaction()
   → payment_transactions (pending)
   → Oxapay: generar URL/QR
   → Manual: esperar verificación admin
   
5. Confirmación
   order_status → processing
   → stock_movements finalize
   → low_stock_alerts si aplica
   → customers.total_spent ++
   → customers.total_orders_count ++
   
6. Envío
   order_status → shipped
   → tracking_number registrado
   
7. Entrega
   order_status → delivered
   → TRIGGER: Award loyalty points
   → customers.loyalty_points += (amount * tier_multiplier)
   → loyalty_points_history registrado
   → Check tier upgrade
```

### 2. SUSCRIPCIÓN

```
1. Seleccionar plan
   subscription_plans (4 opciones)
   
2. Crear suscripción
   Subscription.create()
   → subscriptions (status: pending)
   → subscription_payments (pago inicial)
   → payment_transactions
   
3. Pago confirmado
   → subscriptions.status → active
   → subscriptions.start_date = NOW()
   → subscriptions.current_period_start/end
   → subscriptions.next_billing_date (1 mes)
   
4. Generación mensual
   [CRON: primer día del mes]
   → subscription_shipments (create)
   → mystery_box_contents (select items)
   → product_variants stock -1
   → stock_movements registrar
   
5. Renovación mensual
   → subscription_payments (renewal)
   → payment_transactions
   → Si success: subscriptions.next_billing_date += 1 mes
   → loyalty_points_history (monthly bonus)
   
6. Cancelación
   → subscriptions.status → cancelled
   → subscriptions.cancellation_date
   → subscription.cancellation_reason
   
7. Pausa
   → subscriptions.status → paused
   → subscriptions.pause_date
   → subscriptions.pause_reason
   → Se puede reactivar a active
   
8. Expiración (automática)
   → TRIGGER: Si current_period_end < TODAY() Y status = active
   → subscriptions.status → expired
```

### 3. MYSTERY BOX

```
1. Seleccionar tipo
   mystery_box_types (3 opciones)
   
2. Si "Box por Liga"
   → selected_league_id
   
3. Agregar a carrito
   → carts + cart_items
   
4. Proceder como compra normal
   → Checkout → Order (type: mystery_box)
   → Payment → delivered
   
5. Al abrir (customer)
   → mystery_box_contents registra reveal_date
   → Notificación al cliente
```

### 4. DROP EVENT

```
1. Drop activo
   drop_events.is_active = true
   drop_events.start_date <= NOW() <= end_date
   
2. Customer participa
   - Random selection from drop_pool_items
   - Peso ponderado por rarity
   - COMMON: 62%, RARE: 30%, LEGENDARY: 8%
   
3. Resultado
   → drop_results creado
   → result_date = NOW()
   → purchase_deadline = NOW() + 24-48h
   
4. Oportunidad de compra
   - Customer ve qué ganó
   - 24-48 horas para comprar
   - Si compra: → orders (type: drop)
   - Si no: se pierde
   
5. Registrar compra
   → drop_results.was_purchased = true
   → drop_results.catalog_order_id = order.id
   → drop_results.purchased_at = NOW()
```

---

## SISTEMA DE LEALTAD

### Tiers automáticos

```
STANDARD
- 0 órdenes mínimas
- 0€ gastados
- Multiplicador: 1.0x
- Descuento: 0%
- Sin beneficios

SILVER (3 órdenes, 100€)
- Multiplicador: 1.25x
- Descuento: 5%
- Bonus cumpleaños: 50 pts

GOLD (10 órdenes, 300€)
- Multiplicador: 1.50x
- Descuento: 10%
- Envío gratis
- Acceso drops anticipado
- Soporte prioritario
- Bonus cumpleaños: 100 pts

PLATINUM (25 órdenes, 750€)
- Multiplicador: 2.0x
- Descuento: 15%
- Envío gratis
- Acceso drops anticipado
- VIP prioritario
- Bonus cumpleaños: 200 pts
```

### Cálculo de puntos

```
Base: 1 punto = 1 EUR gastado
Aplicación: puntos = FLOOR(order_total * tier_multiplier)
Trigger: AFTER UPDATE orders (status → delivered)
Historial: loyalty_points_history registra cada transacción

Formas de ganar:
- order_purchase: Al comprar
- order_refund: Reembolso
- birthday_bonus: Cumpleaños (automático)
- referral: Referencia
- manual_adjustment: Admin
- tier_bonus: Ascenso de tier

Canje:
- 500 pts → 5€ descuento
- 900 pts → 10€ descuento
- 300 pts → Envío gratis (máx 5)
- 1200 pts → 15% desc (máx 1)
```

---

## SISTEMA DE PAGOS

### Métodos soportados

```
OXAPAY (Crypto):
├─ Bitcoin (BTC)
├─ Ethereum (ETH)
└─ Tether (USDT)
   └─ Genera: payment_url, qr_code, wallet_address
   └─ Webhooks: payment_webhooks confirman pago
   └─ Estados: pending → processing → completed

MANUAL:
├─ Telegram
├─ WhatsApp
└─ Bank Transfer
   └─ Admin verifica proof
   └─ Status manual: pending → completed

Estados:
pending → processing → completed
                    ├─ failed
                    ├─ expired
                    └─ refunded
```

### Proceso de pago

```
1. Create payment_transactions
   status = pending
   
2. Si Oxapay:
   ├─ Generar oxapay_payment_url
   ├─ Generar QR
   ├─ Store oxapay_transaction_id
   └─ Store wallet_address
   
3. Si Manual:
   ├─ Generar payment_reference
   ├─ Customer sube proof
   └─ Admin verifica (verified_by, verified_at)
   
4. Webhook/Confirmación:
   → payment_transactions.status = completed
   → payment_transactions.completed_at = NOW()
   
5. Actualizar order:
   → order_status = processing
   → payment_status = completed
```

---

## SISTEMA DE DESCUENTOS

### Cupones actuales

```
WELCOME5 (5€)
├─ Primera compra
├─ Mínimo 60€
└─ 1 uso por cliente

NOTBETTING10 (10% máx 5€)
├─ General
└─ 3 usos por cliente

TOPBONUS10 (10% máx 5€)
├─ General
└─ 3 usos por cliente

KICKVERSE10 (10% máx 5€)
├─ General
└─ 5 usos por cliente

MYSTERY10 (10% máx 15€)
├─ Solo Mystery Boxes
├─ Mínimo 100€
└─ 2 usos por cliente

CATALOGO5 (5€)
├─ Solo Jerseys
├─ Mínimo 50€
└─ 3 usos por cliente
```

---

## CONTACTOS DE SISTEMA

```
Telegram:    @esKickverse
WhatsApp:    +34 614 299 735
Email:       hola@kickverse.es
Instagram:   @kickverse.es
Twitter:     @kickverse_es
TikTok:      @kickverse_es
```

---

## PRECIOS Y CONFIGURACIÓN

```
Jersey base:           24.99€
Jersey display:        79.99€
Parches:               1.99€
Personalización:       2.99€
Envío normal:          5.99€
Envío gratis si:       >= 50€

Plan FAN:              24.99€/mes
Plan Premium Random:   29.99€/mes
Plan Premium TOP:      34.99€/mes
Plan Retro TOP:        39.99€/mes

Box Clásica:          124.95€
Box por Liga:         174.95€
Box Premium Elite:    174.95€

Moneda: EUR
Idioma default: ES
Devoluciones: 14 días
```

---

## MODELOS PHP EXISTENTES

### Model.php (Base)
```php
find($id)
all($orderBy, $limit)
where($conditions, $orderBy, $limit)
whereOne($conditions)
create($data)
update($id, $data)
delete($id)
softDelete($id)
count($conditions)
query($sql, $params)
fetchAll($sql, $params)
fetchOne($sql, $params)
beginTransaction() / commit() / rollback()
```

### Customer.php
```php
findByEmail($email)
findByTelegram($username)
findByWhatsApp($number)
register($email, $password, $fullName, $phone)
registerSocial($fullName, $telegram, $whatsapp, $phone)
verifyPassword($email, $password)
updateLastLogin($customerId, $ipAddress)
getPreferences($customerId)
updatePreferences($customerId, $preferences)
getAddresses($customerId)
getDefaultAddress($customerId)
addLoyaltyPoints($customerId, $points, $type, $orderId, $description)
checkTierUpgrade($customerId)
getVIPCustomers($limit)
deleteCustomer($customerId)
```

### Product.php
```php
getByLeague($leagueSlug, $active)
getByTeam($teamSlug, $active)
getActive($limit)
getFeatured($limit)
getRandom($limit)
getByType($type, $limit)
getBySlug($slug)
getFullDetails($productId)
search($query, $limit)
hasStock($productId, $size)
getVariant($productId, $size)
decreaseStock($variantId, $quantity)
getPriceHistory($productId, $limit)
recordPriceChange($productId, $oldPrice, $newPrice, $reason, $adminId)
```

### Order.php
```php
createOrder($customerId, $items, $shippingAddressId, $couponId)
getOrderWithItems($orderId)
getCustomerOrders($customerId, $limit)
updateStatus($orderId, $status, $trackingNumber)
updatePaymentStatus($orderId, $status, $paymentId)
getPendingOrders($limit)
getRevenueStats($startDate, $endDate)
cancelOrder($orderId, $reason)
```

### League.php
```php
getAllActive()
getBySlug($slug)
getWithTeams($leagueSlug)
```

### Admin.php
```php
findByEmail($email)
emailExists($email)
createMagicToken($email, $ipAddress, $userAgent)
verifyToken($token)
cleanExpiredTokens()
getDashboardStats()
```

### Cart.php
```php
getOrCreate($customerId, $sessionId)
getItems($cartId)
addItem($cartId, $productId, $variantId, $quantity, $customizations)
removeItem($cartItemId)
clear($cartId)
convertToOrder($cartId, $orderId)
```

---

## PRÓXIMOS MODELOS A CREAR

### Para CRM Completo

**Subscription.php**
```php
create($customerId, $planId, $preferredSize)
updateStatus($subscriptionId, $status, $reason)
getActiveSubscriptions($limit)
getExpiringSubscriptions($days)
generateMonthlyShipment($subscriptionId)
processMonthlyPayment($subscriptionId)
pause($subscriptionId, $reason)
resume($subscriptionId)
```

**MysteryBox.php**
```php
getTypes()
createOrder($customerId, $boxTypeId, $leagueId, $size)
generateContents($boxOrderId)
trackReveal($orderId)
getCustomerBoxes($customerId)
```

**DropEvent.php**
```php
getActive()
createResult($customerId, $sessionId)
calculateWinner()
getPendingResults($limit)
markAsPurchased($resultId, $orderId)
```

**Payment.php**
```php
createTransaction($customerId, $amount, $method)
updateStatus($transactionId, $status)
createOxapayPayment($amount, $orderId)
verifyWebhook($payload, $signature)
getTransactionHistory($customerId, $limit)
```

**LoyaltyReward.php**
```php
getAvailableRewards()
redeemReward($customerId, $rewardId)
getCustomerRedemptions($customerId)
updateTierBenefits()
```

**Notification.php**
```php
create($customerId, $type, $message)
markAsRead($notificationId)
sendVia($notificationId, $channel)
getForCustomer($customerId, $limit)
```

**Analytics.php**
```php
trackEvent($customerId, $sessionId, $type, $data)
trackProductView($productId, $customerId, $sessionId)
getEventStats($startDate, $endDate)
getConversionFunnel()
```

**Report.php**
```php
revenueByPeriod($startDate, $endDate)
topProducts($limit, $period)
customerSegmentation()
subscriptionMetrics()
loyaltyMetrics()
churnAnalysis()
```

---

## TRIGGERS AUTOMÁTICOS

### 1. update_customer_stats_after_order
```
AFTER INSERT ON orders
Si: status IN ('delivered', 'processing')
Actualiza: total_orders_count, total_spent, last_activity_date
```

### 2. create_stock_movement_on_order
```
AFTER INSERT ON order_items
INSERT: stock_movements (reserved)
UPDATE: product_variants.stock_quantity -quantity
```

### 3. check_low_stock_after_movement
```
AFTER INSERT ON stock_movements
Si: stock_after <= low_stock_threshold
INSERT/UPDATE: low_stock_alerts (status: pending)
```

### 4. award_loyalty_points_on_order
```
AFTER UPDATE ON orders
Si: status → delivered
Calcula: floor(amount * tier_multiplier)
UPDATE: customers.loyalty_points
INSERT: loyalty_points_history
CHECK: tier upgrade
```

### 5. check_subscription_expiration
```
BEFORE UPDATE ON subscriptions
Si: current_period_end < TODAY() Y status = active
SET: status = expired
```

---

## CONFIGURACIÓN BASE DE DATOS

```
Host:       50.31.174.69
Database:   iqvfmscx_kickverse
User:       iqvfmscx_kickverse
Charset:    utf8mb4
Collation:  utf8mb4_unicode_ci

Archivo: /config/database.php
```

---

## RECURSOS

### Archivos de esquema
- `/database/schema.sql` - Creación de tablas
- `/database/data_migration.sql` - Datos iniciales
- `/config/database.php` - Configuración

### Documentación
- `/DATABASE_STRUCTURE.md` - Estructura detallada
- `/DATABASE_SUMMARY.md` - Resumen ejecutivo
- `/DATABASE_DIAGRAM.md` - Diagramas y relaciones
- `/CRM_QUICK_REFERENCE.md` - Este archivo

### Modelos
- `/app/models/Model.php` - Base
- `/app/models/Customer.php` - Clientes
- `/app/models/Product.php` - Productos
- `/app/models/Order.php` - Órdenes
- `/app/models/League.php` - Ligas
- `/app/models/Admin.php` - Admin
- `/app/models/Cart.php` - Carrito

---

**Última actualización:** 2025-11-06
**Versión:** 1.0
**Status:** Documentación Completa
