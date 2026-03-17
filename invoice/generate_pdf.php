<?php
require_once('../tcpdf/tcpdf.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$invoice_id = $_GET['invoice_id'];

$result = $conn->query("SELECT * FROM invoice WHERE invoice_id = $invoice_id");

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Invoice');

$pdf->AddPage();

// ---- HEADER SECTION ----

// Title (LEFT)
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(120, 10, 'INVOICE', 0, 0, 'L');

// Logo (RIGHT)
$logo = 'Logo.png'; // put your logo in project folder
if (file_exists($logo)) {
    $pdf->Image($logo, 150, 10, 40); // X, Y, Width
}

$pdf->Ln(15);

// Invoice Info
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 6, "Invoice ID: $invoice_id", 0, 1);
$pdf->Cell(100, 6, "Date: " . date("d-m-Y"), 0, 1);

$pdf->Ln(5);

// ---- TABLE ----

$html = '
<style>
table {
    border-collapse: collapse;
    width: 100%;
}
th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: center;
}
td, th {
    border: 1px solid #000;
    padding: 6px;
    font-size: 10px;
}
</style>

<table>
<tr>
    <th>Product Name</th>
    <th>Packing</th>
    <th>Billing Price</th>
    <th>50% Discount(7 Days Credit)</th>
    <th>40% Discount (20 Days Credit)</th>
    <th>30% Discount (45 Days Credit)</th>
    <th>General Discount (25%)</th>
    <th>Product Code</th>
</tr>
';

$total = 0;

while ($row = $result->fetch_assoc()) {

    $price = $row['billing_price'];
    $total += $price;

    $html .= '<tr>
        <td>'.$row['product_name'].'</td>
        <td align="center">'.$row['packing'].'</td>
        <td align="right">'.$row['billing_price'].'</td>
        <td align="right">'.$row['disc_50'].'</td>
        <td align="right">'.$row['disc_40'].'</td>
        <td align="right">'.$row['disc_30'].'</td>
        <td align="right">'.$row['general_discount'].'</td>
        <td align="center">'.$row['product_code'].'</td>
    </tr>';
}

// Total row
$html .= '
<tr>
    <td colspan="2"><b>Total</b></td>
    <td colspan="6" align="right"><b>'.$total.'</b></td>
</tr>
';

$html .= '</table>
<br><br><br><br>
<b>Terms & Conditions Summary</b>

<ul>
<p><b>1. Cash Discount:</b> 50% reduction if paid within 7 days.<p><br>
<p><b>2. Standard Credit:</b> 40% discount for 20 days; 30% discount for 45 days.<p><br>
<p><b>3. General Discount:</b> 25% flat discount for standard billing.";<p><br>
</ul>
';

$pdf->writeHTML($html, true, false, true, false, '');


// Output PDF
$pdf->Output("invoice_$invoice_id.pdf", "I");