# Sección "Mis Pedidos" - Documentación

## Descripción General

Se ha implementado completamente la sección **"Mis Pedidos"** del panel de usuario de Kickverse, permitiendo a los clientes consultar el historial y estado de sus pedidos.

---

## Archivos Creados y Modificados

### 1. Vista Principal
**Archivo:** `/app/views/account/orders.php`

**Características:**
- Lista completa de pedidos del cliente con paginación
- Filtros por estado (Pendiente, En proceso, Enviado, Entregado, Cancelado)
- Buscador por número de pedido o tracking
- Cards responsive adaptadas a móvil
- Modal de detalles del pedido con información completa
- Timeline visual del estado del pedido
- Integración con API REST

**Estructura de la vista:**
```
├── Header con título y botón "Volver"
├── Sección de filtros
│   ├── Filtro por estado (dropdown)
│   └── Buscador (input)
├── Lista de pedidos
│   ├── Loading state
│   ├── Empty state
│   └── Cards de pedidos
└── Modal de detalles
    ├── Header con número y estado
    ├── Timeline visual
    ├── Lista de productos
    ├── Resumen de totales
    ├── Dirección de envío
    ├── Información de tracking
    └── Método de pago
```

### 2. Estilos CSS
**Archivo:** `/public/css/account-orders.css`

**Características:**
- Diseño mobile-first totalmente responsive
- Sistema de colores semántico para estados
- Animaciones suaves y transiciones
- Soporte para dark mode
- Estilos de impresión optimizados
- Accesibilidad mejorada (focus states)

**Estados con colores:**
- `pending_payment` → Amarillo (warning)
- `processing` → Azul (info)
- `shipped` → Azul (info)
- `delivered` → Verde (success)
- `cancelled` → Rojo (danger)
- `refunded` → Rojo (danger)

### 3. Controlador
**Archivo:** `/app/controllers/AccountPageController.php`

**Métodos:**
- `orders()` - Renderiza la vista de lista de pedidos
- `orderDetail($orderId)` - Renderiza los detalles de un pedido específico (protegido por autenticación)

### 4. Modelo Order
**Archivo:** `/app/models/Order.php`

**Métodos añadidos/modificados:**
- `getCustomerOrders($customerId, $limit)` - Obtiene pedidos con contador de items
- `getCustomerOrdersFiltered($customerId, $status, $search, $limit)` - Obtiene pedidos con filtros
- `getOrderWithItems($orderId)` - Obtiene pedido completo con items, imágenes, dirección de envío

**Mejoras:**
- Inclusión de imágenes de productos en las consultas
- Contador de items por pedido
- Soporte para filtrado y búsqueda

### 5. Rutas
**Archivo:** `/routes/web.php`

**Rutas añadidas:**
```php
// API
GET  /api/account/orders      → OrderController@index
GET  /api/account/orders/:id  → OrderController@show

// Frontend
GET  /mis-pedidos             → AccountPageController@orders
GET  /mis-pedidos/:id         → AccountPageController@orderDetail
```

---

## Funcionalidades Implementadas

### 1. Lista de Pedidos
- ✅ Vista en cards responsive (no tablas en móvil)
- ✅ Información mostrada por pedido:
  - Número de pedido (#123)
  - Fecha del pedido
  - Estado con badge de color
  - Total del pedido (€)
  - Número de tracking (si existe)
- ✅ Botones de acción:
  - "Ver Detalles" → Abre modal con información completa
  - "Rastrear" → Enlace a Correos (si hay tracking)
- ✅ Ordenados del más reciente al más antiguo

### 2. Filtros y Búsqueda
- ✅ Filtro por estado (dropdown con todos los estados)
- ✅ Buscador en tiempo real por:
  - Número de pedido
  - Número de tracking
- ✅ Actualización instantánea de resultados (debounce 300ms)

### 3. Modal de Detalles
- ✅ Información del pedido:
  - Número y fecha
  - Estado actual
- ✅ Timeline visual del proceso:
  1. Pedido realizado ✓
  2. Pago confirmado ✓
  3. En preparación ✓ (si processing)
  4. Enviado ✓ (si shipped)
  5. Entregado ✓ (si delivered)
- ✅ Lista completa de productos:
  - Imagen del producto
  - Nombre y equipo
  - Talla/variante
  - Cantidad
  - Precio unitario
  - Subtotal
  - Personalización (si aplica)
  - Parches oficiales (si aplica)
- ✅ Resumen de totales:
  - Subtotal
  - Descuento (si aplica)
  - Envío
  - **Total**
- ✅ Dirección de envío completa
- ✅ Información de tracking:
  - Número de seguimiento
  - Empresa de transporte
  - Botón para rastrear envío
- ✅ Método de pago usado
- ✅ Estado del pago

### 4. Estados y Notificaciones
- ✅ Loading state mientras carga
- ✅ Empty state si no hay pedidos
- ✅ Error handling con mensajes claros

### 5. Responsive Design
- ✅ Mobile-first (optimizado para móvil)
- ✅ Tablet (768px+): Layout mejorado
- ✅ Desktop (1024px+): Vista completa expandida
- ✅ Cards que se adaptan perfectamente

---

## API Endpoints

### 1. GET `/api/orders`
**Descripción:** Obtiene todos los pedidos del cliente autenticado

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "order_id": 123,
      "customer_id": 45,
      "order_status": "delivered",
      "payment_status": "completed",
      "total_amount": "79.98",
      "order_date": "2025-11-01 10:30:00",
      "tracking_number": "AB123456789ES",
      "item_count": 2
    }
  ]
}
```

### 2. GET `/api/orders/:id`
**Descripción:** Obtiene detalles completos de un pedido específico

**Parámetros:**
- `:id` - ID del pedido

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "order_id": 123,
    "customer_id": 45,
    "order_status": "delivered",
    "payment_status": "completed",
    "subtotal": "74.98",
    "discount_amount": "0.00",
    "shipping_cost": "5.00",
    "total_amount": "79.98",
    "order_date": "2025-11-01 10:30:00",
    "tracking_number": "AB123456789ES",
    "payment_method": "oxapay",
    "items": [
      {
        "order_item_id": 234,
        "product_id": 12,
        "product_name": "Camiseta Real Madrid 24/25",
        "product_slug": "real-madrid-home-2024-25",
        "team_name": "Real Madrid",
        "league_name": "LaLiga EA Sports",
        "size": "L",
        "quantity": 1,
        "unit_price": "24.99",
        "has_patches": true,
        "has_personalization": true,
        "personalization_name": "MBAPPÉ",
        "personalization_number": "9",
        "subtotal": "29.97",
        "image_path": "/storage/products/real-madrid-home.jpg"
      }
    ],
    "shipping_address": {
      "recipient_name": "Juan Pérez",
      "street_address": "Calle Mayor 123",
      "city": "Madrid",
      "postal_code": "28001",
      "province": "Madrid",
      "country": "España",
      "phone": "+34 612345678"
    }
  }
}
```

---

## Base de Datos

### Tablas Utilizadas

1. **`orders`** - Pedidos principales
   - `order_id` (PK)
   - `customer_id` (FK)
   - `order_status` (ENUM)
   - `payment_status` (ENUM)
   - `total_amount`
   - `order_date`
   - `tracking_number`
   - `shipping_address_id`

2. **`order_items`** - Items del pedido
   - `order_item_id` (PK)
   - `order_id` (FK)
   - `product_id` (FK)
   - `variant_id` (FK)
   - `quantity`
   - `unit_price`
   - `has_patches`
   - `has_personalization`
   - `personalization_name`
   - `personalization_number`

3. **`shipping_addresses`** - Direcciones de envío
   - `address_id` (PK)
   - `customer_id` (FK)
   - `recipient_name`
   - `street_address`
   - `city`, `province`, `postal_code`
   - `phone`

4. **`products`** - Información de productos
5. **`product_variants`** - Variantes (tallas)
6. **`product_images`** - Imágenes de productos
7. **`teams`** - Equipos
8. **`leagues`** - Ligas

---

## JavaScript - Clase OrdersManager

### Métodos Principales

```javascript
class OrdersManager {
  // Inicialización
  constructor()
  init()
  setupEventListeners()

  // Carga de datos
  loadOrders()

  // Filtrado
  filterOrders()

  // Renderizado
  renderOrders()
  renderOrderCard(order)
  renderOrderDetails(order)
  renderOrderItem(item)
  renderTimeline(order)

  // UI
  updatePagination()
  viewOrderDetails(orderId)
  getStatusBadge(status)
  getPaymentMethodName(method)
  getPaymentStatusName(status)
  showError(message)
}

// Funciones globales
closeOrderModal()
```

### Event Listeners

1. **Filtro por estado:** Change event en el select
2. **Búsqueda:** Input event con debounce (300ms)
3. **Paginación:** Click en botones prev/next
4. **Ver detalles:** Click en botones de cada card
5. **Cerrar modal:**
   - Click en overlay
   - Tecla ESC
   - Botón cerrar

---

## Integración con el Sistema

### Autenticación
- Todas las rutas requieren autenticación
- Verificación de que el pedido pertenece al cliente
- Protección contra acceso no autorizado

### Sesión
```php
$user = $this->getUser(); // Obtiene usuario de sesión
$customerId = $user['customer_id'];
```

### CSRF Protection
```php
'csrf_token' => $this->generateCSRF()
```

---

## Próximos Pasos Recomendados

### 1. Funcionalidades Adicionales
- [ ] Exportar pedido a PDF (factura)
- [ ] Reordenar pedido (volver a comprar)
- [ ] Cancelar pedido (si está pendiente)
- [ ] Solicitar devolución
- [ ] Dejar valoración del producto

### 2. Notificaciones
- [ ] Notificación cuando cambia el estado
- [ ] Notificación cuando se añade tracking
- [ ] Email con detalles del pedido

### 3. Mejoras de UX
- [ ] Guardar filtros en localStorage
- [ ] Animación de transición entre estados
- [ ] Chat de soporte desde el pedido
- [ ] Vista previa rápida (hover)

### 4. Analytics
- [ ] Tracking de eventos en Google Analytics:
  - Ver lista de pedidos
  - Ver detalles de pedido
  - Usar tracking
  - Filtrar pedidos

---

## Testing

### Casos de Prueba

1. **Sin pedidos:**
   - Usuario nuevo sin pedidos
   - Debe mostrar empty state

2. **Con pedidos:**
   - Lista correcta de pedidos
   - Ordenados por fecha DESC
   - Información correcta

3. **Filtros:**
   - Filtro por cada estado
   - Búsqueda por número
   - Búsqueda por tracking
   - Limpiar filtros

4. **Modal:**
   - Abrir detalles
   - Timeline correcto según estado
   - Productos con imágenes
   - Dirección completa
   - Tracking si existe
   - Cerrar con ESC/overlay/botón

5. **Responsive:**
   - Móvil (320px - 767px)
   - Tablet (768px - 1023px)
   - Desktop (1024px+)

6. **Errores:**
   - Error de red
   - Pedido no encontrado
   - Sin autorización
   - Timeout de sesión

---

## Notas de Implementación

### Seguridad
- ✅ Autenticación obligatoria
- ✅ Verificación de propiedad del pedido
- ✅ CSRF token en formularios
- ✅ Escape de HTML en renderizado

### Performance
- ✅ Lazy loading de detalles (solo al abrir modal)
- ✅ Paginación (10 pedidos por página)
- ✅ Debounce en búsqueda (300ms)
- ✅ Queries optimizadas con JOINs

### Accesibilidad
- ✅ Focus states visibles
- ✅ Navegación por teclado
- ✅ ARIA labels
- ✅ Textos alternativos en imágenes

### SEO
- ✅ Títulos semánticos (h1-h6)
- ✅ Estructura HTML correcta
- ✅ Meta tags apropiados

---

## Soporte y Mantenimiento

### Logs
Errores se pueden rastrear en:
- Console del navegador (JavaScript)
- Logs del servidor PHP
- Network tab (API calls)

### Debugging
```javascript
// Habilitar modo debug
localStorage.setItem('debug_orders', 'true');

// En OrdersManager, añadir:
if (localStorage.getItem('debug_orders')) {
  console.log('Orders loaded:', this.orders);
  console.log('Filtered orders:', this.filteredOrders);
}
```

---

## Contacto

Para soporte técnico o preguntas sobre esta implementación, contactar al equipo de desarrollo.

---

**Última actualización:** 6 de noviembre de 2025
**Versión:** 1.0.0
**Autor:** Claude Code (Anthropic AI)
