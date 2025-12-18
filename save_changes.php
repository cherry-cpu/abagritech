<?php
require_once 'check_auth.php';
requireLogin();
require_once 'config.php';
session_start();

/* ===============================
   DB CONNECTION
   =============================== */
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Database connection failed");
}

/* ===============================
   VALIDATE POST
   =============================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token");
}

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid ID");
}

/* ===============================
   FETCH EXISTING DATA
   =============================== */
$stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) die("Application not found");

/* ===============================
   COLLECT DATA
   =============================== */
$data = [
    'full_name' => trim($_POST['full_name']),
    'date_of_birth' => $_POST['date_of_birth'],
    'age' => (int)$_POST['age'],
    'gender' => $_POST['gender'],
    'email' => trim($_POST['email']),
    'phone' => trim($_POST['phone']),
    'father_name' => trim($_POST['father_name']),
    'aadhar' => trim($_POST['aadhar']),
    'caste' => trim($_POST['caste']),
    'address' => trim($_POST['address']),
    'ssc_year' => trim($_POST['ssc_year']),
    'ssc_percentage' => trim($_POST['ssc_percentage']),
    'inter_year' => trim($_POST['inter_year']),
    'inter_percentage' => trim($_POST['inter_percentage']),
    'degree_year' => trim($_POST['degree_year']),
    'degree_percentage' => trim($_POST['degree_percentage']),
    'position' => trim($_POST['position']),
    'exam_center' => trim($_POST['exam_center']),
    'transaction_id' => trim($_POST['transaction_id']),
];

/* ===============================
   FILE UPLOADS
   =============================== */
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Photo
if (!empty($_FILES['photo']['name'])) {
    $photoName = time().'_'.basename($_FILES['photo']['name']);
    $photoPath = $uploadDir.$photoName;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
        $data['photo_path'] = $photoPath;
    }
} else {
    $data['photo_path'] = $app['photo_path'];
}

// Signature
if (!empty($_FILES['signature']['name'])) {
    $sigName = time().'_'.basename($_FILES['signature']['name']);
    $sigPath = $uploadDir.$sigName;
    if (move_uploaded_file($_FILES['signature']['tmp_name'], $sigPath)) {
        $data['signature_path'] = $sigPath;
    }
} else {
    $data['signature_path'] = $app['signature_path'];
}

/* ===============================
   UPDATE DB
   =============================== */
$data['id'] = $id;
$sql = "UPDATE exam_applications SET 
    full_name=:full_name,
    date_of_birth=:date_of_birth,
    age=:age,
    gender=:gender,
    email=:email,
    phone=:phone,
    father_name=:father_name,
    aadhar=:aadhar,
    caste=:caste,
    address=:address,
    ssc_year=:ssc_year,
    ssc_percentage=:ssc_percentage,
    inter_year=:inter_year,
    inter_percentage=:inter_percentage,
    degree_year=:degree_year,
    degree_percentage=:degree_percentage,
    position=:position,
    exam_center=:exam_center,
    transaction_id=:transaction_id,
    photo_path=:photo_path,
    signature_path=:signature_path
    WHERE id=:id
";
$stmt = $pdo->prepare($sql);
$stmt->execute($data);

/* ===============================
   ALERT & REDIRECT
   =============================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Successful</title>
<script>
alert('Data saved successfully!');
window.location.href = 'view_application.php?id=<?= $id ?>';
</script>
</head>
<body>
</body>
</html>
