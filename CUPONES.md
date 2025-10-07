# 🎟️ Sistema de Cupones y Descuentos - Kickverse

## 📋 Cupones Disponibles

### 1. **WELCOME5**
- **Tipo**: Descuento fijo
- **Valor**: 5€
- **Compra mínima**: 60€
- **Descripción**: Cupón de bienvenida para nuevos clientes
- **Uso**: Aparece automáticamente en el popup de primera visita

### 2. **NOTBETTING10**
- **Tipo**: Porcentaje
- **Valor**: 10%
- **Descuento máximo**: 5€
- **Compra mínima**: 0€
- **Descripción**: Descuento del 10% hasta un máximo de 5€

### 3. **TOPBONUS10**
- **Tipo**: Porcentaje
- **Valor**: 10%
- **Descuento máximo**: 5€
- **Compra mínima**: 0€
- **Descripción**: Descuento del 10% hasta un máximo de 5€

### 4. **KICKVERSE10**
- **Tipo**: Porcentaje
- **Valor**: 10%
- **Descuento máximo**: 5€
- **Compra mínima**: 0€
- **Descripción**: Descuento del 10% hasta un máximo de 5€

## 🎯 Características del Sistema

### Popup de Bienvenida
- ✅ Aparece automáticamente en la primera visita
- ✅ Muestra el cupón WELCOME5 destacado
- ✅ Permite copiar el código con un clic
- ✅ Se almacena en localStorage para no volver a mostrarse
- ✅ Diseño atractivo con animaciones

### Aplicación de Cupones
- ✅ Campo de entrada en el carrito de compras
- ✅ Validación de códigos en tiempo real
- ✅ Verificación de compra mínima
- ✅ Cálculo automático del descuento
- ✅ Muestra visual del cupón aplicado
- ✅ Opción para eliminar cupón

### Cálculo de Descuentos
- ✅ **Descuento fijo**: Se aplica el valor exacto (ej: 5€)
- ✅ **Descuento porcentual**: Se calcula el % sobre el subtotal
- ✅ **Límite máximo**: Los descuentos porcentuales tienen un tope
- ✅ **Protección**: El descuento nunca supera el subtotal

### Integración con WhatsApp
- ✅ El descuento se incluye en el mensaje de compra
- ✅ Muestra el código del cupón aplicado
- ✅ Calcula el total final correctamente

## 🔧 Configuración Técnica

### Ubicación del código:
- **JavaScript**: `/js/main.js` (líneas finales)
- **CSS**: `/css/coupon.css`
- **HTML**: Integrado en `index.html` y `catalogo.html`

### Variables importantes:
```javascript
const AVAILABLE_COUPONS = {
    'WELCOME5': { ... },
    'NOTBETTING10': { ... },
    'TOPBONUS10': { ... },
    'KICKVERSE10': { ... }
}

let appliedCoupon = null; // Cupón actualmente aplicado
```

### Funciones principales:
- `showWelcomePopup()` - Muestra el popup de bienvenida
- `applyCoupon()` - Aplica un cupón al carrito
- `removeCoupon()` - Elimina el cupón aplicado
- `calculateDiscount()` - Calcula el monto del descuento
- `copyCouponCode()` - Copia el código al portapapeles

## 📝 Cómo añadir nuevos cupones

Para añadir un nuevo cupón, edita el objeto `AVAILABLE_COUPONS` en `/js/main.js`:

```javascript
'NUEVO_CUPON': {
    type: 'fixed',        // 'fixed' o 'percentage'
    value: 10,            // Valor del descuento
    maxDiscount: 5,       // (Opcional) Descuento máximo para porcentajes
    minPurchase: 50,      // Compra mínima requerida
    description: 'Descripción del cupón'
}
```

## 🎨 Personalización

### Colores:
Los estilos usan las variables CSS del tema:
- `--primary-color`: Color principal (naranja)
- `--accent-color`: Color de acento (amarillo)
- `--card-bg`: Fondo de las tarjetas
- `--border-color`: Bordes

### Tiempo del popup:
Modificar en `showWelcomePopup()`:
```javascript
setTimeout(() => {
    popup.classList.add('active');
}, 2000); // 2 segundos - cambiar aquí
```

## 📱 Responsive

El sistema está completamente optimizado para:
- ✅ Desktop (>1024px)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (480px - 768px)
- ✅ Small Mobile (<480px)

## 🧪 Testing

### Para probar el popup de bienvenida:
1. Abrir DevTools (F12)
2. Ir a "Application" > "Local Storage"
3. Eliminar la clave `kickverse_visited`
4. Recargar la página

### Para probar cupones:
1. Añadir productos al carrito
2. Abrir el carrito
3. Introducir código en el campo de cupón
4. Verificar que el descuento se aplique correctamente

## ⚡ Rendimiento

- Cupones validados en cliente (sin llamadas al servidor)
- LocalStorage para persistencia
- Animaciones optimizadas con CSS
- Carga asíncrona del popup

## 🔒 Seguridad

- Validación de códigos antes de aplicar
- Límites máximos de descuento
- Verificación de compra mínima
- Protección contra descuentos negativos

---

**Última actualización**: 7 de octubre de 2025
