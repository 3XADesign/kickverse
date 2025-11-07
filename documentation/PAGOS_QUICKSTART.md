# Quick Start - Sistema de Pagos

## Instalación Rápida

### 1. Verificar Archivos Creados
```bash
✓ app/controllers/admin/PagosController.php
✓ app/views/admin/pagos/index.php
✓ app/models/PaymentTransaction.php
```

### 2. Configurar Rutas

**Opción A: Agregar al archivo de rutas existente**
```php
// En routes/admin.php
require_once __DIR__ . '/../app/controllers/admin/PagosController.php';

$router->get('/admin/pagos', function() {
    $controller = new PagosController();
    $controller->index();
});

// Ver documentación completa en PAGOS_ROUTES_EXAMPLE.php
```

**Opción B: Si no tienes sistema de rutas**
Crear archivo `/admin/pagos.php`:
```php
<?php
session_start();
require_once __DIR__ . '/../app/controllers/admin/PagosController.php';

$controller = new PagosController();
$controller->index();
```

### 3. Agregar al Menú del CRM

En tu archivo de layout (ejemplo: `app/views/layouts/admin.php`):

```php
<!-- En el sidebar -->
<nav class="crm-sidebar">
    <!-- ... otros links ... -->

    <a href="/admin/pagos" class="nav-link <?= $current_page === 'pagos' ? 'active' : '' ?>">
        <i class="fas fa-credit-card"></i>
        <span>Pagos</span>
    </a>

    <!-- ... más links ... -->
</nav>
```

### 4. Verificar Base de Datos

La tabla `payment_transactions` debe existir. Si no:
```sql
-- Ejecutar el schema completo que está en database/schema.sql
-- O al menos la tabla payment_transactions (líneas 728-777)
```

### 5. Probar la Instalación

1. Acceder a: `http://tu-dominio/admin/pagos`
2. Deberías ver la página de gestión de pagos
3. Si hay error 404: verificar las rutas
4. Si hay error de base de datos: verificar la tabla existe

## Uso Básico

### Ver Transacciones
- Acceder a `/admin/pagos`
- Ver lista completa con filtros
- Hacer clic en una fila para ver detalles

### Filtrar Transacciones
1. **Por estado**: Dropdown "Todos los estados"
2. **Por método**: Dropdown "Todos los métodos"
3. **Por fecha**: Botón "Filtrar por Fecha"
4. **Por búsqueda**: Campo de texto para buscar

### Aprobar Pago Manual
1. Filtrar por método manual (Telegram/WhatsApp/Transferencia)
2. Click en transacción pendiente
3. Revisar comprobante de pago
4. Click en "Marcar como Completado"
5. Confirmar acción

### Procesar Reembolso
1. Buscar transacción completada
2. Abrir detalles
3. Click en "Procesar Reembolso"
4. Confirmar (acción no reversible)

## Métodos de Pago Soportados

| Método | Código | Verificación |
|--------|--------|--------------|
| Bitcoin | `oxapay_btc` | Automática vía webhook |
| Ethereum | `oxapay_eth` | Automática vía webhook |
| USDT | `oxapay_usdt` | Automática vía webhook |
| Telegram Manual | `telegram_manual` | Manual por admin |
| WhatsApp Manual | `whatsapp_manual` | Manual por admin |
| Transferencia | `bank_transfer` | Manual por admin |

## Estados de Transacción

| Estado | Descripción | Color |
|--------|-------------|-------|
| `pending` | Esperando confirmación | Amarillo |
| `processing` | Siendo procesado | Azul |
| `completed` | Pago confirmado | Verde |
| `failed` | Pago fallido | Rojo |
| `expired` | Tiempo expirado | Gris |
| `refunded` | Reembolsado | Rojo |

## Acciones por Estado

### Estado: Pending / Processing
- ✓ Marcar como completado
- ✓ Marcar como fallido

### Estado: Completed
- ✓ Procesar reembolso

### Estado: Failed / Expired / Refunded
- Solo vista (sin acciones)

## API Endpoints

```javascript
// Obtener detalles de transacción
GET /admin/pagos/show/{id}
Response: JSON con todos los detalles

// Marcar como completado
POST /admin/pagos/{id}/mark-completed
Response: {success: true, message: "..."}

// Marcar como fallido
POST /admin/pagos/{id}/mark-failed
Response: {success: true, message: "..."}

// Procesar reembolso
POST /admin/pagos/{id}/refund
Response: {success: true, message: "..."}
```

## Integración con JavaScript

El sistema ya incluye funciones JavaScript:

```javascript
// Abrir modal de detalles
openPagoModal(transactionId)

// Marcar como completado
markAsCompleted(transactionId)

// Marcar como fallido
markAsFailed(transactionId)

// Procesar reembolso
processRefund(transactionId)

// Aplicar filtros de fecha
applyDateFilters()

// Limpiar filtros de fecha
clearDateFilters()
```

## Personalización

### Cambiar colores de estados
En el archivo `index.php`, modificar:
```php
$statusColors = [
    'pending' => 'warning',    // Cambiar a 'info', 'success', etc.
    'completed' => 'success',
    // ...
];
```

### Agregar campos al modal
En la función `renderModalContent()` del JavaScript:
```javascript
window.renderModalContent = function(data) {
    return `
        <div class="modal-pago-details">
            ${/* Tu contenido adicional */}
            ${/* Los campos de data están disponibles */}
        </div>
    `;
};
```

### Modificar filtros
En el controlador `PagosController.php`, método `index()`:
```php
// Agregar nuevo filtro
if (isset($_GET['mi_filtro']) && $_GET['mi_filtro'] !== '') {
    $whereConditions[] = "pt.mi_campo = ?";
    $params[] = $_GET['mi_filtro'];
}
```

## Troubleshooting

### Error: "Class PagosController not found"
- Verificar que el archivo existe en `app/controllers/admin/`
- Verificar el `require_once` en las rutas

### Error: "Table payment_transactions doesn't exist"
- Ejecutar el schema de base de datos
- Verificar conexión a la base de datos correcta

### Modal no se abre
- Verificar que `crmAdmin` object existe
- Verificar en la consola del navegador errores JS
- Verificar que la ruta `/admin/pagos/show/{id}` funciona

### Botones no responden
- Abrir consola del navegador (F12)
- Verificar que no hay errores JavaScript
- Verificar que las rutas POST están configuradas

### Estilos no se ven bien
- Verificar que el layout admin carga el CSS principal
- Verificar que Font Awesome está cargado
- Verificar que las variables CSS existen (--primary, --success, etc.)

## Seguridad

### Checklist de Seguridad
- [ ] Solo admins autenticados pueden acceder
- [ ] Todas las acciones requieren confirmación
- [ ] Se registra en audit_log cada cambio
- [ ] Los datos sensibles solo son visibles por admins
- [ ] Las queries usan prepared statements
- [ ] Los outputs usan htmlspecialchars()

### Permisos Recomendados
```php
// Agregar verificación de permisos específicos
if (!$_SESSION['admin_can_manage_payments']) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
```

## Próximos Pasos

1. **Testing**: Crear transacciones de prueba y verificar cada flujo
2. **Dashboard**: Agregar métricas al dashboard principal
3. **Notificaciones**: Enviar emails/Telegram cuando cambia estado
4. **Webhooks**: Implementar procesamiento automático de Oxapay
5. **Reportes**: Exportar a Excel/CSV
6. **Auditoría**: Revisar el audit_log periódicamente

## Soporte

Para más información:
- Leer `PAGOS_IMPLEMENTATION.md` - Documentación completa
- Revisar `PAGOS_ROUTES_EXAMPLE.php` - Ejemplos de rutas
- Revisar los comentarios en el código fuente
