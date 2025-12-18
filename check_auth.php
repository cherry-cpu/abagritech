<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Require login - redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.html');
        exit;
    }
}

// Check user role
function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_role = $_SESSION['role'] ?? '';
    
    // Super admin has access to everything
    if ($user_role === 'super_admin') {
        return true;
    }
    
    return $user_role === $required_role;
}

// Require specific role
function requireRole($required_role) {
    requireLogin();
    
    if (!hasRole($required_role)) {
        header('Location: view_applications.php');
        exit;
    }
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}
?>

