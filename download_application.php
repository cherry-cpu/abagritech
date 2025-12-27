<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: download_application.html');
    exit;
}

if (!isset($_POST['transaction_id'])) {
    header('Location: download_application.html?error=empty');
    exit;
}

$transaction_id = trim($_POST['transaction_id']);

if ($transaction_id === '') {
    header('Location: download_application.html?error=empty');
    exit;
}

/* ===============================
   DB CONNECTION
   =============================== */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    header('Location: download_application.html?error=database');
    exit;
}

/* ===============================
   VERIFY TRANSACTION ID
   =============================== */
$stmt = $pdo->prepare(
    "SELECT transaction_id FROM exam_applications WHERE transaction_id = :tid or application_id=:tid or phone=:tid LIMIT 1"
);
$stmt->execute([':tid' => $transaction_id]);

if (!$stmt->fetch()) {
    header('Location: download_application.html?error=notfound');
    exit;
}

/* ===============================
   REDIRECT TO PDF
   =============================== */
header("Location: generate_pdf.php?transaction_id=" . urlencode($transaction_id));
exit;
