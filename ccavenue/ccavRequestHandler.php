<html>
<head>
<title> Iframe</title>
</head>
<body>
<center>
<?php include('Crypto.php')?>
<?php 
   error_reporting(0);
   $working_key='';//Shared by CCAVENUES
   $access_code='';//Shared by CCAVENUES
   $merchant_data='';
   foreach ($_POST as $key => $value){
      $merchant_data.=$key.'='.$value.'&';
   }
    $order_id='234';
     // use product id and find amount and other details of product.
     $product_id=isset($_POST['product_id']) ? $_POST['product_id'] : '' ;
     $sql_statement = 'put your sql query to find product amount and merchant_id';
     // fetch product amount from sql query
     $amount_value = $sql_statement->amount;
     // fetch merchant id from sql query
     $merchant_id = $sql_statement->merchant_id;
     $merchant_data.='merchant_id'.'='.$merchant_id.'&'.'amount'.'='.$amount_value.'&'.'order_id'.'='.$order_id.'&';
     $encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.
   $production_url='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;
?>
<iframe src="<?php echo $production_url?>" id="paymentFrame" width="482" height="450" frameborder="0" scrolling="No" ></iframe>
<script type="text/javascript" src="jquery-1.7.2.js"></script>
<script type="text/javascript">
       $(document).ready(function(){
           window.addEventListener('message', function(e) {
              $("#paymentFrame").css("height",e.data['newHeight']+'px');     
          }, false);
      });
</script>
</center>
</body>
</html>