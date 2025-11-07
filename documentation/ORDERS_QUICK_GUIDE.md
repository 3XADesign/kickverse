# Guía Rápida - Sección "Mis Pedidos"

## ✅ Archivos Creados

1. **Vista:** `/app/views/account/orders.php`
2. **CSS:** `/public/css/account-orders.css`
3. **Documentación:** `/ORDERS_SECTION_README.md`

## ✅ Archivos Modificados

1. **Rutas:** `/routes/web.php`
   - Añadidas rutas API: `/api/account/orders` y `/api/account/orders/:id`

2. **Modelo:** `/app/models/Order.php`
   - Método `getCustomerOrders()` mejorado con contador de items
   - Método `getCustomerOrdersFiltered()` añadido
   - Método `getOrderWithItems()` mejorado con imágenes

3. **Controlador:** `/app/controllers/AccountPageController.php`
   - Ya existían los métodos necesarios

## 🚀 Cómo Usar

### Frontend
```
URL: https://kickverse.com/mis-pedidos
```

### API Endpoints
```bash
# Listar pedidos del usuario
GET /api/orders
Headers: Cookie con sesión autenticada

# Ver detalles de un pedido
GET /api/orders/123
Headers: Cookie con sesión autenticada
```

## 🎨 Características

### Lista de Pedidos
- Cards responsive (mobile-first)
- Filtro por estado (todos, pendiente, proceso, enviado, entregado, cancelado)
- Buscador por número de pedido o tracking
- Paginación (10 pedidos por página)
- Badges de color según estado

### Modal de Detalles
- Timeline visual del proceso
- Lista de productos con imágenes
- Dirección de envío completa
- Información de tracking (si existe)
- Resumen de totales con descuentos
- Método de pago

## 🎯 Estados del Pedido

| Estado | Color | Icono |
|--------|-------|-------|
| pending_payment | Amarillo | clock |
| processing | Azul | cog |
| shipped | Azul | shipping-fast |
| delivered | Verde | check-circle |
| cancelled | Rojo | times-circle |
| refunded | Rojo | undo |

## 📱 Responsive

- **Móvil (320px+):** Cards en columna, filtros apilados
- **Tablet (768px+):** Cards mejorados, filtros en fila
- **Desktop (1024px+):** Vista completa expandida

## 🔐 Seguridad

- Requiere autenticación (`$this->requireAuth()`)
- Verifica que el pedido pertenece al cliente
- CSRF tokens en formularios
- Escape de HTML en renderizado

## 🐛 Debugging

### Frontend (JavaScript)
```javascript
// En la consola del navegador
localStorage.setItem('debug_orders', 'true');
location.reload();
```

### Backend (PHP)
```php
// En OrderController.php o Order.php
error_log('Orders loaded: ' . print_r($orders, true));
```

## 📝 Próximos Pasos

1. **Probar en navegador:**
   - Ir a `/mis-pedidos`
   - Verificar que se cargan los pedidos
   - Probar filtros y búsqueda
   - Abrir modal de detalles
   - Verificar responsive en móvil

2. **Verificar base de datos:**
   - Ejecutar consultas de prueba
   - Verificar que existen pedidos para el usuario de prueba
   - Revisar que las imágenes de productos existan

3. **Opcional - Mejoras futuras:**
   - PDF de facturas
   - Cancelación de pedidos
   - Reordenar (volver a comprar)
   - Valoraciones de productos

## 🆘 Troubleshooting

### "No tienes pedidos aún"
- Verificar que el usuario tiene pedidos en la BD
- Revisar que la sesión está activa
- Check API endpoint: `/api/orders` devuelve datos

### Modal no se abre
- Verificar que el CSS está cargado
- Check errores en consola del navegador
- Verificar que el ID del modal es correcto

### Imágenes no cargan
- Verificar ruta: `/storage/products/` o `/images/products/`
- Revisar tabla `product_images`
- Verificar permisos de carpeta

### API retorna error 403
- Usuario no autenticado
- Sesión expirada
- Verificar cookies

### API retorna error 404
- Pedido no existe
- ID incorrecto
- Verificar rutas en `web.php`

## 💡 Tips

1. **Testing rápido:** Crear pedidos de prueba en la BD
2. **Estilos:** Personalizar colores en CSS variables
3. **Traducciones:** Editar textos directamente en `orders.php`
4. **Analytics:** Añadir eventos de GA en los botones

---

**Todo listo para usar! 🎉**
