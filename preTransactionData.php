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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    error_log($e->getMessage());
    exit;
}

// =======================
// HANDLE POST
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -----------------------
    // FORM DATA
    // -----------------------
    $full_name     = $_POST['full_name'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender        = $_POST['gender'] ?? '';
    $email         = $_POST['email'] ?? '';
    $phone         = $_POST['phone'] ?? '';
    $father_name   = $_POST['father_name'] ?? '';
    $aadhar        = $_POST['aadhar'] ?? '';
    $caste         = $_POST['caste'] ?? '';
    $address       = $_POST['address'] ?? '';

    $ssc_year          = $_POST['ssc_year'] ?? null;
    $ssc_percentage    = $_POST['ssc_percentage'] ?? null;
    $inter_year        = $_POST['inter_year'] ?? null;
    $inter_percentage  = $_POST['inter_percentage'] ?? null;
    $degree_year       = $_POST['degree_year'] ?? null;
    $degree_percentage = $_POST['degree_percentage'] ?? null;

    $position       = $_POST['position'] ?? '';
    $exam_center    = $_POST['exam_center'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? null;

    // -----------------------
    // BASIC VALIDATION
    // -----------------------
    if (empty($full_name) || empty($date_of_birth) || empty($phone) || empty($position)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
        exit;
    }

    // -----------------------
    // AGE CALCULATION
    // -----------------------
    try {
        $dob = new DateTime($date_of_birth);
        $age = (new DateTime())->diff($dob)->y;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid DOB']);
        exit;
    }

    // -----------------------
    // UPLOAD DIR SETUP
    // -----------------------
    $baseDir = __DIR__ . '/' . trim(UPLOAD_DIR, '/');
    $photoDir = $baseDir . '/photos/';
    $signDir  = $baseDir . '/signature/';

    if (!is_dir($photoDir)) mkdir($photoDir, 0777, true);
    if (!is_dir($signDir)) mkdir($signDir, 0777, true);

    $photo_path = null;
    $signature_path = null;

    // -----------------------
    // PHOTO UPLOAD
    // -----------------------
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ALLOWED_IMAGE_TYPES) && $_FILES['photo']['size'] <= MAX_FILE_SIZE) {
            $photoName = time() . '_photo.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoDir . $photoName)) {
                $photo_path = UPLOAD_DIR . 'photos/' . $photoName;
            }
        }
    }

    // -----------------------
    // SIGNATURE UPLOAD
    // -----------------------
    if (!empty($_FILES['signature']['name']) && $_FILES['signature']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['signature']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ALLOWED_IMAGE_TYPES) && $_FILES['signature']['size'] <= MAX_FILE_SIZE) {
            $signName = time() . '_signature.' . $ext;
            if (move_uploaded_file($_FILES['signature']['tmp_name'], $signDir . $signName)) {
                $signature_path = UPLOAD_DIR . 'signature/' . $signName;
            }
        }
    }

    // -----------------------
    // APPLICATION ID
    // -----------------------
    $application_id = strtoupper($position) . '-PRE-' . date('YmdHis') . '-' . rand(1000, 9999);

    // -----------------------
    // INSERT DB
    // -----------------------
    try {
        $sql = "INSERT INTO pre_transaction_data (
            application_id, full_name, date_of_birth, age, gender, email, phone,
            father_name, aadhar, caste, address,
            ssc_year, ssc_percentage, inter_year, inter_percentage,
            degree_year, degree_percentage,
            position, exam_center, transaction_id,
            photo_path, signature_path, created_at
        ) VALUES (
            :application_id, :full_name, :date_of_birth, :age, :gender, :email, :phone,
            :father_name, :aadhar, :caste, :address,
            :ssc_year, :ssc_percentage, :inter_year, :inter_percentage,
            :degree_year, :degree_percentage,
            :position, :exam_center, :transaction_id,
            :photo_path, :signature_path, NOW()
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(compact(
            'application_id','full_name','date_of_birth','age','gender','email','phone',
            'father_name','aadhar','caste','address',
            'ssc_year','ssc_percentage','inter_year','inter_percentage',
            'degree_year','degree_percentage',
            'position','exam_center','transaction_id',
            'photo_path','signature_path'
        ));

        echo json_encode([
            'success' => true,
            'application_id' => $application_id,
            'message' => 'Pre-transaction data saved successfully'
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'DB insert failed']);
        error_log($e->getMessage());
    }

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
