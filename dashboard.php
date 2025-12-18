<?php
require_once 'check_auth.php';
requireLogin();
require_once 'config.php';

session_start();

/* ===============================
   CSRF TOKEN
   =============================== */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    die("Database connection failed");
}

/* ===============================
   HANDLE DELETE
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $stmt = $pdo->prepare("DELETE FROM exam_applications WHERE id = :id");
    $stmt->execute(['id' => (int)$_POST['delete_id']]);

    header("Location: dashboard.php?deleted=1");
    exit;
}

/* ===============================
   DASHBOARD STATS
   =============================== */
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(position = 'MAFO') AS mafo,
        SUM(position = 'DAFO') AS dafo,
        SUM(position = 'RAFO') AS rafo,
        SUM(position = 'ZAFO') AS zafo
    FROM exam_applications
")->fetch(PDO::FETCH_ASSOC);

$recent_applications = $pdo->query("
    SELECT *
    FROM exam_applications
    ORDER BY created_at DESC
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    display: flex;
    margin: 0;
    background: #f4f6f8;
    font-family: Arial;
}

.sidebar {
    width: 250px;
    background: #1b5e20;
    color: #fff;
    min-height: 100vh;
    padding: 1.5rem;
}

.sidebar a {
    display: block;
    color: #fff;
    padding: .75rem 1rem;
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: .5rem;
}

.sidebar a:hover,
.sidebar a.active {
    background: #2e7d32;
}

.main {
    flex: 1;
    padding: 2rem;
}

/* STATS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: #fff;
    padding: 1.2rem;
    border-radius: 8px;
    text-align: center;
}

.stat-card-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1b5e20;
}

.stat-card-label {
    margin-top: 6px;
    font-size: 0.95rem;
    color: #555;
    font-weight: 600;
}

/* FILTER BAR */
.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-bar input,
.filter-bar select {
    padding: .5rem;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

th, td {
    padding: .75rem;
    border-bottom: 1px solid #eee;
}

th {
    background: #f1f1f1;
}

.action-btn {
    padding: 6px 10px;
    border-radius: 6px;
    color: #fff;
    border: none;
    cursor: pointer;
}

.action-btn.view { background: #1976d2; }
.action-btn.edit { background: #2e7d32; }
.action-btn.delete { background: #c62828; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a class="active" href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="contact_messages.php"><i class="far fa-comment"></i> Contact Messages</a>
    <a href="auth_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Dashboard</h1>

<?php if (isset($_GET['deleted'])): ?>
<div style="background:#e8f5e9;color:#2e7d32;padding:10px;border-radius:6px;margin-bottom:1rem">
    Application deleted successfully.
</div>
<?php endif; ?>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-number"><?= $stats['total'] ?></div>
        <div class="stat-card-label">Total Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $stats['mafo'] ?></div>
        <div class="stat-card-label">MAFO</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $stats['dafo'] ?></div>
        <div class="stat-card-label">DAFO</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $stats['rafo'] ?></div>
        <div class="stat-card-label">RAFO</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $stats['zafo'] ?></div>
        <div class="stat-card-label">ZAFO</div>
    </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search...">
    <select id="statusFilter">
        <option value="">All Status</option>
        <option>pending</option>
        <option>approved</option>
        <option>rejected</option>
    </select>
    <select id="positionFilter">
        <option value="">All Positions</option>
        <option>MAFO</option>
        <option>DAFO</option>
        <option>RAFO</option>
        <option>ZAFO</option>
    </select>
</div>

<!-- TABLE -->
<table id="applicationsTable">
<thead>
<tr>
    <th>S.No</th>
    <th>Application ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Position</th>
    <th>Status</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php $i = 1; foreach ($recent_applications as $app): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($app['application_id']) ?></td>
    <td><?= htmlspecialchars($app['full_name']) ?></td>
    <td><?= htmlspecialchars($app['email']) ?></td>
    <td><?= htmlspecialchars($app['phone']) ?></td>
    <td><?= htmlspecialchars($app['position']) ?></td>
    <td><?= htmlspecialchars($app['status']) ?></td>
    <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
    <td style="display:flex;gap:6px">
        <a href="view_application.php?id=<?= $app['id'] ?>" class="action-btn view"><i class="fas fa-eye"></i></a>
        <a href="edit_application.php?id=<?= $app['id'] ?>" class="action-btn edit"><i class="fas fa-edit"></i></a>
        <form method="POST" onsubmit="return confirm('Delete this application?');">
            <input type="hidden" name="delete_id" value="<?= $app['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button class="action-btn delete"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

<script>
const rows = document.querySelectorAll("#applicationsTable tbody tr");

function filter() {
    let s = searchInput.value.toLowerCase();
    let st = statusFilter.value;
    let p = positionFilter.value;

    rows.forEach(r => {
        let t = r.innerText.toLowerCase();
        r.style.display =
            t.includes(s) &&
            (!st || r.cells[6].innerText === st) &&
            (!p || r.cells[5].innerText === p)
            ? "" : "none";
    });
}

searchInput.onkeyup = filter;
statusFilter.onchange = filter;
positionFilter.onchange = filter;
</script>

</body>
</html>
