<?php
ob_start(); // prevent TCPDF output errors

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
    error_log($e->getMessage());
    exit('Database connection failed');
}

/* ===============================
   VALIDATE REQUEST
   =============================== */
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$application_id = trim($_GET['id']);

/* ===============================
   FETCH DATA
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
    c.district_name,
    c.address,
    c.exam_date,
    e.photo_path,
    e.signature_path
FROM exam_applications e
LEFT JOIN exam_centers c ON e.exam_center = c.district_name
WHERE e.application_id = :application_id
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':application_id', $application_id);
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
$pdf->SetCreator('Exam System');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Hall Ticket');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

/* ===============================
   LOGO (NEW)
   =============================== */
$logoPath = __DIR__ . '/images/Logo.png';
if (file_exists($logoPath)) {
    $startY = 25; // push logo down to avoid overlap
    $pdf->SetY($startY);
    $pdf->Image($logoPath, 80, $startY, 50); // centered logo
    $pdf->Ln(25); // reserve space below logo
} else {
    $pdf->Ln(15);
}

/* ===============================
   HEADER
   =============================== */
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'EXAM HALL TICKET', 0, 1, 'C');
$pdf->Ln(5);

/* ===============================
   PROFILE PHOTO
   =============================== */
   if (!empty($data['photo_path'])) {
    $photoAbsolute = __DIR__ . '/' . ltrim($data['photo_path'], '/');
    if (file_exists($photoAbsolute)) {
        $pdf->Image($photoAbsolute, 80, $pdf->GetY(), 50);
        $pdf->Ln(75);
    } else {
        $pdf->Ln(10);
    }
} else {
    $pdf->Ln(10);
}

/* ===============================
   DETAILS TABLE
   =============================== */
$pdf->SetFont('helvetica', '', 11);
$html = '
<table cellpadding="6" border="1">
<tr><td width="30%"><b>Application ID</b></td><td width="70%">'.$data['application_id'].'</td></tr>
<tr><td><b>Full Name</b></td><td>'.$data['full_name'].'</td></tr>
<tr><td><b>Father Name</b></td><td>'.$data['father_name'].'</td></tr>
<tr><td><b>Date of Birth</b></td><td>'.$data['date_of_birth'].'</td></tr>
<tr><td><b>Gender</b></td><td>'.$data['gender'].'</td></tr>
<tr><td><b>Email</b></td><td>'.$data['email'].'</td></tr>
<tr><td><b>Phone</b></td><td>'.$data['phone'].'</td></tr>
<tr><td><b>Exam Center</b></td><td>'.$data['district_name'].'</td></tr>
<tr><td><b>Center Address</b></td><td>'.$data['address'].'</td></tr>
<tr><td><b>Exam Date</b></td><td>'.$data['exam_date'].'</td></tr>
</table>
';
$pdf->writeHTML($html, true, false, true, false, '');

/* ===============================
   SIGNATURE
   =============================== */
   if (!empty($data['signature_path'])) {
    $signatureAbsolute = __DIR__ . '/' . ltrim($data['signature_path'], '/');
    if (file_exists($signatureAbsolute)) {
        $pdf->Ln(10);
        $pdf->Image($signatureAbsolute, 140, $pdf->GetY(), 40);
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Candidate Signature', 0, 1, 'R');
    }
}

/* ===============================
   OUTPUT PDF
   =============================== */
ob_end_clean();
$filename = 'HallTicket_' . $data['application_id'] . '.pdf';
$pdf->Output($filename, 'D');
exit;
