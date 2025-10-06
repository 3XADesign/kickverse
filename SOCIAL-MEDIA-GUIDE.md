# 🎨 Optimizaciones de Imágenes para Redes Sociales

## Dimensiones Recomendadas

### Open Graph (Facebook, LinkedIn)
- **Tamaño**: 1200 x 630 px
- **Ratio**: 1.91:1
- **Formato**: JPG o PNG
- **Peso máximo**: 8 MB

### Twitter Cards
- **Tamaño**: 1200 x 628 px (summary_large_image)
- **Tamaño**: 120 x 120 px (summary)
- **Ratio**: 2:1 o 1:1
- **Formato**: JPG, PNG, WebP, GIF
- **Peso máximo**: 5 MB

### Instagram
- **Stories**: 1080 x 1920 px (9:16)
- **Post cuadrado**: 1080 x 1080 px (1:1)
- **Post landscape**: 1080 x 566 px (1.91:1)

## 📸 Imágenes Recomendadas para Crear

### Para el sitio web:
1. **og-image-default.jpg** (1200x630px)
   - Imagen principal con logo de Kickverse
   - Texto: "Camisetas de Fútbol | 3x2 desde 29,99€"
   - Fondo atractivo con camisetas

2. **og-image-catalog.jpg** (1200x630px)
   - Collage de camisetas más populares
   - Texto: "Más de 200 camisetas disponibles"

3. **og-image-offer.jpg** (1200x630px)
   - Destacar oferta 3x2
   - Call to action visual

### Ubicación recomendada:
```
/img/social/
  ├── og-default.jpg
  ├── og-catalog.jpg
  ├── og-offer.jpg
  ├── twitter-card.jpg
  └── favicon-512.png
```

## 🔄 Actualizar en HTML

Una vez tengas las imágenes, actualiza:

```html
<!-- index.html -->
<meta property="og:image" content="https://kickverse.com/img/social/og-default.jpg">
<meta name="twitter:image" content="https://kickverse.com/img/social/twitter-card.jpg">

<!-- catalogo.html -->
<meta property="og:image" content="https://kickverse.com/img/social/og-catalog.jpg">
<meta name="twitter:image" content="https://kickverse.com/img/social/og-catalog.jpg">
```

## ✅ Testing de Redes Sociales

### Facebook Debugger
https://developers.facebook.com/tools/debug/

### Twitter Card Validator
https://cards-dev.twitter.com/validator

### LinkedIn Post Inspector
https://www.linkedin.com/post-inspector/

## 📝 Alt Tags Implementados

Todas las imágenes del sitio ahora tienen alt tags descriptivos para:
- Mejor accesibilidad (WCAG 2.1)
- Mejor SEO de imágenes
- Mejor experiencia en lectores de pantalla

Ejemplo:
```html
<img src="img/camisetas/laliga_real-madrid_local.png" 
     alt="Camiseta oficial Real Madrid temporada 2024/2025 - Local">
```
