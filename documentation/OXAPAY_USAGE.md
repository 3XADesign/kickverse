# OxaPay API Integration - Guía de Uso

## Descripción

Helper PHP para integrar pagos con criptomonedas usando la API de OxaPay.

**Archivo:** `/app/helpers/OxaPayAPI.php`

---

## Configuración

### 1. Configurar API Key

La API key puede configurarse de dos formas:

#### Opción A: Variable de entorno (RECOMENDADO)
```bash
export OXAPAY_API_KEY="4KULOQ-PRQXDI-PGHLQN-CQXRET"
```

O en archivo `.env`:
```env
OXAPAY_API_KEY=4KULOQ-PRQXDI-PGHLQN-CQXRET
```

#### Opción B: En config/app.php
```php
'oxapay' => [
    'api_key' => '4KULOQ-PRQXDI-PGHLQN-CQXRET',
    'webhook_url' => 'https://kickverse.es/api/webhooks/oxapay',
],
```

---

## Uso Básico

### 1. Crear un Pago

```php
<?php
require_once __DIR__ . '/app/helpers/OxaPayAPI.php';

$oxapay = new OxaPayAPI();

// Crear invoice de pago
$result = $oxapay->createPayment(
    orderId: 'ORDER-12345',
    amount: 49.99,
    currency: 'USD',
    callbackUrl: null, // null usa el webhook por defecto
    options: [
        'email' => 'cliente@ejemplo.com',
        'description' => 'Pedido #12345 - 2x Camisetas Nike',
        'lifetime' => 60, // 60 minutos para pagar
        'return_url' => 'https://kickverse.es/order/success',
    ]
);

if ($result['success']) {
    $trackId = $result['data']['track_id'];
    $paymentUrl = $result['data']['payment_url'];

    echo "Pago creado exitosamente!\n";
    echo "Track ID: {$trackId}\n";
    echo "URL de pago: {$paymentUrl}\n";

    // Redirigir al usuario a la URL de pago
    header("Location: {$paymentUrl}");
    exit;
} else {
    echo "Error: {$result['error']}\n";
}
```

### 2. Verificar Estado de Pago

```php
<?php
$oxapay = new OxaPayAPI();

$trackId = 'ABC123XYZ'; // Track ID del pago

$result = $oxapay->getPaymentStatus($trackId);

if ($result['success']) {
    $status = $result['data']['status'];
    $amount = $result['data']['amount'];
    $currency = $result['data']['currency'];

    echo "Estado del pago: {$status}\n";
    echo "Monto: {$amount} {$currency}\n";

    if ($oxapay->isPaymentCompleted($trackId)) {
        echo "Pago completado! Procesar pedido...\n";
        // Actualizar base de datos, enviar confirmación, etc.
    }
} else {
    echo "Error: {$result['error']}\n";
}
```

### 3. Verificar Webhook (Callback)

```php
<?php
// Archivo: /api/webhooks/oxapay.php

require_once __DIR__ . '/../../app/helpers/OxaPayAPI.php';

$oxapay = new OxaPayAPI();

// Obtener datos del webhook
$rawPostData = file_get_contents('php://input');
$hmacHeader = $_SERVER['HTTP_HMAC'] ?? '';

// Verificar autenticidad del webhook
$result = $oxapay->verifyWebhook($rawPostData, $hmacHeader);

if ($result['success']) {
    $trackId = $result['data']['track_id'];
    $status = $result['data']['status'];
    $amount = $result['data']['amount'];
    $currency = $result['data']['currency'];

    error_log("Webhook recibido - Track ID: {$trackId}, Status: {$status}");

    // Procesar según el estado
    switch (strtolower($status)) {
        case 'paying':
            // Pago iniciado, esperando confirmación blockchain
            updateOrderStatus($trackId, 'processing');
            break;

        case 'paid':
            // Pago confirmado - PROCESAR PEDIDO
            updateOrderStatus($trackId, 'paid');
            processOrder($trackId);
            sendConfirmationEmail($trackId);
            break;

        case 'failed':
            // Pago fallido
            updateOrderStatus($trackId, 'failed');
            break;
    }

    // IMPORTANTE: Responder con 200 OK
    http_response_code(200);
    echo 'ok';
} else {
    error_log("Webhook inválido: {$result['error']}");
    http_response_code(400);
    echo 'Invalid webhook';
}
```

---

## Métodos Disponibles

### `createPayment($orderId, $amount, $currency, $callbackUrl, $options)`

Crea un invoice de pago en OxaPay.

**Parámetros:**
- `$orderId` (string): ID único del pedido
- `$amount` (float): Monto a cobrar
- `$currency` (string): Moneda (USD, EUR, etc.)
- `$callbackUrl` (string|null): URL del webhook (null = usar default)
- `$options` (array): Opciones adicionales
  - `email`: Email del cliente
  - `description`: Descripción del pedido
  - `lifetime`: Tiempo de expiración en minutos (15-2880)
  - `return_url`: URL de retorno después del pago
  - `fee_paid_by_payer`: 1 = cliente paga comisión, 0 = comerciante
  - `under_paid_coverage`: Diferencia aceptable en pago (0-60%)

**Retorna:**
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

---

### `verifyPayment($trackId)`

Alias de `getPaymentStatus()` - Verifica el estado de un pago.

**Retorna:** Igual que `getPaymentStatus()`

---

### `getPaymentStatus($trackId)`

Obtiene información completa del pago.

**Retorna:**
```php
[
    'success' => true,
    'data' => [
        'track_id' => 'ABC123XYZ',
        'status' => 'paid',
        'amount' => 49.99,
        'currency' => 'USD',
        'type' => 'invoice',
        'order_id' => 'ORDER-12345',
        'email' => 'cliente@ejemplo.com',
        'description' => 'Pedido #12345',
        'callback_url' => 'https://...',
        'return_url' => 'https://...',
        'expired_at' => 1699364400,
        'date' => 1699360800,
        'txs' => [
            [
                'tx_hash' => '0x...',
                'amount' => 0.00125,
                'currency' => 'BTC',
                'network' => 'bitcoin',
                'confirmations' => 3,
                'status' => 'confirmed'
            ]
        ],
        'raw' => [...] // Respuesta completa de la API
    ],
    'error' => null,
    'message' => 'Payment information retrieved successfully'
]
```

---

### `verifyWebhook($rawPostData, $hmacHeader)`

Verifica la autenticidad de un webhook usando HMAC-SHA512.

**Parámetros:**
- `$rawPostData`: Contenido raw del POST (`file_get_contents('php://input')`)
- `$hmacHeader`: Header HMAC (`$_SERVER['HTTP_HMAC']`)

**Retorna:**
```php
[
    'success' => true,
    'data' => [
        'track_id' => 'ABC123XYZ',
        'status' => 'paid',
        'type' => 'invoice',
        'amount' => 49.99,
        'currency' => 'USD',
        'txs' => [...],
        'raw' => [...] // Payload completo del webhook
    ],
    'error' => null,
    'message' => 'Webhook verified successfully'
]
```

---

### `isPaymentCompleted($trackId)`

Verifica si un pago está completado.

**Retorna:** `bool` - `true` si status = "paid"

---

### `isPaymentPending($trackId)`

Verifica si un pago está pendiente.

**Retorna:** `bool` - `true` si status = "pending" o "paying"

---

### `getSupportedCurrencies()` (static)

Obtiene lista de criptomonedas soportadas.

**Retorna:**
```php
[
    'BTC' => 'Bitcoin',
    'ETH' => 'Ethereum',
    'USDT' => 'Tether (USDT)',
    'USDC' => 'USD Coin',
    'LTC' => 'Litecoin',
    'TRX' => 'TRON',
    'BNB' => 'Binance Coin',
    'DAI' => 'Dai',
    'DOGE' => 'Dogecoin',
    'TON' => 'Toncoin',
    'SOL' => 'Solana',
]
```

---

### `getStatusLabel($status)` (static)

Obtiene etiqueta en español del estado.

**Retorna:** `string` - Ej: "Pagado", "Pendiente", "Procesando pago"

---

## Estados de Pago

| Estado | Descripción | Acción |
|--------|-------------|--------|
| `pending` | Esperando inicio de pago | Esperar |
| `paying` | Pago iniciado, esperando confirmaciones blockchain | Esperar, actualizar estado a "Procesando" |
| `paid` | Pago confirmado | **PROCESAR PEDIDO** |
| `confirmed` | Pago completamente confirmado | Pedido completado |
| `failed` | Pago fallido | Cancelar pedido |
| `expired` | Tiempo de pago expirado | Cancelar pedido |

---

## Ejemplo de Integración Completa

```php
<?php
// 1. Crear pago al confirmar orden
function createCryptoPayment($orderId, $totalAmount, $customerEmail) {
    $oxapay = new OxaPayAPI();

    $result = $oxapay->createPayment(
        $orderId,
        $totalAmount,
        'USD',
        null, // Usar webhook por defecto
        [
            'email' => $customerEmail,
            'description' => "Kickverse Order #{$orderId}",
            'lifetime' => 30, // 30 minutos
            'return_url' => "https://kickverse.es/order/{$orderId}/success"
        ]
    );

    if ($result['success']) {
        // Guardar track_id en base de datos
        saveTrackId($orderId, $result['data']['track_id']);

        // Redirigir a página de pago
        return $result['data']['payment_url'];
    }

    return false;
}

// 2. Verificar estado manualmente (opcional, como backup del webhook)
function checkPaymentStatus($orderId) {
    $trackId = getTrackId($orderId);

    $oxapay = new OxaPayAPI();
    $result = $oxapay->getPaymentStatus($trackId);

    if ($result['success']) {
        return $result['data']['status'];
    }

    return 'unknown';
}

// 3. Procesar webhook automático
// Ver ejemplo en sección "Verificar Webhook" arriba
```

---

## Seguridad

1. **API Key**: Nunca expongas la API key en código frontend
2. **HMAC Verification**: Siempre verifica el webhook con `verifyWebhook()`
3. **HTTPS**: Usa HTTPS para todos los endpoints de webhook
4. **Variables de entorno**: Guarda credenciales en variables de entorno

---

## Troubleshooting

### Error: "API Key not configured"
- Verifica que la API key esté en `config/app.php` o como variable de entorno
- Verifica que el archivo de config se carga correctamente

### Error: "Invalid HMAC signature"
- Asegúrate de pasar el raw POST data: `file_get_contents('php://input')`
- Verifica que estás usando la API key correcta
- NO proceses el JSON antes de verificar el HMAC

### Webhook no se recibe
- Verifica que tu URL de webhook sea accesible públicamente
- Revisa los logs del servidor web
- Verifica que el endpoint retorne HTTP 200 con "ok"

---

## Links Útiles

- **Documentación oficial:** https://docs.oxapay.com
- **Dashboard OxaPay:** https://oxapay.com/dashboard
- **API Reference:** https://docs.oxapay.com/api-reference
- **Webhook Guide:** https://docs.oxapay.com/webhook

---

## Contacto y Soporte

Para soporte técnico de OxaPay:
- Email: support@oxapay.com
- Website: https://oxapay.com
