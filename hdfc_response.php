<?php
include('hdfc_config.php');
include('hdfc_crypto.php');

$workingKey = HDFC_WORKING_KEY;
$encResponse = $_POST["encResp"]; 
$rcvdString = decrypt($encResponse, $workingKey); 
$order_status = "";
$decryptValues = explode('&', $rcvdString);
$dataSize = sizeof($decryptValues);

$responseMap = array();

for($i = 0; $i < $dataSize; $i++) 
{
    $information = explode('=', $decryptValues[$i]);
    if(count($information) == 2) {
        $responseMap[$information[0]] = $information[1];
    }
}

$order_status = isset($responseMap['order_status']) ? $responseMap['order_status'] : '';
$order_id = isset($responseMap['order_id']) ? $responseMap['order_id'] : '';
$tracking_id = isset($responseMap['tracking_id']) ? $responseMap['tracking_id'] : '';
$amount = isset($responseMap['amount']) ? $responseMap['amount'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: green; }
        .failure { color: red; }
        .aborted { color: orange; }
        .details { margin-top: 20px; text-align: left; display: inline-block; }
    </style>
</head>
<body>
    <?php if($order_status === "Success") { ?>
        <h1 class="success">Payment Successful!</h1>
        <p>Thank you for your payment.</p>
        <div class="details">
            <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
            <p><strong>Transaction ID:</strong> <?php echo $tracking_id; ?></p>
            <p><strong>Amount:</strong> <?php echo $amount; ?></p>
        </div>
        <br><br>
        <p>You can now close this window and continue with your application, or verify the Transaction ID in the application form.</p>
        
        <script>
            // Attempt to send data back to parent window if opened in popup
            if(window.opener) {
                window.opener.postMessage({
                    status: 'success',
                    tracking_id: '<?php echo $tracking_id; ?>',
                    order_id: '<?php echo $order_id; ?>'
                }, "*");
                // window.close(); // Optional: Close window automatically
            }
        </script>

    <?php } else if($order_status === "Aborted") { ?>
        <h1 class="aborted">Payment Aborted</h1>
        <p>The payment transaction was aborted.</p>
    <?php } else if($order_status === "Failure") { ?>
        <h1 class="failure">Payment Failed</h1>
        <p>The payment transaction failed.</p>
    <?php } else { ?>
        <h1 class="failure">Invalid Security Error</h1>
        <p>Security check failed. Please try again.</p>
    <?php } ?>

    <br>
    <a href="exam_application_back_up.html">Return to Application</a>
</body>
</html>
