<?php
ob_start();

require_once 'config.php';
require_once __DIR__ . '/tcpdf/tcpdf.php';

/* ===============================
   DB CONNECTION
   =============================== */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit('Database connection failed');
}

/* ===============================
   VALIDATE REQUEST
   =============================== */
/*if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['transaction_id'])) {
    http_response_code(400);
    exit('Invalid request');
}
*/
$transaction_id = trim($_GET['transaction_id']);

/* ===============================
   FETCH APPLICATION DATA
   =============================== */
$sql = "
SELECT 
    e.application_id,
    e.full_name,
    e.father_name,
    e.date_of_birth,
    e.gender,
    e.email,
    e.phone,
    e.age,
    e.aadhar,
    e.caste,
    e.address,
    e.ssc_year,
    e.ssc_percentage,
    e.inter_year,
    e.inter_percentage,
    e.degree_year,
    e.degree_percentage,
    e.position,
    e.exam_center,
    e.transaction_id,
    e.photo_path,
    e.signature_path
FROM exam_applications e
WHERE e.transaction_id = :transaction_id or e.application_id=:transaction_id
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':transaction_id', $transaction_id);
$stmt->execute();
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    exit('Application not found');
}

/* ===============================
   TCPDF SETUP
   =============================== */
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Application Form');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

/* ===============================
   LOGO
   =============================== */
$logoPath = __DIR__ . '/images/Logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 80, 15, 50);
    $pdf->Ln(30);
}

/* ===============================
   HEADER
   =============================== */
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'APPLICATION FORM', 0, 1, 'C');
$pdf->Ln(5);

/* ===============================
   PHOTO
   =============================== */
if (!empty($data['photo_path'])) {
    $photo = __DIR__ . '/' . ltrim($data['photo_path'], '/');
    if (file_exists($photo)) {
        $pdf->Image($photo, 150, 50, 40);
        $pdf->Ln(25);
    }
}

/* ===============================
   DETAILS TABLE
   =============================== */

$pdf->SetFont('helvetica', '', 11);
$html = '
<table cellpadding="6" border="1">
<tr><td><b>Application ID</b></td><td>'.$data['application_id'].'</td></tr>
<tr><td><b>Full Name</b></td><td>'.$data['full_name'].'</td></tr>
<tr><td><b>Father Name</b></td><td>'.$data['father_name'].'</td></tr>
<tr><td><b>Date of Birth</b></td><td>'.$data['date_of_birth'].'</td></tr>
<tr><td><b>Age</b></td><td>'.$data['age'].'</td></tr>
<tr><td><b>Gender</b></td><td>'.$data['gender'].'</td></tr>
<tr><td><b>Email</b></td><td>'.$data['email'].'</td></tr>
<tr><td><b>Phone</b></td><td>'.$data['phone'].'</td></tr>
<tr><td><b>Aadhar</b></td><td>'.$data['aadhar'].'</td></tr>
<tr><td><b>Caste</b></td><td>'.$data['caste'].'</td></tr>
<tr><td><b>Address</b></td><td>'.$data['address'].'</td></tr>
<tr><td><b>SSC Year</b></td><td>'.$data['ssc_year'].'</td></tr>
<tr><td><b>SSC %</b></td><td>'.$data['ssc_percentage'].'</td></tr>
<tr><td><b>Inter Year</b></td><td>'.$data['inter_year'].'</td></tr>
<tr><td><b>Inter %</b></td><td>'.$data['inter_percentage'].'</td></tr>
<tr><td><b>Degree Year</b></td><td>'.$data['degree_year'].'</td></tr>
<tr><td><b>Degree %</b></td><td>'.$data['degree_percentage'].'</td></tr>
<tr><td><b>Position</b></td><td>'.$data['position'].'</td></tr>
<tr><td><b>Exam Center</b></td><td>'.$data['exam_center'].'</td></tr>
<tr><td><b>Transaction ID</b></td><td>'.$data['transaction_id'].'</td></tr>
<tr><td><b>Generated On</b></td><td>'.date('d M Y H:i:s').'</td></tr>
</table>
';

$pdf->writeHTML($html);

/* ===============================
   SIGNATURE
   =============================== */
if (!empty($data['signature_path'])) {
    $sign = __DIR__ . '/' . ltrim($data['signature_path'], '/');
    if (file_exists($sign)) {
        $pdf->Ln(25);
        $pdf->Image($sign, 140, $pdf->GetY(), 40);
        $pdf->Cell(0, 10, 'Candidate Signature', 0, 1, 'R');
    }
}

/* ===============================
   OUTPUT
   =============================== */
ob_end_clean();
$pdf->Output('Application_Form_'.$transaction_id.'.pdf', 'D');
exit;
