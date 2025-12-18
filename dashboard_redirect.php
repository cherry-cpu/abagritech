<?php
/**
 * Dashboard Redirect
 * Redirects to dashboard.php with proper authentication check
 */

// Check if user is logged in
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// If logged in, redirect to dashboard
header('Location: dashboard.php');
exit;

