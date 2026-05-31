<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
    exit;
}

require_once 'config.php';
require_once 'phonepe_config.php';
require_once 'phonepe_client.php';

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data)) {
    $data = $_POST;
}

$full_name = trim($data['full_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$position = trim($data['position'] ?? '');
$amount = isset($data['amount']) ? (float) $data['amount'] : EXAM_FEE;

if (empty($full_name) || empty($email) || empty($phone) || empty($position) || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid payment request data.']);
    exit;
}

$amountPaisa = (int) round($amount * 100);
if ($amountPaisa < 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payment amount.']);
    exit;
}

try {
    $merchantOrderId = PhonePeClient::generateMerchantOrderId();
    $payment = PhonePeClient::createPayment($merchantOrderId, $amountPaisa, [
        'email' => $email,
        'phone' => $phone,
        'full_name' => $full_name,
        'position' => $position,
    ]);

    $log_data = [
        'created_at' => date('c'),
        'merchant_order_id' => $merchantOrderId,
        'phonepe_order_id' => $payment['order_id'],
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'position' => $position,
        'amount_paisa' => $amountPaisa,
        'state' => $payment['state'],
        'redirect_url' => $payment['redirect_url'],
    ];
    file_put_contents(__DIR__ . '/phonepe_requests.log', json_encode($log_data) . PHP_EOL, FILE_APPEND);

    echo json_encode([
        'success' => true,
        'message' => 'Redirecting to PhonePe payment page.',
        'transaction_id' => $merchantOrderId,
        'merchant_order_id' => $merchantOrderId,
        'redirect_url' => $payment['redirect_url'],
        'state' => $payment['state'],
    ]);
} catch (Throwable $e) {
    error_log('phonepe_request.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
