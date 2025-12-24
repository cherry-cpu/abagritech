<?php
require_once 'config.php';

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
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: download_hallticket.html');
    exit;
}

/* ===============================
   GET APPLICATION ID (FIXED)
   =============================== */
$application_id = trim($_POST['application_id'] ?? '');

if (empty($application_id)) {
    header('Location: download_hallticket.html?error=empty');
    exit;
}

/* ===============================
   VERIFY APPLICATION EXISTS
   =============================== */
$sql = "SELECT application_id FROM exam_applications WHERE application_id =:application_id or phone=:application_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':application_id', $application_id);
$stmt->execute();
$app = $stmt->fetch();

if (!$app) {
    header('Location: download_hallticket.html?error=notfound');
    exit;
}

/* ===============================
   REDIRECT TO PDF GENERATOR
   =============================== */
header("Location: generate_hallticket.php?id=" . urlencode($app['application_id']));
exit;
