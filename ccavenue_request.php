<?php
include('ccavenue_config.php');
include('ccavenue_crypto.php');

// Validate Input
if(empty($_POST['amount']) || empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['phone'])) {
    die("Missing required parameters");
}

$merchant_data = '';
$working_key = CCAVENUE_WORKING_KEY;
$access_code = CCAVENUE_ACCESS_CODE;

// Standard Parameters required by CCAvenue/HDFC
$data = array(
    'merchant_id' => CCAVENUE_MERCHANT_ID,
    'order_id' => 'ORD_' . uniqid() . '_' . time(), // Unique Order ID
    'amount' => $_POST['amount'],
    'currency' => 'INR',
    'redirect_url' => CCAVENUE_REDIRECT_URL,
    'cancel_url' => CCAVENUE_CANCEL_URL,
    'language' => 'EN',
    'billing_name' => $_POST['full_name'],
    'billing_address' => isset($_POST['address']) ? $_POST['address'] : '',
    'billing_city' => isset($_POST['city']) ? $_POST['city'] : 'City',
    'billing_state' => isset($_POST['state']) ? $_POST['state'] : 'State',
    'billing_zip' => isset($_POST['zip']) ? $_POST['zip'] : '000000',
    'billing_country' => 'India',
    'billing_tel' => $_POST['phone'],
    'billing_email' => $_POST['email'],
    // Custom parameters can be added here
    'merchant_param1' => isset($_POST['position']) ? $_POST['position'] : '',
);

foreach ($data as $key => $value){
    $merchant_data .= $key.'='.$value.'&';
}

$encrypted_data = encrypt($merchant_data, $working_key);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to Payment Gateway...</title>
</head>
<body>
    <center>
        <h2>Please wait, redirecting to payment gateway...</h2>
        <form method="post" name="redirect" action="<?php echo CCAVENUE_TRANSACTION_URL; ?>"> 
            <?php
            echo "<input type=hidden name=encRequest value=$encrypted_data>";
            echo "<input type=hidden name=access_code value=$access_code>";
            ?>
        </form>
    </center>
    <script language='javascript'>document.redirect.submit();</script>
</body>
</html>
