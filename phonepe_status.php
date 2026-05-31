<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
    exit;
}

require_once 'phonepe_config.php';
require_once 'phonepe_client.php';

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!is_array($data)) {
    $data = $_POST;
}

$merchantOrderId = trim($data['merchant_order_id'] ?? $data['transaction_id'] ?? '');

if ($merchantOrderId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'merchant_order_id is required.']);
    exit;
}

try {
    $status = PhonePeClient::getOrderStatus($merchantOrderId);
    echo json_encode([
        'success' => true,
        'merchant_order_id' => $merchantOrderId,
        'state' => $status['state'],
        'payment_completed' => $status['payment_completed'],
        'order_id' => $status['order_id'],
        'amount_paisa' => $status['amount'],
    ]);
} catch (Throwable $e) {
    error_log('phonepe_status.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
