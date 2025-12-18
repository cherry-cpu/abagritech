<?php
/**
 * Create Admin User Script
 * Run this file once to create admin users
 * DELETE THIS FILE after creating users for security
 */

require_once 'config.php';

// Create database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user input
echo "=== Create Admin User ===\n";
$username = readline("Enter username: ");
$email = readline("Enter email: ");
$full_name = readline("Enter full name: ");
$password = readline("Enter password: ");
$role = readline("Enter role (admin/super_admin) [admin]: ") ?: 'admin';

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
try {
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (:username, :email, :password_hash, :full_name, :role)");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password_hash' => $password_hash,
        'full_name' => $full_name,
        'role' => $role
    ]);
    
    echo "\n✓ Admin user created successfully!\n";
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "Role: $role\n";
    
} catch(PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "\n✗ Error: Username or email already exists!\n";
    } else {
        echo "\n✗ Error: " . $e->getMessage() . "\n";
    }
}
?>

