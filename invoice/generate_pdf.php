<?php
ob_start();

require_once('../tcpdf/tcpdf.php');
require_once('../config.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("DB Error");
}

$invoice_id = $_GET['invoice_id'] ?? '';

$stmt = $conn->prepare("SELECT invoice_json FROM invoice WHERE invoice_id = ?");
$stmt->bind_param("s", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Invoice not found");
}

$data = json_decode($row['invoice_json'], true);

// CREATE PDF
// $pdf = new TCPDF();
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Aakasha Bindu Agritech');
$pdf->SetTitle('Invoice');

$pdf->AddPage();

// HEADER
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(120, 10, 'ABAGRITECH', 0, 0, 'L');
$pdf->Cell(120, 10, 'INVOICE', 0, 0, 'C');

$pdf->Ln(15);

// META
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, "Invoice ID: $invoice_id", 0, 1);
$pdf->Cell(0, 6, "Date: " . $data['date'], 0, 1);
$pdf->Cell(0, 6, "GST No. " . $data['customer']['gstNo'], 0, 1);


// CUSTOMER
$pdf->Ln(5);
$pdf->Cell(0, 6, "Customer: " . $data['customer']['name'], 0, 1);
$pdf->Cell(0, 6, "Phone: " . $data['customer']['phone'], 0, 1);
$pdf->Cell(0, 6, "City: " . $data['customer']['city'], 0, 1);


$html = '

<table border="0" cellpadding="6" cellspacing="0" width="100%">

<tr>
<td width="65%">
    <h1 style="color:#1E3A8A;">INVOICE</h1>
    </td>
</tr>

</table>

<br>

<table border="1" cellpadding="5" cellspacing="0" width="100%">

<tr style="background-color:#E8F1FF;">
    <td colspan="2"><b>CUSTOMER DETAILS</b></td>
</tr>

<tr>
    <td width="25%"><b>Name</b></td>
    <td width="75%">'.$data['customer']['name'].'</td>
</tr>

<tr>
    <td><b>Phone</b></td>
    <td>'.$data['customer']['phone'].'</td>
</tr>

<tr>
    <td><b>City</b></td>
    <td>'.$data['customer']['city'].'</td>
</tr>

</table>

<br>

<table border="1" cellpadding="6" cellspacing="0" width="100%">

<tr style="background-color:#1E3A8A;color:white;">

<td width="8%" align="center"><b>#</b></td>

<td width="32%" align="center"><b>Product</b></td>

<td width="15%" align="center"><b>Code</b></td>

<td width="10%" align="center"><b>Qty</b></td>

<td width="15%" align="center"><b>Price</b></td>

<td width="20%" align="center"><b>Total</b></td>

</tr>
';

$total = 0;
$i = 1;
foreach ($data['products'] as $p) {
    $html .= '<tr>
        <td>'.$i++.'</td>
        <td>'.$p['name'].'</td>
        <td>'.$p['code'].'</td>
        <td align="center">'.$p['qty'].'</td>
        <td align="right">'.$p['price'].'</td>
        <td align="right">'.$p['subtotal'].'</td>
    </tr>';
}

$html .= '
</table>
<b>Subtotal: Rs. </b> '.$data['summary']['subtotal'].'<br>
<b>SGST: Rs. </b> '.$data['summary']['sgst'].'<br>
<b>CGST: Rs. </b> '.$data['summary']['cgst'].'<br>
<b>Discount:</b> '.$data['summary']['discount'].'<br>
<b>Grand Total: Rs. </b> '.$data['summary']['total'].'<br><br>

<b>Terms & Conditions</b>
<ul>
<li>Goods once sold will not be taken back</li>
<li>Payment due within agreed time</li>
<li>Subject to jurisdiction</li>
</ul>
';

$html .= '
<br><br><br><br>
<b>Terms & Conditions Summary</b>

<ul>
<p><b>1. Cash Discount:</b> 50% reduction if paid within 7 days.<p><br>
<p><b>2. Standard Credit:</b> 40% discount for 20 days; 30% discount for 45 days.<p><br>
<p><b>3. General Discount:</b> 25% flat discount for standard billing.";<p><br>
</ul>
';

$pdf->writeHTML($html, true, false, true, false, '');

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output("invoice_$invoice_id.pdf", "I");