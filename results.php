<?php
require_once 'config.php';
$data = null;

if (isset($_POST['application_id'])) {
    $application_id=$_POST['application_id'];
    $string=$application_id;
    $application_id = substr($string, 0, 4) . '-' .substr($string, 4, 8) . '-' .substr($string, 12);

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $st = $pdo->prepare("SELECT * FROM exam_marks WHERE application_id = ?");
        $st->execute([$application_id]);
        $data = $st->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $data = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examination Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main Styles -->
    <link rel="stylesheet" href="styles.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- PAGE-SPECIFIC STYLES -->
    <style>
        /* ================= RESULT PAGE ================= */
        .result-section {
            background: #f4f6f9;
            padding: 60px 0;
        }

        .result-search-card {
            max-width: 520px;
            margin: 0 auto 30px;
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .result-search-card h2 {
            margin-bottom: 8px;
        }

        .result-search-card p {
            color: #666;
            margin-bottom: 20px;
        }

        .result-search-form {
            display: flex;
            gap: 10px;
        }

        .result-search-form input {
            flex: 1;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .result-search-form button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 14px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .result-main-card {
            max-width: 720px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .result-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .result-top h4 {
            margin: 0;
            font-size: 15px;
            color: #777;
        }

        .result-top span {
            font-size: 18px;
            font-weight: bold;
        }

        .result-status {
            padding: 10px 24px;
            border-radius: 30px;
            color: #fff;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .result-status.pass {
            background: linear-gradient(135deg, #28a745, #5ddc84);
        }

        .result-status.fail {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
        }

        .marks-table th,
        .marks-table td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }

        .marks-table th {
            background: #f8f9fa;
            text-align: left;
        }

        .marks-table .total {
            font-weight: bold;
            background: #f1f3f5;
        }

        .result-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
            padding: 14px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
            padding: 14px 28px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .result-error {
            margin-top: 30px;
            text-align: center;
            color: #dc3545;
            font-size: 18px;
            font-weight: bold;
        }

        @media(max-width: 600px) {
            .result-search-form {
                flex-direction: column;
            }

            .result-top {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- ================= HEADER ================= -->
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="index.html">
                <img src="./images/Logo.png" alt="Aakasha Bindu Agritech Logo" class="navbar-logo">
            </a>
        </div>
        <nav class="nav">
            <ul class="nav-list">
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="index.html#products">Products</a></li>
                <li><a href="team.html">Team</a></li>
                <li><a href="index.html#careers">Careers</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="videos.html">Videos</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </div>
</header>

<!-- ================= PAGE HEADER ================= -->
<section class="page-header">
    <div class="container">
        <h1>Examination Result</h1>
        <p>View and download your exam result</p>
    </div>
</section>

<!-- ================= RESULT SECTION ================= -->
<section class="result-section">
    <div class="container">

        <div class="result-search-card">
            <h2>Check Result</h2>
            <p>Enter your Hall Ticket Number below</p>

            <form method="post" class="result-search-form">
                <input type="text" name="application_id" placeholder="Hall Ticket No." required>
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php if ($data): ?>
            <div class="result-main-card">
                <div class="result-top">
                    <div>
                        <h4>Application ID</h4>
                        <span><?= htmlspecialchars($application_id) ?></span>
                    </div>

                    <div class="result-status <?= strtolower($data['result']) ?>">
                        <?= strtoupper($data['result']) ?>
                    </div>
                </div>

                <table class="marks-table">
                    <tr class="total">
                        <th>Total Marks</th>
                        <td><?= $data['subject1'] ?></td>
                    </tr>
                </table>
                <div class="result-actions">
                    <a href="download_result_pdf.php?application_id=<?= urlencode($application_id) ?>" class="btn-primary">
                    <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>
        <?php elseif ($_POST): ?>
            <div class="result-error">
                <i class="fas fa-exclamation-circle"></i>
                No result found for this Application ID
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Aakasha Bindu Agritech</h3>
                <p>Leading the way in organic pesticide innovation.</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="exam_application.html">Exam Application</a></li>
                    <li><a href="download_hallticket.html">Download Hall Ticket</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Contact Info</h4>
                <p><i class="fas fa-phone"></i> +91 93903 44299</p>
                <p><i class="fas fa-envelope"></i> aakasabindhuagritech@gmail.com</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Aakasha Bindu Agritech. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
