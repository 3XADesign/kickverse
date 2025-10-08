# Sistema de Upselling y Cross-selling - Kickverse

## 📋 Descripción General

Sistema completo de upselling (3x2) y cross-selling contextual implementado en el formulario de pedido personalizado (`form.html`).

## 🎯 Características Principales

### 1. Upsell - Oferta 3x2

**Cuándo se activa:**
- Cuando el usuario completa el formulario con exactamente 2 camisetas en el carrito
- Se muestra un modal tipo alerta en modo oscuro

**Opciones del usuario:**
- ✅ **Añadir otra camiseta**: Vuelve al paso 1 del formulario para agregar una tercera camiseta gratis
- ❌ **Finalizar pedido**: Continúa al resumen con cross-sell

**Estilo:**
- Modo oscuro 100%
- Iconos Font Awesome (sin emojis)
- Animaciones suaves (pulse, fadeIn, slideUp)
- Colores: verde lima, morado, rosa neón

### 2. Cross-sell Contextual

**Productos sugeridos según el equipo elegido:**

#### Equipos principales (Real Madrid, FC Barcelona, Atlético):
1. **Segunda equipación** del mismo equipo
   - Precio original: ~~79,99 €~~
   - Precio oferta: **27,99 €**
   - Badge con descuento (-65%)

2. **Gorra oficial** con escudo bordado
   - Precio original: ~~19,99 €~~
   - Precio oferta: **7,99 €**
   - Badge con descuento (-60%)

#### Otros equipos:
- Solo muestra la **gorra oficial**

**Interactividad:**
- Botón "Añadir al pedido" con feedback visual
- Cambia a verde con check al añadir
- Se deshabilita temporalmente (2 segundos)
- Actualiza automáticamente el contador del carrito

### 3. Resumen Final

**Elementos:**
- Lista de todos los productos en el carrito
- Total calculado dinámicamente
- Iconos para cada tipo de producto
- Botones de acción:
  - Volver al inicio
  - Finalizar y enviar a WhatsApp

## 🗂️ Archivos Modificados

### Nuevos archivos:
1. **`css/upsell.css`** (578 líneas)
   - Estilos para modal de upsell
   - Estilos para tarjetas de cross-sell
   - Resumen final
   - Responsive completo

2. **`img/icons/gorra.svg`** (SVG)
   - Icono de gorra para accesorios

### Archivos modificados:
1. **`form.html`**
   - Añadido link a `upsell.css`
   - Modal de upsell HTML
   - Container para cross-sell dinámico

2. **`js/main.js`**
   - Variables globales: `upsellActivado`, `crosssellItems`
   - Funciones nuevas:
     - `verificarUpsell()`
     - `mostrarModalUpsell()`
     - `cerrarModalUpsell()`
     - `aceptarUpsell()`
     - `rechazarUpsell()`
     - `generarCrossSellContextual()`
     - `renderizarCrossSell()`
     - `añadirCrossSell(index)`
     - `mostrarCrossSellYResumen()`
     - `finalizarConCrossSell()`
   - Modificada: `finalizarPedidoWhatsApp()` para integrar el flujo

## 🔄 Flujo de Usuario

```
Usuario completa formulario (paso 7)
         ↓
Clic en "Finalizar pedido"
         ↓
Sistema detecta cantidad de camisetas
         ↓
    [¿Tiene 2 camisetas?]
         ↓
    ┌────┴────┐
   SÍ         NO
    ↓          ↓
Modal 3x2   Cross-sell directo
    ↓
[Usuario decide]
    ↓
┌───┴───┐
SÍ      NO
↓       ↓
Paso 1  Cross-sell + Resumen
         ↓
    Añadir accesorios (opcional)
         ↓
    Resumen final con totales
         ↓
    Dirección de envío
         ↓
    WhatsApp con pedido completo
```

## 🎨 Diseño y Estética

### Paleta de Colores:
- **Fondo**: Gradientes oscuros (#1a1a1a, #2d2d2d)
- **Acento primario**: Morado (#a855f7) `var(--accent-purple)`
- **Acento secundario**: Rosa (#ec4899) `var(--accent-pink)`
- **Éxito/Ofertas**: Verde lima (#84cc16) `var(--accent-green)`
- **Texto**: Blanco/gris claro

### Animaciones:
- **fadeIn**: Aparición suave del modal
- **slideUp**: Deslizamiento desde abajo
- **pulse**: Pulsación del icono de regalo
- **badgeBounce**: Rebote del badge de oferta

### Responsive:
- Desktop: 1024px+
- Tablet: 768px - 1024px
- Mobile: 480px - 768px
- Small mobile: < 480px

## 📱 Compatibilidad

- ✅ Chrome/Edge (últimas versiones)
- ✅ Firefox (últimas versiones)
- ✅ Safari (últimas versiones)
- ✅ Mobile browsers (iOS/Android)

## 🔧 Configuración

### Personalizar productos de cross-sell:

Editar en `js/main.js` la función `generarCrossSellContextual()`:

```javascript
const crosssellData = {
    'Nombre del Equipo': {
        camiseta: {
            nombre: 'Nombre del producto',
            descripcion: 'Descripción',
            imagen: 'ruta/imagen.png',
            precioOriginal: 79.99,
            precioOferta: 27.99,
            tipo: 'camiseta'
        },
        accesorio: {
            // ... similar
        }
    }
};
```

### Cambiar condición del upsell:

En `verificarUpsell()` cambiar el número de camisetas:

```javascript
if (numCamisetas === 2 && !upsellActivado) {
    // Cambiar el 2 por el número deseado
}
```

## 📊 Métricas Sugeridas

Para trackear la efectividad:

1. **Tasa de aceptación del upsell**
   - Usuarios que añaden tercera camiseta / Total de usuarios con 2 camisetas

2. **Tasa de conversión del cross-sell**
   - Productos de cross-sell añadidos / Total de veces mostrado

3. **Valor promedio del pedido**
   - Comparar antes/después de la implementación

## 🐛 Debugging

### Consola del navegador:

```javascript
// Ver estado del carrito
console.log(cartItems);

// Ver si upsell está activado
console.log(upsellActivado);

// Ver items de cross-sell generados
console.log(crosssellItems);

// Forzar mostrar upsell
mostrarModalUpsell();
```

## 📝 Notas Importantes

- El sistema usa `localStorage` para persistir el carrito
- Los productos se añaden automáticamente al carrito actual
- El mensaje de WhatsApp incluye todos los productos añadidos
- Las imágenes de productos deben existir en la carpeta `img/camisetas/`
- Si una imagen no existe, se usa fallback a `hero-jersey.png`

## 🚀 Mejoras Futuras Sugeridas

1. A/B testing de diferentes ofertas
2. Personalización por historial del usuario
3. Temporizador de oferta limitada
4. Cross-sell basado en popularidad
5. Bundles predefinidos (equipo + accesorio)
6. Sistema de puntos o cashback
7. Recomendaciones basadas en talla/liga

---

**Desarrollado por:** 3XA Design  
**Versión:** 1.0  
**Fecha:** Octubre 2025
