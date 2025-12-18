<?php
session_start();

/*
|--------------------------------------------------------------------------
| Error Logging
|--------------------------------------------------------------------------
*/
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.log');

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Only allow POST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Require Config & DB Connection
|--------------------------------------------------------------------------
*/
require_once 'config.php';

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    error_log("DB CONNECTION FAILED: ".$e->getMessage());
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Read Input
|--------------------------------------------------------------------------
*/
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'admin';

/*
|--------------------------------------------------------------------------
| Input Validation
|--------------------------------------------------------------------------
*/
if ($username === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Password must be at least 6 characters']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Check if username or email exists
|--------------------------------------------------------------------------
*/
try {
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username=:u OR email=:e LIMIT 1");
    $stmt->execute(['u'=>$username,'e'=>$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success'=>false,'message'=>'Username or email already exists']);
        exit;
    }
} catch (PDOException $e) {
    error_log("CHECK EXISTING USER FAILED: ".$e->getMessage());
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Hash Password & Insert
|--------------------------------------------------------------------------
*/
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO admin_users (username, email, password_hash, role, created_at)
        VALUES (:u, :e, :p, :r, NOW())
    ");
    $stmt->execute([
        'u'=>$username,
        'e'=>$email,
        'p'=>$hash,
        'r'=>$role
    ]);

    echo json_encode(['success'=>true,'message'=>'User created successfully']);
} catch (PDOException $e) {
    error_log("INSERT USER FAILED: ".$e->getMessage());
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
