# OxaPay Integration - Kickverse

Integración completa de pagos con criptomonedas usando OxaPay API.

---

## Archivos Creados

### 1. `/app/helpers/OxaPayAPI.php` (14KB)
Clase principal del helper con todos los métodos para interactuar con la API de OxaPay.

**Características:**
- Crear invoices de pago
- Verificar estado de pagos
- Validar webhooks con HMAC-SHA512
- Manejo de errores robusto
- Respuestas estructuradas
- Métodos auxiliares (isPaymentCompleted, isPaymentPending)
- Soporte para 11 criptomonedas

### 2. `OXAPAY_USAGE.md` (10KB)
Documentación completa de uso con ejemplos prácticos.

**Contenido:**
- Guía de configuración
- Ejemplos de uso para cada método
- Manejo de webhooks
- Estados de pago
- Troubleshooting
- Links útiles

### 3. `OXAPAY_EXAMPLE_CONTROLLER.php` (13KB)
Ejemplo completo de controlador con implementación real.

**Incluye:**
- Controlador PHP completo
- Endpoints de API REST
- Manejo de webhooks
- Ejemplo de routing
- Ejemplo de vista frontend con JavaScript

### 4. `test_oxapay.php` (6KB)
Script de testing para verificar la integración.

**Tests incluidos:**
- Lista de criptomonedas soportadas
- Crear invoice de prueba
- Verificar estado de pago
- Validar webhook HMAC
- Métodos auxiliares

---

## Configuración Rápida

### Paso 1: Configurar API Key

**Opción A - Variable de entorno (RECOMENDADO):**
```bash
export OXAPAY_API_KEY="4KULOQ-PRQXDI-PGHLQN-CQXRET"
```

**Opción B - Archivo .env:**
```env
OXAPAY_API_KEY=4KULOQ-PRQXDI-PGHLQN-CQXRET
```

**Opción C - Config (ya configurado en `/config/app.php`):**
```php
'oxapay' => [
    'api_key' => '4KULOQ-PRQXDI-PGHLQN-CQXRET',
    'webhook_url' => 'https://kickverse.es/api/webhooks/oxapay',
],
```

### Paso 2: Probar la Integración

```bash
cd /Users/danielgomezmartin/Desktop/3XA/kickverse
php test_oxapay.php
```

Este script:
- Verifica la configuración
- Crea un invoice de prueba
- Muestra la URL de pago
- Verifica el estado
- Prueba la validación de webhooks

### Paso 3: Implementar en tu Aplicación

Ver ejemplos completos en `OXAPAY_EXAMPLE_CONTROLLER.php`

---

## Uso Básico

### Crear un Pago

```php
require_once __DIR__ . '/app/helpers/OxaPayAPI.php';

$oxapay = new OxaPayAPI();

$result = $oxapay->createPayment(
    'ORDER-12345',  // Order ID
    49.99,          // Amount
    'USD',          // Currency
    null,           // Callback URL (null = usar default)
    [
        'email' => 'cliente@ejemplo.com',
        'description' => 'Pedido #12345',
        'lifetime' => 30, // 30 minutos
        'return_url' => 'https://kickverse.es/order/success'
    ]
);

if ($result['success']) {
    $paymentUrl = $result['data']['payment_url'];
    $trackId = $result['data']['track_id'];

    // Redirigir al usuario
    header("Location: {$paymentUrl}");
}
```

### Verificar Estado

```php
$result = $oxapay->getPaymentStatus($trackId);

if ($result['success']) {
    $status = $result['data']['status'];

    if ($oxapay->isPaymentCompleted($trackId)) {
        // Pago completado - procesar pedido
    }
}
```

### Webhook Handler

```php
// POST /api/webhooks/oxapay

$rawData = file_get_contents('php://input');
$hmac = $_SERVER['HTTP_HMAC'] ?? '';

$result = $oxapay->verifyWebhook($rawData, $hmac);

if ($result['success']) {
    $trackId = $result['data']['track_id'];
    $status = $result['data']['status'];

    if ($status === 'paid') {
        // Procesar pedido
    }

    // IMPORTANTE: Responder con 200 OK
    http_response_code(200);
    echo 'ok';
}
```

---

## Métodos Disponibles

| Método | Descripción |
|--------|-------------|
| `createPayment()` | Crear invoice de pago |
| `verifyPayment()` | Verificar estado de pago |
| `getPaymentStatus()` | Obtener info completa del pago |
| `verifyWebhook()` | Validar autenticidad de webhook |
| `isPaymentCompleted()` | Verificar si pago está completado |
| `isPaymentPending()` | Verificar si pago está pendiente |
| `getSupportedCurrencies()` | Lista de criptos soportadas (static) |
| `getStatusLabel()` | Etiqueta en español del estado (static) |

---

## Criptomonedas Soportadas

- **Bitcoin (BTC)**
- **Ethereum (ETH)**
- **Tether (USDT)**
- **USD Coin (USDC)**
- **Litecoin (LTC)**
- **TRON (TRX)**
- **Binance Coin (BNB)**
- **Dai (DAI)**
- **Dogecoin (DOGE)**
- **Toncoin (TON)**
- **Solana (SOL)**

---

## Estados de Pago

| Estado | Significado | Acción |
|--------|-------------|--------|
| `pending` | Esperando inicio de pago | Esperar |
| `paying` | Pago iniciado, esperando confirmación | Actualizar a "Procesando" |
| `paid` | Pago confirmado | **PROCESAR PEDIDO** |
| `confirmed` | Pago completamente confirmado | Pedido completado |
| `failed` | Pago fallido | Cancelar pedido |
| `expired` | Tiempo expirado | Cancelar pedido |

---

## Estructura de Respuestas

Todos los métodos devuelven un array con esta estructura:

```php
[
    'success' => bool,      // true si operación exitosa
    'data' => array|null,   // Datos de respuesta
    'error' => string|null, // Mensaje de error si falló
    'message' => string     // Mensaje descriptivo
]
```

**Ejemplo exitoso:**
```php
[
    'success' => true,
    'data' => [
        'track_id' => 'ABC123XYZ',
        'payment_url' => 'https://pay.oxapay.com/...',
        'expired_at' => 1699364400,
        'date' => 1699360800
    ],
    'error' => null,
    'message' => 'Payment invoice created successfully'
]
```

**Ejemplo error:**
```php
[
    'success' => false,
    'data' => null,
    'error' => 'Invalid parameters: orderId and amount are required',
    'message' => 'Invalid parameters: orderId and amount are required'
]
```

---

## Seguridad

### API Key
- **NUNCA** expongas la API key en código frontend
- Usa variables de entorno en producción
- La API key NO se incluye en respuestas JSON

### Webhooks
- **SIEMPRE** valida webhooks con `verifyWebhook()`
- Usa HMAC-SHA512 para autenticación
- Rechaza webhooks con firma inválida
- Responde con HTTP 200 + "ok" solo si es válido

### HTTPS
- Todos los endpoints de webhook DEBEN usar HTTPS
- OxaPay rechazará URLs HTTP

---

## Configuración del Webhook en OxaPay

1. Accede a **OxaPay Dashboard**: https://oxapay.com/dashboard
2. Ve a **Settings** > **Webhooks**
3. Configura la URL:
   ```
   https://kickverse.es/api/webhooks/oxapay
   ```
4. Guarda los cambios

---

## Testing

### Test Manual
```bash
php test_oxapay.php
```

### Test con cURL
```bash
# Crear pago
curl -X POST https://api.oxapay.com/v1/payment/invoice \
  -H "merchant_api_key: 4KULOQ-PRQXDI-PGHLQN-CQXRET" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1.00,
    "currency": "USD",
    "order_id": "TEST-123",
    "callback_url": "https://kickverse.es/api/webhooks/oxapay"
  }'
```

---

## Troubleshooting

### "API Key not configured"
- Verifica que la API key esté en `config/app.php` o como variable de entorno
- Revisa permisos del archivo de configuración

### "Invalid HMAC signature"
- Usa el raw POST data: `file_get_contents('php://input')`
- NO decodifiques el JSON antes de verificar
- Verifica que estás usando la API key correcta

### Webhook no se recibe
- Verifica que la URL sea pública y accesible
- Revisa logs del servidor web
- Asegúrate de responder con HTTP 200 + "ok"
- Verifica que no haya firewall bloqueando IPs de OxaPay

### Error de conexión cURL
- Verifica conectividad a internet
- Revisa certificados SSL
- Verifica que cURL esté habilitado en PHP

---

## Próximos Pasos

### 1. Integración con Base de Datos
Añadir columnas a la tabla `orders`:
```sql
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(20) DEFAULT 'card';
ALTER TABLE orders ADD COLUMN payment_track_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN payment_data JSON;
```

### 2. Crear Endpoint de Webhook
```php
// routes/api.php
$router->post('/api/webhooks/oxapay', [PaymentController::class, 'handleWebhook']);
```

### 3. Actualizar Checkout Flow
- Añadir opción "Pagar con Criptomonedas"
- Mostrar lista de criptos soportadas
- Redirigir a OxaPay al seleccionar crypto

### 4. Panel de Administración
- Vista de pagos pendientes
- Vista de pagos completados
- Reporte de transacciones cripto

---

## Recursos

### Documentación
- **OxaPay Docs**: https://docs.oxapay.com
- **API Reference**: https://docs.oxapay.com/api-reference
- **Webhook Guide**: https://docs.oxapay.com/webhook

### Dashboard
- **OxaPay Dashboard**: https://oxapay.com/dashboard
- **Transaction History**: https://oxapay.com/dashboard/transactions

### Soporte
- **Email**: support@oxapay.com
- **Website**: https://oxapay.com
- **Help Center**: https://oxapay.help

---

## Licencia

Este código es parte del proyecto Kickverse y está sujeto a su licencia.

---

## Changelog

### v1.0 - 2024-11-07
- Creación inicial del helper OxaPayAPI
- Implementación de todos los métodos principales
- Documentación completa
- Ejemplos de controlador
- Script de testing
- Integración con config existente

---

**Desarrollado para Kickverse by 3XA Design**
