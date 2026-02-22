<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['examiner_logged_in'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

require_once 'config.php';


$application_id = trim($_POST['application_id'] ?? '');
$s1 = intval($_POST['s1'] ?? -1);

if ($application_id === '' || $s1 < 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}
if($s1 >100) {
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    /* Validate application ID */
    $sql="SELECT 1 FROM exam_applications WHERE application_id=:application_id";
        /* convert raw applicationId into valid Id by adding hypen '-' */
        $string=$application_id;
        $application_id = substr($string, 0, 4) . '-' .substr($string, 4, 8) . '-' .substr($string, 12);
    error_log($application_id);
    $check = $pdo->prepare($sql);
    $check->bindParam(":application_id", $application_id);
    $check->execute();
    if (!$check->fetch()) {
        echo json_encode(['success'=>false,'message'=>'Application ID not found.',  "applicationId"=>$application_id]);
        exit;
    }
    $result = ($s1>=35) ? 'PASS' : 'FAIL';

/*
    $sql="
        INSERT INTO exam_marks (application_id,subject1,subject2,subject3,total,result) values(:application_id, :subject1,:subject2,:subject3,:total,:result)
        ON DUPLICATE KEY UPDATE
        subject1=:subject1, subject2=:subject2, subject3=:subject3, total=:total, result=:result
    "; */
    $sql="INSERT INTO exam_marks (application_id,subject1,result) values(:application_id, :subject1,:result)";
    error_log($sql.'  '.$result);
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":application_id", $application_id);
    $stmt->bindParam(":subject1", $s1);
    $stmt->bindParam(":result", $result);
    $stmt->execute();
    $data = $stmt->fetch();
    error_log('step 6');

    echo json_encode(['success'=>true,'message'=>'Marks saved successfully']);

} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
