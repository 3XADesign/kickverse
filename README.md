# 📄 DOCUMENTACIÓN OFICIAL – KICKVERSE ⚽

![Kickverse](./img/logo.png)

🛒 **Kickverse** es una tienda online especializada en camisetas de fútbol de clubes y selecciones, enfocada en una experiencia de compra dinámica y moderna, con diseño en modo oscuro, navegación por cuestionario guiado y atención por WhatsApp Business.

---

## 📌 OBJETIVO DEL PROYECTO

- **Crear una tienda online centrada en el usuario**, rápida, responsive y visualmente impactante.
- **Sustituir el clásico catálogo** por una experiencia de asistente guiado de compra paso a paso.
- **Facilitar el cierre de pedidos** mediante WhatsApp Business (614299735).
- **Mantener escalabilidad** con una estructura de código refactorizada por módulos (HTML, CSS y JS).
- **Estética oscura, deportiva y moderna**, inspirada en dashboards de videojuegos y apps de fútbol.

---

## 📁 ESTRUCTURA DE ARCHIVOS

```
kickverse/
├── index.html             # Landing principal con CTA al formulario
├── form.html              # Cuestionario dinámico paso a paso
├── catalogo.html          # Catálogo clásico alternativo
├── css/
│   ├── base.css           # Variables, resets y modo oscuro
│   ├── layout.css         # Estructura general y responsive
│   ├── header.css         # Navegación superior
│   ├── hero.css           # Estilos del hero principal
│   ├── form.css           # Estilos del formulario dinámico
│   ├── catalogo.css       # Estilos del catálogo visual
│   ├── camiseta-card.css  # Tarjetas de producto individuales
│   ├── modal.css          # Modales de resumen y confirmación
│   ├── footer.css         # Footer completo
│   └── utils.css          # Clases utilitarias
├── js/
│   └── main.js            # Interacciones, lógica del formulario y CTA WhatsApp
├── img/
│   ├── camisetas/         # Camisetas por equipo y equipación
│   ├── clubs/             # Escudos de equipos
│   ├── leagues/           # Logos de ligas
│   ├── payment/           # Iconos de métodos de pago
│   ├── hero-jersey.png    # Imagen destacada del hero
│   └── logo.png           # Logotipo de Kickverse
├── fonts/                 # Tipografías personalizadas (opcional)
├── README.md              # Documentación del proyecto
└── .gitkeep               # Mantiene carpetas vacías en control de versiones
```

---

## 🎨 ESTÉTICA Y LINEAMIENTO VISUAL

### Paleta de Colores

- **Modo oscuro obligatorio**
  - Fondo: `#121212`
  - Texto: `#ffffff`
  - Acentos: púrpura, rosa neón, verde lima (gradientes opcionales)

### Iconografía

- **Solo usar Font Awesome** o frameworks compatibles (nunca emojis ni SVGs sueltos)
- Ejemplo: `<i class="fas fa-shirt"></i>`

### Tipografía

- Sans-serif moderna: **Poppins**, **Inter**, **Montserrat**
- Grosor medio a bold
- Uso de fuentes web mediante Google Fonts

### Diseño Visual

- **Estilo videojuego/dashboard deportivo** (inspiración: carta FIFA o interfaz de jugador)
- Tarjetas grandes tipo carta de jugador con estadísticas visuales
- Efectos de hover con transiciones suaves
- Diseño mobile-first y completamente responsive

---

## ⚙️ FUNCIONALIDADES CLAVE

### ✅ Formulario Dinámico (`form.html`)

**Pasos guiados:**

1. Elige liga
2. Elige equipo
3. Elige equipación (1.ª o 2.ª)
4. Elige talla
5. ¿Parches? Sí/No
6. ¿Personalización? (nombre y dorsal)

➡️ Se genera automáticamente un **resumen** con botón directo a WhatsApp Business, con mensaje preformateado y datos del pedido.

### ✅ WhatsApp Business

- **Teléfono:** 614299735
- **Enlace generado:**

```
https://wa.me/34614299735?text=Hola%20Kickverse!%20Quiero%20comprar:%0A- Equipo: Real Madrid%0A- Talla: L%0A- Equipación: Segunda%0A- Con parches%0A- Personalizada: Mbappé #10
```

### ✅ Promoción Activa

- Al añadir 3 camisetas, se muestra mensaje: **"¡La tercera es GRATIS!"**
- Incentivo para añadir más prendas (CTA con "Añadir otra camiseta")

### ✅ Botón Flotante de WhatsApp

- Presente en todas las páginas
- Acceso rápido al contacto directo
- Animación sutil para llamar la atención

---

## 📐 CONVENCIONES PARA IMÁGENES

### Formato de Nomenclatura

```
/img/camisetas/<liga>_<equipo>_<equipacion>.png
```

### Ejemplos

- `laliga_barcelona_local.png`
- `laliga_barcelona_visitante.png`
- `premier_manutd_local.png`
- `premier_manutd_visitante.png`
- `selecciones_espana_local.png`

### Convenciones

- Nombres en minúsculas
- Guiones bajos para separar palabras
- Equipación: `local` o `visitante` (en lugar de `1` o `2`)
- Formato PNG con fondo transparente
- Resolución recomendada: 800x800px mínimo

---

## 🔌 INTEGRACIONES

### Librerías y Servicios

- **Font Awesome 6** (para todos los iconos)
  ```html
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  ```

- **Google Fonts** (para Poppins o Montserrat)
  ```html
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  ```

- **Google Analytics** (opcional)
- **Meta Pixel** (opcional)
- **Stripe links simulados** (opcional para mostrar checkout, no funcional en esta versión)

---

## 🚀 CÓMO EMPEZAR

### Requisitos

- Navegador moderno (Chrome, Firefox, Safari, Edge)
- Servidor local (opcional): Live Server, XAMPP, MAMP, o Python SimpleHTTPServer

### Instalación

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/3XADesign/kickverse.git
   cd kickverse
   ```

2. Abrir con Live Server o cualquier servidor local
   ```bash
   # Usando Python 3
   python3 -m http.server 8000
   
   # O usando PHP
   php -S localhost:8000
   ```

3. Acceder en el navegador:
   ```
   http://localhost:8000
   ```

---

## 🎯 PÁGINAS PRINCIPALES

### `index.html` - Landing Principal

**Debe incluir:**

- Hero pantalla completa con camiseta destacada
- CTA principal: "Empezar pedido personalizado" → redirige a `form.html`
- Sección con 3 camisetas destacadas
- Sección "¿Por qué Kickverse?" con beneficios
- Promoción 3x2 visible y destacada
- Footer completo con redes sociales y contacto

### `form.html` - Cuestionario Guiado

- Formulario paso a paso dinámico
- Validación en cada paso
- Resumen visual del pedido
- Generación automática de mensaje para WhatsApp
- Botón CTA para finalizar compra

### `catalogo.html` - Catálogo Visual Alternativo

- Mosaico/grid de todas las camisetas disponibles
- Filtros por liga, equipo, equipación
- Tarjetas con:
  - Imagen de la camiseta
  - Nombre del equipo
  - Precio tachado (ej. ~~79,99 €~~)
  - Precio real (29,99 €)
  - Botón WhatsApp individual

---

## 💻 GUÍA DE DESARROLLO

### Estructura Modular CSS

Cada archivo CSS tiene una responsabilidad única:

- `base.css` - Variables CSS, resets, estilos base
- `layout.css` - Grid system, contenedores, responsive
- `header.css` - Navegación y menú
- `hero.css` - Sección hero/banner principal
- `form.css` - Estilos del formulario paso a paso
- `catalogo.css` - Grid de productos y filtros
- `camiseta-card.css` - Tarjetas individuales de productos
- `modal.css` - Ventanas modales y overlays
- `footer.css` - Pie de página
- `utils.css` - Clases utilitarias (spacing, colors, etc.)

### JavaScript (`main.js`)

**Funciones principales:**

- Manejo del formulario dinámico
- Validación de pasos
- Generación de mensaje WhatsApp
- Interacciones de UI (modales, botones)
- Filtrado de productos en catálogo
- Contador de promoción 3x2

---

## 📱 RESPONSIVE DESIGN

### Breakpoints Recomendados

```css
/* Mobile First */
/* Base: 320px - 767px */

/* Tablet */
@media (min-width: 768px) { }

/* Desktop */
@media (min-width: 1024px) { }

/* Large Desktop */
@media (min-width: 1440px) { }
```

---

## ✅ SUPERPROMPT COMPLETO PARA IA O CODIFICACIÓN ASISTIDA

```
Desarrollar una tienda online llamada **Kickverse**, especializada en camisetas 
de fútbol de clubes y selecciones. El diseño debe ser en **modo oscuro total**, 
inspirado en dashboards deportivos, con colores neón, tipografías modernas y 
navegación dinámica por cuestionario.

🔧 Características:
- Web 100% en HTML, CSS y JS (sin frameworks)
- Cuestionario paso a paso: liga → equipo → equipación → talla → parches → personalización
- Al finalizar, generar botón CTA con mensaje directo a WhatsApp Business: `614299735`
- También debe haber catálogo visual clásico (mosaico de camisetas)
- Cada camiseta tiene: imagen, precio tachado (ej. 79,99 €), precio real (29,99 €), 
  y botón de WhatsApp
- CTA clara de promoción: "La tercera camiseta es gratis"
- Todo debe ser responsive y mobile-first
- Solo iconos de **Font Awesome** (nunca emojis ni SVGs sueltos)
- Estética: fondo negro o gris muy oscuro, acentos en rosa/verde neón, 
  tarjetas grandes tipo carta de jugador
- Botón flotante de WhatsApp activo en toda la web

🗂️ Organización modular de código:
- Archivos CSS separados por componente: base, layout, header, hero, form, footer, tarjetas
- Estructura de imágenes clara: `/img/camisetas/laliga_barcelona_local.png`
- JS centralizado en `/js/main.js`

🎯 Página principal (`index.html`) debe tener:
- Hero pantalla completa
- CTA: "Empezar pedido personalizado" → `form.html`
- 3 camisetas destacadas
- Sección "¿Por qué Kickverse?" con beneficios
- Promoción 3x2 visible
- Footer con redes y contacto
```

---

## 🤝 CONTRIBUCIÓN

### Para Desarrolladores

1. Fork el proyecto
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit tus cambios: `git commit -m 'Add: nueva funcionalidad'`
4. Push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

### Para Diseñadores

- Seguir la guía de estilo visual definida
- Usar solo iconos de Font Awesome
- Mantener la paleta de colores oscura
- Crear mockups en Figma antes de implementar

---

## 📞 CONTACTO

- **WhatsApp Business:** +34 614 299 735
- **Email:** contacto@kickverse.com
- **Instagram:** @kickverse
- **Twitter:** @kickverse

---

## 📝 LICENCIA

Este proyecto es propiedad de **3XA Design** y Kickverse. Todos los derechos reservados.

---

## 🎨 INSPIRACIÓN VISUAL

El diseño está inspirado en:
- Dashboards de EA Sports FC (FIFA)
- Aplicaciones deportivas como LaLiga, OneFootball
- Interfaces de videojuegos con estética neón
- Cartas de jugadores con estadísticas visuales

![Inspiración Dashboard](./img/inspiration-dashboard.png)

---

**Versión:** 1.0.0  
**Última actualización:** Octubre 2025  
**Desarrollado por:** 3XA Design
