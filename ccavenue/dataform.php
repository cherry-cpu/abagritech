<html>
<head>
<script>
window.onload = function() {
var d = new Date().getTime();
document.getElementById("tid").value = d;
};
</script>
</head>
<body>
<?php
// product_id is used to find product details like amount etc.
$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
$name = isset($_POST["name"]) ? $_POST["name"] : '' ;
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
?>
<form method="post" name="customerData" action="ccavRequestHandler.php">
<table width="40%" height="100" border='1' align="center"><caption><font size="4" color="blue"><b>CCAVENUE PAYMENT GATEWAY</b></font></caption></table>
<table width="40%" height="100" border='1' align="center">
<tr>
<td>Parameter Name:</td><td>Parameter Value:</td>
</tr>
<tr>
<td colspan="2"> Compulsory information</td>
</tr>
<tr>
<td>Transaction ID :</td><td><input type="text" name="tid" id="tid" readonly /></td>
</tr>
<tr>
<td>Currency :</td><td><input type="text"  name="currency" value="INR"/></td>
</tr>
<tr>
<td>Language :</td><td><input type="text" name="language" value="EN"/></td>
 </tr>
<input type="hidden" name="billing_name" value="<?php echo $name;?>"/>
<input type="hidden" name="product_id" value="<?php echo $product_id;?>"/></td>
<input type="hidden" name="billing_address" value=“website_name”/></td>
<input type="hidden" name="billing_city" value=“city”/></td>
<input type="hidden" name="billing_state" value=“state”/></td>
<input type="hidden" name="billing_zip" value=“12345”/></td>
<input type="hidden" name="billing_country" value="India"/></td>
<input type="hidden" name="billing_tel" value="<?php echo $phone;?>"/></td>
<input type="hidden" name="billing_email" value="<?php echo $email;?>"/></td>
<input type="hidden" name="delivery_name" value=“abcd”/></td>
<input type="hidden" name="delivery_address" value=“abcd”/></td>
<input type="hidden" name="delivery_city" value=“abcd”/></td>
<input type="hidden" name="delivery_state" value=“abcd”/></td>
<input type="hidden" name="delivery_zip" value=“1234”/></td>
<input type="hidden" name="delivery_country" value="India"/></td>
<input type="hidden" name="delivery_tel" value="<?php echo $phone;?>"/></td>
<input type="hidden" name="merchant_param1" value="additional Info."/></td>
<input type="hidden" name="merchant_param2" value="additional Info."/></td>
<input type="hidden" name="merchant_param3" value="additional Info."/></td>
<input type="hidden" name="merchant_param4" value="additional Info."/></td>
<input type="hidden" name="merchant_param5" value="additional Info."/></td>
<input type="hidden" name="promo_code" value=""/></td>
<input type="hidden" name="customer_identifier" value=""/></td>
<tr>
<td></td><td><input type="submit" value="CheckOut"></td>
</tr>
<input type="hidden"  name="integration_type" value="iframe_normal"/>
<input type="hidden" name="redirect_url" value="http://youdomainname.com/ccavResponseHandler.php"/>
<input type="hidden" name="cancel_url" value="http://youdomainname.com/ccavResponseHandler.php"/>
</table>
</form>
</body>
</html>