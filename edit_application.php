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
   GET APPLICATION ID
   =============================== */
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid ID");
}

/* ===============================
   CSRF TOKEN
   =============================== */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ===============================
   FETCH EXISTING DATA
   =============================== */
$stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) {
    die("Application not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Application - <?= htmlspecialchars($app['application_id']) ?></title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.container{max-width:900px;margin:2rem auto;background:#fff;padding:2rem;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.1)}
h2{margin-bottom:1rem;color:#1b5e20}
form .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;margin-bottom:1rem}
form label{font-weight:600;color:#555;margin-bottom:.25rem;display:block}
form input,form select,form textarea{width:100%;padding:.5rem;border:1px solid #ccc;border-radius:6px}
form button{padding:.6rem 1.2rem;background:#1b5e20;color:#fff;border:none;border-radius:6px;cursor:pointer;margin-top:1rem}
form img{max-width:150px;border:1px solid #ccc;border-radius:6px;margin-top:.5rem}
</style>
</head>
<body>

<div class="container">
<h2>Edit Application - <?= htmlspecialchars($app['application_id']) ?></h2>

<form method="POST" action="save_changes.php" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="id" value="<?= $app['id'] ?>">

<h3>Personal Information</h3>
<div class="grid">
    <div><label>Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($app['full_name']) ?>" required></div>
    <div><label>Date of Birth</label><input type="date" name="date_of_birth" value="<?= $app['date_of_birth'] ?>" required></div>
    <div><label>Age</label><input type="number" name="age" value="<?= $app['age'] ?>" required></div>
    <div><label>Gender</label>
        <select name="gender" required>
            <option value="Male" <?= $app['gender']=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= $app['gender']=='Female'?'selected':'' ?>>Female</option>
            <option value="Other" <?= $app['gender']=='Other'?'selected':'' ?>>Other</option>
        </select>
    </div>
    <div><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($app['email']) ?>" required></div>
    <div><label>Phone</label><input type="text" name="phone" value="<?= htmlspecialchars($app['phone']) ?>" required></div>
    <div><label>Father Name</label><input type="text" name="father_name" value="<?= htmlspecialchars($app['father_name']) ?>"></div>
    <div><label>Aadhar</label><input type="text" name="aadhar" value="<?= htmlspecialchars($app['aadhar']) ?>"></div>
    <div><label>Caste</label><input type="text" name="caste" value="<?= htmlspecialchars($app['caste']) ?>"></div>
    <div><label>Address</label><textarea name="address"><?= htmlspecialchars($app['address']) ?></textarea></div>
</div>

<h3>Educational Qualifications</h3>
<div class="grid">
    <div><label>SSC Year</label><input type="text" name="ssc_year" value="<?= htmlspecialchars($app['ssc_year']) ?>"></div>
    <div><label>SSC %</label><input type="text" name="ssc_percentage" value="<?= htmlspecialchars($app['ssc_percentage']) ?>"></div>
    <div><label>Inter Year</label><input type="text" name="inter_year" value="<?= htmlspecialchars($app['inter_year']) ?>"></div>
    <div><label>Inter %</label><input type="text" name="inter_percentage" value="<?= htmlspecialchars($app['inter_percentage']) ?>"></div>
    <div><label>Degree Year</label><input type="text" name="degree_year" value="<?= htmlspecialchars($app['degree_year']) ?>"></div>
    <div><label>Degree %</label><input type="text" name="degree_percentage" value="<?= htmlspecialchars($app['degree_percentage']) ?>"></div>
</div>

<h3>Application Details</h3>
<div class="grid">
    <div><label>Position</label><input type="text" name="position" value="<?= htmlspecialchars($app['position']) ?>"></div>
    <div><label>Exam Center</label><input type="text" name="exam_center" value="<?= htmlspecialchars($app['exam_center']) ?>"></div>
    <div><label>Transaction ID</label><input type="text" name="transaction_id" value="<?= htmlspecialchars($app['transaction_id']) ?>"></div>
</div>

<h3>Photo & Signature</h3>
<div class="grid">
    <div>
        <label>Photo</label>
        <input type="file" name="photo">
        <?php if($app['photo_path']): ?><img src="<?= $app['photo_path'] ?>" alt="Photo"><?php endif; ?>
    </div>
    <div>
        <label>Signature</label>
        <input type="file" name="signature">
        <?php if($app['signature_path']): ?><img src="<?= $app['signature_path'] ?>" alt="Signature"><?php endif; ?>
    </div>
</div>

<button type="submit"><i class="fas fa-save"></i> Save Changes</button>
</form>

</div>
</body>
</html>
