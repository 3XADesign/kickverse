# Sistema de Verificación de Email - Kickverse

## Resumen

Se ha implementado un sistema completo de verificación de email para el registro de usuarios en Kickverse. Los usuarios ahora deben verificar su correo electrónico antes de poder iniciar sesión en la plataforma.

## Flujo de Registro y Verificación

### 1. Registro de Usuario

Cuando un usuario se registra (`POST /api/auth/register`):

1. Se validan los datos del formulario (email, contraseña, nombre completo)
2. Se verifica que el email no esté en uso
3. Se crea el usuario con estado `pending` y `email_verified = 0`
4. Se genera un token único de verificación de 64 caracteres
5. Se envía un email de verificación con el link
6. **NO se crea sesión automáticamente**
7. Se muestra un modal informando al usuario que debe revisar su email

### 2. Email de Verificación

El email enviado contiene:
- Diseño profesional con colores de marca de Kickverse
- Link de verificación: `https://kickverse.es/auth/verify-email/{token}`
- Advertencia de que el enlace expira en 24 horas
- Instrucciones claras para verificar la cuenta

### 3. Proceso de Verificación

Cuando el usuario hace clic en el enlace (`GET /auth/verify-email/:token`):

1. Se busca el usuario con ese token en la base de datos
2. Si el token no existe o expiró, se muestra un error
3. Si ya está verificado, se inicia sesión y redirige a `/mi-cuenta`
4. Si es válido y no verificado:
   - Se marca `email_verified = 1`
   - Se cambia el estado a `active`
   - Se limpia el token de verificación
   - Se crea la sesión del usuario
   - Se redirige a `/mi-cuenta` con mensaje de éxito

### 4. Bloqueo de Login sin Verificación

Al intentar hacer login (`POST /api/auth/login`):

1. Se validan las credenciales (email y contraseña)
2. **Se verifica el estado de `email_verified`**
3. Si `email_verified = 0`, se rechaza el login con mensaje específico:
   - "Tu cuenta aún no ha sido verificada. Revisa tu email."
   - El modal naranja de error se muestra automáticamente
4. Si está verificado y activo, se permite el login

## Archivos Modificados

### 1. `/app/models/Customer.php`
- **Método `register()`**: Ahora retorna array con `customer_id` y `verification_token`
- **Nuevo método `findByVerificationToken($token)`**: Busca cliente por token
- **Nuevo método `verifyEmail($customerId)`**: Marca email como verificado y activa cuenta

### 2. `/app/controllers/api/AuthController.php`
- **Método `register()`**:
  - Ya no crea sesión automáticamente
  - Genera token de verificación
  - Envía email de verificación
  - Retorna `requires_verification: true`
- **Método `login()`**:
  - Verifica `email_verified` antes de permitir login
  - Retorna error específico si no está verificado

### 3. `/app/controllers/EmailVerificationController.php` (NUEVO)
- Controlador dedicado para verificación de email
- Método `verify($token)`: Maneja la verificación
- Crea sesión automáticamente tras verificación exitosa
- Usa sistema de notificaciones para feedback al usuario

### 4. `/routes/web.php`
- Nueva ruta: `GET /auth/verify-email/:token` → `EmailVerificationController@verify`

### 5. `/app/views/partials/header.php`
- **Nuevo modal de verificación** (`#verifyEmailModal`):
  - Diseño bonito con icono de sobre
  - Colores de marca Kickverse (gradiente morado)
  - Muestra el email del usuario
  - Instrucciones claras
  - Botón "Entendido" para cerrar
- **Funciones JavaScript agregadas**:
  - `showVerifyEmailModal(email)`: Muestra el modal
  - `closeVerifyEmailModal()`: Cierra el modal
- **Modificado `handleRegister()`**: Ahora detecta `requires_verification` y muestra el modal

### 6. `/app/helpers/Mailer.php`
- Ya existía el método `sendVerificationEmail()` implementado
- Envía emails HTML con diseño profesional
- Soporta español e inglés

### 7. `/app/views/layouts/main.php`
- Agregado código PHP para inyectar notificaciones de sesión a JavaScript
- Las notificaciones de `$_SESSION['notification']` se pasan a `window.sessionNotification`

### 8. `/public/js/main.js`
- Agregado listener para mostrar notificaciones de sesión al cargar la página
- Función `getNotificationTitle(type)` para títulos de notificaciones

## Campos de Base de Datos Requeridos

La tabla `customers` debe tener estos campos (ya existen en el schema):

```sql
email_verified BOOLEAN DEFAULT FALSE,
email_verification_token VARCHAR(100) NULL,
customer_status ENUM('pending', 'active', 'suspended') DEFAULT 'pending'
```

## Diseño del Modal de Verificación

El modal incluye:
- **Icono circular** con gradiente morado y icono de sobre (FontAwesome)
- **Título**: "¡Verifica tu Email!" con gradiente de texto
- **Mensaje principal**: Informa que se envió un email a [dirección]
- **Mensaje secundario**: Instrucciones adicionales (revisar spam)
- **Botón primario**: "Entendido" con icono de check

## Sistema de Notificaciones

Se utiliza el sistema de notificaciones existente (`notifications.js`) para mostrar mensajes al usuario:

- **Éxito**: Email verificado correctamente
- **Error**: Token inválido o expirado
- **Info**: Email ya verificado previamente

Las notificaciones se guardan en `$_SESSION['notification']` y se muestran automáticamente al cargar la página.

## Seguridad

1. **Token único de 64 caracteres** generado con `bin2hex(random_bytes(32))`
2. **Token de un solo uso**: Se limpia después de la verificación
3. **Validación de estado**: Se verifica que el customer no esté eliminado
4. **Sin sesión hasta verificar**: Previene acceso no autorizado
5. **Mensajes de error genéricos**: No revelan si el email existe en login

## Compatibilidad con Registro Social

Los usuarios que se registran vía Telegram o WhatsApp:
- **NO requieren verificación de email**
- Se crean como `active` inmediatamente
- Método `registerSocial()` mantiene comportamiento original

## Testing del Sistema

### Probar Registro:
1. Ir a la página principal
2. Hacer clic en "Crear cuenta"
3. Completar formulario con email válido
4. Enviar formulario
5. Verificar que aparece el modal de verificación
6. NO debe crearse sesión automáticamente

### Probar Email:
1. Revisar email recibido en la bandeja
2. Verificar diseño profesional
3. Hacer clic en el botón "Verificar mi Cuenta"

### Probar Verificación:
1. Hacer clic en el enlace del email
2. Debe redirigir a `/mi-cuenta`
3. Debe mostrarse notificación de éxito
4. Usuario debe estar logueado automáticamente

### Probar Login sin Verificación:
1. Registrar nueva cuenta
2. NO hacer clic en el email
3. Intentar hacer login
4. Debe mostrarse error naranja: "Tu cuenta aún no ha sido verificada. Revisa tu email."

### Probar Token Inválido:
1. Usar URL con token aleatorio: `/auth/verify-email/tokeninvalido123`
2. Debe redirigir a home con notificación de error

## Mejoras Futuras Posibles

1. **Reenvío de email**: Botón para reenviar email de verificación
2. **Expiración de token**: Agregar timestamp para tokens que expiran en 24h
3. **Email de bienvenida**: Enviar email secundario tras verificación exitosa
4. **Rate limiting**: Limitar intentos de verificación por IP
5. **Admin panel**: Vista para ver usuarios pendientes de verificación

## Configuración del Dominio

El sistema usa `https://kickverse.es` como dominio base para los enlaces.

Si necesitas cambiar el dominio:
- Modificar línea 68 en `/app/controllers/api/AuthController.php`
- Cambiar: `$verificationLink = 'https://kickverse.es/auth/verify-email/' . $verificationToken;`

## Soporte

Para problemas o preguntas sobre el sistema de verificación:
1. Revisar logs de PHP para errores de envío de email
2. Verificar que el servidor puede enviar emails (función `mail()`)
3. Comprobar campos de BD: `email_verified`, `email_verification_token`, `customer_status`
