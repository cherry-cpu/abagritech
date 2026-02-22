<?php
session_start();
include('Crypto.php'); // From CCAvenue kit

$merchant_id = "YOUR_MERCHANT_ID";
$access_code = "YOUR_ACCESS_CODE";
$working_key = "YOUR_WORKING_KEY";

$order_id = uniqid();
$_SESSION['order_id'] = $order_id;

$redirect_url = "https://yourdomain.com/ccavResponseHandler.php";
$cancel_url   = "https://yourdomain.com/submit_application.php?payment=failed";

$merchant_data = "";
$merchant_data .= "merchant_id=".$merchant_id;
$merchant_data .= "&order_id=".$order_id;
$merchant_data .= "&currency=INR";
$merchant_data .= "&amount=".$_POST['amount'];
$merchant_data .= "&redirect_url=".$redirect_url;
$merchant_data .= "&cancel_url=".$cancel_url;
$merchant_data .= "&billing_name=".$_POST['billing_name'];
$merchant_data .= "&billing_email=".$_POST['billing_email'];
$merchant_data .= "&billing_tel=".$_POST['billing_tel'];

$encrypted_data = encrypt($merchant_data,$working_key);
?>

<form method="post"
action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
<input type="hidden" name="encRequest" value="<?php echo $encrypted_data; ?>">
<input type="hidden" name="access_code" value="<?php echo $access_code; ?>">
</form>

<script>document.forms[0].submit();</script>