<?php
/**
 * Test Password Hash Utility
 * Use this to test if a password hash works with a given password
 * DELETE THIS FILE after use for security
 */

require_once 'config.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "=== Test Password Hash Utility ===\n\n";

// Get username
$username = readline("Enter username to test: ");

// Get user from database
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username OR email = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        die("User not found!\n");
    }
    
    echo "Found user: " . $user['username'] . " (" . $user['email'] . ")\n\n";
    
    $password_hash = trim($user['password_hash']);
    
    echo "=== Hash Information ===\n";
    echo "Hash length: " . strlen($password_hash) . "\n";
    echo "Hash format: " . substr($password_hash, 0, 7) . "\n";
    echo "Full hash: " . $password_hash . "\n\n";
    
    // Test with known default passwords
    echo "=== Testing with Default Passwords ===\n";
    $test_passwords = [
        'admin123' => 'Default admin password',
        'test123' => 'Default test password',
    ];
    
    $found_match = false;
    foreach ($test_passwords as $test_pwd => $description) {
        $result = password_verify($test_pwd, $password_hash);
        echo ($result ? "✓" : "✗") . " '$test_pwd' ($description): " . ($result ? "MATCH" : "NO MATCH") . "\n";
        if ($result) {
            $found_match = true;
            echo "  → This hash belongs to password: '$test_pwd'\n";
        }
    }
    
    if (!$found_match) {
        echo "\n⚠ Hash does not match any default passwords.\n";
    }
    
    // Test with user input
    echo "\n=== Test Your Password ===\n";
    $test_password = readline("Enter password to test: ");
    
    if (!empty($test_password)) {
        $result = password_verify($test_password, $password_hash);
        echo "\nResult: " . ($result ? "✓ MATCH - This password is correct!" : "✗ NO MATCH - This password is incorrect") . "\n";
        
        if (!$result) {
            echo "\nThe password you entered does not match the hash in the database.\n";
            echo "Possible reasons:\n";
            echo "1. The password was changed but hash wasn't updated\n";
            echo "2. You're entering the wrong password\n";
            echo "3. There's a character encoding issue\n";
            echo "\nSolution: Use fix_password_hash.php to reset the password.\n";
        }
    }
    
    // Show how to create a new hash
    echo "\n=== Generate New Hash ===\n";
    $new_password = readline("Enter new password to generate hash (or press Enter to skip): ");
    
    if (!empty($new_password)) {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        echo "\nNew hash: " . $new_hash . "\n";
        echo "Hash length: " . strlen($new_hash) . "\n";
        
        // Verify it works
        if (password_verify($new_password, $new_hash)) {
            echo "✓ Verification test: PASSED\n";
            echo "\nYou can update the database with:\n";
            echo "UPDATE admin_users SET password_hash = '$new_hash' WHERE id = " . $user['id'] . ";\n";
        } else {
            echo "✗ Verification test: FAILED - Something is wrong!\n";
        }
    }
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
