<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['examiner_logged_in'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

define('DB_HOST', 'ls-09293e762cbb8c1e5c806444a82e3a4fe2f22b56.cfiikwus2tht.ap-south-1.rds.amazonaws.com');
define('DB_NAME', 'dbmaster');
define('DB_USER', 'dbmasteruser');
define('DB_PASS', 'Bu:ws6j,sFpdH~%WF<0kHYc3)x2Z[<T<');
define('DB_CHARSET', 'utf8mb4');

$app_id = trim($_POST['application_id'] ?? '');

if ($app_id === '') {
    echo json_encode(['success'=>false,'message'=>'Invalid Application ID']);
    exit;
}

try{
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("SELECT 1 FROM exam_applications WHERE application_id=?");
    $stmt->execute([$app_id]);

    if ($stmt->fetch()) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Application ID does not exist']);
    }

}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
