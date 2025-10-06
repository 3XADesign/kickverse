# 📁 Iconos PNG - Kickverse

Esta carpeta contiene todos los iconos en formato PNG para la web Kickverse.

## 📋 Iconos necesarios

### 🎯 Iconos principales (48x48px o 64x64px)
- `soccer.png` - Balón de fútbol
- `jersey.png` - Camiseta
- `cart.png` - Carrito de compras
- `check.png` - Verificado/Check
- `close.png` - Cerrar/X
- `lightning.png` - Rayo (rápido)
- `gift.png` - Regalo (oferta 3x2)
- `whatsapp.png` - Logo WhatsApp
- `trophy.png` - Trofeo
- `star.png` - Estrella
- `user.png` - Usuario
- `globe.png` - Mundo/global
- `flag.png` - Bandera
- `palette.png` - Paleta de colores
- `money.png` - Dinero/precio
- `tag.png` - Etiqueta/tag
- `trash.png` - Papelera/eliminar

### 🔄 Iconos de navegación (32x32px)
- `arrow-right.png` - Flecha derecha
- `arrow-left.png` - Flecha izquierda
- `arrow-down.png` - Flecha abajo
- `plus.png` - Más/añadir
- `minus.png` - Menos/restar
- `hash.png` - Número (#)

### 💳 Iconos de pago (ya disponibles en `/img/payment/`)
- Los iconos de pago ya están en formato SVG en la carpeta `img/payment/`
- Si prefieres PNG, puedes convertirlos o usar los originales

## 🎨 Especificaciones técnicas

### Tamaños recomendados:
- **Iconos principales**: 64x64px (alta resolución)
- **Iconos pequeños**: 32x32px o 48x48px
- **Iconos grandes** (hero, features): 128x128px

### Formato:
- **Formato**: PNG-24 con transparencia
- **Fondo**: Transparente
- **Color**: Los iconos se adaptarán al color del texto mediante CSS

### Estilo visual:
- **Líneas**: Limpias y minimalistas
- **Grosor**: 2-3px para buena visibilidad
- **Estilo**: Flat design, sin sombras ni gradientes internos
- **Cohesión**: Todos los iconos deben tener el mismo estilo visual

## 🔗 Fuentes recomendadas

### Bancos de iconos gratuitos:
1. **Flaticon** - https://www.flaticon.com/
   - Miles de iconos PNG gratuitos
   - Packs completos con estilo coherente
   - Recomendado: buscar "football icons pack"

2. **Icons8** - https://icons8.com/
   - PNG de alta calidad
   - Editor integrado para personalizar colores
   - Descargas gratuitas hasta 100x100px

3. **Freepik** - https://www.freepik.com/
   - Iconos deportivos de calidad
   - Packs temáticos de fútbol

4. **Iconfinder** - https://www.iconfinder.com/
   - Filtro por licencia gratuita
   - Múltiples tamaños disponibles

### ⚽ Iconos específicos de fútbol:
- Buscar: "football icon pack PNG"
- Buscar: "soccer minimal icons"
- Buscar: "sports icons flat design"

## 📝 Naming conventions

Los archivos deben seguir estas convenciones:
```
nombre-descriptivo.png
```

Ejemplos:
- ✅ `soccer.png`
- ✅ `jersey.png`
- ✅ `arrow-right.png`
- ❌ `icon1.png`
- ❌ `Soccer Icon.png`

## 🎯 Integración en el código

Una vez subidos los iconos PNG, se usarán así en HTML:

```html
<!-- Icono simple -->
<img src="img/icons-png/soccer.png" alt="Fútbol" class="icon">

<!-- Icono con tamaño específico -->
<img src="img/icons-png/jersey.png" alt="Camiseta" class="icon icon-lg">

<!-- Icono en botón -->
<button class="btn btn-primary">
    <img src="img/icons-png/whatsapp.png" alt="WhatsApp" class="icon">
    Confirmar por WhatsApp
</button>
```

## 🔧 CSS para iconos PNG

Los iconos PNG ya tienen soporte CSS en `css/icons.css`:

```css
.icon {
    width: 24px;
    height: 24px;
    vertical-align: middle;
}

.icon-lg {
    width: 32px;
    height: 32px;
}

.icon-xl {
    width: 48px;
    height: 48px;
}
```

## ✅ Checklist de iconos

Al subir cada icono, marca con ✅:

- [ ] soccer.png
- [ ] jersey.png
- [ ] cart.png
- [ ] check.png
- [ ] close.png
- [ ] lightning.png
- [ ] gift.png
- [ ] whatsapp.png
- [ ] trophy.png
- [ ] star.png
- [ ] user.png
- [ ] globe.png
- [ ] flag.png
- [ ] palette.png
- [ ] money.png
- [ ] tag.png
- [ ] trash.png
- [ ] arrow-right.png
- [ ] arrow-left.png
- [ ] arrow-down.png
- [ ] plus.png
- [ ] minus.png
- [ ] hash.png

## 💡 Tip

Para mantener consistencia visual:
1. Descarga un pack completo de iconos del mismo estilo
2. Asegúrate de que todos tengan el mismo grosor de línea
3. Usa el mismo tamaño base para todos (64x64px recomendado)
4. Verifica que se vean bien sobre fondo oscuro y claro

---

**Color del degradado principal**: #6c287f → #7762b7 → #8197e8  
Los iconos monocromáticos funcionarán mejor con este esquema de color.
