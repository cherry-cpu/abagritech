<?php
/**
 * Fix Password Hash Utility
 * Use this to regenerate password hashes for existing users
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

echo "=== Fix Password Hash Utility ===\n\n";

// Get username
$username = readline("Enter username to fix: ");

// Get user from database
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username OR email = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        die("User not found!\n");
    }
    
    echo "Found user: " . $user['username'] . " (" . $user['email'] . ")\n";
    echo "Current hash: " . substr($user['password_hash'], 0, 20) . "...\n";
    echo "Hash length: " . strlen($user['password_hash']) . "\n";
    
    // Get new password
    $new_password = readline("Enter new password: ");
    $confirm_password = readline("Confirm new password: ");
    
    if ($new_password !== $confirm_password) {
        die("Passwords do not match!\n");
    }
    
    if (strlen($new_password) < 6) {
        die("Password must be at least 6 characters!\n");
    }
    
    // Generate new hash
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    echo "\nNew hash: " . substr($new_hash, 0, 20) . "...\n";
    echo "New hash length: " . strlen($new_hash) . "\n";
    
    // Verify the new hash works
    if (password_verify($new_password, $new_hash)) {
        echo "✓ Hash verification test: PASSED\n";
    } else {
        die("✗ Hash verification test: FAILED - Something is wrong!\n");
    }
    
    // Update database
    $confirm = readline("\nUpdate password hash in database? (yes/no): ");
    if (strtolower($confirm) === 'yes') {
        $update_stmt = $pdo->prepare("UPDATE admin_users SET password_hash = :password_hash WHERE id = :id");
        $update_stmt->execute([
            'password_hash' => $new_hash,
            'id' => $user['id']
        ]);
        
        echo "\n✓ Password hash updated successfully!\n";
        echo "You can now login with:\n";
        echo "  Username: " . $user['username'] . "\n";
        echo "  Password: " . $new_password . "\n";
    } else {
        echo "\nUpdate cancelled.\n";
    }
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
