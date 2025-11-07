# Configuración de Rutas para Suscripciones

## Rutas que deben agregarse al archivo `routes/web.php`

Agregar las siguientes rutas en la sección **ADMIN ROUTES** después de la línea 131:

```php
// Admin Suscripciones
$router->get('/admin/suscripciones', 'SuscripcionesController@index');
$router->get('/api/admin/suscripciones/:id', 'SuscripcionesController@show');
$router->post('/admin/suscripciones/pause/:id', 'SuscripcionesController@pause');
$router->post('/admin/suscripciones/cancel/:id', 'SuscripcionesController@cancel');
$router->post('/admin/suscripciones/reactivate/:id', 'SuscripcionesController@reactivate');
```

## Explicación de las Rutas

1. **GET `/admin/suscripciones`**
   - Muestra la página principal con el listado de todas las suscripciones
   - Soporta filtros por estado, plan y búsqueda por cliente
   - Controlador: `SuscripcionesController@index`

2. **GET `/api/admin/suscripciones/:id`**
   - API endpoint para obtener detalles completos de una suscripción
   - Retorna JSON con: datos de suscripción, historial de pagos, envíos
   - Usado por el modal de detalles
   - Controlador: `SuscripcionesController@show`

3. **POST `/admin/suscripciones/pause/:id`**
   - Pausa una suscripción activa
   - Parámetros: `reason` (opcional)
   - Controlador: `SuscripcionesController@pause`

4. **POST `/admin/suscripciones/cancel/:id`**
   - Cancela una suscripción
   - Parámetros: `reason` (opcional)
   - Controlador: `SuscripcionesController@cancel`

5. **POST `/admin/suscripciones/reactivate/:id`**
   - Reactiva una suscripción pausada o cancelada
   - Controlador: `SuscripcionesController@reactivate`

## Verificación

El sistema ya está configurado para:
- ✅ El JavaScript del admin (`public/js/admin/admin-crm.js`) ya detecta rutas de suscripciones (línea 165-166)
- ✅ El layout del admin (`app/views/layouts/admin.php`) ya tiene el menú de Suscripciones (línea 48-52)
- ✅ Los archivos del modelo, controlador y vista están creados y funcionando

**Solo falta agregar las rutas al archivo `routes/web.php`**
