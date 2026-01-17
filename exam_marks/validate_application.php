<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['examiner_logged_in'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}
require_once 'config.php';


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
