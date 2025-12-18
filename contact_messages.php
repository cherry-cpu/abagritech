<?php
require_once 'check_auth.php';  // Authentication check
requireLogin();  // Ensure the user is logged in
require_once 'config.php';  // Database configuration

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
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed");
}

/* ===============================
   HANDLE STATUS UPDATE
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $id = (int) $_POST['message_id'];
    $status = $_POST['status'];

    // Update message status in the database
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);

    header("Location: contact_messages.php?status_updated=1");
    exit;
}

/* ===============================
   FETCH CONTACT MESSAGES
   =============================== */
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            display: flex;
            background: #f4f6f8;
            font-family: Arial;
            margin: 0;
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
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: .75rem;
            border-bottom: 1px solid #eee;
            text-align: left;
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
            font-size: 14px;
        }
        .action-btn.view { background: #1976d2; }
        .action-btn.edit { background: #2e7d32; }
        .action-btn.delete { background: #c62828; }
        .action-btn:hover { opacity: .85; }
        .filter-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }
        .filter-bar input,
        .filter-bar select {
            padding: .5rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a class="active" href="contact_messages.php"><i class="far fa-comment"></i> Contact Messages</a>
    <a href="auth_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1><i class="far fa-comment"></i> Contact Messages</h1>

<?php if (isset($_GET['status_updated'])): ?>
<div style="background:#e8f5e9;color:#2e7d32;padding:10px;border-radius:6px;margin-bottom:1rem">
    Message status updated successfully.
</div>
<?php endif; ?>

<!-- FILTER BAR -->
<div class="filter-bar">
    <input type="text" id="subjectFilter" placeholder="Filter by Subject">
    <select id="statusFilter">
        <option value="">All Status</option>
        <option value="unread">Unread</option>
        <option value="read">Read</option>
        <option value="replied">Replied</option>
        <option value="archived">Archived</option>
    </select>
</div>

<!-- CONTACT MESSAGES TABLE -->
<table id="messagesTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messages as $message): ?>
        <tr class="message-row">
            <td><?= htmlspecialchars($message['id']) ?></td>
            <td><?= htmlspecialchars($message['name']) ?></td>
            <td><?= htmlspecialchars($message['email']) ?></td>
            <td><?= htmlspecialchars($message['phone']) ?></td>
            <td class="subject"><?= htmlspecialchars($message['subject']) ?></td>
            <td><?= nl2br(htmlspecialchars($message['message'])) ?></td>
            <td>
                <form method="POST" class="status-form">
                    <select name="status">
                        <option value="unread" <?= $message['status'] == 'unread' ? 'selected' : ''; ?>>Unread</option>
                        <option value="read" <?= $message['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                        <option value="replied" <?= $message['status'] == 'replied' ? 'selected' : ''; ?>>Replied</option>
                        <option value="archived" <?= $message['status'] == 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" name="update_status" class="action-btn edit">Update</button>
                </form>
            </td>
            <td><?= date('d M Y', strtotime($message['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

<script>
// Dynamic Filtering
const subjectFilter = document.getElementById('subjectFilter');
const statusFilter = document.getElementById('statusFilter');
const rows = document.querySelectorAll('#messagesTable tbody tr');

function filterMessages() {
    const subjectValue = subjectFilter.value.toLowerCase();
    const statusValue = statusFilter.value.toLowerCase();

    rows.forEach(row => {
        const subject = row.querySelector('.subject').innerText.toLowerCase();
        const status = row.querySelector('select[name="status"]').value.toLowerCase();

        const subjectMatch = subject.includes(subjectValue);
        const statusMatch = !statusValue || status === statusValue;

        row.style.display = subjectMatch && statusMatch ? '' : 'none';
    });
}

subjectFilter.addEventListener('input', filterMessages);
statusFilter.addEventListener('change', filterMessages);
</script>

</body>
</html>
