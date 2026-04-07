<?php
include('ccavenue_config.php');
include('ccavenue_crypto.php');

if (!isset($_POST['encResp'])) {
    die('Invalid response from payment gateway.');
}

$encResponse = $_POST['encResp'];
$rcvdString = decrypt($encResponse, CCAVENUE_WORKING_KEY);

$decryptValues = explode('&', $rcvdString);
$data = [];

foreach ($decryptValues as $val) {
    $parts = explode('=', $val);
    if (count($parts) == 2) {
        $data[$parts[0]] = $parts[1];
    }
}

$order_status = $data['order_status'] ?? '';
$tracking_id  = $data['tracking_id'] ?? '';

// You can log $data here for debugging/audit if needed.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
</head>
<body>
<?php if ($order_status === 'Success' && !empty($tracking_id)) : ?>
<script>
    if (window.opener && !window.opener.closed) {
        window.opener.postMessage(
            {
                status: 'success',
                tracking_id: '<?php echo htmlspecialchars($tracking_id, ENT_QUOTES, "UTF-8"); ?>'
            },
            '*'
        );
    }
    window.close();
</script>
<p>Payment successful. You can close this window.</p>
<?php else : ?>
<script>
    if (window.opener && !window.opener.closed) {
        window.opener.postMessage(
            {
                status: 'failed'
            },
            '*'
        );
    }
    window.close();
</script>
<p>Payment failed or cancelled. You can close this window.</p>
<?php endif; ?>
</body>
</html>

