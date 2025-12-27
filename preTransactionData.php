<?php
// =======================
// CORS HEADERS
// =======================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight request
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
    error_log('DB Connection Error: ' . $e->getMessage());
    exit;
}

// =======================
// HANDLE POST REQUEST
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -----------------------
    // GET FORM DATA
    // -----------------------
    $full_name   = $_POST['full_name'] ?? '';
    $email       = $_POST['email'] ?? '';
    $phone       = $_POST['phone'] ?? '';
    $position    = $_POST['position'] ?? '';
    $exam_center = $_POST['exam_center'] ?? '';
    $amount      = $_POST['amount'] ?? 0;

    // -----------------------
    // BASIC VALIDATION
    // -----------------------
    if (empty($full_name) || empty($phone) || empty($position)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Required fields are missing'
        ]);
        exit;
    }

    // -----------------------
    // GENERATE PRE-TRANSACTION ID
    // -----------------------
    $pre_transaction_id = 'PRE-' . date('YmdHis') . '-' . rand(1000, 9999);

    // -----------------------
    // INSERT INTO DATABASE
    // -----------------------
    try {
        $sql = "INSERT INTO pre_transaction_data (
                    pre_transaction_id,
                    full_name,
                    email,
                    phone,
                    position,
                    exam_center,
                    amount,
                    status,
                    created_at
                ) VALUES (
                    :pre_transaction_id,
                    :full_name,
                    :email,
                    :phone,
                    :position,
                    :exam_center,
                    :amount,
                    'INITIATED',
                    NOW()
                )";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':pre_transaction_id', $pre_transaction_id);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':exam_center', $exam_center);
        $stmt->bindParam(':amount', $amount);

        $stmt->execute();

        // -----------------------
        // SUCCESS RESPONSE
        // -----------------------
        echo json_encode([
            'success' => true,
            'pre_transaction_id' => $pre_transaction_id,
            'message' => 'Pre-transaction data saved successfully'
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save pre-transaction data'
        ]);
        error_log('PreTransaction DB Error: ' . $e->getMessage());
    }

} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
