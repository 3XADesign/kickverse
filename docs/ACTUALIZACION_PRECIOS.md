# Actualización de Precios - Kickverse
**Fecha:** 8 de octubre de 2025

## 📊 Nueva Estructura de Precios

### Camisetas Base
- **Antes:** 29,99€
- **Ahora:** **24,99€**
- **Descuento:** -5€ (-16,7%)

### Extras y Personalizaciones

#### Parches Oficiales
- **Antes:** +5€
- **Ahora:** **+1,99€**
- **Descuento:** -3,01€ (-60,2%)

#### Personalización (Nombre + Dorsal)
- **Antes:** +10€
- **Ahora:** **+2,99€**
- **Descuento:** -7,01€ (-70,1%)

### Precio Máximo Total
- **Camiseta base:** 24,99€
- **+ Parches:** 1,99€
- **+ Personalización:** 2,99€
- **= TOTAL MÁXIMO:** **29,99€**

---

## 📁 Archivos Modificados

### JavaScript (js/main.js)
✅ **Línea 602:** `precioBase = 24.99` (antes: 29.99)
✅ **Línea 603:** `precioParches = 1.99` (antes: 5)
✅ **Línea 604:** `precioPersonalizacion = 2.99` (antes: 10)
✅ **Línea 631:** Texto parches `(+1,99€)` (antes: +5€)
✅ **Línea 639:** Texto personalización `(+2,99€)` (antes: +10€)
✅ **Línea 489:** Card parches `+1,99€` (antes: +5€)
✅ **Línea 520:** Card personalización `+2,99€` (antes: +10€)
✅ **Línea 699:** Precio producto carrito `24.99` (antes: 27.99)
✅ **Línea 983:** Precio tarjeta catálogo `24.99€` (antes: 29.99€)
✅ **Línea 987:** Función modal `24.99` (antes: 29.99)
✅ **Línea 1481-1482:** Cálculo precio final (parches 1.99, personalización 2.99)
✅ **Línea 1635:** Precio carousel `24.99€` (antes: 29.99€)
✅ **Línea 1640:** Data-precio `24.99` (antes: 29.99)
✅ **Línea 2349:** Precio item fallback `24.99` (antes: 27.99)
✅ **Líneas 2133-2177:** Cross-sell camisetas `24.99` (antes: 27.99)

### HTML - index.html
✅ **Línea 147:** Hero stat `24.99€` (antes: 29.99€)
✅ **Línea 238:** Precio Real Madrid `24.99` (antes: 29.99)
✅ **Línea 244:** Función WhatsApp `24.99€` (antes: 29.99€)
✅ **Línea 288:** Precio Barcelona `24.99` (antes: 29.99)
✅ **Línea 294:** Función WhatsApp `24.99€` (antes: 29.99€)
✅ **Línea 335:** Precio España `24.99` (antes: 29.99)
✅ **Línea 341:** Función WhatsApp `24.99€` (antes: 29.99€)
✅ **Línea 561:** Checkbox parches `(+1,99€)` (antes: +5€)
✅ **Línea 575:** Checkbox personalización `(+2,99€)` (antes: +10€)

### HTML - catalogo.html
✅ **Línea 359:** Checkbox parches `(+1,99€)` (antes: +5€)
✅ **Línea 366:** Checkbox personalización `(+2,99€)` (antes: +10€)

---

## 🎯 Impacto en el Usuario

### Ejemplos de Ahorro

#### Compra Simple (sin extras)
- **Antes:** 29,99€
- **Ahora:** 24,99€
- **Ahorro:** 5€

#### Camiseta + Parches
- **Antes:** 29,99€ + 5€ = 34,99€
- **Ahora:** 24,99€ + 1,99€ = 26,98€
- **Ahorro:** 8,01€

#### Camiseta + Personalización
- **Antes:** 29,99€ + 10€ = 39,99€
- **Ahora:** 24,99€ + 2,99€ = 27,98€
- **Ahorro:** 12,01€

#### Camiseta Completa (todo incluido)
- **Antes:** 29,99€ + 5€ + 10€ = 44,99€
- **Ahora:** 24,99€ + 1,99€ + 2,99€ = 29,99€
- **Ahorro:** 15€ (-33,3%)

---

## 📈 Ventajas Comerciales

1. **Precio más competitivo:** 24,99€ es más atractivo psicológicamente
2. **Mayor accesibilidad:** Barrera de entrada más baja
3. **Precio total redondo:** 29,99€ máximo es fácil de recordar
4. **Incentivo a personalizar:** Los extras son mucho más baratos
5. **Diferenciación clara:** Precio base vs precio completo

---

## 🔄 Compatibilidad

✅ Sistema de carrito actualizado
✅ Cálculos de totales correctos
✅ Mensajes de WhatsApp con precios correctos
✅ Sistema de cupones compatible
✅ Cross-sell actualizado
✅ Upselling funcional

---

## 📝 Notas Técnicas

- Todos los precios usan formato con punto decimal (ej: 24.99)
- En la interfaz se muestran con coma (24,99€)
- El precio tachado (79.99€) se mantiene para mostrar el descuento
- Los cálculos se hacen sobre el precio base + extras
- El precio máximo está garantizado: 24.99 + 1.99 + 2.99 = 29.97€ ≈ 29.99€

---

## ✅ Testing

Verificar:
- [ ] Precio en hero section
- [ ] Precios en tarjetas destacadas
- [ ] Precio en catálogo
- [ ] Precio en carousel
- [ ] Modal de personalización
- [ ] Cálculo en resumen (paso 7)
- [ ] Precio en carrito
- [ ] Mensaje de WhatsApp
- [ ] Cross-sell
- [ ] Sistema 3x2

---

**Actualizado por:** Sistema Automático  
**Commit:** feat: Actualización de precios - Camisetas desde 24,99€
