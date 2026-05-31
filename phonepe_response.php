<?php
require_once 'phonepe_config.php';
require_once 'phonepe_client.php';

$merchantOrderId = trim(
    $_GET['merchantOrderId']
    ?? $_GET['merchant_order_id']
    ?? $_GET['tr']
    ?? ''
);

$success = false;
$state = '';
$errorMessage = '';

if ($merchantOrderId !== '') {
    try {
        $status = PhonePeClient::getOrderStatus($merchantOrderId);
        $state = $status['state'];
        $success = $status['payment_completed'];

        $log_entry = [
            'received_at' => date('c'),
            'merchant_order_id' => $merchantOrderId,
            'state' => $state,
            'payment_completed' => $success,
            'phonepe_order_id' => $status['order_id'],
            'request' => $_GET,
        ];
        file_put_contents(__DIR__ . '/phonepe_responses.log', json_encode($log_entry) . PHP_EOL, FILE_APPEND);
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
        error_log('phonepe_response.php: ' . $errorMessage);
    }
} else {
    $errorMessage = 'Missing order reference in redirect URL.';
}

$safeOrderId = htmlspecialchars($merchantOrderId, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhonePe Payment</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .success { color: #1a7c17; }
        .error { color: #a12b2b; }
        .pending { color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <h1 class="success">Payment Successful</h1>
            <p>Your exam fee payment was confirmed by PhonePe.</p>
            <p><strong>Order reference:</strong> <?= $safeOrderId; ?></p>
            <p>You can return to the application form to submit your application.</p>
            <script>
                (function () {
                    var payload = {
                        status: 'success',
                        transaction_id: <?= json_encode($merchantOrderId); ?>,
                        state: <?= json_encode($state); ?>
                    };
                    try {
                        if (window.opener && !window.opener.closed) {
                            window.opener.postMessage(payload, '*');
                        }
                    } catch (e) {}
                    try {
                        localStorage.setItem('phonepe_payment', JSON.stringify(payload));
                    } catch (e) {}
                    setTimeout(function () {
                        if (window.opener && !window.opener.closed) {
                            window.close();
                        } else {
                            window.location.href = 'exam_application_with_form.html?payment=success&transaction_id=' + encodeURIComponent(<?= json_encode($merchantOrderId); ?>);
                        }
                    }, 2000);
                })();
            </script>
        <?php elseif ($merchantOrderId !== '' && in_array($state, ['PENDING', 'INITIATED', 'IN_PROGRESS'], true)): ?>
            <h1 class="pending">Payment Pending</h1>
            <p>PhonePe reports status: <strong><?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <p>Please wait a moment and refresh, or complete payment in the PhonePe app.</p>
            <p><a href="exam_application_with_form.html">Back to application form</a></p>
        <?php else: ?>
            <h1 class="error">Payment Not Confirmed</h1>
            <?php if ($errorMessage): ?>
                <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php else: ?>
                <p>Status: <strong><?= htmlspecialchars($state ?: 'FAILED', ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <?php endif; ?>
            <p>Please try the payment again from the application form.</p>
            <p><a href="exam_application_with_form.html">Back to application form</a></p>
            <script>
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage({
                        status: 'failed',
                        transaction_id: <?= json_encode($merchantOrderId); ?>,
                        state: <?= json_encode($state); ?>
                    }, '*');
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
