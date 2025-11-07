# Sistema de Gestión de Pedidos - CRM Admin Kickverse

## Resumen de Implementación

Se ha implementado un sistema completo de gestión de pedidos para el CRM administrativo de Kickverse, siguiendo el mismo diseño visual y estructura que el módulo de Clientes existente.

---

## Archivos Creados

### 1. Controlador Principal
**Ruta:** `/app/controllers/admin/PedidosController.php`

**Funcionalidades:**
- `index()` - Lista de todos los pedidos con filtros y paginación
- `show($id)` - Obtener detalles completos de un pedido (API JSON)
- `updateStatus($id)` - Actualizar estado del pedido
- `updatePayment($id)` - Actualizar estado de pago
- `updateTracking($id)` - Añadir/actualizar número de tracking
- `cancel($id)` - Cancelar pedido con motivo
- `getOrderTimeline($id)` - Generar timeline del pedido

### 2. Vista Principal
**Ruta:** `/app/views/admin/pedidos/index.php`

**Características:**
- Tabla completa con todos los pedidos
- Filtros por: estado del pedido, estado de pago, tipo de pedido, método de pago
- Buscador por: ID de pedido, nombre de cliente, o tracking number
- Modal detallado con toda la información del pedido
- Timeline visual del estado del pedido
- Acciones rápidas: ver detalles, copiar tracking

---

## Rutas Configuradas

### Rutas Web (Admin)
```php
// Vista principal
GET /admin/pedidos → PedidosController@index
```

### Rutas API (Admin)
```php
// Obtener detalles del pedido
GET /api/admin/pedidos/:id → PedidosController@show

// Actualizar estado del pedido
POST /api/admin/pedidos/:id/status → PedidosController@updateStatus

// Actualizar estado de pago
POST /api/admin/pedidos/:id/payment → PedidosController@updatePayment

// Actualizar tracking
POST /api/admin/pedidos/:id/tracking → PedidosController@updateTracking

// Cancelar pedido
POST /api/admin/pedidos/:id/cancel → PedidosController@cancel
```

---

## Estructura de la Tabla de Pedidos

### Columnas Mostradas:
1. **ID** - Número de pedido con formato #XXX
2. **Cliente** - Avatar, nombre y email/telegram
3. **Productos** - Cantidad de productos y nombres (con tooltip)
4. **Total** - Precio total del pedido en grande y destacado
5. **Estado Pedido** - Badge de color según estado:
   - `pending_payment` - Amarillo (warning)
   - `processing` - Azul (info)
   - `shipped` - Azul (info)
   - `delivered` - Verde (success)
   - `cancelled` - Rojo (danger)
   - `refunded` - Rojo (danger)
6. **Estado Pago** - Badge con estado del pago:
   - `pending` - Amarillo
   - `completed` - Verde
   - `failed` - Rojo
   - `refunded` - Rojo
7. **Método Pago** - Icono y nombre del método:
   - Oxapay (Crypto)
   - Telegram
   - WhatsApp
   - Manual
8. **Fecha** - Fecha y hora del pedido
9. **Acciones** - Botones de acción rápida

---

## Modal de Detalles del Pedido

### Secciones del Modal:

#### 1. Header
- ID del pedido
- Badges de: Estado del pedido, Estado de pago, Tipo de pedido

#### 2. Estadísticas Rápidas
- Número de productos
- Total del pedido
- Cliente

#### 3. Productos del Pedido
- Lista detallada de todos los productos
- Información de: nombre, equipo, liga, talla, cantidad
- Indicadores de: personalización, parches
- Subtotales por producto
- **Resumen de totales:**
  - Subtotal
  - Descuento (si aplica)
  - Envío
  - Total final

#### 4. Dirección de Envío
- Nombre del destinatario
- Dirección completa
- Teléfono de contacto
- Notas adicionales (si hay)

#### 5. Información General
- ID del pedido
- Fecha y hora
- Tipo de pedido
- Origen del pedido (web, telegram, whatsapp, instagram)
- Método de pago

#### 6. Tracking
- Número de seguimiento (con botón de copiar)
- Transportista
- Botón para añadir tracking si no existe

#### 7. Timeline del Pedido
Muestra visualmente el progreso del pedido con:
- Pedido creado
- Pago confirmado
- En preparación
- Enviado (con tracking)
- Entregado
- Cancelado (si aplica)

Cada evento incluye:
- Icono con color según estado
- Fecha y hora
- Información adicional (tracking, etc.)

#### 8. Notas del Administrador
- Notas internas del pedido (si existen)

---

## Funcionalidades Especiales

### 1. Filtros Múltiples
Los filtros son acumulativos y permiten:
- Filtrar por estado del pedido
- Filtrar por estado de pago
- Filtrar por tipo de pedido (catalog, mystery_box, subscription, drop)
- Filtrar por método de pago
- Buscar por ID, nombre de cliente, o tracking

### 2. Actualización de Estado
Desde el modal se puede:
- Cambiar el estado del pedido
- Añadir número de tracking
- Especificar transportista
- Añadir notas del administrador

**Estados disponibles:**
- `pending_payment` - Pago Pendiente
- `processing` - En Proceso
- `shipped` - Enviado (requiere tracking)
- `delivered` - Entregado
- `cancelled` - Cancelado
- `refunded` - Reembolsado

### 3. Gestión de Tracking
- Añadir tracking number automáticamente cambia el estado a "Enviado"
- Se puede especificar el transportista
- Botón de copia rápida del tracking
- Se registra la fecha de envío

### 4. Cancelación de Pedidos
- Requiere especificar un motivo
- Confirmación antes de cancelar
- Se guarda en las notas del administrador
- Registro en audit log

### 5. Sistema de Modal con URL
- Al hacer clic en un pedido, se abre el modal y se añade `?id=123` a la URL
- Permite compartir enlaces directos a pedidos específicos
- El botón "atrás" del navegador cierra el modal correctamente
- Sistema compatible con navegación por historial

### 6. Audit Log
Todas las acciones importantes se registran:
- Cambios de estado
- Actualizaciones de pago
- Añadir tracking
- Cancelaciones

---

## Integración con el Sistema Existente

### Modelo Order.php
Se utiliza el modelo existente en `/app/models/Order.php` que incluye:
- `getOrderWithItems($id)` - Obtiene pedido completo con items, cliente y dirección
- `updateStatus($id, $status, $trackingNumber)` - Actualiza estado
- `updatePaymentStatus($id, $status)` - Actualiza pago
- `cancelOrder($id, $reason)` - Cancela pedido

### Base de Datos
Utiliza las tablas existentes:
- `orders` - Tabla principal de pedidos
- `order_items` - Items del pedido
- `customers` - Información del cliente
- `shipping_addresses` - Dirección de envío
- `products` - Información de productos
- `teams` - Equipos
- `leagues` - Ligas

### CSS y JavaScript
Reutiliza completamente:
- `/public/css/admin/admin-crm.css` - Estilos del CRM
- `/public/js/admin/admin-crm.js` - Funcionalidad de modales y UI

---

## Estilos Visuales

### Badges de Estado
Colores consistentes con el sistema de diseño:
- **Warning** (Amarillo) - Estados pendientes
- **Info** (Azul) - Estados en progreso
- **Success** (Verde) - Estados completados
- **Danger** (Rojo) - Estados cancelados/fallidos

### Iconos
Iconos específicos para cada elemento:
- `fa-shopping-bag` - Pedidos
- `fa-clock` - Pago pendiente
- `fa-box` - En proceso
- `fa-shipping-fast` - Enviado
- `fa-check-circle` - Entregado
- `fa-times-circle` - Cancelado
- `fa-credit-card` - Pagos
- `fa-bitcoin` - Oxapay
- `fab fa-telegram` - Telegram
- `fab fa-whatsapp` - WhatsApp

### Timeline Visual
- Línea vertical conectando eventos
- Círculos de colores para cada estado
- Cards con información detallada
- Indicador visual de completado

---

## Responsive Design

El diseño es completamente responsive:
- En desktop: tabla completa con todas las columnas
- En tablet: algunas columnas se adaptan
- En mobile: filtros se apilan verticalmente, tabla se adapta

---

## Seguridad

### Autenticación
- Todas las rutas verifican sesión de administrador
- Redirección a login si no está autenticado

### Audit Log
- Se registra el admin_id en cada acción
- Se guarda la IP del usuario
- Se registran valores antiguos y nuevos

### Validaciones
- Validación de estados permitidos
- Verificación de existencia del pedido
- Control de permisos por rol (preparado para futuro)

---

## Próximos Pasos Sugeridos

1. **Exportación de Pedidos**
   - Botón para exportar a CSV/Excel
   - Filtros aplicados en la exportación

2. **Impresión de Etiquetas**
   - Generar PDF con etiqueta de envío
   - Incluir código de barras del tracking

3. **Notificaciones Automáticas**
   - Enviar email/telegram al cliente cuando cambia el estado
   - Notificación cuando se añade tracking

4. **Estadísticas en el Dashboard**
   - Widget de pedidos pendientes
   - Gráfico de pedidos por estado
   - Alertas de pedidos sin tracking después de X días

5. **Filtros Avanzados**
   - Filtrar por rango de fechas
   - Filtrar por rango de precios
   - Filtrar por cliente específico

6. **Búsqueda Avanzada**
   - Buscar por producto específico
   - Buscar por equipo/liga
   - Buscar por dirección de envío

---

## Testing

Para probar el sistema:

1. **Acceder al módulo:**
   - Ir a `/admin/pedidos`
   - Ver la lista completa de pedidos

2. **Probar filtros:**
   - Seleccionar diferentes estados
   - Usar el buscador
   - Combinar múltiples filtros

3. **Abrir modal:**
   - Click en cualquier fila
   - Verificar que se carga la información completa
   - Verificar que la URL se actualiza

4. **Actualizar estado:**
   - Click en "Actualizar Estado"
   - Cambiar a "shipped" y añadir tracking
   - Verificar que se actualiza correctamente

5. **Copiar tracking:**
   - Click en el botón de copiar
   - Verificar notificación de éxito

6. **Cancelar pedido:**
   - Intentar cancelar un pedido
   - Añadir motivo
   - Confirmar cancelación

---

## Soporte

Para cualquier duda o problema:
- Revisar logs en `/var/log/`
- Verificar configuración de rutas en `/routes/web.php`
- Comprobar permisos de archivos
- Revisar consola del navegador para errores JavaScript

---

**Implementación completada el:** 2025-11-06
**Versión:** 1.0
**Desarrollado para:** Kickverse CRM Admin
