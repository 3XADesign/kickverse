# Sistema de Gestión de Productos - CRM Admin Kickverse

## Resumen de Implementación

Se ha implementado un sistema completo de gestión de productos para el CRM admin de Kickverse, siguiendo el mismo patrón y estilo visual del módulo de Clientes existente.

---

## Archivos Creados

### 1. Vista Principal
**Ubicación:** `/app/views/admin/productos/index.php`

**Características:**
- Tabla completa con columnas: ID, Imagen, Nombre, Equipo/Liga, Tipo, Precio, Stock, Estado, Acciones
- Sistema de filtros en tiempo real:
  - Tipo de producto (Camiseta, Accesorio, Mystery Box, Suscripción)
  - Liga
  - Estado (Activo/Inactivo)
  - Buscador de texto
- Paginación automática
- Modal de detalles con URL persistente
- Diseño responsive mobile-first
- Badges de colores Kickverse (primary: #b054e9, accent: #ec4899)

**Elementos visuales especiales:**
- Imágenes de productos con placeholder si no hay imagen
- Descuentos calculados automáticamente
- Stock con colores: verde (>20), amarillo (6-20), rojo (≤5)
- Badges para producto destacado
- Logos de equipos y ligas

### 2. Controlador
**Ubicación:** `/app/controllers/admin/ProductosController.php`

**Métodos implementados:**
- `index()` - Lista de productos con paginación y filtros
- `show($id)` - Detalles de un producto (API JSON para modal)
- `create()` - Formulario de creación (placeholder)
- `edit($id)` - Formulario de edición (placeholder)
- `store()` - Guardar nuevo producto (placeholder)
- `update($id)` - Actualizar producto (placeholder)
- `delete($id)` - Eliminar producto (placeholder)

**Métodos privados:**
- `getProductosWithDetails()` - Obtiene productos con JOIN a ligas, equipos e imágenes
- `countProductosWithFilters()` - Cuenta productos aplicando filtros
- `getProductoDetails()` - Obtiene detalles completos incluyendo variantes e imágenes
- `loadView()` - Carga vista con layout admin

### 3. Modelos Creados

#### ProductImage
**Ubicación:** `/app/models/ProductImage.php`

**Métodos principales:**
- `getByProductId($productId)` - Todas las imágenes de un producto
- `getMainImage($productId)` - Imagen principal
- `getByType($productId, $type)` - Imágenes por tipo (main, detail, hover, gallery)
- `addImage()` - Agregar imagen con orden automático
- `setAsMain()` - Establecer como imagen principal
- `updateOrder()` - Reordenar imágenes

#### ProductVariant
**Ubicación:** `/app/models/ProductVariant.php`

**Métodos principales:**
- `getByProductId($productId)` - Todas las variantes con orden inteligente
- `getAvailableVariants($productId)` - Solo variantes con stock
- `getBySize($productId, $size, $category)` - Variante específica
- `getBySku($sku)` - Buscar por SKU
- `isAvailable()` - Verificar disponibilidad
- `updateStock()` / `decreaseStock()` / `increaseStock()` - Gestión de inventario
- `getLowStock()` / `getOutOfStock()` - Alertas de stock
- `getTotalStock($productId)` - Stock total del producto
- `createVariant()` - Crear con SKU auto-generado

### 4. Modelos Actualizados

#### League & Team
**Actualización:** Se agregó método `getAll()` en ambos modelos para el dropdown de filtros.

---

## Modal de Detalles - Características

El modal muestra información completa del producto:

### Sección Header
- Imagen principal grande (200x200px)
- Nombre del producto
- Badges: Tipo, Estado (Activo/Inactivo), Destacado
- Información del equipo con logo
- Liga asociada
- Tipo de camiseta (Local, Visitante, etc.)
- Temporada y versión

### Galería de Imágenes
- Grid responsive con todas las imágenes
- Tipos de imagen etiquetados (main, detail, hover, gallery)
- Hover effects

### Estadísticas Visuales (Cards con gradientes)
1. **Precio**
   - Precio actual
   - Precio original tachado
   - Porcentaje de descuento calculado
   - Gradiente verde

2. **Stock Total**
   - Cantidad total
   - Número de variantes
   - Gradiente morado

3. **Personalización** (si disponible)
   - Precio adicional
   - Gradiente rosa

4. **Parches** (si disponible)
   - Precio adicional
   - Gradiente fucsia

### Descripción
- Texto completo del producto

### Tallas Disponibles
- Agrupadas por categoría (General, Jugador, Niños, Chandal)
- Grid responsive de variantes
- Cada variante muestra:
  - Talla
  - Stock con badge de color (verde/amarillo/rojo)
  - SKU
  - Estado "Agotado" si no hay stock

### Información General
- ID del producto
- Slug
- Fecha de creación
- Fecha de última actualización

### Acciones
- Botón "Cerrar"
- Botón "Editar Producto" (redirige a formulario de edición)

---

## Sistema de Filtros en Tiempo Real

La tabla se filtra instantáneamente mediante JavaScript sin recargar la página:

```javascript
filterTable() {
    - Busca en todo el texto de la fila
    - Filtra por tipo de producto (data-type)
    - Filtra por liga (data-league)
    - Filtra por estado (data-status)
    - Oculta/muestra filas que no coinciden
}
```

---

## Colores y Badges

### Tipos de Producto
- **Camiseta** → Badge azul (info)
- **Accesorio** → Badge gris (secondary)
- **Mystery Box** → Badge morado (purple)
- **Suscripción** → Badge amarillo (warning)

### Estados de Stock
- **> 20 unidades** → Badge verde (success)
- **6-20 unidades** → Badge amarillo (warning)
- **≤ 5 unidades** → Badge rojo (danger)

### Estado del Producto
- **Activo** → Badge verde
- **Inactivo** → Badge rojo

### Producto Destacado
- Badge amarillo con estrella

---

## Estructura de Datos

### Query Principal (getProductosWithDetails)
```sql
SELECT
    p.*,                          -- Todos los campos de products
    l.name as league_name,        -- Nombre de la liga
    l.logo_path as league_logo,   -- Logo de la liga
    t.name as team_name,          -- Nombre del equipo
    t.logo_path as team_logo,     -- Logo del equipo
    (SELECT image_path...) as main_image,     -- Imagen principal
    (SELECT COUNT(*)...) as total_variants    -- Total de variantes
FROM products p
LEFT JOIN leagues l ON p.league_id = l.league_id
LEFT JOIN teams t ON p.team_id = t.team_id
```

### Datos del Modal
```json
{
    "product_id": 123,
    "name": "Camiseta Real Madrid 2024/25",
    "product_type": "jersey",
    "jersey_type": "home",
    "season": "2024/25",
    "base_price": "24.99",
    "original_price": "79.99",
    "stock_quantity": 150,
    "is_active": true,
    "is_featured": true,
    "team_name": "Real Madrid",
    "team_logo": "/uploads/teams/real-madrid.png",
    "league_name": "LaLiga",
    "league_logo": "/uploads/leagues/laliga.png",
    "images": [
        {
            "image_id": 1,
            "image_path": "/uploads/products/...",
            "image_type": "main",
            "display_order": 0
        }
    ],
    "variants": [
        {
            "variant_id": 1,
            "size": "M",
            "size_category": "general",
            "stock_quantity": 25,
            "sku": "RM-HOME-24-M-GEN"
        }
    ]
}
```

---

## Responsive Design

### Desktop (> 768px)
- Tabla completa visible
- Filtros en línea horizontal
- Modal a 800px de ancho máximo

### Tablet (768px - 1024px)
- Tabla con scroll horizontal
- Filtros apilados

### Mobile (< 768px)
- Tabla con scroll horizontal
- Filtros en columna completa
- Modal a ancho completo
- Galería de imágenes en grid 2x
- Variantes en grid compacto

---

## Sistema de URL con Modal

Al hacer clic en un producto:
```javascript
openProductoModal(123)
  → URL cambia a: /admin/productos?id=123
  → Fetch a: /api/admin/productos/123
  → Muestra modal con datos
```

Al cerrar el modal:
```javascript
crmAdmin.closeModal()
  → URL vuelve a: /admin/productos
  → Modal desaparece con animación
```

Navegador Back/Forward:
```javascript
window.addEventListener('popstate')
  → Detecta cambio de URL
  → Abre/cierra modal automáticamente
```

---

## Integración con CRM Existente

El sistema utiliza:
- **CSS:** `/public/css/admin/admin-crm.css` (existente)
- **JS:** `/public/js/admin/admin-crm.js` (clase `CRMAdmin`)
- **Layout:** `/app/views/layouts/admin.php` (existente)

### Variables CSS utilizadas:
```css
--primary: #b054e9        /* Morado Kickverse */
--accent: #ec4899         /* Rosa/Fucsia */
--success: #10b981        /* Verde */
--warning: #f59e0b        /* Amarillo */
--danger: #ef4444         /* Rojo */
--info: #3b82f6           /* Azul */
--gray-*: ...             /* Escala de grises */
```

---

## Próximos Pasos (Para el Usuario)

### 1. Configurar Routing
Agregar las rutas del archivo `PRODUCTOS_ROUTING_EXAMPLE.md` a tu sistema de routing.

### 2. Implementar Formularios (Opcional)
Los métodos `create()`, `edit()`, `store()`, `update()` están preparados como placeholders.

### 3. Agregar al Menú Admin
En el sidebar del admin, agregar:
```html
<a href="/admin/productos" class="nav-item <?= $current_page === 'productos' ? 'active' : '' ?>">
    <i class="fas fa-tshirt"></i>
    <span class="nav-text">Productos</span>
</a>
```

### 4. Poblar Base de Datos
Asegurarse de tener:
- Productos en la tabla `products`
- Imágenes en `product_images`
- Variantes en `product_variants`
- Ligas en `leagues`
- Equipos en `teams`

---

## Testing Rápido

### Sin Base de Datos
Si `$productos` está vacío, se muestra:
```
🎽
No hay productos
Comienza agregando tu primer producto
```

### Con Productos
La tabla se puebla automáticamente con todos los datos.

### API Endpoint
```bash
curl http://tu-dominio.com/api/admin/productos/123
```
Debe devolver JSON con estructura completa del producto.

---

## Archivos de Documentación Creados

1. **PRODUCTOS_IMPLEMENTATION.md** (este archivo)
   - Documentación completa del sistema

2. **PRODUCTOS_ROUTING_EXAMPLE.md**
   - Ejemplos de routing para copiar/pegar

---

## Resumen Visual

```
┌─────────────────────────────────────────────────────┐
│  KICKVERSE - Gestión de Productos                   │
│  [🔍 Buscar] [Tipo▼] [Liga▼] [Estado▼] [+ Nuevo]   │
├─────────────────────────────────────────────────────┤
│ ID │ 🖼️ │ Nombre      │ Equipo │ Tipo │ € │ Stock │✓│
├────┼────┼─────────────┼────────┼──────┼───┼───────┼─┤
│ #1 │ 📷 │ Real Madrid │ 🏆 RM  │ 🎽   │€25│ 🟢 50 │✓│
│ #2 │ 📷 │ Barcelona   │ 🏆 FCB │ 🎽   │€25│ 🟡 15 │✓│
│ #3 │ 📷 │ PSG Away    │ 🇫🇷 PSG │ 🎽   │€22│ 🔴  3 │✓│
└─────────────────────────────────────────────────────┘
         Click en fila → Modal con detalles
```

---

## Soporte

Para cualquier duda sobre la implementación:
1. Revisar este documento
2. Revisar `PRODUCTOS_ROUTING_EXAMPLE.md`
3. Comparar con `/app/views/admin/clientes/index.php` (referencia)

---

**Estado:** ✅ Sistema completamente implementado y listo para usar
**Falta:** Solo configurar el routing (ver PRODUCTOS_ROUTING_EXAMPLE.md)
