<?php
require_once('config.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_POST['invoice_data'])) {
    die("No data received");
}

$data = json_decode($_POST['invoice_data'], true);

$invoice_id = $data['invoice_id'];
$json_data = json_encode($data);

// SAVE TO DB
$stmt = $conn->prepare("INSERT INTO invoice (invoice_id, invoice_json) VALUES (?, ?)");
$stmt->bind_param("ss", $invoice_id, $json_data);
$stmt->execute();

// REDIRECT TO PDF
header("Location: generate_pdf.php?invoice_id=" . urlencode($invoice_id));
exit;
?>