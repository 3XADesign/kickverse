# Guía de Testing - Sistema de Carrito y Personalización

## ✅ Cambios Implementados

### 1. **Sistema de Carrito Completo**
   - ✅ JavaScript con 15+ funciones para gestión del carrito
   - ✅ Persistencia con localStorage (clave: 'kickverse_cart')
   - ✅ Detección de promoción 3x2 automática
   - ✅ Notificaciones visuales al añadir productos
   - ✅ Badge con contador en todos los headers

### 2. **Modales HTML**
   - ✅ Modal del carrito añadido a: index.html, form.html, catalogo.html
   - ✅ Modal de personalización añadido a: catalogo.html
   - ✅ Botón del carrito añadido a todos los headers

### 3. **CSS Completo**
   - ✅ Nuevo archivo: `css/cart.css` con estilos para:
     - Botón del carrito con badge animado
     - Items del carrito con imagen, detalles y controles de cantidad
     - Mensaje de promoción 3x2 con animación
     - Notificaciones toast
     - Responsive design completo
   - ✅ Actualizaciones en `css/modal.css`:
     - Estilos para checkboxes personalizados
     - Animación slideDown para campos condicionales

### 4. **Filtros del Catálogo**
   - ✅ Función `aplicarFiltros()` implementada con:
     - Filtro por liga
     - Filtro por equipación (Local/Visitante)
     - Búsqueda por texto
     - Contador de resultados actualizado dinámicamente
   - ✅ Función `limpiarFiltros()` para resetear todos los filtros

### 5. **Personalización en Catálogo**
   - ✅ Modal con formulario completo:
     - Select de tallas (XS - XXL)
     - Checkbox para parches oficiales (+5€)
     - Checkbox para personalización con nombre/dorsal (+10€)
     - Campos condicionales que aparecen al activar personalización
     - Validación de campos requeridos
   - ✅ 4 productos del catálogo actualizados para usar el modal

---

## 🧪 Plan de Testing

### **Test 1: Carrito Básico**

#### Objetivo: Verificar que el carrito permite añadir múltiples productos

**Pasos:**
1. Abre `catalogo.html` en el navegador
2. Haz clic en "Comprar" en cualquier camiseta
3. En el modal de personalización:
   - Selecciona una talla (ej: M)
   - Haz clic en "Añadir al Carrito"
4. Deberías ver:
   - ✅ Notificación toast en la esquina superior derecha
   - ✅ Badge del carrito con número "1"
5. Repite el proceso con otra camiseta diferente
6. Verifica:
   - ✅ Badge ahora muestra "2"
   - ✅ Notificación aparece de nuevo

**Resultado Esperado:** El carrito debe permitir añadir múltiples productos sin problemas.

---

### **Test 2: Incremento de Cantidad**

#### Objetivo: Verificar que productos idénticos incrementan cantidad

**Pasos:**
1. En `catalogo.html`, añade al carrito:
   - Real Madrid Local, Talla M, sin personalización
2. Añade de nuevo:
   - Real Madrid Local, Talla M, sin personalización
3. Abre el carrito (clic en el botón del carrito)
4. Verifica:
   - ✅ Solo hay UN item de Real Madrid Local
   - ✅ La cantidad muestra "2"
   - ✅ El precio se multiplica correctamente

**Resultado Esperado:** Productos idénticos deben incrementar cantidad, no duplicarse.

---

### **Test 3: Productos Diferentes por Personalización**

#### Objetivo: Verificar que personalizaciones diferentes crean items separados

**Pasos:**
1. Añade: Real Madrid Local, Talla M, sin personalización
2. Añade: Real Madrid Local, Talla M, con personalización "RAMOS" 4
3. Añade: Real Madrid Local, Talla M, con personalización "BENZEMA" 9
4. Abre el carrito
5. Verifica:
   - ✅ Hay 3 items diferentes de Real Madrid Local
   - ✅ Cada uno tiene su personalización respectiva
   - ✅ Los precios reflejan los +10€ de personalización

**Resultado Esperado:** Personalizaciones diferentes deben crear items separados.

---

### **Test 4: Promoción 3x2**

#### Objetivo: Verificar detección y aplicación de promoción

**Pasos:**
1. Vacía el carrito (botón "Vaciar Carrito")
2. Añade 2 camisetas al carrito
3. Abre el carrito
4. Verifica:
   - ✅ Aparece mensaje: "¡Añade 1 camiseta más y la más barata es GRATIS!"
5. Añade una tercera camiseta
6. Abre el carrito de nuevo
7. Verifica:
   - ✅ Mensaje cambia a: "¡3x2 APLICADO! La camiseta más barata es GRATIS"
   - ✅ El descuento se refleja en el total
8. Añade una cuarta camiseta
9. Verifica:
   - ✅ Mensaje informa: "¡Añade 2 camisetas más para otro 3x2!"

**Resultado Esperado:** El sistema debe detectar automáticamente y aplicar el 3x2.

---

### **Test 5: Filtros del Catálogo**

#### Objetivo: Verificar que los filtros muestran/ocultan productos correctamente

**Pasos:**
1. En `catalogo.html`, selecciona en el filtro "Liga": LaLiga
2. Verifica:
   - ✅ Solo se muestran camisetas de LaLiga
   - ✅ Contador actualiza: "Mostrando X camisetas"
3. Selecciona "Equipación": Local
4. Verifica:
   - ✅ Solo camisetas locales de LaLiga
5. Escribe en búsqueda: "Real Madrid"
6. Verifica:
   - ✅ Solo aparece Real Madrid
7. Haz clic en "Limpiar Filtros"
8. Verifica:
   - ✅ Todos los productos vuelven a aparecer

**Resultado Esperado:** Los filtros deben funcionar individualmente y en combinación.

---

### **Test 6: Personalización Condicional**

#### Objetivo: Verificar que los campos de personalización aparecen/desaparecen

**Pasos:**
1. En catálogo, haz clic en "Comprar" en cualquier producto
2. En el modal, selecciona talla M
3. Marca checkbox "Personalizar con nombre y dorsal"
4. Verifica:
   - ✅ Aparecen campos de Nombre y Dorsal con animación
5. Desmarca el checkbox
6. Verifica:
   - ✅ Los campos desaparecen
7. Intenta añadir al carrito sin seleccionar talla
8. Verifica:
   - ✅ Aparece alerta: "Por favor selecciona una talla"

**Resultado Esperado:** Los campos deben aparecer/desaparecer y la validación debe funcionar.

---

### **Test 7: Controles de Cantidad en Carrito**

#### Objetivo: Verificar botones +/- en items del carrito

**Pasos:**
1. Añade un producto al carrito
2. Abre el carrito
3. Haz clic en el botón "+" del item
4. Verifica:
   - ✅ Cantidad incrementa
   - ✅ Precio total se actualiza
   - ✅ Badge del carrito se actualiza
5. Haz clic en el botón "-"
6. Verifica:
   - ✅ Cantidad decrementa
   - ✅ Precio se actualiza
7. Reduce cantidad a 0
8. Verifica:
   - ✅ El item se elimina del carrito

**Resultado Esperado:** Los controles deben actualizar cantidades correctamente.

---

### **Test 8: Persistencia con localStorage**

#### Objetivo: Verificar que el carrito se mantiene al recargar

**Pasos:**
1. Añade 2-3 productos al carrito
2. Recarga la página (F5)
3. Verifica:
   - ✅ Badge del carrito muestra la cantidad correcta
4. Abre el carrito
5. Verifica:
   - ✅ Todos los productos siguen ahí
   - ✅ Cantidades y personalizaciones se mantienen

**Resultado Esperado:** El carrito debe persistir entre recargas.

---

### **Test 9: Finalizar Compra WhatsApp**

#### Objetivo: Verificar generación del mensaje de WhatsApp

**Pasos:**
1. Añade varios productos al carrito (con y sin personalización)
2. Abre el carrito
3. Haz clic en "Finalizar Compra"
4. Verifica:
   - ✅ Se abre WhatsApp Web/App
   - ✅ El mensaje contiene:
     - Lista de productos
     - Tallas
     - Personalizaciones (si aplica)
     - Parches (si aplica)
     - Total con 3x2 aplicado
   - ✅ Número de destino: 34614299735

**Resultado Esperado:** El mensaje debe estar completo y bien formateado.

---

### **Test 10: Responsive Design**

#### Objetivo: Verificar que el carrito funciona en móvil

**Pasos:**
1. Abre DevTools (F12)
2. Activa modo responsive (Ctrl+Shift+M)
3. Selecciona iPhone 12 Pro (o similar)
4. Verifica:
   - ✅ Botón del carrito se adapta (solo ícono)
   - ✅ Badge visible y bien posicionado
   - ✅ Modal del carrito ocupa pantalla completa
   - ✅ Items del carrito se adaptan verticalmente
   - ✅ Botones son fácilmente clicables
5. Añade productos y prueba funcionalidad
6. Verifica:
   - ✅ Todo funciona igual que en desktop

**Resultado Esperado:** La experiencia móvil debe ser fluida y usable.

---

## 🐛 Problemas Conocidos a Verificar

### Issue #1: Badge del carrito no actualiza en form.html
- **Síntoma:** El badge puede no actualizarse en la página de formulario
- **Causa posible:** initCart() no se llama en form.html
- **Solución:** Ya implementada, verificar que funciona

### Issue #2: Modal no cierra con clic fuera
- **Síntoma:** Al hacer clic en el overlay, el modal no cierra
- **Causa posible:** Event listener no configurado
- **Solución:** Añadir event listener al overlay si es necesario

### Issue #3: Imágenes no cargan en modal del carrito
- **Síntoma:** Imágenes rotas en items del carrito
- **Causa posible:** Rutas de imágenes incorrectas
- **Verificar:** Las rutas deben comenzar con `./img/camisetas/`

---

## 📋 Checklist de Funcionalidades

### Carrito
- [ ] Se pueden añadir múltiples productos diferentes
- [ ] Productos idénticos incrementan cantidad
- [ ] Productos con distinta personalización se separan
- [ ] Badge actualiza en todas las páginas
- [ ] Notificaciones aparecen al añadir
- [ ] Promoción 3x2 se detecta correctamente
- [ ] Controles +/- funcionan
- [ ] Botón eliminar funciona
- [ ] Vaciar carrito funciona
- [ ] Persistencia con localStorage funciona
- [ ] Mensaje de WhatsApp es correcto

### Personalización
- [ ] Modal abre correctamente
- [ ] Select de tallas funciona
- [ ] Checkbox de parches suma 5€
- [ ] Checkbox de personalización muestra campos
- [ ] Campos de nombre/dorsal se validan
- [ ] Precio final se calcula correctamente
- [ ] Modal cierra al añadir producto

### Filtros
- [ ] Filtro de liga funciona
- [ ] Filtro de equipación funciona
- [ ] Búsqueda por texto funciona
- [ ] Filtros combinados funcionan
- [ ] Contador de resultados actualiza
- [ ] Limpiar filtros restaura todo

### UI/UX
- [ ] Animaciones funcionan suavemente
- [ ] Responsive en móvil funciona
- [ ] Colores y estilos son consistentes
- [ ] No hay errores en consola
- [ ] Carga de página es rápida

---

## 🚀 Comandos Útiles

### Ver el sitio en navegador
```bash
# Opción 1: Abrir directamente el archivo
open index.html

# Opción 2: Usar un servidor local
python3 -m http.server 8000
# Luego abrir: http://localhost:8000
```

### Ver la consola del navegador
- Chrome/Edge: `F12` o `Ctrl+Shift+I`
- Firefox: `F12` o `Ctrl+Shift+K`
- Safari: `Cmd+Option+I` (activar Developer menu primero)

### Limpiar localStorage
```javascript
// En la consola del navegador:
localStorage.clear();
location.reload();
```

---

## 📞 Soporte

Si encuentras algún bug o tienes dudas:
1. Verifica la consola del navegador (F12)
2. Revisa el localStorage: `localStorage.getItem('kickverse_cart')`
3. Comprueba que todas las rutas de archivos CSS/JS son correctas
4. Verifica que no hay errores de sintaxis con: `get_errors`

---

## ✨ Próximas Mejoras Sugeridas

1. **Animaciones adicionales:**
   - Transición al eliminar items
   - Efecto de rebote en badge

2. **Funcionalidades extra:**
   - Guardar favoritos
   - Comparador de productos
   - Historial de pedidos

3. **Optimizaciones:**
   - Lazy loading de imágenes
   - Minificación de CSS/JS
   - Service Worker para offline

---

**Fecha de creación:** 2025-01-XX  
**Versión:** 1.0.0  
**Estado:** ✅ Implementación completa
