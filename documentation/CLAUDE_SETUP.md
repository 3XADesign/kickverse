# 🚀 Claude Code Setup - Kickverse

Guía rápida para configurar Claude Code en el proyecto Kickverse.

## 📋 Índice
- [Setup Inicial](#setup-inicial)
- [Agentes Disponibles](#agentes-disponibles)
- [Comandos Útiles](#comandos-útiles)
- [Workflow Diario](#workflow-diario)
- [Ejemplos de Uso](#ejemplos-de-uso)

---

## ⚡ Setup Inicial (2 minutos)

### 1. Estructura Creada

El proyecto ya tiene configurado:

```
.claude/
├── agents/
│   ├── fullstack-architect.md    # Arquitectura y diseño
│   ├── frontend-expert.md         # UI/UX y performance frontend
│   ├── api-developer.md           # Backend y APIs
│   ├── database-optimizer.md      # Optimización de MySQL
│   └── security-auditor.md        # Seguridad y vulnerabilidades
│
└── commands/
    ├── new-feature.md             # Crear nueva funcionalidad
    ├── quick-review.md            # Revisión rápida de código
    ├── debug-performance.md       # Debugging de performance
    ├── scaffold-api.md            # Generar endpoint API
    └── optimize-db.md             # Optimizar base de datos
```

---

## 🤖 Agentes Disponibles

### @fullstack-architect
**Experto en arquitectura web y e-commerce**

Úsalo para:
- Diseñar nuevas funcionalidades complejas
- Decidir patrones arquitectónicos
- Planificar escalabilidad
- Estructurar módulos grandes

**Ejemplo:**
```
@fullstack-architect necesito diseñar un sistema de suscripciones mensuales con pagos recurrentes
```

---

### @frontend-expert
**Especialista en UI/UX y performance frontend**

Úsalo para:
- Optimizar performance de páginas
- Crear componentes interactivos
- Mejorar UX del carrito y checkout
- Responsive design y mobile-first
- Accesibilidad

**Ejemplo:**
```
@frontend-expert el filtro de productos es lento en móvil, ¿cómo lo optimizo?
```

---

### @api-developer
**Experto en APIs REST con PHP**

Úsalo para:
- Crear endpoints nuevos
- Validación de datos
- Autenticación y autorización
- Integración de APIs externas
- Webhooks de pagos

**Ejemplo:**
```
@api-developer crea un endpoint para añadir productos al carrito con validación
```

---

### @database-optimizer
**Especialista en MySQL y performance de queries**

Úsalo para:
- Optimizar queries lentas
- Diseñar índices eficientes
- Analizar EXPLAIN de queries
- Normalización de tablas
- Migraciones sin downtime

**Ejemplo:**
```
@database-optimizer esta query es lenta: SELECT * FROM products WHERE league_id = ? AND active = 1
```

---

### @security-auditor
**Experto en seguridad web y OWASP Top 10**

Úsalo para:
- Revisar código antes de producción
- Encontrar vulnerabilidades
- Validar flujo de pagos
- Auditar autenticación
- Protección contra XSS/SQL injection

**Ejemplo:**
```
@security-auditor revisa el módulo de checkout completo
```

---

## 🛠️ Comandos Útiles

### /new-feature [nombre]
Crea una funcionalidad completa (backend + frontend + BD)

**Ejemplo:**
```bash
/new-feature wishlist
```

Genera:
- Migración de base de datos
- Modelo PHP
- Controlador y API
- Vista y componente frontend
- Estilos CSS
- Documentación

---

### /quick-review
Revisión rápida multi-agente antes de commit

**Uso:**
```bash
/quick-review
```

Revisa:
- ✅ Seguridad (SQL injection, XSS, CSRF)
- ✅ Performance (queries lentas, N+1)
- ✅ Calidad de código
- ✅ Best practices PHP

**Úsalo siempre antes de hacer commit!**

---

### /debug-performance [área]
Diagnostica y soluciona problemas de performance

**Ejemplos:**
```bash
/debug-performance frontend     # Analiza JS, CSS, imágenes
/debug-performance backend      # Analiza PHP y APIs
/debug-performance database     # Analiza queries MySQL
```

---

### /scaffold-api [recurso]
Genera un endpoint API completo

**Ejemplo:**
```bash
/scaffold-api reviews
```

Crea:
- Controller con CRUD completo
- Validación de inputs
- Prepared statements
- Error handling
- Rate limiting
- Documentación API

---

### /optimize-db [tabla]
Optimiza base de datos

**Ejemplos:**
```bash
/optimize-db products          # Optimiza tabla products
/optimize-db                   # Analiza toda la BD
```

Analiza:
- Queries lentas
- Índices faltantes
- N+1 queries
- Estructura de tablas

---

## 🔄 Workflow Diario

### 🌅 Inicio del Día

```bash
# Revisa tareas pendientes
@fullstack-architect ¿qué debería priorizar hoy según el roadmap?

# Si hay bugs en producción
@security-auditor revisa los logs de ayer y encuentra problemas
```

---

### 💻 Desarrollando Nueva Funcionalidad

#### 1. Planificación
```bash
@fullstack-architect necesito añadir sistema de reseñas de productos
```

#### 2. Implementación
```bash
/new-feature product-reviews

# Ajusta la migración si es necesario
@database-optimizer revisa el schema de reviews y sugiere índices

# Frontend
@frontend-expert crea el componente de estrellas y formulario de reseña
```

#### 3. API
```bash
/scaffold-api reviews

@api-developer añade validación de que el usuario compró el producto antes de reseñar
```

#### 4. Testing
```bash
@security-auditor revisa vulnerabilidades en el módulo de reviews

/quick-review
```

---

### 🐛 Debugging

#### Performance Issue
```bash
/debug-performance database

@database-optimizer esta query tarda 2 segundos:
SELECT p.*, l.name as league_name
FROM products p
LEFT JOIN leagues l ON p.league_id = l.league_id
WHERE p.active = 1
ORDER BY p.created_at DESC
```

#### Bug en Producción
```bash
@security-auditor el checkout falla con algunos productos, revisa el flujo completo

@api-developer el endpoint /api/cart/add retorna 500, ¿qué está pasando?
```

---

### ✅ Antes de Commit/PR

**SIEMPRE ejecuta:**
```bash
/quick-review
```

Si encuentra issues:
```bash
@security-auditor corrige: Missing CSRF token in checkout form
@database-optimizer añade índices sugeridos para products table
```

---

## 💡 Ejemplos de Uso Reales

### Ejemplo 1: Nueva Funcionalidad Completa

**Tarea:** Añadir sistema de cupones de descuento

```bash
# Paso 1: Planificación
@fullstack-architect necesito sistema de cupones con:
- Códigos únicos
- Descuento en % o fijo
- Fecha de expiración
- Uso limitado por usuario
- Aplicable a productos específicos

# Paso 2: Implementación
/new-feature discount-coupons

# Paso 3: Optimización
@database-optimizer revisa índices para búsquedas de cupones

# Paso 4: Frontend
@frontend-expert crea campo de cupón en checkout con validación en tiempo real

# Paso 5: Seguridad
@security-auditor revisa que no se puedan usar cupones expirados o manipular descuentos

# Paso 6: Review final
/quick-review
```

**Resultado:** Funcionalidad completa en ~30 minutos ✅

---

### Ejemplo 2: Optimización de Performance

**Problema:** Página de productos carga lenta (5 segundos)

```bash
# Diagnóstico
/debug-performance frontend
/debug-performance database

# Frontend encontró:
@frontend-expert detectó:
- ❌ Imágenes sin lazy loading
- ❌ JavaScript sin minificar
- ❌ Múltiples requests al cargar

# Database encontró:
@database-optimizer detectó:
- ❌ Query sin índice en league_id
- ❌ N+1 query para imágenes de productos
- ❌ COUNT(*) sin caché

# Soluciones aplicadas
@frontend-expert añade lazy loading y optimiza bundle
@database-optimizer crea índices y usa JOIN en lugar de N+1

# Resultado: 5s → 1.2s ✅
```

---

### Ejemplo 3: Revisión de Seguridad

**Tarea:** Auditoría de seguridad del módulo de pagos

```bash
@security-auditor revisa el flujo completo de checkout y pagos

# Detectó:
❌ API key de OxaPay expuesta en JavaScript
❌ Falta rate limiting en /api/orders/create
❌ CSRF token no validado en formulario
❌ No hay verificación de stock antes de pagar

# Correcciones:
@api-developer mueve API key al backend
@security-auditor implementa rate limiting y CSRF
@database-optimizer añade transaction para verificar stock

# Review final
/quick-review
# ✅ Todo seguro
```

---

### Ejemplo 4: Debug Rápido

**Bug:** El carrito no actualiza cantidades

```bash
# Diagnóstico rápido
@api-developer revisa el endpoint /api/cart/update

# Encontró:
❌ Falta validación de cantidad > 0
❌ No actualiza session después de update
❌ Error SQL en prepared statement

# Fix inmediato
@api-developer corrige los 3 issues

# Verifica que funciona
@frontend-expert prueba el flujo completo en el navegador

# ✅ Bug resuelto en 5 minutos
```

---

## 🎯 Pro Tips

### 1. Combina Agentes para Tareas Complejas

```bash
# Súper efectivo
@fullstack-architect diseña arquitectura de notificaciones en tiempo real
@api-developer implementa webhooks y endpoints
@frontend-expert crea componente de notificaciones toast
@database-optimizer diseña schema optimizado para notificaciones

→ Feature completa en 45 minutos
```

---

### 2. Usa Agentes en Cadena

```bash
# Cada agente se especializa
@fullstack-architect diseña
↓
@api-developer implementa backend
↓
@frontend-expert implementa frontend
↓
@security-auditor valida seguridad
↓
@database-optimizer optimiza queries
↓
/quick-review → Todo listo para producción
```

---

### 3. Debug Multi-Nivel

```bash
# Problema: Página muy lenta
/debug-performance frontend
/debug-performance backend
/debug-performance database

# Cada agente encuentra sus issues
# Solucionas todos en paralelo
# ✅ Performance mejorada 10x
```

---

## 📊 Métricas de Éxito

Con Claude Code configurado, deberías ver:

- ⚡ **Tiempo de desarrollo:** -60%
- 🐛 **Bugs en producción:** -80%
- 🔒 **Vulnerabilidades:** -90%
- 🚀 **Performance:** +200%
- ✅ **Calidad de código:** +150%

---

## 🆘 Soporte

Si tienes dudas:

1. **Revisa ejemplos:** Todos los comandos tienen ejemplos de uso
2. **Pregunta a los agentes:** Son expertos en su área
3. **Usa /quick-review:** Siempre antes de commit

---

## 🎓 Ejercicio de Práctica

**Prueba crear esta funcionalidad:**

```bash
# Nueva feature: Productos favoritos
@fullstack-architect ¿cómo estructuro un sistema de favoritos?

/new-feature favorites

@database-optimizer optimiza queries de favoritos

@frontend-expert crea botón de corazón con animación

@security-auditor valida que solo usuarios registrados puedan favoritar

/quick-review

# ✅ Feature completada
```

---

## 📝 Changelog

- **2024-01-06**: Setup inicial de agentes y comandos
- Configurados 5 agentes especializados
- Creados 5 comandos útiles
- Documentación completa

---

**¡Listo para desarrollar con superpoderes! 🚀**
