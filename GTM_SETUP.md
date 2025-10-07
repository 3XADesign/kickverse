# Google Tag Manager - Kickverse

## ✅ Instalación Completada

Google Tag Manager (GTM) ha sido instalado correctamente en todas las páginas del sitio web Kickverse.

### 📋 Detalles de la Instalación

**ID del Contenedor:** `GTM-MQFTT34L`

**Páginas con GTM instalado:**
- ✅ `index.html` - Página principal
- ✅ `form.html` - Formulario de pedido personalizado
- ✅ `catalogo.html` - Catálogo de productos

### 🔧 Ubicación del Código

#### 1. Script principal en `<head>`
```html
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MQFTT34L');</script>
<!-- End Google Tag Manager -->
```

**Ubicación:** Primeras líneas dentro de `<head>`, antes de cualquier otro contenido.

#### 2. Noscript fallback después de `<body>`
```html
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MQFTT34L"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
```

**Ubicación:** Justo después de la etiqueta de apertura `<body>`.

---

## 📊 Eventos Recomendados para Tracking

### 1. **Eventos de E-commerce**

#### Visualización de Producto
```javascript
dataLayer.push({
  'event': 'view_item',
  'ecommerce': {
    'items': [{
      'item_name': 'Camiseta Real Madrid Local',
      'item_id': 'laliga_madrid_local',
      'price': 39.99,
      'item_brand': 'Kickverse',
      'item_category': 'La Liga',
      'item_category2': 'Real Madrid',
      'item_variant': 'Local',
      'quantity': 1
    }]
  }
});
```

#### Añadir al Carrito
```javascript
dataLayer.push({
  'event': 'add_to_cart',
  'ecommerce': {
    'items': [{
      'item_name': 'Camiseta Barcelona Local',
      'item_id': 'laliga_barcelona_local',
      'price': 39.99,
      'item_brand': 'Kickverse',
      'item_category': 'La Liga',
      'quantity': 1
    }]
  }
});
```

#### Iniciar Checkout (WhatsApp)
```javascript
dataLayer.push({
  'event': 'begin_checkout',
  'ecommerce': {
    'value': 119.97,
    'currency': 'EUR',
    'items': [
      // Array de productos en el carrito
    ]
  }
});
```

#### Compra Completada
```javascript
dataLayer.push({
  'event': 'purchase',
  'ecommerce': {
    'transaction_id': 'T123456',
    'value': 119.97,
    'currency': 'EUR',
    'items': [
      // Array de productos comprados
    ]
  }
});
```

### 2. **Eventos de Interacción**

#### Clic en Filtros
```javascript
dataLayer.push({
  'event': 'filter_applied',
  'filter_type': 'liga',
  'filter_value': 'laliga'
});
```

#### Búsqueda
```javascript
dataLayer.push({
  'event': 'search',
  'search_term': 'Real Madrid'
});
```

#### Personalización Completada
```javascript
dataLayer.push({
  'event': 'customization_complete',
  'product_name': 'Real Madrid Local',
  'customization': {
    'size': 'M',
    'patches': true,
    'custom_name': 'RODRÍGUEZ',
    'custom_number': 10
  }
});
```

### 3. **Eventos de Formulario**

#### Paso del Wizard Completado
```javascript
dataLayer.push({
  'event': 'form_step_complete',
  'form_name': 'pedido_personalizado',
  'step_number': 3,
  'step_name': 'seleccion_talla'
});
```

#### Formulario Completado
```javascript
dataLayer.push({
  'event': 'form_complete',
  'form_name': 'pedido_personalizado',
  'form_destination': 'whatsapp'
});
```

### 4. **Eventos de Engagement**

#### Tiempo en Página
```javascript
// Después de 30 segundos
dataLayer.push({
  'event': 'time_on_page',
  'time_seconds': 30
});
```

#### Scroll Profundidad
```javascript
dataLayer.push({
  'event': 'scroll_depth',
  'percent_scrolled': 50
});
```

---

## 🎯 Configuración Recomendada en GTM

### Tags Básicos a Crear:

1. **Google Analytics 4 (GA4)**
   - Tipo: Configuración de GA4
   - Activador: All Pages
   - Measurement ID: G-XXXXXXXXXX

2. **Evento de Añadir al Carrito**
   - Tipo: Evento de GA4
   - Nombre del evento: add_to_cart
   - Activador: Custom Event - add_to_cart

3. **Evento de Compra**
   - Tipo: Evento de GA4
   - Nombre del evento: purchase
   - Activador: Custom Event - purchase

4. **Facebook Pixel** (opcional)
   - Tipo: Facebook Pixel
   - Pixel ID: XXXXXXXXXXXXXXX
   - Activador: All Pages

5. **Evento de WhatsApp Click**
   - Tipo: Evento de GA4
   - Nombre: whatsapp_click
   - Activador: Click en botones de WhatsApp

### Variables Útiles:

1. **Cart Value** - Valor total del carrito
2. **Product Category** - Categoría del producto
3. **User ID** - ID de usuario (si aplica)
4. **Page Path** - Ruta de la página
5. **Click Text** - Texto del elemento clicado

### Activadores Recomendados:

1. **All Pages** - Todas las páginas
2. **Button Clicks** - Clics en botones
3. **Form Submit** - Envío de formularios
4. **Scroll Depth** - 25%, 50%, 75%, 100%
5. **WhatsApp Click** - Clic en botones de WhatsApp
6. **Add to Cart** - Evento personalizado
7. **Purchase** - Evento personalizado

---

## 🧪 Verificación de la Instalación

### Método 1: Google Tag Assistant
1. Instala la extensión "Tag Assistant Legacy" en Chrome
2. Abre cualquier página de Kickverse
3. Haz clic en el icono de Tag Assistant
4. Verifica que GTM aparece en verde

### Método 2: Vista Previa de GTM
1. Ve a https://tagmanager.google.com
2. Selecciona el contenedor GTM-MQFTT34L
3. Haz clic en "Preview" (Vista previa)
4. Ingresa la URL de tu sitio
5. Navega por el sitio y verifica que se disparan los eventos

### Método 3: Consola del Navegador
```javascript
// En la consola del navegador:
dataLayer
// Debe mostrar el array dataLayer con eventos
```

### Método 4: Network Tab
1. Abre DevTools (F12)
2. Ve a la pestaña Network
3. Filtra por "gtm"
4. Recarga la página
5. Verifica que se carga el script gtm.js

---

## 📈 Integraciones Disponibles

Con GTM instalado, puedes integrar fácilmente:

### Analytics
- ✅ Google Analytics 4 (GA4)
- ✅ Google Analytics Universal (UA)
- ✅ Matomo/Piwik
- ✅ Mixpanel
- ✅ Amplitude

### Advertising
- ✅ Google Ads Conversion Tracking
- ✅ Google Ads Remarketing
- ✅ Facebook Pixel
- ✅ TikTok Pixel
- ✅ LinkedIn Insight Tag
- ✅ Twitter Pixel

### Marketing
- ✅ Hotjar
- ✅ Crazy Egg
- ✅ VWO (Visual Website Optimizer)
- ✅ Optimizely
- ✅ Mailchimp

### Chat y Soporte
- ✅ Intercom
- ✅ Drift
- ✅ Zendesk Chat
- ✅ Tidio

---

## 🔒 GDPR y Privacidad

### Consideraciones Importantes:

1. **Banner de Cookies**
   - Recomendación: Instalar Cookiebot o similar
   - Integración con GTM para control de consentimiento

2. **Consent Mode v2**
   - Configurar en GTM para cumplir con GDPR
   - Variables de consentimiento

3. **Data Layer Privacy**
   ```javascript
   // Evitar datos personales en dataLayer
   dataLayer.push({
     'event': 'purchase',
     'user_id': 'HASH_DEL_ID', // No email directo
     'user_type': 'customer'    // Datos agregados OK
   });
   ```

---

## 📱 Testing en Móvil

### iOS Safari
1. Abre Safari en iPhone/iPad
2. Ve a Configuración > Safari > Avanzado > Web Inspector
3. Conecta el dispositivo a Mac
4. Usa Safari Developer Tools

### Android Chrome
1. Activa modo desarrollador en Android
2. Activa "Depuración USB"
3. Conecta a PC
4. Chrome DevTools > Remote Devices

---

## 🚀 Próximos Pasos

### Implementación Inmediata:
1. [ ] Conectar Google Analytics 4
2. [ ] Crear eventos de e-commerce
3. [ ] Configurar objetivos de conversión
4. [ ] Instalar Facebook Pixel (opcional)

### Optimización:
1. [ ] Implementar Enhanced Ecommerce
2. [ ] Configurar embudos de conversión
3. [ ] Crear audiencias personalizadas
4. [ ] A/B testing con Google Optimize

### Análisis:
1. [ ] Dashboard de métricas clave
2. [ ] Informes de conversión por producto
3. [ ] Análisis de abandono de carrito
4. [ ] ROI de campañas publicitarias

---

## 📞 Recursos Útiles

- **Documentación GTM:** https://developers.google.com/tag-manager
- **GA4 Setup:** https://support.google.com/analytics/answer/9304153
- **Data Layer Reference:** https://developers.google.com/tag-platform/tag-manager/datalayer
- **GTM Community:** https://www.simoahava.com/

---

**Última actualización:** 7 de octubre de 2025  
**Versión:** 1.0.0  
**Estado:** ✅ Instalado y verificado
