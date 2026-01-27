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
$s2 = intval($_POST['s2'] ?? -1);
$s3 = intval($_POST['s3'] ?? -1);

if ($application_id === '' || $s1 < 0 || $s2 < 0 || $s3 < 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    /* Validate application ID */
    $check = $pdo->prepare("SELECT 1 FROM exam_applications WHERE application_id=?");
    $check->execute([$application_id]);

    if (!$check->fetch()) {
        echo json_encode(['success'=>false,'message'=>'Application ID not found']);
        exit;
    }

    $total = $s1 + $s2 + $s3;
    $result = ($s1>=35 && $s2>=35 && $s3>=35) ? 'PASS' : 'FAIL';

/*
    $sql="
        INSERT INTO exam_marks (application_id,subject1,subject2,subject3,total,result) values(:application_id, :subject1,:subject2,:subject3,:total,:result)
        ON DUPLICATE KEY UPDATE
        subject1=:subject1, subject2=:subject2, subject3=:subject3, total=:total, result=:result
    "; */
    $sql="INSERT INTO exam_marks (application_id,subject1,subject2,subject3,total,result) values(:application_id, :subject1,:subject2,:subject3,:total,:result)";
    error_log($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":application_id", $application_id);
    $stmt->bindParam(":subject1", $s1);
    $stmt->bindParam(":subject2", $s2);
    $stmt->bindParam(":subject3", $s3);
    $stmt->bindParam(":total", $total);
    $stmt->bindParam(":result", $result);
    $stmt->execute();
    $data = $stmt->fetch();
    error_log('step 6');

    echo json_encode(['success'=>true,'message'=>'Marks saved successfully']);

} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
