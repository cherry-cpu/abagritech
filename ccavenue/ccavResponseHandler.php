<?php
session_start();
include('Crypto.php');

$working_key = "YOUR_WORKING_KEY";

$encResponse = $_POST["encResp"];
$rcvdString = decrypt($encResponse,$working_key);

parse_str($rcvdString, $response_array);

$order_status = $response_array['order_status'];
$tracking_id  = $response_array['tracking_id'];
$order_id     = $response_array['order_id'];

if($order_status === "Success" && $order_id == $_SESSION['order_id']){
    header("Location: submit_application.php?payment=success&tid=".$tracking_id);
    exit();

}else{

    header("Location: submit_application.php?payment=failed");
    exit();
}
?>