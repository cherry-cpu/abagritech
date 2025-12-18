<?php
// Admin view for applications with authentication
require_once 'check_auth.php';
requireLogin(); // Require user to be logged in

require_once 'config.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get all applications
$stmt = $pdo->query("SELECT * FROM exam_applications ORDER BY created_at DESC");
$applications = $stmt->fetchAll();

// Get statistics
$stats_stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN position = 'MAFO' THEN 1 ELSE 0 END) as mafo,
    SUM(CASE WHEN position = 'DAFO' THEN 1 ELSE 0 END) as dafo,
    SUM(CASE WHEN position = 'RAFO' THEN 1 ELSE 0 END) as rafo,
    SUM(CASE WHEN position = 'ZAFO' THEN 1 ELSE 0 END) as zafo
    FROM exam_applications");
$stats = $stats_stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications - Aakasha Bindu Agritech</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }
        .applications-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            box-shadow: var(--shadow);
            border-radius: 10px;
            overflow: hidden;
        }
        .applications-table th {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            text-align: left;
        }
        .applications-table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .applications-table tr:hover {
            background: var(--light-bg);
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.html"><h1>Aakasha Bindu Agritech</h1></a>
            </div>
            <nav class="nav">
                <ul class="nav-list">
                    <li><span style="color: var(--primary-color);"><i class="fas fa-user"></i> <?php echo htmlspecialchars(getCurrentUser()['full_name'] ?? getCurrentUser()['username']); ?></span></li>
                    <li><a href="auth_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Exam Applications</h1>
            <div style="display: flex; gap: 1rem;">
                <div class="export-dropdown" style="position: relative;">
                    <button class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-download"></i> Export Data
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                    </button>
                    <div class="export-menu" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 0.5rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 180px; z-index: 1000;">
                        <a href="export_applications.php?format=csv" class="export-option" style="display: block; padding: 0.75rem 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">
                            <i class="fas fa-file-csv"></i> Export as CSV
                        </a>
                        <a href="export_applications.php?format=excel" class="export-option" style="display: block; padding: 0.75rem 1rem; text-decoration: none; color: #333;">
                            <i class="fas fa-file-excel"></i> Export as Excel
                        </a>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['approved']; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['mafo']; ?></div>
                <div class="stat-label">MAFO</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['dafo']; ?></div>
                <div class="stat-label">DAFO</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['rafo']; ?></div>
                <div class="stat-label">RAFO</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['zafo']; ?></div>
                <div class="stat-label">ZAFO</div>
            </div>
        </div>

        <table class="applications-table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Exam Center</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem;">No applications found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app['application_id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td><?php echo htmlspecialchars($app['phone']); ?></td>
                            <td><?php echo htmlspecialchars($app['position']); ?></td>
                            <td><?php echo htmlspecialchars($app['exam_center']); ?></td>
                            <td><?php echo htmlspecialchars($app['transaction_id']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $app['status']; ?>">
                                    <?php echo ucfirst($app['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($app['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        // Export dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.querySelector('.export-dropdown button');
            const exportMenu = document.querySelector('.export-menu');
            
            if (exportBtn && exportMenu) {
                exportBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    exportMenu.style.display = exportMenu.style.display === 'none' ? 'block' : 'none';
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!exportBtn.contains(e.target) && !exportMenu.contains(e.target)) {
                        exportMenu.style.display = 'none';
                    }
                });
                
                // Handle export option clicks
                document.querySelectorAll('.export-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        // Show loading state
                        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
                        exportBtn.disabled = true;
                    });
                });
            }
        });
    </script>
</body>
</html>

