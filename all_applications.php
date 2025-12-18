<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.html");
    exit;
}

$categoryFilter = $_GET['category'] ?? '';
$searchMobile = $_GET['phone'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );

    $where = [];
    $params = [];

    if($categoryFilter){
        $where[] = "position=:category";
        $params['category']=$categoryFilter;
    }

    if($searchMobile){
        $where[] = "phone LIKE :phone";
        $params['phone']="%".$searchMobile."%";
    }

    $whereSql = '';
    if($where){
        $whereSql = " WHERE ".implode(' AND ',$where);
    }

    $stmt = $pdo->prepare("SELECT * FROM exam_applications $whereSql ORDER BY id DESC");
    $stmt->execute($params);
    $applications = $stmt->fetchAll();

} catch(PDOException $e){
    error_log("ALL APPLICATIONS ERROR: ".$e->getMessage());
    die("Database error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Applications</title>
<style>
body{font-family:Arial,sans-serif;margin:0;display:flex;background:#f4f6f8;}
.sidebar{width:220px;background:#2c3e50;height:100vh;color:#fff;display:flex;flex-direction:column;padding:20px;}
.sidebar a{color:#fff;text-decoration:none;margin:10px 0;}
.main{flex:1;padding:20px;}
.table {width:100%; border-collapse:collapse;}
.table th, .table td{padding:10px; border:1px solid #ddd;}
.table th{background:#f0f0f0;}
.button{padding:5px 10px;text-decoration:none;background:#3498db;color:#fff;border-radius:3px;}
</style>
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <a href="dashboard.php">Dashboard</a>
    <a href="all_applications.php">All Applications</a>
    <a href="export_csv.php">Export CSV</a>
    <a href="export_sql.php">Export SQL</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>All Applications</h2>
    <form method="get">
        <input type="text" name="phone" placeholder="Search by mobile" value="<?php echo htmlspecialchars($searchMobile); ?>">
        <input type="text" name="category" placeholder="Filter by category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
        <button type="submit">Filter</button>
    </form>
    <br>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Transaction/UTR</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Applying For</th>
            <th>Actions</th>
        </tr>
        <?php foreach($applications as $app): ?>
        <tr>
            <td><?php echo $app['application_id']; ?></td>
            <td><?php echo htmlspecialchars($app['transaction_id']); ?></td>
            <td><?php echo htmlspecialchars($app['full_name']); ?></td>
            <td><?php echo htmlspecialchars($app['phone']); ?></td>
            <td><?php echo htmlspecialchars($app['position']); ?></td>
            <td>
                <a class="button" href="view_application.php?id=<?php echo urlencode($app['application_id']); ?>">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
