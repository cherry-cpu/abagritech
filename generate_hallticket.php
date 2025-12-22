<?php
ob_start();

require_once 'config.php';
require_once 'tcpdf/tcpdf.php';

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
$application_id = trim($_GET['id']);

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
    e.position,
    e.exam_center,
    e.transaction_id,
    e.photo_path,
    e.signature_path,
    c.address,
    c.exam_date
FROM exam_applications e
LEFT JOIN exam_centers c
  ON TRIM(e.exam_center) = TRIM(c.district_name)
 AND TRIM(e.position) = TRIM(c.position)
 AND c.center_active = 1
WHERE e.application_id = :application_id and e.id>=start_id AND e.id<=end_id and c.address IS NOT NULL and status='completed'
 LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':application_id', $application_id);
$stmt->execute();
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    exit('Application not found');
}

$photo_path=$data['photo_path'];
$signature_path=$data['signature_path'];
$transaction_id=$data['transaction_id'];
$date_of_birth=$data['date_of_birth'];
$full_name=$data['full_name'];
$gender=$data['gender'];
$father_name=$data['father_name'];
$phone=$data['phone'];
$mail=$data['email'];
$aadhar_no=$data['aadhar'];
$position=$data['position'];
$exam_center=$data['exam_center'];
$exam_center_address=$data['address'];
$exam_date=$data['exam_date'];
//$submitted_on=$data['submitted_on'];

/*$sql = "
SELECT e.address FROM exam_centers e
WHERE e.district_name = :district ;";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':district', $data['exam_center']);
$stmt->execute();
$exam_center_data = $stmt->fetch();
 */

// Create new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Abagritech');
$pdf->SetTitle('Exam HallTicket');
$pdf->SetSubject('Exam HallTicket');


// Add a page
$pdf->AddPage();
// Set font
$pdf->SetFont('dejavusans', '', 12);
// title
// $pdf->ln(10);
$pdf->Image('Logo.PNG',(($pdf->getPageWidth()-50)/2),10,50,0, 'PNG', '','RTLM');
$pdf->ln(20);
$pdf->WriteHTML('<h1>Hall Ticket</h1>', align:'C');
$pdf->ln(10);
// Add image
// $pdf->Image('photo.jpg', 0, 0, 25*1.5, 25*2, 'JPG');

// image(path, x, y, w, h, type, link, align)
$html = '<html>
<table border="1" cellPadding="5">
    <tr>
        <th colspan="4" class="heading"><h2>Personal details</h2></th>
        <th rowspan="4">
            <img src="'.$photo_path.'" alt="photo" height="90" width="90">
            <img src="'.$signature_path.'" alt="photo" height="45"  width="90">
        </th>
    </tr>
    <tr>
    </tr>
    <tr>
           <td colspan="1" class="side_variable">Hall Ticket No</td>
           <td colspan="4">'.$application_id.'</td>
       </tr>
        <tr>
        <td class="side_variable">Full Name</td>
        <td colspan="3">'.$full_name.'</td>
    </tr>

    <tr>
        <td colspan="1" class="side_variable">Date of Birth</td>
        <td colspan="1">'.$date_of_birth.'</td>
        <td class="side_variable">Gender</td>
        <td colspan="2">'.$gender.'</td>
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
        <td class="side_variable">Aadhar No</td>
        <td colspan="4">'.$aadhar_no.'</td>
    </tr>
    <tr style="height: 25px;">

    </tr>
    <tr>
        <td class="side_variable">Exam For</td>
        <td colspan="1">'.$position.'</td>
        <td class="side_variable">Exam Date</td>
        <td colspan="1">'.$exam_date.'</td>
    </tr>
  <tr>
        <td class="side_variable">Exam Center Address</td>
        <td colspan="4">'.$exam_center_address.'</td>
    </tr>
</table>
<style>
.heading{font-weight:bold; background-color: #e9f4f5;}
.side_variable{font-weight:200; ;background-color: #e8f5e9;}
</style>
';

$pdf->writeHTML($html);

$pdf->AddPage();
$pdf->ln(15);
$pdf->WriteHTML('<h3 style="font-weight:150; ">Hall ticket should be preserved till the end of the examinations</h3>', align:'C');
$pdf->WriteHTML('<h1>INSTRUCTIONS TO THE CANDIDATES</h1>', align:'C');
$pdf->ln(5);


$pdf->MultiCell(0,0,'1. The candidates are held responsible for obtaining correct question paper from the Invigilator as per the Scheme. Answering a wrong question Paper may lead to cancellation of Examination of that paper.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'2. The Hall Ticket must be presented for entry into the examination hall along with at least photo identification card issued by government.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'3. Company reserves the right to cancel the admission of the candidates at any stage, when it is detected that his / her admission to the examination or the company is against the rules.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'4. Candidates are required to bring their Hall Ticket compulsory. ', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'5. Candidates should occupy their seats at least 15 minutes before the commencement of the examination.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'6. Candidate should not write anything except his / her hall ticket number on Question Paper.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'7. Candidates are prohibited from bringing and using printed / written material of any kind into the Exam Hall. If found booked under Malpractice.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'8. Candidates are prohibited from bringing Mathematical Tables, however, if required will be supplied by the Chief Superintendent. Students of Mathematics, Science and Engineering are allowed to bring their own non programmable calculator. If violated, booked under Malpractice.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'9. No electronic gadgets / Mobiles are permitted in the exam Hall. If found booked under malpractice.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'10 .Candidates if found in copying / communicating with others or indulging in any malpractice will be expelled from the examination Hall and will not be allowed to appear for the remaining papers / subjects and will be booked under Malpractice.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'11 .Candidates are requested to cooperate to have a safe, healthy environment for the examinations', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'12 .Candidates are required to bring their payment transactions slip compulsory.', align:'L');
$pdf->ln(2.5);
$pdf->MultiCell(0,0,'13 .In the absence of the candidate’s photograph and signature on the hall ticket, the signature of a Gazetted Officer is required', align:'L');
ob_end_clean();
// Output the PDF to the browser
$filename = 'HallTicket_' . $application_id . '.pdf';
$pdf->Output($filename, 'I');
?>
