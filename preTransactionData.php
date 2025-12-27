<?php
// =======================
// CORS HEADERS
// =======================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =======================
// CONFIG
// =======================
require_once 'config.php';

// =======================
// DB CONNECTION
// =======================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    error_log('DB Error: ' . $e->getMessage());
    exit;
}

// =======================
// HANDLE POST
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -----------------------
    // GET FORM DATA
    // -----------------------
    $full_name         = $_POST['full_name'] ?? '';
    $date_of_birth     = $_POST['date_of_birth'] ?? '';
    $gender            = $_POST['gender'] ?? '';
    $email             = $_POST['email'] ?? '';
    $phone             = $_POST['phone'] ?? '';
    $father_name       = $_POST['father_name'] ?? '';
    $aadhar            = $_POST['aadhar'] ?? '';
    $caste             = $_POST['caste'] ?? '';
    $address           = $_POST['address'] ?? '';

    $ssc_year          = $_POST['ssc_year'] ?? null;
    $ssc_percentage    = $_POST['ssc_percentage'] ?? null;
    $inter_year        = $_POST['inter_year'] ?? null;
    $inter_percentage  = $_POST['inter_percentage'] ?? null;
    $degree_year       = $_POST['degree_year'] ?? null;
    $degree_percentage = $_POST['degree_percentage'] ?? null;

    $position          = $_POST['position'] ?? '';
    $exam_center       = $_POST['exam_center'] ?? '';

    // NEW FIELDS (NOW ACCEPTED)
    $transaction_id    = $_POST['transaction_id'] ?? null;
    $photo_path        = $_POST['photo_path'] ?? null;
    $signature_path    = $_POST['signature_path'] ?? null;

    // -----------------------
    // BASIC VALIDATION
    // -----------------------
    if (empty($full_name) || empty($date_of_birth) || empty($phone) || empty($position)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Required fields missing'
        ]);
        exit;
    }

    // -----------------------
    // CALCULATE AGE
    // -----------------------
    try {
        $dob = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid date of birth'
        ]);
        exit;
    }

    // -----------------------
    // GENERATE APPLICATION ID
    // -----------------------
    $application_id = strtoupper($position) . '-PRE-' . date('YmdHis') . '-' . rand(1000, 9999);

    // -----------------------
    // INSERT INTO DATABASE
    // -----------------------
    try {
        $sql = "INSERT INTO pre_transaction_data (
            application_id,
            full_name,
            date_of_birth,
            age,
            gender,
            email,
            phone,
            father_name,
            aadhar,
            caste,
            address,
            ssc_year,
            ssc_percentage,
            inter_year,
            inter_percentage,
            degree_year,
            degree_percentage,
            position,
            exam_center,
            transaction_id,
            photo_path,
            signature_path,
            created_at
        ) VALUES (
            :application_id,
            :full_name,
            :date_of_birth,
            :age,
            :gender,
            :email,
            :phone,
            :father_name,
            :aadhar,
            :caste,
            :address,
            :ssc_year,
            :ssc_percentage,
            :inter_year,
            :inter_percentage,
            :degree_year,
            :degree_percentage,
            :position,
            :exam_center,
            :transaction_id,
            :photo_path,
            :signature_path,
            NOW()
        )";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':application_id', $application_id);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':father_name', $father_name);
        $stmt->bindParam(':aadhar', $aadhar);
        $stmt->bindParam(':caste', $caste);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':ssc_year', $ssc_year);
        $stmt->bindParam(':ssc_percentage', $ssc_percentage);
        $stmt->bindParam(':inter_year', $inter_year);
        $stmt->bindParam(':inter_percentage', $inter_percentage);
        $stmt->bindParam(':degree_year', $degree_year);
        $stmt->bindParam(':degree_percentage', $degree_percentage);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':exam_center', $exam_center);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':photo_path', $photo_path);
        $stmt->bindParam(':signature_path', $signature_path);

        $stmt->execute();

        echo json_encode([
            'success' => true,
            'application_id' => $application_id,
            'message' => 'Pre-transaction data saved successfully'
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save pre-transaction data'
        ]);
        error_log('Insert Error: ' . $e->getMessage());
    }

} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
