<?php
require_once 'check_auth.php';
requireLogin();
require_once 'config.php';

/* ===============================
   VALIDATE INPUT
   =============================== */
$id = $_GET['id'] ?? null;
$appId = $_GET['application_id'] ?? null;

if (!$id && !$appId) {
    die("Invalid ID");
}

/* ===============================
   DB CONNECTION
   =============================== */
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database error");
}

/* ===============================
   FETCH APPLICATION
   =============================== */
if ($appId) {
    $stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE application_id = :aid LIMIT 1");
    $stmt->execute(['aid' => $appId]);

} elseif (is_numeric($id)) {
    $stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int)$id]);

} else {
    // id passed but non-numeric → treat as application_id
    $stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE application_id = :aid LIMIT 1");
    $stmt->execute(['aid' => $id]);
}

$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Application - <?= htmlspecialchars($app['application_id']) ?></title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.container{max-width:1100px;margin:2rem auto;}
.card{background:#fff;border-radius:10px;padding:1.5rem;margin-bottom:1.5rem;
box-shadow:0 4px 10px rgba(0,0,0,.08);}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;}
label{font-weight:600;color:#555;}
.top-actions{display:flex;justify-content:space-between;margin-bottom:1.5rem;}
.btn{background:#1b5e20;color:#fff;padding:.6rem 1.2rem;border-radius:6px;text-decoration:none;}
.btn.secondary{background:#555;}
img{max-width:180px;border-radius:6px;border:1px solid #ccc;}
</style>
</head>

<body>

<div class="container">

<div class="top-actions">
    <a href="dashboard.php" class="btn secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <a href="edit_application.php?id=<?= $app['id'] ?>" class="btn">
        <i class="fas fa-edit"></i> Edit
    </a>
</div>

<div class="card">
<h3>Application ID: <?= htmlspecialchars($app['application_id']) ?></h3>
</div>

<div class="card">
<h3><i class="fas fa-user"></i> Personal Information</h3>
<div class="grid">
<div><label>Name</label><div><?= htmlspecialchars($app['full_name']) ?></div></div>
<div><label>DOB</label><div><?= $app['date_of_birth'] ?></div></div>
<div><label>Age</label><div><?= $app['age'] ?></div></div>
<div><label>Gender</label><div><?= $app['gender'] ?></div></div>
<div><label>Email</label><div><?= $app['email'] ?></div></div>
<div><label>Phone</label><div><?= $app['phone'] ?></div></div>
<div><label>Father Name</label><div><?= $app['father_name'] ?></div></div>
<div><label>Aadhar</label><div><?= $app['aadhar'] ?></div></div>
<div><label>Caste</label><div><?= $app['caste'] ?></div></div>
<div><label>Address</label><div><?= nl2br(htmlspecialchars($app['address'])) ?></div></div>
</div>
</div>

<div class="card">
<h3><i class="fas fa-graduation-cap"></i> Educational Qualifications</h3>
<div class="grid">
<div><label>SSC</label><div><?= $app['ssc_year'] ?> (<?= $app['ssc_percentage'] ?>%)</div></div>
<div><label>Inter</label><div><?= $app['inter_year'] ?> (<?= $app['inter_percentage'] ?>%)</div></div>
<div><label>Degree</label><div><?= $app['degree_year'] ?> (<?= $app['degree_percentage'] ?>%)</div></div>
</div>
</div>

<div class="card">
<h3><i class="fas fa-file-alt"></i> Application Details</h3>
<div class="grid">
<div><label>Position</label><div><?= $app['position'] ?></div></div>
<div><label>Exam Center</label><div><?= $app['exam_center'] ?></div></div>
<div><label>Transaction ID</label><div><?= $app['transaction_id'] ?></div></div>
</div>
</div>

<div class="card">
<h3><i class="fas fa-images"></i> Photos & Documents</h3>
<div class="grid">
<div>
<label>Photo</label><br>
<?= $app['photo_path'] ? "<img src='{$app['photo_path']}'>" : "Not uploaded"; ?>
</div>
<div>
<label>Signature</label><br>
<?= $app['signature_path'] ? "<img src='{$app['signature_path']}'>" : "Not uploaded"; ?>
</div>
</div>
</div>

</div>
</body>
</html>
