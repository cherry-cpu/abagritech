<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['logged_in']) || $_SESSION['role']!=='admin'){
    header("Location: login.html");
    exit;
}

$appId = $_GET['id'] ?? '';

if(!$appId){
    die("Invalid ID");
}

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );

    $stmt = $pdo->prepare("SELECT * FROM exam_applications WHERE application_id=:id LIMIT 1");
    $stmt->execute(['id'=>$appId]);
    $app = $stmt->fetch();

    if(!$app){
        die("Invalid ID");
    }

} catch(PDOException $e){
    error_log("VIEW APPLICATION ERROR: ".$e->getMessage());
    die("Database error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Application Details</title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#f4f6f8;padding:20px;}
.card{background:#fff;padding:15px;margin-bottom:20px;border-radius:5px;box-shadow:0 0 5px rgba(0,0,0,0.1);}
h3{margin-top:0;}
img{max-width:150px;display:block;margin:5px 0;}
</style>
</head>
<body>
<a href="all_applications.php">← Back to Applications</a>
<div class="card">
    <h3>Personal Information</h3>
    <p>Full Name: <?php echo htmlspecialchars($app['full_name']); ?></p>
    <p>DOB: <?php echo htmlspecialchars($app['date_of_birth']); ?></p>
    <p>Age: <?php echo htmlspecialchars($app['age']); ?></p>
    <p>Gender: <?php echo htmlspecialchars($app['gender']); ?></p>
    <p>Email: <?php echo htmlspecialchars($app['email']); ?></p>
    <p>Phone: <?php echo htmlspecialchars($app['phone']); ?></p>
    <p>Father Name: <?php echo htmlspecialchars($app['father_name']); ?></p>
    <p>Aadhar: <?php echo htmlspecialchars($app['aadhar']); ?></p>
    <p>Caste: <?php echo htmlspecialchars($app['caste']); ?></p>
    <p>Address: <?php echo htmlspecialchars($app['address']); ?></p>
</div>

<div class="card">
    <h3>Educational Qualifications</h3>
    <p>SSC Year/Percentage: <?php echo htmlspecialchars($app['ssc_year'].' / '.$app['ssc_percentage']); ?></p>
    <p>Inter Year/Percentage: <?php echo htmlspecialchars($app['inter_year'].' / '.$app['inter_percentage']); ?></p>
    <p>Degree Year/Percentage: <?php echo htmlspecialchars($app['degree_year'].' / '.$app['degree_percentage']); ?></p>
</div>

<div class="card">
    <h3>Application Details</h3>
    <p>Position: <?php echo htmlspecialchars($app['position']); ?></p>
    <p>Exam Center: <?php echo htmlspecialchars($app['exam_center']); ?></p>
    <p>Transaction ID: <?php echo htmlspecialchars($app['transaction_id']); ?></p>
</div>

<div class="card">
    <h3>Uploaded Documents</h3>
    <?php if($app['photo_path']): ?>
        <p>Photo:</p>
        <img src="<?php echo htmlspecialchars($app['photo_path']); ?>" alt="Photo">
    <?php endif; ?>
    <?php if($app['signature_path']): ?>
        <p>Signature:</p>
        <img src="<?php echo htmlspecialchars($app['signature_path']); ?>" alt="Signature">
    <?php endif; ?>
</div>
</body>
</html>
