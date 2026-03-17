<?php
ob_start();
require_once('../tcpdf/tcpdf.php');
require_once('../config.php');

mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR); // Enable exceptions for mysqli

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8");

    // Only handle POST submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $invoice_id = $_POST['invoice_id'];
        $timestamp = date('Y-m-d H:i:s');

        // Check if customer already exists for this invoice
        $stmtCheckTo = $conn->prepare("SELECT COUNT(*) FROM invoice_to WHERE invoice_id=?");
        $stmtCheckTo->bind_param("s", $invoice_id);
        $stmtCheckTo->execute();
        $stmtCheckTo->bind_result($countTo);
        $stmtCheckTo->fetch();
        $stmtCheckTo->close();

        if ($countTo == 0) {
            // Insert customer (TO) details
            $stmtTo = $conn->prepare("INSERT INTO invoice_to 
                (invoice_id, customer_name, address, city, state, zip, phone, email)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtTo->bind_param("ssssssss", $invoice_id, $_POST['customer_name'], $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['phone'], $_POST['email']);
            try {
                $stmtTo->execute();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) { // Duplicate entry
                    echo "<script>alert('Duplicate invoice detected!'); window.history.back();</script>";
                    exit;
                } else {
                    throw $e;
                }
            }
            $stmtTo->close();
        }

        // Collect products
        $product_name  = (array)$_POST['product_name'];
        $packing       = (array)$_POST['packing'];
        $billing_price = (array)$_POST['billing_price'];
        $product_code  = (array)$_POST['product_code'];

        for ($i = 0; $i < count($product_name); $i++) {
            $name = $product_name[$i];
            $pack = (float)$packing[$i];
            $bill = (float)$billing_price[$i];
            $code = $product_code[$i];

            // Correct discount calculations
            $disc_50 = round($bill * 0.5, 2);  // 50% of billing
            $disc_40 = round($bill * 0.4, 2);  // 40% of billing
            $disc_30 = round($bill * 0.3, 2);  // 30% of billing
            $general_discount = round($bill * 0.25, 2); // 25% general

            $stmt = $conn->prepare("INSERT INTO invoice 
                (invoice_id, product_name, packing, billing_price, disc_50, disc_40, disc_30, general_discount, product_code, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssddddddis",
                $invoice_id, $name, $pack, $bill,
                $disc_50, $disc_40, $disc_30, $general_discount,
                $code, $timestamp
            );

            try {
                $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) { // Duplicate product
                    echo "<script>alert('Duplicate product entry detected!'); window.history.back();</script>";
                    exit;
                } else {
                    throw $e;
                }
            }
            $stmt->close();
        }

        // Redirect to avoid duplicate insertion on reload
        header("Location: save_invoice.php?invoice_id=" . $invoice_id);
        exit;
    }

} catch (mysqli_sql_exception $e) {
    echo "<script>alert('Database error: " . $e->getMessage() . "'); window.history.back();</script>";
    exit;
}

// ============================
// STEP 2: Fetch invoice & products
// ============================

if (!isset($_GET['invoice_id'])) {
    die("Invalid access");
}

$invoice_id = $_GET['invoice_id'];

// Fetch TO details
$stmtTo = $conn->prepare("SELECT * FROM invoice_to WHERE invoice_id=?");
$stmtTo->bind_param("s",$invoice_id);
$stmtTo->execute();
$resultTo = $stmtTo->get_result();
$toDetails = $resultTo->fetch_assoc();
$stmtTo->close();

// Fetch products
$stmt = $conn->prepare("SELECT * FROM invoice WHERE invoice_id=?");
$stmt->bind_param("s",$invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($r = $result->fetch_assoc()) $rows[] = $r;
$stmt->close();

// Invoice creation timestamp
$timestamp = count($rows) ? $rows[0]['created_at'] : date('Y-m-d H:i:s');

// ============================
// STEP 3: Generate PDF
// ============================

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 20, 15);

// Company Name + Logo
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetXY(15, 10);
$pdf->Cell(100, 10, 'ABAGRITECH', 0, 0, 'L');

$logoFile = '../images/Logo.png';
if(file_exists($logoFile)){
    $pdf->Image($logoFile, 150, 10, 45);
}

$pdf->Ln(20);
$pdf->SetMargins(15,20,15);

// Invoice title
$pdf->SetFont('helvetica','B',20);
$pdf->Cell(0,10,'INVOICE',0,1,'L');

$pdf->SetFont('helvetica','',11);
$pdf->Cell(95,6,"Invoice ID: $invoice_id",0,0);
$pdf->Cell(0,6,"Date: $timestamp",0,1,'R');

// FROM section
$fromCompany = "ABAGRITECH\nAddress Line 1\nCity, State, ZIP\nPhone: XXX\nEmail: info@abagritech.com";
$startY = $pdf->GetY();
$pdf->SetFont('helvetica','B',12);
$pdf->SetXY(15, $startY);
$pdf->Cell(0,6,"From:",0,1);
$pdf->SetFont('helvetica','',11);
$pdf->SetXY(15,$startY+6);
$pdf->MultiCell(90,5,$fromCompany,1,'L',0,0);

// TO section
$pdf->SetFont('helvetica','B',12);
$pdf->SetXY(115,$startY);
$pdf->Cell(0,6,"To:",0,1);
$pdf->SetFont('helvetica','',11);
$pdf->SetXY(115,$startY+6);
$toText = "{$toDetails['customer_name']}\n{$toDetails['address']}\n{$toDetails['city']}, {$toDetails['state']} {$toDetails['zip']}\nPhone: {$toDetails['phone']}\nEmail: {$toDetails['email']}";
$pdf->MultiCell(90,5,$toText,1,'L',0,0);

$pdf->Ln(40);

// Products table
$html = '<table border="1" cellpadding="6">
<tr style="background-color:#eee; font-weight:bold; text-align:center;">
<th style="width:25%">Product</th>
<th style="width:10%">Packing</th>
<th style="width:12%">Billing Price</th>
<th style="width:10%">50%</th>
<th style="width:10%">40%</th>
<th style="width:10%">30%</th>
<th style="width:12%">General</th>
<th style="width:11%">Code</th>
</tr>';

$fill=false;
foreach($rows as $r){
    $fillColor = $fill?"#f9f9f9":"#ffffff";
    $html .= "<tr style='background-color:$fillColor;text-align:center;'>
    <td>{$r['product_name']}</td>
    <td>{$r['packing']}</td>
    <td>$".number_format($r['billing_price'],2)."</td>
    <td>$".number_format($r['disc_50'],2)."</td>
    <td>$".number_format($r['disc_40'],2)."</td>
    <td>$".number_format($r['disc_30'],2)."</td>
    <td>$".number_format($r['general_discount'],2)."</td>
    <td>{$r['product_code']}</td>
    </tr>";
    $fill=!$fill;
}

$html .= "</table>";
$pdf->writeHTML($html,true,false,true,false,'');

$pdf->Ln(8);
$pdf->SetFont('helvetica','',10);
$pdf->writeHTML("
<h4>Terms & Conditions Summary</h4>
<ul>
    <p><b>Cash Discount:</b> 50% reduction if paid within 7 days.</p>
    <p><b>Standard Credit:</b> 40% discount for 20 days; 30% discount for 45 days.</p>
    <p><b>General Discount:</b> 25% flat discount for standard billing.</p>
</ul>
");

ob_end_clean();
$pdf->Output("Invoice_$invoice_id.pdf",'I');
$conn->close();
?>