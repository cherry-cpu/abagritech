
<?php
session_start();

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include configuration file
require_once 'config.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please contact administrator.'
    ]);
    error_log('Database connection error: ' . $e->getMessage());
    exit;
}

// Handle login request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't trim password - spaces might be intentional
    $remember = isset($_POST['remember']) && $_POST['remember'] == '1';
    
    // Remove any null bytes or dangerous characters from username
    $username = str_replace(["\0", "\r", "\n"], '', $username);
    
    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter both username and password.'
        ]);
        exit;
    }
    
    try {
        // Check if user exists (by username or email) - case insensitive
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE LOWER(username) = LOWER(:username) OR LOWER(email) = LOWER(:username)");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // User not found
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password.'
            ]);
            error_log('Login attempt failed: User not found - ' . $username);
            exit;
        }
        
        // Verify password
        if (empty($user['password_hash'])) {
            // Password hash is missing
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Account configuration error. Please contact administrator.'
            ]);
            error_log('Login error: Password hash missing for user - ' . $username);
            exit;
        }
        
        // Clean the password hash - remove any whitespace or encoding issues
        $password_hash = trim($user['password_hash']);
        
        // Validate hash format (should start with $2y$, $2a$, or $2b$ for bcrypt)
        if (!preg_match('/^\$2[ayb]\$/', $password_hash)) {
            error_log('Login error: Invalid password hash format for user - ' . $username);
            error_log('Hash format: ' . substr($password_hash, 0, 10));
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Account configuration error. Please contact administrator.'
            ]);
            exit;
        }
        
        // Debug logging (remove in production)
        error_log("Login attempt - Username: " . $username);
        error_log("Password hash length: " . strlen($password_hash));
        error_log("Password hash format: " . substr($password_hash, 0, 7));
        error_log("Password hash (first 30 chars): " . substr($password_hash, 0, 30));
        error_log("Password length: " . strlen($password));
        error_log("Password (first char): " . (strlen($password) > 0 ? substr($password, 0, 1) . '...' : 'EMPTY'));
        
        // Test the hash with known passwords from database_setup.sql
        // This helps identify if the hash is correct but password is wrong
        $test_passwords = ['admin123', 'test123'];
        $hash_matches_known = false;
        foreach ($test_passwords as $test_pwd) {
            if (password_verify($test_pwd, $password_hash)) {
                error_log("Hash matches known password: " . $test_pwd);
                $hash_matches_known = true;
                break;
            }
        }
        if (!$hash_matches_known) {
            error_log("Hash does NOT match any known default passwords");
        }
        
        // Verify password using password_verify
        $password_valid = password_verify($password, $password_hash);

        /* ===========================
           CHANGE MADE HERE
           REMOVED: $password_valid = true;
           =========================== */

        // Additional check: if password_verify fails, try with trimmed password (in case of copy-paste issues)
        if (!$password_valid) {
            $password_trimmed = trim($password);
            if ($password_trimmed !== $password) {
                error_log("Trying with trimmed password (original had whitespace)");
                $password_valid = password_verify($password_trimmed, $password_hash);
                if ($password_valid) {
                    error_log('Login succeeded with trimmed password for user - ' . $username);
                }
            }
        }
        
        // If still failing, try common variations
        if (!$password_valid) {
            // Try with different encodings or common issues
            $variations = [
                $password,
                trim($password),
                rtrim($password),
                ltrim($password),
                mb_convert_encoding($password, 'UTF-8', 'UTF-8'), // Normalize encoding
            ];
            
            foreach ($variations as $index => $variation) {
                if ($variation !== $password && password_verify($variation, $password_hash)) {
                    error_log("Password verified with variation #" . $index);
                    $password_valid = true;
                    break;
                }
            }
        }
        
        error_log("Password verification result: " . ($password_valid ? 'SUCCESS' : 'FAILED'));
        
        // If verification failed, log more details and provide helpful message
        if (!$password_valid) {
            error_log("=== PASSWORD VERIFICATION FAILED ===");
            error_log("Entered password: '" . addslashes($password) . "'");
            error_log("Password bytes: " . bin2hex(substr($password, 0, min(10, strlen($password)))));
            error_log("Hash stored in DB: " . substr($password_hash, 0, 30) . "...");
            error_log("Hash bytes: " . bin2hex(substr($password_hash, 0, 30)));
            
            // Check if hash matches default passwords (for debugging)
            $default_passwords = ['admin123', 'test123'];
            $matched_default = null;
            foreach ($default_passwords as $default_pwd) {
                if (password_verify($default_pwd, $password_hash)) {
                    $matched_default = $default_pwd;
                    error_log("Hash matches default password: " . $default_pwd);
                    break;
                }
            }
            
            if ($matched_default) {
                error_log("INFO: The hash in database matches password: '" . $matched_default . "'");
                error_log("INFO: You are entering a different password. Try: '" . $matched_default . "'");
            } else {
                error_log("WARNING: Hash does not match any known default passwords");
                error_log("RECOMMENDATION: Use fix_password_hash.php to reset the password");
            }
        }
        
        if ($password_valid) {
            // Login successful
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'] ?? '';
            $_SESSION['role'] = $user['role'] ?? 'admin';
            $_SESSION['logged_in'] = true;
            
            // Update last login
            try {
                $update_stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
                $update_stmt->execute(['id' => $user['id']]);
            } catch(PDOException $e) {
                error_log('Failed to update last_login: ' . $e->getMessage());
            }
            
            // Set remember me cookie if requested
            if ($remember) {
                $cookie_value = base64_encode($user['id'] . ':' . hash('sha256', $user['password_hash']));
                setcookie('remember_token', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => 'dashboard.php',
                'user' => [
                    'username' => $user['username'],
                    'full_name' => $user['full_name'] ?? '',
                    'role' => $user['role'] ?? 'admin'
                ]
            ]);
            
        } else {
            // Invalid password
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password.'
            ]);
            error_log('Login attempt failed: Invalid password for user - ' . $username);
        }
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again later.'
        ]);
        error_log('Login error: ' . $e->getMessage());
        error_log('Login error trace: ' . $e->getTraceAsString());
    }
    
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
