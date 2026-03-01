<?php
require_once 'config.php';
$data = null;

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit('Database connection failed');
}

if (isset($_POST['application_id'])) {
    $application_id=$_POST['application_id'];
    $string=$application_id;
    $string=str_replace("-", "", $string);
    $application_id = substr($string, 0, 4) . '-' .substr($string, 4, 8) . '-' .substr($string, 12);
    /*
        $st = $pdo->prepare("SELECT * FROM exam_marks WHERE application_id = ?");
        $st->execute([$application_id]);
        $data = $st->fetch(PDO::FETCH_ASSOC);*/
        $sql="SELECT em.application_id, ea.full_name, em.subject1, em.result FROM exam_marks em, exam_applications ea WHERE ea.application_id = :application_id and em.application_id = :application_id";
        $st = $pdo->prepare($sql);
        $st->bindParam(':application_id', $application_id);
        $st->execute();
        $data = $st->fetch();
}else{
        $data = null;
        error_log(' except data '.$data);

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
                <li><a href="results.php">Results</a></li>
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
<section class="result-section" style="opacity: 1;">
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
                        <h4>Hall Ticket Number</h4>
                        <span><?= htmlspecialchars($application_id) ?></span>
                    </div>
                    <div>
                        <h4>Full Name</h4>
                        <span><?= $data['full_name'] ?></span>
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
<!-- results -->

<!-- Telangana & Andhra Pradesh Tables -->
<style>
    .containerTable {
        max-width: 1200px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #2d3748;
        color: white;
    }

    thead th {
        padding: 14px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
    }


    tbody tr:hover {
        background-color: #f7fafc;
    }

    td {
        padding: 12px;
        text-align: center;
        color: #444;
    }

    .district-row td {
        background: #edf2f7;
        font-weight: bold;
        text-align: left;
        font-size: 16px;
    }

    .total-cell {
        font-weight: bold;
        color: #2b6cb0;
    }

    @media (max-width: 768px) {
        body { padding: 10px; }
        .containerTable { padding: 15px; }
        td, th { font-size: 12px; padding: 8px; }
    }
</style>

<div class="containerTable">
    <h2>Telangana District-wise Data</h2>

    <table id="resultsTable">
        <thead>
            <tr>
                <th>District</th>
                <th>Caste</th>
                <th>DAFO</th>
                <th>MAFO</th>
                <th>RAFO</th>
                <th>ZAFO</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>

    <!-- Adilabad -->
    <tr class="district-row"><td colspan="7">Adilabad</td></tr>
    <tr><td></td><td>Total</td><td>3</td><td>3</td><td>1</td><td>1</td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td>3</td><td>1</td><td>1</td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td></td><td></td><td>1</td><td class="total-cell"></td></tr>
    <tr><td></td><td>ST</td><td></td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Hyderabad -->
    <tr class="district-row"><td colspan="7">Hyderabad</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>11</td><td>2</td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>8</td><td>2</td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>ST</td><td></td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Karimnagar -->
    <tr class="district-row"><td colspan="7">Karimnagar</td></tr>
    <tr><td></td><td>Total</td><td></td><td></td><td>2</td><td>1</td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td></td><td></td><td>2</td><td>1</td><td class="total-cell"></td></tr>

    <!-- Khammam -->
    <tr class="district-row"><td colspan="7">Khammam</td></tr>
    <tr><td></td><td>Total</td><td></td><td>4</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td></td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>ST</td><td></td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Mahabubnagar -->
    <tr class="district-row"><td colspan="7">Mahabubnagar</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>3</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Medak -->
    <tr class="district-row"><td colspan="7">Medak</td></tr>
    <tr><td></td><td>Total</td><td></td><td>2</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Nalgonda -->
    <tr class="district-row"><td colspan="7">Nalgonda</td></tr>
    <tr><td></td><td>Total</td><td></td><td>4</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td></td><td>3</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Ranga Reddy -->
    <tr class="district-row"><td colspan="7">Ranga Reddy</td></tr>
    <tr><td></td><td>OC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>

    <!-- Warangal -->
    <tr class="district-row"><td colspan="7">Warangal</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>4</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>BC</td><td></td><td>3</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>SC</td><td></td><td>1</td><td></td><td></td><td class="total-cell"></td></tr>
    <tr><td></td><td>ST</td><td>1</td><td></td><td></td><td></td><td class="total-cell"></td></tr>

        </tbody>
        </table>
        <br>
        <br>
            <h2>Andhra Pradesh District-wise Data</h2>

    <table id="resultsTable">
        <thead>
            <tr>
                <th>District</th>
                <th>Caste</th>
                <th>DAFO</th>
                <th>MAFO</th>
                <th>RAFO</th>
                <th>ZAFO</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
    <!-- Alluri Sitharama Raju -->
    <tr class="district-row"><td colspan="7">Alluri Sitharama Raju</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- Anakapalli -->
    <tr class="district-row"><td colspan="7">Anakapalli</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>2</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>2</td><td>0</td><td>0</td><td></td></tr>

    <!-- Anantapur -->
    <tr class="district-row"><td colspan="7">Anantapur</td></tr>
    <tr><td></td><td>Total</td><td>9</td><td>11</td><td>10</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>7</td><td>9</td><td>4</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>1</td><td>4</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>2</td><td>1</td><td>2</td><td>0</td><td></td></tr>

    <!-- Ananthapuramu -->
    <tr class="district-row"><td colspan="7">Ananthapuramu</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>6</td><td>1</td><td>1</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>4</td><td>1</td><td>1</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- Bapatla -->
    <tr class="district-row"><td colspan="7">Bapatla</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>1</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- Chittoor -->
    <tr class="district-row"><td colspan="7">Chittoor</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>1</td><td>3</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>1</td><td>2</td><td>0</td><td></td></tr>

    <!-- Dr. B. R. Ambedkar Konaseema -->
    <tr class="district-row"><td colspan="7">Dr. B. R. Ambedkar Konaseema</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>2</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>1</td><td>2</td><td>1</td><td>0</td><td></td></tr>

    <!-- Eluru -->
    <tr class="district-row"><td colspan="7">Eluru</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>1</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>

    <!-- Guntur -->
    <tr class="district-row"><td colspan="7">Guntur</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>1</td><td>2</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>2</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>1</td><td>2</td><td>0</td><td></td></tr>

    <!-- Kakinada -->
    <tr class="district-row"><td colspan="7">Kakinada</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- Krishna -->
    <tr class="district-row"><td colspan="7">Krishna</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>2</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>2</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>

    <!-- Kurnool -->
    <tr class="district-row"><td colspan="7">Kurnool</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>14</td><td>11</td><td>5</td><td></td></tr>
    <tr><td></td><td>BC</td><td>2</td><td>10</td><td>7</td><td>3</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>1</td><td>3</td><td>1</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>3</td><td>1</td><td>1</td><td></td></tr>

    <!-- Madanapalle -->
    <tr class="district-row"><td colspan="7">Madanapalle</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>

    <!-- Markapuram -->
    <tr class="district-row"><td colspan="7">Markapuram</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>

    <!-- Nandyal -->
    <tr class="district-row"><td colspan="7">Nandyal</td></tr>
    <tr><td></td><td>Total</td><td>3</td><td>3</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>2</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>1</td><td>2</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- NTR -->
    <tr class="district-row"><td colspan="7">NTR</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>0</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>2</td><td>0</td><td>0</td><td>0</td><td></td></tr>

    <!-- Prakasam -->
    <tr class="district-row"><td colspan="7">Prakasam</td></tr>
    <tr><td></td><td>Total</td><td>2</td><td>2</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>1</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- Sri Potti Sriramulu Nellore -->
    <tr class="district-row"><td colspan="7">Sri Potti Sriramulu Nellore</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>3</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>0</td><td>2</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>

    <!-- Sri Sathya Sai -->
    <tr class="district-row"><td colspan="7">Sri Sathya Sai</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>2</td><td>2</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>2</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>

    <!-- Tirupati -->
    <tr class="district-row"><td colspan="7">Tirupati</td></tr>
    <tr><td></td><td>Total</td><td>1</td><td>0</td><td>6</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>0</td><td>2</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>1</td><td>0</td><td>3</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>

    <!-- Vijayawada -->
    <tr class="district-row"><td colspan="7">Vijayawada</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>

    <!-- Visakhapatnam -->
    <tr class="district-row"><td colspan="7">Visakhapatnam</td></tr>
    <tr><td></td><td>Total</td><td>0</td><td>1</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>0</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>0</td><td>1</td><td>0</td><td>0</td><td></td></tr>

    <!-- YSR Kadapa -->
    <tr class="district-row"><td colspan="7">YSR Kadapa</td></tr>
    <tr><td></td><td>Total</td><td>3</td><td>7</td><td>7</td><td>0</td><td></td></tr>
    <tr><td></td><td>BC</td><td>2</td><td>3</td><td>2</td><td>0</td><td></td></tr>
    <tr><td></td><td>OC</td><td>0</td><td>3</td><td>1</td><td>0</td><td></td></tr>
    <tr><td></td><td>SC</td><td>0</td><td>1</td><td>4</td><td>0</td><td></td></tr>
    <tr><td></td><td>ST</td><td>1</td><td>0</td><td>0</td><td>0</td><td></td></tr>

            </tbody>
        </table>
</div>

<script>
    document.querySelectorAll("#apTable tbody tr").forEach(row => {
        if (!row.classList.contains("district-row")) {
            let cells = row.querySelectorAll("td");
            if (cells.length === 7) {
                let total = 0;
                for (let i = 2; i <= 5; i++) {
                    total += parseInt(cells[i].innerText) || 0;
                }
                cells[6].innerText = total;
            }
        }
    });


    // Auto-calculate totals
    function calculateTotals(tableId) {
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            if (!row.classList.contains("district-row")) {
                let cells = row.querySelectorAll("td");
                let total = 0;
                for (let i = 2; i <= 5; i++) {
                    total += parseInt(cells[i].innerText) || 0;
                }
                cells[6].innerText = total;
            }
        });
    }
    calculateTotals("resultsTable");
    calculateTotals("apTable");
</script>

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
