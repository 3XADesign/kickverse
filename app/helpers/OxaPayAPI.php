<?php
/**
 * OxaPay API Helper
 * Integration with OxaPay Cryptocurrency Payment Gateway
 *
 * Documentation: https://docs.oxapay.com
 * Version: 1.0
 */

class OxaPayAPI {
    /**
     * OxaPay API Base URL
     */
    private const BASE_URL = 'https://api.oxapay.com';

    /**
     * API Key (loaded from config or environment)
     */
    private $apiKey;

    /**
     * Webhook URL for payment notifications
     */
    private $webhookUrl;

    /**
     * Constructor - Initialize API credentials from config
     */
    public function __construct() {
        // Load config
        $config = require dirname(__DIR__, 2) . '/config/app.php';

        // Get API key from environment variable or config (prioritize env vars for security)
        $this->apiKey = getenv('OXAPAY_API_KEY') ?: ($config['oxapay']['api_key'] ?? '');
        $this->webhookUrl = $config['oxapay']['webhook_url'] ?? '';

        if (empty($this->apiKey)) {
            error_log('OxaPayAPI: API Key is not configured');
        }
    }

    /**
     * Create a payment invoice
     *
     * @param string $orderId Unique order identifier
     * @param float $amount Payment amount (in USD by default or specified currency)
     * @param string $currency Currency code (default: USD)
     * @param string $callbackUrl Custom callback URL (optional, uses default if not provided)
     * @param array $options Additional options: email, description, lifetime, return_url, etc.
     *
     * @return array Response with structure: [
     *   'success' => bool,
     *   'data' => array|null (track_id, payment_url, expired_at, date),
     *   'error' => string|null,
     *   'message' => string
     * ]
     */
    public function createPayment($orderId, $amount, $currency = 'USD', $callbackUrl = null, $options = []) {
        // Validate required parameters
        if (empty($orderId) || empty($amount) || $amount <= 0) {
            return $this->errorResponse('Invalid parameters: orderId and amount are required');
        }

        if (empty($this->apiKey)) {
            return $this->errorResponse('API Key not configured');
        }

        // Prepare request payload
        $payload = [
            'amount' => (float) $amount,
            'currency' => strtoupper($currency),
            'order_id' => (string) $orderId,
            'callback_url' => $callbackUrl ?: $this->webhookUrl,
        ];

        // Add optional parameters
        if (isset($options['email'])) {
            $payload['email'] = $options['email'];
        }

        if (isset($options['description'])) {
            $payload['description'] = $options['description'];
        }

        if (isset($options['lifetime'])) {
            $payload['lifetime'] = max(15, min(2880, (int) $options['lifetime'])); // 15-2880 minutes
        }

        if (isset($options['return_url'])) {
            $payload['return_url'] = $options['return_url'];
        }

        if (isset($options['fee_paid_by_payer'])) {
            $payload['fee_paid_by_payer'] = (int) $options['fee_paid_by_payer'];
        }

        if (isset($options['under_paid_coverage'])) {
            $payload['under_paid_coverage'] = (float) $options['under_paid_coverage'];
        }

        // Make API request
        $response = $this->request('POST', '/v1/payment/invoice', $payload);

        // Process response
        if ($response['success'] && isset($response['data']['track_id'])) {
            return [
                'success' => true,
                'data' => [
                    'track_id' => $response['data']['track_id'],
                    'payment_url' => $response['data']['payment_url'],
                    'expired_at' => $response['data']['expired_at'] ?? null,
                    'date' => $response['data']['date'] ?? null,
                ],
                'error' => null,
                'message' => 'Payment invoice created successfully'
            ];
        }

        return $this->errorResponse(
            $response['message'] ?? 'Failed to create payment invoice',
            $response['data'] ?? null
        );
    }

    /**
     * Verify payment status by track_id
     *
     * @param string $trackId Unique payment tracking identifier
     *
     * @return array Response with structure: [
     *   'success' => bool,
     *   'data' => array|null (complete payment information),
     *   'error' => string|null,
     *   'message' => string
     * ]
     */
    public function verifyPayment($trackId) {
        if (empty($trackId)) {
            return $this->errorResponse('Track ID is required');
        }

        return $this->getPaymentStatus($trackId);
    }

    /**
     * Get payment status and details by track_id
     *
     * @param string $trackId Unique payment tracking identifier
     *
     * @return array Response with structure: [
     *   'success' => bool,
     *   'data' => array|null (track_id, status, amount, currency, type, txs, etc.),
     *   'error' => string|null,
     *   'message' => string
     * ]
     */
    public function getPaymentStatus($trackId) {
        if (empty($trackId)) {
            return $this->errorResponse('Track ID is required');
        }

        if (empty($this->apiKey)) {
            return $this->errorResponse('API Key not configured');
        }

        // Make API request
        $response = $this->request('GET', "/v1/payment/{$trackId}");

        // Process response
        if ($response['success'] && isset($response['data'])) {
            $paymentData = $response['data'];

            return [
                'success' => true,
                'data' => [
                    'track_id' => $paymentData['track_id'] ?? $trackId,
                    'status' => $paymentData['status'] ?? 'unknown',
                    'amount' => $paymentData['amount'] ?? 0,
                    'currency' => $paymentData['currency'] ?? '',
                    'type' => $paymentData['type'] ?? '',
                    'order_id' => $paymentData['order_id'] ?? null,
                    'email' => $paymentData['email'] ?? null,
                    'description' => $paymentData['description'] ?? null,
                    'callback_url' => $paymentData['callback_url'] ?? null,
                    'return_url' => $paymentData['return_url'] ?? null,
                    'expired_at' => $paymentData['expired_at'] ?? null,
                    'date' => $paymentData['date'] ?? null,
                    'txs' => $paymentData['txs'] ?? [],
                    'raw' => $paymentData // Full raw response for advanced usage
                ],
                'error' => null,
                'message' => 'Payment information retrieved successfully'
            ];
        }

        return $this->errorResponse(
            $response['message'] ?? 'Failed to retrieve payment information',
            $response['data'] ?? null
        );
    }

    /**
     * Verify webhook callback authenticity using HMAC signature
     *
     * @param string $rawPostData Raw POST data from webhook (use file_get_contents('php://input'))
     * @param string $hmacHeader HMAC signature from HTTP header
     *
     * @return array Response with structure: [
     *   'success' => bool,
     *   'data' => array|null (decoded webhook payload if valid),
     *   'error' => string|null,
     *   'message' => string
     * ]
     */
    public function verifyWebhook($rawPostData, $hmacHeader) {
        if (empty($this->apiKey)) {
            return $this->errorResponse('API Key not configured');
        }

        if (empty($rawPostData) || empty($hmacHeader)) {
            return $this->errorResponse('Missing webhook data or HMAC header');
        }

        // Calculate HMAC signature using SHA-512
        $calculatedHmac = hash_hmac('sha512', $rawPostData, $this->apiKey);

        // Compare signatures (timing-safe comparison)
        if (!hash_equals($calculatedHmac, $hmacHeader)) {
            return $this->errorResponse('Invalid HMAC signature - webhook verification failed');
        }

        // Decode webhook data
        $webhookData = json_decode($rawPostData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->errorResponse('Invalid JSON in webhook payload');
        }

        return [
            'success' => true,
            'data' => [
                'track_id' => $webhookData['track_id'] ?? null,
                'status' => $webhookData['status'] ?? 'unknown',
                'type' => $webhookData['type'] ?? '',
                'amount' => $webhookData['amount'] ?? 0,
                'currency' => $webhookData['currency'] ?? '',
                'txs' => $webhookData['txs'] ?? [],
                'raw' => $webhookData
            ],
            'error' => null,
            'message' => 'Webhook verified successfully'
        ];
    }

    /**
     * Check if payment is completed (status = "Paid")
     *
     * @param string $trackId Track ID to check
     * @return bool True if payment is completed
     */
    public function isPaymentCompleted($trackId) {
        $result = $this->getPaymentStatus($trackId);

        if (!$result['success']) {
            return false;
        }

        $status = strtolower($result['data']['status'] ?? '');
        return $status === 'paid';
    }

    /**
     * Check if payment is pending (status = "Pending" or "Paying")
     *
     * @param string $trackId Track ID to check
     * @return bool True if payment is pending
     */
    public function isPaymentPending($trackId) {
        $result = $this->getPaymentStatus($trackId);

        if (!$result['success']) {
            return false;
        }

        $status = strtolower($result['data']['status'] ?? '');
        return in_array($status, ['pending', 'paying']);
    }

    /**
     * Make HTTP request to OxaPay API
     *
     * @param string $method HTTP method (GET, POST)
     * @param string $endpoint API endpoint path
     * @param array $data Request payload (for POST)
     *
     * @return array Response with success, data, message
     */
    private function request($method, $endpoint, $data = []) {
        $url = self::BASE_URL . $endpoint;

        $headers = [
            'merchant_api_key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        // Handle cURL errors
        if ($error) {
            error_log("OxaPayAPI: cURL error - {$error}");
            return [
                'success' => false,
                'data' => null,
                'message' => 'Connection error: ' . $error,
                'http_code' => 0
            ];
        }

        // Decode JSON response
        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("OxaPayAPI: JSON decode error - " . json_last_error_msg());
            return [
                'success' => false,
                'data' => null,
                'message' => 'Invalid JSON response from API',
                'http_code' => $httpCode
            ];
        }

        // Check HTTP status code
        $success = ($httpCode >= 200 && $httpCode < 300);

        return [
            'success' => $success && ($decodedResponse['status'] ?? 0) === 200,
            'data' => $decodedResponse['data'] ?? null,
            'message' => $decodedResponse['message'] ?? 'Request completed',
            'error' => $decodedResponse['error'] ?? null,
            'http_code' => $httpCode,
            'raw_response' => $decodedResponse
        ];
    }

    /**
     * Create standardized error response
     *
     * @param string $message Error message
     * @param mixed $data Additional error data
     *
     * @return array
     */
    private function errorResponse($message, $data = null) {
        return [
            'success' => false,
            'data' => $data,
            'error' => $message,
            'message' => $message
        ];
    }

    /**
     * Get list of supported cryptocurrencies (static list based on OxaPay documentation)
     *
     * @return array List of supported crypto currencies
     */
    public static function getSupportedCurrencies() {
        return [
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
        ];
    }

    /**
     * Get payment status label in Spanish
     *
     * @param string $status Payment status code
     * @return string Human-readable status
     */
    public static function getStatusLabel($status) {
        $statuses = [
            'pending' => 'Pendiente',
            'paying' => 'Procesando pago',
            'paid' => 'Pagado',
            'confirmed' => 'Confirmado',
            'confirming' => 'Confirmando',
            'failed' => 'Fallido',
            'expired' => 'Expirado',
            'refunded' => 'Reembolsado',
        ];

        return $statuses[strtolower($status)] ?? 'Desconocido';
    }
}
