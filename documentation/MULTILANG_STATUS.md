# Sistema Multilingüe Kickverse - Documentación de Implementación

## ✅ Completado

### 1. Infraestructura Base
- **js/lang.js**: Sistema completo de cambio de idioma con:
  - Función `setLanguage(lang)` que muestra/oculta elementos según idioma
  - Persistencia en `localStorage`
  - Eventos personalizados (`languageChanged`)
  - Soporte para accesibilidad (Alt+L para cambiar idioma)
  - API global `window.KickverseLang`

### 2. Estilos CSS (subscription.css)
- `.lang-switcher`: Contenedor del botón de idiomas
- `.lang-btn`: Botones ES/EN con estados hover y active
- Gradiente morado/fucsia cuando está activo
- Responsive para móviles

### 3. index.html (Parcial)
✅ Header con botón de idioma ES/EN
✅ Hero section completamente bilingüe
✅ Script lang.js incluido al final
⏳ Pendiente: Secciones planes, features, FAQ, footer

### 4. mystery-box.html (Parcial)
✅ Header con botón de idioma ES/EN
✅ Hero section completamente bilingüe
⏳ Pendiente: Secciones de boxes, features, FAQ, footer, script

## ⏳ Pendiente de Implementación

### index.html - Secciones Restantes

#### Carousel Section
```html
<h2 class="section-title">
    <span data-lang="es">Camisetas que podrías recibir</span>
    <span data-lang="en">Jerseys you could receive</span>
</h2>
```

#### Plans Section
- Títulos: "Elige tu suscripción" / "Choose your subscription"
- Badges: "Esencial", "El más elegido", "Clubes TOP", "Exclusivo"
- Planes: Traducir nombres, descripciones y features
- Botones CTA: "Suscribirme al..." / "Subscribe to..."

#### Features Section
- "¿Cómo funciona?" / "How does it work?"
- Traduc ir los 4 feature cards

#### FAQ Section
- "Preguntas frecuentes" / "Frequently asked questions"
- Traducir 6 preguntas y respuestas completas

#### Footer
- "Suscripciones" / "Subscriptions"
- "Legal" / "Legal" 
- "Contacto" / "Contact"
- Texto descriptivo

### mystery-box.html - Secciones Restantes

#### Boxes Section
```html
<h2 class="section-title">
    <span data-lang="es">Elige tu Mystery Box</span>
    <span data-lang="en">Choose your Mystery Box</span>
</h2>
```

Traducir 3 boxes:
- Box Clásica / Classic Box
- Box por Liga / League Box
- Box Premium / Premium Box

Con features y botones CTA

#### Features Section
- "¿Por qué elegir una Mystery Box?" / "Why choose a Mystery Box?"
- Traducir 4 feature cards

#### FAQ Section
- Traducir 6 preguntas y respuestas

#### Footer
- Mismo footer que index.html

#### Script
Añadir al final:
```html
<script src="./js/lang.js"></script>
```

### catalogo.html

#### Header
- Añadir botón de idioma (igual que los demás)

#### Hero/Search
- "Catálogo Completo" / "Full Catalog"
- "Buscar equipo..." / "Search team..."

#### League Tabs
- Mantener nombres de ligas igual (LaLiga, Premier League, etc.)
- Botón "Ver más" / "View more"

#### Availability Message
- "Para consultar disponibilidad..." / "To check availability..."
- Botón "Consultar por Telegram" / "Contact via Telegram"

#### Footer
- Mismo footer que los demás

#### Script
```html
<script src="./js/lang.js"></script>
```

## 📋 Patrón de Traducción

### Estructura HTML
```html
<!-- Texto Simple -->
<span data-lang="es">Texto en español</span>
<span data-lang="en">Text in English</span>

<!-- En títulos -->
<h2 class="section-title">
    <span data-lang="es">Título</span>
    <span data-lang="en">Title</span>
</h2>

<!-- En botones -->
<button class="btn">
    <span data-lang="es">Comprar</span>
    <span data-lang="en">Buy</span>
</button>
```

### Traducciones Clave

| Español | English |
|---------|---------|
| Catálogo | Catalog |
| Suscripción | Subscription |
| Camiseta | Jersey |
| Por mes | Per month |
| Envío incluido | Shipping included |
| Cancela cuando quieras | Cancel anytime |
| Preguntas frecuentes | Frequently asked questions |
| Contacto | Contact |
| Todos los derechos reservados | All rights reserved |

## 🧪 Testing

### Checklist de Pruebas
- [ ] El botón de idioma aparece en todas las páginas
- [ ] Al hacer clic en ES/EN cambia el idioma correctamente
- [ ] El idioma se persiste en localStorage
- [ ] Todos los textos cambian sin recargar la página
- [ ] Los elementos ocultos tienen `display: none`
- [ ] Responsive funciona correctamente en móvil
- [ ] Accesibilidad: Alt+L funciona
- [ ] Los enlaces de Stripe siguen funcionando
- [ ] El carousel no se rompe
- [ ] Los FAQs abren/cierran correctamente

## 📝 Notas Importantes

1. **NO usar emojis**: Solo iconos de Font Awesome
2. **Mantener gradientes**: Morado/fucsia (#BA51DD, #DC4CB0)
3. **Script order**: lang.js debe cargarse DESPUÉS del DOM
4. **Accesibilidad**: Usar `aria-label` y `aria-pressed`
5. **Consistencia**: Misma estructura en todas las páginas
6. **SEO**: Actualizar `<html lang="">` dinámicamente

## 🚀 Próximos Pasos

1. Completar traducciones de index.html (secciones planes, features, FAQ, footer)
2. Completar traducciones de mystery-box.html (boxes, features, FAQ, footer) + añadir script
3. Completar traducciones de catalogo.html (todo) + añadir script
4. Testing completo en ambos idiomas
5. Verificar responsive en móvil
6. Commit final y despliegue

## 🔗 Archivos Modificados

- ✅ `js/lang.js` (nuevo)
- ✅ `css/subscription.css` (estilos añadidos)
- ⏳ `index.html` (parcial)
- ⏳ `mystery-box.html` (parcial)
- ⏳ `catalogo.html` (pendiente)
- ✅ `scripts/add-multilang.py` (helper script, opcional)
