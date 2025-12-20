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

$photo_path=$data['photo_path'];
$signature_path=$data['signature_path'];
$transaction_id=$data['transaction_id'];
$dat_of_birth=$data['date_of_birth'];
$full_name=$data['full_name'];
$gender=$data['gender'];
$caste=$data['caste'];
$father_name=$data['father_name'];
$phone=$data['phone'];
$mail=$data['email'];
$address=$data['address'];
$aadhar_no=$data['aadhar'];
$ssc_year=$data['ssc_year'];
$ssc_percentage=$data['ssc_percentage'];
$inter_year=$data['inter_year'];
$inter_percentage=$data['inter_percentage'];
$degree_year=$data['degree_year'];
$degree_percentage=$data['degree_percentage'];
$position=$data['position'];
$exam_center=$data['exam_center'];
$application_id=$data['application_id'];
$transaction_id=$data['transaction_id'];
//$submitted_on=$data['submitted_on'];

// Create new PDF document
$pdf = new TCPDF();
// Add a page
$pdf->AddPage();
// Set font
$pdf->SetFont('dejavusans', '', 12);
// title
// $pdf->ln(10);
$pdf->Image('test/logo.PNG',(($pdf->getPageWidth()-50)/2),10,50,0, 'PNG', '','RTLM');
$pdf->ln(20);
$pdf->WriteHTML('<h1>Application Form</h1>', align:'C');
$pdf->ln(10);
$html = '<html>
<table border="1" cellPadding="5">
    <tr>
        <th colspan="4" class="heading"><h2>Personal Details</h2></th>
        <th rowspan="4">
            <img src=".'.$photo_path.'" alt="photo" height="90" width="90">
            <img src=".'.$signature_path.'" alt="photo" height="45"  width="90">
        </th>
    </tr>
    <tr>
    </tr>
    <tr>
        <td class="side_variable">Application Id</td>
        <td colspan="3">'.$application_id.'</td>
    </tr>
        <tr>
        <td class="side_variable">Full Name</td>
        <td colspan="3">'.$full_name.'</td>
    </tr>
    <tr>
        <td colspan="1" class="side_variable">Date of Birth</td>
        <td colspan="1">'.$dat_of_birth.'</td>
        <td colspan="1" class="side_variable">Transaction Id</td>
        <td colspan="2">'.$transaction_id.'</td>
    </tr>
        <tr>
        <td class="side_variable">Gender</td>
        <td>'.$gender.'</td>
        <td class="side_variable">Caste</td>
        <td colspan="2">'.$caste.'</td>
    </tr>
    <tr>
        <td class="side_variable">Father Name</td>
        <td colspan="4">'.$father_name.'</td>
    </tr>
    <tr>
        <td class="side_variable">Phone</td>
        <td>'.$phone.'</td>
        <td class="side_variable">Mail</td>
        <td colspan="2">'.$mail.'</td>
    </tr>
    <tr>
        <td class="side_variable">Address</td>
        <td colspan="4">'.$address.'</td>
    </tr>
    <tr>
        <td class="side_variable">Aadhar No</td>
        <td colspan="4">'.$aadhar_no.'</td>
    </tr>
    <tr style="height: 25px;">

    </tr>
    <tr>
        <th colspan="5" class="heading"><h2>Educational Qualifications</h2></th>
    </tr>
    <tr>
        <td class="side_variable">SSC YEAR</td>
        <td colspan="2">'.$ssc_year.'</td>
        <td class="side_variable">SSC CGPA</td>
        <td>'.$ssc_percentage.'</td>
    </tr>
    <tr>
        <td class="side_variable">INTERMEDIATE YEAR</td>
        <td colspan="2">'.$inter_year.'</td>
        <td class="side_variable">INTERMEDIATE CGPA</td>
        <td>'.$inter_percentage.'</td>
    </tr>
    <tr>
        <td class="side_variable">DEGREE YEAR</td>
        <td colspan="2">'.$degree_year.'</td>
        <td class="side_variable">DEGREE CGPA</td>
        <td>'.$degree_percentage.'</td>
    </tr>
        <tr style="height: 25px;">

    </tr>
    <tr>
        <th colspan="5" class="heading"><h2>Application Details</h2></th>
    </tr>
    <tr>
        <td class="side_variable">Applying For</td>
        <td colspan="2">'.$position.'</td>
        <td class="side_variable">Exam Center</td>
        <td colspan="2">'.$exam_center.'</td>
    </tr>
</table>
<style>
.heading{font-weight:bold; background-color: #e9f4f5;}
.side_variable{font-weight:200; ;background-color: #e8f5e9;}
</style>
';

$pdf->writeHTML($html);

/* ===============================
   OUTPUT
   =============================== */
ob_end_clean();
$pdf->Output('Application_Form_'.$transaction_id.'.pdf', 'D');
exit;
