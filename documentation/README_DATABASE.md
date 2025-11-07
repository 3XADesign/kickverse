# KICKVERSE DATABASE - DOCUMENTACIÓN COMPLETA

## Bienvenido a la Exploración de la Base de Datos

Has recibido una exploración **completa y sistemática** de la estructura de base de datos del proyecto Kickverse. Esta documentación está pensada para que construyas un **CRM robusto y bien fundamentado**.

---

## DOCUMENTACIÓN GENERADA

### 1. DATABASE_STRUCTURE.md (1,684 líneas)
**El documento más detallado**

Contiene:
- Descripción completa de cada una de las 35+ tablas
- Todos los campos con tipos de datos, restricciones, índices
- Explicación detallada de cada módulo funcional
- Relaciones (Foreign Keys) y sus comportamientos
- 5 Triggers automáticos con ejemplos
- Flujos de datos completos
- Modelos PHP existentes y sus métodos
- Consultas SQL comunes para CRM
- Archivos críticos del proyecto

**Ideal para:** Entender la estructura profunda, desarrollo de nuevas features

---

### 2. DATABASE_SUMMARY.md (739 líneas)
**La visión ejecutiva**

Contiene:
- Tabla de estadísticas de la BD (35+ tablas, 45+ FKs, 60+ índices)
- 15 módulos principales implementados
- Estructura de clientes con autenticación híbrida
- Flujos de compra paso a paso (Catálogo, Suscripción, Mystery Box, Drops)
- Sistema de puntos de lealtad (tiers, cálculos, recompensas)
- Sistema de pagos (Oxapay, Manual, webhooks)
- Sistema de descuentos (6 cupones preconfigurados)
- Gestión de inventario con alertas automáticas
- 5 Triggers automáticos explicados
- Próximos modelos a crear
- Consultas SQL útiles

**Ideal para:** Decisiones rápidas, reuniones, onboarding

---

### 3. DATABASE_DIAGRAM.md (501 líneas)
**Relaciones y arquitectura**

Contiene:
- Diagrama visual jerárquico de todas las tablas
- Flujos de datos principales (4 flujos principales)
- Índices clave por frecuencia de acceso
- Cardinalidades (1:N, M:N)
- UNIQUE constraints (15+)
- Foreign Key constraints (con ON DELETE behavior)
- Tablas de auditoría automatizadas
- Datos iniciales distribuidos
- Límites de capacidad y escalabilidad
- Rendimiento esperado de queries

**Ideal para:** Arquitectura, optimizaciones, relaciones complejas

---

### 4. CRM_QUICK_REFERENCE.md (700+ líneas)
**La guía rápida de bolsillo**

Contiene:
- Tabla resumen de 15 módulos
- Estadísticas de BD en vistazo
- Autenticación de clientes (tipos soportados)
- 4 Flujos principales resumidos en pasos
- Sistema de lealtad simplificado
- Sistema de pagos simplificado
- 6 Cupones actuales
- Contactos y configuración
- Precios base del sistema
- Métodos de todos los modelos PHP
- Próximos modelos a crear
- Triggers en formato simple

**Ideal para:** Consulta rápida durante desarrollo, cheat sheet

---

## PUNTOS CLAVE ENCONTRADOS

### Base de Datos Sólida
- 35+ tablas bien normalizadas
- 45+ relaciones (Foreign Keys)
- 60+ índices para performance
- 5 Triggers automáticos
- Soft delete implementado
- Multi-idioma (ES/EN)
- Auditoría completa

### Autenticación Híbrida
Clientes pueden autenticarse por:
- Email + Password
- Telegram
- WhatsApp
- Combinación de cualquiera

### Módulos de Negocio Implementados
1. **Productos** - Jerseys, variantes, imágenes, historial
2. **Clientes** - Perfiles, preferencias, direcciones
3. **Órdenes** - Catálogo con personalización (parches, nombres)
4. **Suscripciones** - 4 planes diferentes (FAN, Premium Random, Premium TOP, Retro TOP)
5. **Mystery Boxes** - 3 tipos (Clásica, Por Liga, Premium Elite)
6. **Drops** - Gamificación con rareza (62% común, 30% raro, 8% legendario)
7. **Pagos** - Oxapay (BTC, ETH, USDT) + Manual
8. **Lealtad** - 4 tiers con multiplicadores y recompensas
9. **Promociones** - 6 cupones, campañas, promoción 3x2
10. **Carrito** - Guest y registrados
11. **Wishlist** - Con notificaciones de stock/precio
12. **Inventario** - Movimientos y alertas automáticas
13. **Comunicaciones** - Mensajes y notificaciones
14. **Analytics** - Eventos y vistas
15. **Admin** - Usuarios, auditoría, settings

### Datos Iniciales Preconfigurados
- 6 Ligas (La Liga, Premier, Serie A, Bundesliga, Ligue 1, Selecciones)
- 69 Equipos distribuidos
- 200+ Jerseys (home, away, retro)
- 1400+ Variantes (tallas)
- 4 Planes de suscripción
- 3 Tipos de Mystery Box
- 6 Cupones de descuento
- 4 Tiers de lealtad
- 1 Drop Event (Noviembre 2024)
- 28 Guías de tallas
- 15+ Settings del sistema

### Modelos PHP Existentes
- **Model.php** - Base CRUD
- **Customer.php** - Autenticación múltiple + lealtad
- **Product.php** - Búsqueda, filtrado, stock
- **Order.php** - Creación de órdenes con transacciones
- **League.php** - Ligas y equipos
- **Admin.php** - Magic links para admin
- **Cart.php** - Carrito guest/registrado

---

## PARA CONSTRUIR TU CRM

### Fase 1: Modelos Faltantes (Prioridad Alta)
```
Crear los siguientes modelos PHP:
- Subscription.php
- MysteryBox.php
- DropEvent.php
- Payment.php
- LoyaltyReward.php
- Notification.php
- Analytics.php
- Report.php
```

### Fase 2: Rutas API
```
GET/POST /api/customers/{id}
GET /api/orders/{id}
GET /api/subscriptions/{id}
GET /api/reports/revenue
GET /api/reports/products
POST /api/payments/webhook
GET /api/loyalty/points
etc.
```

### Fase 3: Vistas del Panel Admin
```
Dashboard:
- KPIs clave
- Clientes VIP
- Órdenes pendientes
- Stock bajo
- Suscripciones vencidas

Gestión:
- Clientes (búsqueda, filtrado, edición)
- Órdenes (seguimiento, actualizaciones)
- Suscripciones (renovación, pausas)
- Inventario (movimientos, alertas)
- Reportes (ingresos, productos, lealtad)
```

### Fase 4: Automatizaciones
```
Cron Jobs:
- Procesar suscripciones mensuales
- Renovar pagos de suscripción
- Generar shipments
- Verificar expiración
- Limpiar carritos abandonados
- Enviar notificaciones
- Generar reportes

Webhooks:
- Oxapay payment confirmations
- Notificaciones de envío
- Eventos de usuario
```

---

## CÓMO USAR ESTA DOCUMENTACIÓN

### Empezar Proyecto CRM
1. Lee **DATABASE_SUMMARY.md** primero (vista general)
2. Consulta **DATABASE_STRUCTURE.md** para detalles específicos
3. Usa **DATABASE_DIAGRAM.md** para entender relaciones
4. Mantén **CRM_QUICK_REFERENCE.md** a mano mientras desarrollas

### Desarrollo de Feature Específica
1. Busca en **DATABASE_STRUCTURE.md** la tabla relevante
2. Revisa en **DATABASE_DIAGRAM.md** las relaciones
3. Consulta en **CRM_QUICK_REFERENCE.md** métodos disponibles
4. Escribe el modelo si no existe

### Debugging/Troubleshooting
1. Consulta **DATABASE_DIAGRAM.md** para constraints
2. Revisa **DATABASE_STRUCTURE.md** para triggers
3. Usa consultas SQL de **CRM_QUICK_REFERENCE.md**

### Onboarding del Equipo
- Comparte **DATABASE_SUMMARY.md** primero
- Luego **CRM_QUICK_REFERENCE.md**
- Reference **DATABASE_STRUCTURE.md** según sea necesario
- Dibuja relaciones con **DATABASE_DIAGRAM.md**

---

## ESTADÍSTICAS DE LA DOCUMENTACIÓN

| Documento | Líneas | Tamaño | Propósito |
|-----------|--------|--------|-----------|
| DATABASE_STRUCTURE.md | 1,684 | 46 KB | Referencia técnica completa |
| DATABASE_SUMMARY.md | 739 | 16 KB | Resumen ejecutivo |
| DATABASE_DIAGRAM.md | 501 | 14 KB | Relaciones y arquitectura |
| CRM_QUICK_REFERENCE.md | 700+ | 18 KB | Guía rápida |
| README_DATABASE.md | Este archivo | - | Índice y guía |

**Total:** 3,600+ líneas de documentación detallada

---

## ARCHIVOS DE ESQUEMA (SQL)

### /database/schema.sql
- Creación de 35+ tablas
- 45+ Foreign Keys
- 60+ Índices
- 5 Triggers automáticos
- Charset UTF8MB4
- Soporte multi-idioma

### /database/data_migration.sql
- 6 Ligas
- 69 Equipos
- 200+ Productos
- 1400+ Variantes
- 4 Planes
- 3 Tipos Mystery Box
- 6 Cupones
- Datos iniciales completos

### /config/database.php
- Credenciales de conexión
- Opciones PDO
- Configuración de charset

---

## CONEXIÓN BASE DE DATOS

```
Host:       50.31.174.69
Database:   iqvfmscx_kickverse
User:       iqvfmscx_kickverse
Charset:    utf8mb4
Collation:  utf8mb4_unicode_ci
```

**Acceso:** Configurado en `/config/database.php`

---

## SIGUIENTES PASOS RECOMENDADOS

### Inmediato (Día 1-2)
1. Lee DATABASE_SUMMARY.md completamente
2. Identifica qué modelos necesitas primero
3. Planifica tu estrategia de API

### Corto plazo (Semana 1)
1. Crea los modelos PHP faltantes
2. Implementa rutas API básicas
3. Prueba con datos existentes

### Mediano plazo (Semanas 2-4)
1. Construye panel admin básico
2. Implementa webhooks Oxapay
3. Configura jobs cron

### Largo plazo (Semanas 5+)
1. Reportes avanzados
2. Optimizaciones de performance
3. Features adicionales

---

## PREGUNTAS FRECUENTES

**P: ¿Está la BD lista para producción?**
R: El esquema está listo, pero requiere implementar la lógica de negocio en modelos PHP y rutas API.

**P: ¿Cuántos clientes soporta?**
R: INT UNSIGNED = hasta 4 billones. Para tu escala inicial, illimitado.

**P: ¿Cómo escalo suscripciones?**
R: Los triggers automáticos y el esquema soportan crecimiento. Considera particionamiento después de 1M+ registros.

**P: ¿Qué hay de seguridad?**
R: Implementa: passwords hasheados (Bcrypt), prepared statements (ya en modelos), HTTPS, rate limiting, 2FA en admin.

**P: ¿Cómo agrego nuevas características?**
R: Primero define tablas/campos necesarios, ejecuta migrations, crea modelos PHP, implementa rutas.

---

## SOPORTE Y REFERENCIAS

### Dentro de Kickverse
- /app/models/ - Modelos existentes
- /routes/ - Rutas actuales
- /config/database.php - Configuración
- /database/schema.sql - Esquema SQL

### Documentación Externa
- MySQL 8.0 Docs
- PHP PDO Documentation
- Laravel Eloquent (como referencia ORM)

---

## NOTA FINAL

Esta documentación representa una **exploración exhaustiva** de tu base de datos. Has hecho un trabajo excelente diseñando un sistema escalable con muchos módulos interconectados.

Los datos iniciales (6 ligas, 69 equipos, 200+ productos) están listos. El esquema soporta todos los flujos de negocio (catálogo, suscripciones, mystery boxes, drops, pagos, lealtad).

**Lo que falta es la lógica de aplicación** - los controladores, rutas API, y vistas que hagan que todo esto funcione en un CRM completo.

¡Adelante con la construcción!

---

**Documentación Generada:** 2025-11-06  
**Versión:** 1.0  
**Estado:** Completo  
**Total de líneas:** 3,600+  
**Total de documentos:** 5

---

*Si tienes preguntas sobre la estructura, revisa primero el documento específico, luego consulta DATABASE_STRUCTURE.md para detalles técnicos completos.*

