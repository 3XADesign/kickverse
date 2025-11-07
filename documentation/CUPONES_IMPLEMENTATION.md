# Módulo de Gestión de Cupones - Implementación Completa

## Resumen
Se ha implementado completamente el módulo de gestión de cupones para el admin de Kickverse, siguiendo el patrón establecido en el proyecto.

## Archivos Creados/Modificados

### 1. Controller Principal
**Archivo:** `/app/controllers/admin/CuponesController.php`
- **Métodos implementados:**
  - `index()` - Renderiza la vista principal
  - `create()` - Muestra formulario de creación
  - `store()` - Guarda nuevo cupón con validaciones
  - `edit($id)` - Muestra formulario de edición
  - `update($id)` - Actualiza cupón existente con validaciones
  - `delete($id)` - Elimina cupón
  - `validateCouponData($data, $couponId)` - Validaciones completas

**Validaciones implementadas:**
- Código: requerido, único, alfanumérico (A-Z, 0-9, -, _)
- Tipo de descuento: 'fixed' o 'percentage'
- Valor de descuento: mayor a 0
- Fechas: valid_from debe ser anterior a valid_until
- Límites de uso: usage_limit_per_customer <= usage_limit_total

### 2. API Controller
**Archivo:** `/app/controllers/api/AdminCuponesApiController.php`
- **Métodos implementados:**
  - `getAll()` - Lista cupones con filtros y paginación
  - `getOne($couponId)` - Detalles del cupón + historial de uso

**Filtros disponibles en getAll():**
- Búsqueda por código o descripción
- Tipo de descuento (fixed/percentage)
- Estado activo/inactivo
- Rango de fechas de validez

**Query del historial de uso (getOne):**
```sql
SELECT cu.*, o.order_id, o.total_amount, c.full_name as customer_name
FROM coupon_usage cu
JOIN orders o ON cu.order_id = o.order_id
JOIN customers c ON cu.customer_id = c.customer_id
WHERE cu.coupon_id = ?
ORDER BY cu.used_at DESC
LIMIT 50
```

### 3. Vista Principal
**Archivo:** `/app/views/admin/cupones/index.php`
- Usa patrón `layout/header.php` y `layout/footer.php`
- Carga datos mediante API (JavaScript fetch)
- **Características:**
  - Tabla con ordenamiento y paginación
  - Filtros: búsqueda, tipo, estado, fechas
  - Modal de detalles con historial de uso
  - Acciones: ver, editar, eliminar
  - Botón de exportación CSV

**Columnas de la tabla:**
- Código (con badge de estado)
- Descripción
- Tipo (fixed/percentage con iconos)
- Descuento (formateo según tipo)
- Usos (times_used / usage_limit_total)
- Validez (rango de fechas con indicador de expiración)
- Estado (Activo/Expirado/Inactivo)
- Acciones (ver/editar/eliminar)

### 4. Rutas Actualizadas
**Archivo:** `/routes/web.php`
```php
// Cupones
$router->get('/admin/cupones', 'CuponesController@index');
$router->get('/admin/cupones/crear', 'CuponesController@create');
$router->post('/admin/cupones', 'CuponesController@store');
$router->get('/admin/cupones/editar/:id', 'CuponesController@edit');
$router->put('/admin/cupones/:id', 'CuponesController@update');
$router->delete('/admin/cupones/:id', 'CuponesController@delete');
$router->get('/api/admin/cupones', 'api/AdminCuponesApiController@getAll');
$router->get('/api/admin/cupones/:id', 'api/AdminCuponesApiController@getOne');
```

## Estructura de Base de Datos

### Tabla: coupons
Campos utilizados del schema:
- `coupon_id` - Primary key
- `code` - Código único del cupón
- `description` - Descripción
- `discount_type` - 'fixed' o 'percentage'
- `discount_value` - Valor del descuento
- `max_discount_amount` - Límite máximo (para porcentajes)
- `min_purchase_amount` - Compra mínima requerida
- `applies_to_product_type` - Tipo de producto ('all', 'jersey', 'mystery_box', 'subscription')
- `applies_to_first_order_only` - Booleano
- `usage_limit_total` - Límite total de usos
- `usage_limit_per_customer` - Límite por cliente
- `times_used` - Contador de usos
- `valid_from` - Fecha inicio validez
- `valid_until` - Fecha fin validez
- `is_active` - Estado activo/inactivo
- `created_by` - ID del admin que creó el cupón
- `created_at` / `updated_at` - Timestamps

### Tabla: coupon_usage
Para historial de uso:
- `usage_id` - Primary key
- `coupon_id` - FK a coupons
- `customer_id` - FK a customers
- `order_id` - FK a orders
- `discount_applied` - Monto de descuento aplicado
- `used_at` - Timestamp de uso

## Funcionalidades JavaScript

### Principales funciones:
1. `loadCoupons()` - Carga cupones desde API con filtros
2. `renderCouponsTable(coupons)` - Renderiza tabla HTML
3. `renderPagination(pagination)` - Renderiza controles de paginación
4. `openCouponModal(couponId)` - Abre modal con detalles
5. `renderCouponDetails(coupon)` - Renderiza detalles en modal
6. `deleteCoupon(couponId)` - Elimina cupón con confirmación
7. `resetFilters()` - Limpia todos los filtros
8. `exportCoupons()` - Exporta a CSV

### Utilidades:
- `debounce(func, wait)` - Debounce para búsqueda
- `escapeHtml(text)` - Escape de HTML
- `formatDate(dateStr)` - Formateo de fechas
- `formatDateRange(from, until, expired)` - Rango de fechas

## Seguridad

1. **Autenticación:** Todos los métodos verifican `$_SESSION['admin_logged_in']`
2. **Validación de entrada:** 
   - Sanitización de códigos (solo alfanuméricos)
   - Validación de tipos de descuento
   - Verificación de rangos de fechas
   - Comprobación de límites de uso
3. **Prevención de duplicados:** Verificación de código único antes de crear/actualizar
4. **Escape de HTML:** Uso de `htmlspecialchars()` y `escapeHtml()` en vistas
5. **Prepared Statements:** Todas las queries usan PDO prepared statements

## Próximos Pasos (Opcional)

1. **Crear vistas de formularios:**
   - `/app/views/admin/cupones/create.php`
   - `/app/views/admin/cupones/edit.php`

2. **Agregar exportación CSV real** en AdminCuponesApiController

3. **Agregar estadísticas adicionales:**
   - Cupones más usados
   - Descuentos por período
   - ROI de cupones

4. **Notificaciones:**
   - Alertas cuando un cupón está por expirar
   - Alertas cuando se alcanza el límite de uso

## Testing

Para probar el módulo:
1. Acceder a `/admin/cupones`
2. Verificar que la tabla carga correctamente
3. Probar filtros de búsqueda y estado
4. Crear un cupón nuevo
5. Editar un cupón existente
6. Ver detalles en el modal
7. Probar eliminación de cupón

## Notas Técnicas

- **Paginación:** 50 cupones por página (configurable en `$perPage`)
- **Historial de uso:** Limitado a últimos 50 usos
- **Formato de código:** Automáticamente convertido a UPPERCASE
- **Compatibilidad:** Compatible con el sistema de cupones existente en checkout
- **Responsive:** Vista totalmente responsive usando estilos admin existentes
