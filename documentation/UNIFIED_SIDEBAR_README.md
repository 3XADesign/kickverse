# Menu Lateral Unificado - Kickverse

## Descripcion

Sistema de menu lateral unificado para toda la web de Kickverse que proporciona navegacion consistente en todas las paginas.

## Archivos Creados

### 1. CSS
- **Ubicacion**: `/public/css/unified-sidebar.css`
- **Tamano**: ~14KB
- **Descripcion**: Estilos completos del menu lateral con soporte responsive

### 2. HTML/PHP
- **Ubicacion**: `/app/views/partials/unified-sidebar.php`
- **Tamano**: ~7.6KB
- **Descripcion**: Estructura HTML del sidebar con logica PHP para contenido dinamico

### 3. JavaScript
- **Ubicacion**: `/public/js/unified-sidebar.js`
- **Tamano**: ~14KB
- **Descripcion**: Funcionalidad interactiva del sidebar

### 4. Layout Principal (Modificado)
- **Ubicacion**: `/app/views/layouts/main.php`
- **Cambios**:
  - Agregado CSS del unified-sidebar
  - Incluido el partial unified-sidebar.php
  - Agregado JS del unified-sidebar

## Caracteristicas Principales

### En PC/Desktop (> 1024px)
- ✅ Menu lateral fijo a la izquierda
- ✅ **Boton de toggle en esquina superior izquierda** para expandir/colapsar
- ✅ **Iconos grandes y centrados** cuando esta colapsado
- ✅ Texto visible cuando esta expandido
- ✅ **Estado persistente** en localStorage
- ✅ Ancho: 280px expandido, 80px colapsado
- ✅ Contenido principal se ajusta automaticamente

### En Movil (≤ 1024px)
- ✅ Menu oculto por defecto
- ✅ Se abre con el **boton flotante** (mismo del header)
- ✅ **Overlay oscuro** cuando esta abierto
- ✅ Cierra al hacer click fuera o presionar ESC
- ✅ Cierra automaticamente al navegar

### Diseno
- ✅ **Fondo circular rosa/morado** para opcion activa
- ✅ Gradiente de color en enlaces destacados (Mystery Box)
- ✅ Iconos con FontAwesome
- ✅ Animaciones suaves
- ✅ Scrollbar personalizado

## Estructura del Menu

### Seccion Header (Siempre visible)
```
Menu Principal
├── Home (/)
├── Mystery Box (/mystery-box) - Destacado
├── Productos (/productos)
└── Ligas (/ligas)
```

### Seccion Mi Cuenta (Solo en /mi-cuenta/*)
```
Mi Cuenta
├── Dashboard (/mi-cuenta)
├── Mi Perfil (/mi-cuenta/perfil)
├── Mis Pedidos (/mi-cuenta/pedidos)
├── Mis Suscripciones (/mi-cuenta/suscripciones)
└── Mis Direcciones (/mi-cuenta/direcciones)
```

### Footer del Sidebar (Solo en Mi Cuenta y logueado)
```
└── Cerrar Sesion (Boton rojo)
```

## Comportamiento Inteligente

### En la HOME y paginas publicas:
- Muestra solo opciones del header
- Si NO esta logueado: muestra boton "Iniciar Sesion"
- Si esta logueado: muestra enlace a "Mi Cuenta"

### En MI-CUENTA y subpaginas:
- Muestra opciones del header
- Muestra opciones de cuenta (Dashboard, Perfil, Pedidos, etc.)
- Muestra boton "Cerrar Sesion" en el footer

## Compatibilidad

### Con el Header
- El header se ajusta automaticamente al ancho del sidebar
- En desktop: header inicia donde termina el sidebar
- En movil: header ocupa todo el ancho

### Con el Sidebar Antiguo de Mi Cuenta
- El sidebar antiguo (`account-sidebar`) se oculta automaticamente
- No hay conflictos visuales
- La funcionalidad del boton toggle antiguo se desactiva

## Z-Index Jerarquia

```
Unified Sidebar:        9999
Toggle Button:          10000
Overlay:               9998
Floating Menu Button:   10001
```

## Clases CSS Importantes

### En el Body
```css
.has-unified-sidebar   /* Aplicada automaticamente por JS */
.sidebar-collapsed     /* PC: sidebar colapsado */
.sidebar-open          /* Movil: sidebar abierto */
```

### Links del Sidebar
```css
.unified-sidebar-link           /* Link normal */
.unified-sidebar-link.active    /* Link activo (fondo circular) */
.unified-sidebar-link.highlight /* Link destacado (Mystery Box) */
```

## JavaScript API

El sidebar expone una API global `window.UnifiedSidebar`:

```javascript
// Abrir sidebar (movil)
UnifiedSidebar.open();

// Cerrar sidebar (movil)
UnifiedSidebar.close();

// Toggle sidebar (expandir/colapsar en PC, abrir/cerrar en movil)
UnifiedSidebar.toggle();

// Expandir sidebar (solo PC)
UnifiedSidebar.expand();

// Colapsar sidebar (solo PC)
UnifiedSidebar.collapse();
```

## Personalizacion

### Variables CSS Principales
```css
:root {
    --unified-sidebar-width: 280px;        /* Ancho expandido */
    --unified-sidebar-collapsed: 80px;     /* Ancho colapsado */
    --unified-sidebar-bg: #ffffff;         /* Fondo */
    --unified-sidebar-z: 9999;             /* Z-index */
    --unified-sidebar-transition: 0.3s;    /* Duracion animaciones */
}
```

### Colores de Estado Activo
- Fondo activo: `linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(176, 84, 233, 0.15))`
- Borde izquierdo: `linear-gradient(180deg, #ec4899, #b054e9)`
- Color de texto: `#b054e9` (primary)

## Accesibilidad

- ✅ Soporte completo de teclado
- ✅ Focus visible en todos los elementos interactivos
- ✅ ARIA labels en botones
- ✅ Soporte para `prefers-reduced-motion`
- ✅ Soporte para `prefers-contrast: high`
- ✅ Oculto en impresion

## Responsive Breakpoints

| Rango | Comportamiento |
|-------|----------------|
| ≤ 1024px | Movil: sidebar oculto, se abre con overlay |
| 1025px - 1280px | Tablet: sidebar 240px, colapsable |
| > 1280px | Desktop: sidebar 280px, colapsable |

## Integracion con el Menu Movil del Header

El menu lateral unificado se integra con el boton flotante del header:
- Cuando se hace click en el boton flotante, se cierra el menu movil del header
- Se abre el unified sidebar en su lugar
- No hay conflictos entre ambos menus
- El estado del boton flotante se sincroniza con el estado del sidebar

## Testing

### En Desktop
1. Verificar que el sidebar aparece a la izquierda
2. Click en el boton toggle (esquina superior izquierda)
3. Verificar que colapsa mostrando solo iconos centrados
4. Verificar que el contenido se ajusta
5. Recargar pagina y verificar que mantiene el estado

### En Movil
1. Verificar que el sidebar esta oculto
2. Click en el boton flotante (esquina inferior derecha)
3. Verificar que el sidebar se desliza desde la izquierda
4. Verificar overlay oscuro
5. Click fuera del sidebar para cerrar
6. Verificar que se cierra correctamente

### En Mi Cuenta
1. Navegar a /mi-cuenta
2. Verificar que aparecen las opciones de cuenta
3. Verificar que el boton "Cerrar Sesion" esta en el footer
4. Click en alguna opcion de cuenta
5. Verificar que la opcion activa tiene fondo circular

## Notas Importantes

1. **Persistencia**: El estado colapsado/expandido se guarda en `localStorage` con la clave `unified-sidebar-collapsed`

2. **Deteccion de Ruta**: El sidebar detecta automaticamente la ruta actual para:
   - Resaltar la opcion activa
   - Mostrar/ocultar la seccion de cuenta
   - Mostrar/ocultar el boton de cerrar sesion

3. **Performance**: Las animaciones usan `transform` y `opacity` para mejor rendimiento en GPU

4. **Mobile-First**: El diseno esta optimizado para moviles y se mejora progresivamente en pantallas mas grandes

## Soporte de Navegadores

- ✅ Chrome/Edge (ultimas 2 versiones)
- ✅ Firefox (ultimas 2 versiones)
- ✅ Safari (ultimas 2 versiones)
- ✅ Safari iOS
- ✅ Chrome Android

## Solución de Problemas

### El sidebar no aparece
- Verificar que `unified-sidebar.css` esta cargado
- Verificar que `unified-sidebar.php` esta incluido en main.php
- Revisar consola del navegador por errores

### El contenido no se ajusta
- Verificar que la clase `has-unified-sidebar` esta en el body
- Revisar que no hay CSS conflictivo sobrescribiendo los margenes

### El toggle no funciona
- Verificar que `unified-sidebar.js` esta cargado
- Revisar consola por errores de JavaScript
- Verificar que los IDs de los elementos coinciden

### En movil no se abre
- Verificar que el boton flotante existe (`#floating-menu-btn`)
- Revisar que no hay conflictos con otros scripts
- Verificar viewport width en DevTools

## Mantenimiento

### Agregar nueva opcion al menu
1. Editar `/app/views/partials/unified-sidebar.php`
2. Agregar nuevo `<li>` con clase `unified-sidebar-item`
3. Usar la estructura de links existente
4. Actualizar la funcion `isActiveLink()` si es necesario

### Cambiar colores
1. Editar variables CSS en `/public/css/unified-sidebar.css`
2. Buscar `--primary`, `--accent` para colores principales
3. Buscar `linear-gradient` para gradientes de estado activo

### Modificar comportamiento
1. Editar `/public/js/unified-sidebar.js`
2. Las funciones principales estan documentadas en el codigo
3. La API global `UnifiedSidebar` puede extenderse segun necesidad
