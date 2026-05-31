<?php
/**
 * PhonePe Payment Gateway v2 – Standard Checkout (REST, no SDK).
 */
require_once __DIR__ . '/phonepe_config.php';

class PhonePeClient
{
    private static $tokenCache = null;

    public static function isProduction(): bool
    {
        return strtoupper(PHONEPE_ENV) === 'PRODUCTION';
    }

    private static function oauthBaseUrl(): string
    {
        return self::isProduction()
            ? 'https://api.phonepe.com/apis'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    private static function pgBaseUrl(): string
    {
        return self::isProduction()
            ? 'https://api.phonepe.com/apis/pg'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    private static function oauthPath(): string
    {
        return self::isProduction()
            ? '/identity-manager/v1/oauth/token'
            : '/v1/oauth/token';
    }

    /**
     * @return array{access_token:string}
     */
    public static function getAccessToken(): array
    {
        if (self::$tokenCache !== null) {
            $remaining = self::$tokenCache['expires_at'] - time();
            if ($remaining > 60) {
                return self::$tokenCache;
            }
        }

        $url = self::oauthBaseUrl() . self::oauthPath();
        $body = http_build_query([
            'client_id' => PHONEPE_CLIENT_ID,
            'client_version' => PHONEPE_CLIENT_VERSION,
            'client_secret' => PHONEPE_CLIENT_SECRET,
            'grant_type' => 'client_credentials',
        ]);

        $response = self::httpRequest('POST', $url, $body, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        if ($response['http_code'] !== 200) {
            $msg = self::extractErrorMessage($response['body']);
            throw new RuntimeException('PhonePe authentication failed: ' . $msg);
        }

        $json = json_decode($response['body'], true);
        if (empty($json['access_token'])) {
            throw new RuntimeException('PhonePe authentication failed: missing access_token');
        }

        $issuedAt = isset($json['issued_at']) ? (int) $json['issued_at'] : time();
        $expiresIn = isset($json['expires_in']) ? (int) $json['expires_in'] : 3600;
        $expiresAt = isset($json['expires_at']) ? (int) $json['expires_at'] : ($issuedAt + $expiresIn);

        self::$tokenCache = [
            'access_token' => $json['access_token'],
            'expires_at' => $expiresAt,
        ];

        return self::$tokenCache;
    }

    private static function apiHeaders(): array
    {
        $token = self::getAccessToken();
        return [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: O-Bearer ' . $token['access_token'],
            'x-source: API',
            'x-source-version: V2',
            'x-source-platform: BACKEND_PHP',
            'x-source-platform-version: 1.0.0',
        ];
    }

    /**
     * @param array{full_name?:string,email?:string,phone?:string,position?:string} $meta
     * @return array{merchant_order_id:string,order_id:string,state:string,redirect_url:string,expire_at?:int}
     */
    public static function createPayment(string $merchantOrderId, int $amountPaisa, array $meta = []): array
    {
        $redirectUrl = PHONEPE_REDIRECT_URL . (strpos(PHONEPE_REDIRECT_URL, '?') === false ? '?' : '&')
            . 'merchantOrderId=' . rawurlencode($merchantOrderId);

        $metaInfo = null;
        if (!empty($meta['email'])) {
            $metaInfo = ['udf1' => substr((string) $meta['email'], 0, 256)];
        }
        if (!empty($meta['phone'])) {
            $metaInfo = $metaInfo ?? [];
            $metaInfo['udf2'] = substr((string) $meta['phone'], 0, 256);
        }

        $payload = [
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amountPaisa,
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'message' => PHONEPE_TRANSACTION_NOTE,
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ],
        ];
        if ($metaInfo !== null) {
            $payload['metaInfo'] = $metaInfo;
        }

        $url = self::pgBaseUrl() . '/checkout/v2/pay';
        $response = self::httpRequest('POST', $url, json_encode($payload), self::apiHeaders());

        if ($response['http_code'] !== 200) {
            $msg = self::extractErrorMessage($response['body']);
            throw new RuntimeException('PhonePe payment initiation failed: ' . $msg);
        }

        $json = self::unwrapResponse(json_decode($response['body'], true));
        if (empty($json['redirectUrl'])) {
            throw new RuntimeException('PhonePe payment initiation failed: no redirect URL returned');
        }

        return [
            'merchant_order_id' => $merchantOrderId,
            'order_id' => $json['orderId'] ?? '',
            'state' => $json['state'] ?? 'PENDING',
            'redirect_url' => $json['redirectUrl'],
            'expire_at' => $json['expireAt'] ?? null,
        ];
    }

    /**
     * @return array{state:string,order_id:string,amount:int,payment_completed:bool}
     */
    public static function getOrderStatus(string $merchantOrderId): array
    {
        $path = '/checkout/v2/order/' . rawurlencode($merchantOrderId) . '/status?details=false';
        $url = self::pgBaseUrl() . $path;
        $response = self::httpRequest('GET', $url, null, self::apiHeaders());

        if ($response['http_code'] !== 200) {
            $msg = self::extractErrorMessage($response['body']);
            throw new RuntimeException('PhonePe order status failed: ' . $msg);
        }

        $json = self::unwrapResponse(json_decode($response['body'], true));
        $state = strtoupper((string) ($json['state'] ?? ''));

        return [
            'state' => $state,
            'order_id' => $json['orderId'] ?? '',
            'amount' => (int) ($json['amount'] ?? 0),
            'payment_completed' => $state === 'COMPLETED',
        ];
    }

    public static function isPaymentCompleted(string $merchantOrderId): bool
    {
        try {
            $status = self::getOrderStatus($merchantOrderId);
            return $status['payment_completed'];
        } catch (Throwable $e) {
            error_log('PhonePe status check: ' . $e->getMessage());
            return false;
        }
    }

    public static function generateMerchantOrderId(): string
    {
        return 'EXAM' . date('YmdHis') . random_int(100, 999);
    }

    private static function unwrapResponse(?array $json): array
    {
        if (!is_array($json)) {
            return [];
        }
        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }
        return $json;
    }

    private static function extractErrorMessage(string $body): string
    {
        $json = json_decode($body, true);
        if (!is_array($json)) {
            return $body !== '' ? substr($body, 0, 500) : 'Unknown error';
        }
        if (!empty($json['message'])) {
            return (string) $json['message'];
        }
        if (!empty($json['errorCode'])) {
            return (string) $json['errorCode'];
        }
        if (!empty($json['code'])) {
            return (string) $json['code'];
        }
        return json_encode($json);
    }

    /**
     * @param string[] $headers
     * @return array{http_code:int,body:string}
     */
    private static function httpRequest(string $method, string $url, ?string $body, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body ?? '');
        }

        $responseBody = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseBody === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('PhonePe HTTP error: ' . $err);
        }
        curl_close($ch);

        return ['http_code' => $httpCode, 'body' => $responseBody];
    }
}
