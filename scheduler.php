<?php
/* commands
crontab -e
0 * * * * /usr/bin/php {path}/scheduler.php >> /var/log/razorpay_cron.log 2>&1
*/

/**
* Cron Job: Capture ALL authorized Razorpay payments
 * No database used
 */

// ==========================
// Razorpay Credentials
// ==========================
require_once 'config.php';

// Razorpay credentials
$keyId     = RAZORPAY_KEY_ID;
$keySecret = RAZORPAY_KEY_SECRET;

// Amount to capture (in paise)
// Must match authorized amount
$amount = 120000; // ₹1200.00

// ==========================
// Fetch authorized payments
// ==========================
$fetchUrl = "https://api.razorpay.com/v1/payments?status=authorized&count=100";

$ch = curl_init($fetchUrl);
curl_setopt_array($ch, [
    CURLOPT_USERPWD        => $keyId . ":" . $keySecret,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60
]);

$response = curl_exec($ch);

if ($response === false) {
    error_log("Fetch error: " . curl_error($ch));
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log("Failed to fetch payments. HTTP: $httpCode");
    exit;
}

$data = json_decode($response, true);

if (empty($data['items'])) {
    echo "No authorized payments found\n";
    exit;
}

// ==========================
// Capture each payment
// ==========================
foreach ($data['items'] as $payment) {

    $paymentId = $payment['id'];

    try {
        $captureUrl = "https://api.razorpay.com/v1/payments/$paymentId/capture";

        $payload = json_encode([
            "amount"   => $amount,
            "currency" => "INR"
        ]);

        $ch = curl_init($captureUrl);
        curl_setopt_array($ch, [
            CURLOPT_USERPWD        => $keyId . ":" . $keySecret,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 60
        ]);

        $captureResponse = curl_exec($ch);

        if ($captureResponse === false) {
            throw new Exception(curl_error($ch));
        }

        $captureCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($captureResponse, true);

        if ($captureCode === 200 && ($result['status'] ?? '') === 'captured') {
            echo "Captured: $paymentId\n";
        } else {
            $error = $result['error']['description'] ?? 'Unknown error';
            echo "Failed: $paymentId | $error\n";
        }

    } catch (Exception $e) {
        error_log("Error capturing $paymentId: " . $e->getMessage());
    }
}

echo "Cron finished at " . date('Y-m-d H:i:s') . "\n";
