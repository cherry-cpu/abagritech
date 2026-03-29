<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('ccavenue_config.php');
include('ccavenue_crypto.php');

// Validate Input - accept both field name variants
$full_name = isset($_POST['full_name']) ? $_POST['full_name'] : (isset($_POST['billing_name']) ? $_POST['billing_name'] : '');
$email = isset($_POST['email']) ? $_POST['email'] : (isset($_POST['billing_email']) ? $_POST['billing_email'] : '');
$phone = isset($_POST['phone']) ? $_POST['phone'] : (isset($_POST['billing_tel']) ? $_POST['billing_tel'] : '');
$amount = isset($_POST['amount']) ? $_POST['amount'] : '';

if (empty($amount) || empty($full_name) || empty($email) || empty($phone)) {
    die("Missing required parameters. amount=$amount, name=$full_name, email=$email, phone=$phone");
}

$merchant_data = '';
$working_key = CCAVENUE_WORKING_KEY;

$access_code = CCAVENUE_ACCESS_CODE;

// Standard Parameters required by CCAvenue
$data = array(
    'merchant_id' => CCAVENUE_MERCHANT_ID,
    'order_id' => 'ORD_' . uniqid() . '_' . time(),
    'amount' => $amount,
    'currency' => 'INR',
    'redirect_url' => CCAVENUE_REDIRECT_URL,
    'cancel_url' => CCAVENUE_CANCEL_URL,
    'language' => 'EN',
    'billing_name' => $full_name,
    'billing_address' => isset($_POST['address']) ? $_POST['address'] : '',
    'billing_city' => isset($_POST['city']) ? $_POST['city'] : '',
    'billing_state' => isset($_POST['state']) ? $_POST['state'] : '',
    'billing_zip' => isset($_POST['zip']) ? $_POST['zip'] : '',
    'billing_country' => 'India',
    'billing_tel' => $phone,
    'billing_email' => $email,
    'merchant_param1' => isset($_POST['position']) ? $_POST['position'] : '',
);

// Build merchant data string WITHOUT trailing &
$parts = array();
foreach ($data as $key => $value) {
    $parts[] = $key . '=' . urlencode(trim($value));
}
$merchant_data = implode('&', $parts);

$encrypted_data = encrypt($merchant_data, $working_key);
echo "encryptedText: " . $encrypted_data . "\n";
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
        <form method="post" name="redirect" action="<?php echo CCAVENUE_TRANSACTION_URL_1; ?>"> 
            <?php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
?>
        </form>
    </center>
    <script language='javascript'>document.redirect.submit();</script>
</body>
</html>
