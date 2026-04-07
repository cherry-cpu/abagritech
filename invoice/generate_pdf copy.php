<?php
require_once('../tcpdf/tcpdf.php');
require_once('config.php');

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
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Aakasha Bindu Agritech');
$pdf->SetTitle('Invoice');

$pdf->AddPage();

// HEADER
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(120, 10, 'INVOICE', 0, 0, 'L');

$logo = 'Logo.png';
if (file_exists($logo)) {
    $pdf->Image($logo, 150, 10, 40);
}

$pdf->Ln(15);

// META
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, "Invoice ID: $invoice_id", 0, 1);
$pdf->Cell(0, 6, "Date: " . $data['date'], 0, 1);

// CUSTOMER
$pdf->Ln(5);
$pdf->Cell(0, 6, "Customer: " . $data['customer']['name'], 0, 1);
$pdf->Cell(0, 6, "Phone: " . $data['customer']['phone'], 0, 1);
$pdf->Cell(0, 6, "City: " . $data['customer']['city'], 0, 1);

// TABLE
$html = '
<style>
table { border-collapse: collapse; width: 100%; }
th { background-color: #f2f2f2; font-weight: bold; }
td, th { border: 1px solid #000; padding: 6px; font-size: 10px; }
</style>

<table>
<tr>
<th>#</th>
<th>Product</th>
<th>Code</th>
<th>Qty</th>
<th>Price</th>
<th>Subtotal</th>
</tr>
';

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

$html .= '</table><br>';

// SUMMARY
$html .= '
<b>Subtotal:</b> '.$data['summary']['subtotal'].'<br>
<b>SGST:</b> '.$data['summary']['sgst'].'<br>
<b>CGST:</b> '.$data['summary']['cgst'].'<br>
<b>Discount:</b> '.$data['summary']['discount'].'<br>
<b>Grand Total:</b> '.$data['summary']['total'].'<br><br>

<b>Terms & Conditions</b>
<ul>
<li>Goods once sold will not be taken back</li>
<li>Payment due within agreed time</li>
<li>Subject to jurisdiction</li>
</ul>
';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output("invoice_$invoice_id.pdf", "I");
?>