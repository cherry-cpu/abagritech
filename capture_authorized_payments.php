<?php

require_once 'config.php';

/**
 * ================================
 * DB CONNECTION (AS PROVIDED)
 * ================================
 */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please contact administrator.'
    ]);
    error_log('Database connection error: ' . $e->getMessage());
    exit;
}
error_log('aa==='.$_SERVER["REQUEST_METHOD"]);


/**
 * ================================
 * DB CONNECTION (AS PROVIDED)
 * ================================
 */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed");
}

/**
 * ================================
 * CURL HELPER FUNCTION
 * ================================
 */
function razorpayRequest($url, $method = 'GET', $data = null)
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET,
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    return [$httpCode, json_decode($response, true)];
}

/**
 * ================================
 * FETCH PENDING PAYMENTS FROM DB
 * ================================
 */
$stmt = $pdo->prepare("
    SELECT id, razorpay_payment_id, amount
    FROM payments
    WHERE payment_status = 'pending'
      AND razorpay_payment_id IS NOT NULL
");
$stmt->execute();

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$payments) {
    echo "No pending payments found.\n";
    exit;
}

/**
 * ================================
 * PROCESS EACH PAYMENT
 * ================================
 */
foreach ($payments as $row) {

    $dbPaymentId   = $row['id'];
    $razorpayId    = $row['razorpay_payment_id'];
    $amountPaise   = (int) round($row['amount'] * 100);

    try {
        /**
         * STEP 1: FETCH PAYMENT STATUS FROM RAZORPAY
         */
        list($statusCode, $paymentData) = razorpayRequest(
            "https://api.razorpay.com/v1/payments/{$razorpayId}"
        );

        if ($statusCode !== 200) {
            throw new Exception("Unable to fetch Razorpay payment");
        }

        if ($paymentData['status'] !== 'authorized') {
            continue; // skip if not authorized
        }

        /**
         * STEP 2: CAPTURE PAYMENT
         */
        list($captureCode, $captureData) = razorpayRequest(
            "https://api.razorpay.com/v1/payments/{$razorpayId}/capture",
            'POST',
            ['amount' => $amountPaise]
        );

        if ($captureCode !== 200) {
            throw new Exception($captureData['error']['description'] ?? 'Capture failed');
        }

        /**
         * STEP 3: UPDATE YOUR PAYMENTS TABLE
         */
        $update = $pdo->prepare("
            UPDATE payments
            SET payment_status = 'success',
                updated_at = NOW()
            WHERE id = ?
        ");
        $update->execute([$dbPaymentId]);

        echo "Captured & updated DB: {$razorpayId}\n";

    } catch (Exception $e) {

        // Mark as failed
        $fail = $pdo->prepare("
            UPDATE payments
            SET payment_status = 'failed',
                updated_at = NOW()
            WHERE id = ?
        ");
        $fail->execute([$dbPaymentId]);

        echo "Failed: {$razorpayId} | {$e->getMessage()}\n";
    }
}
