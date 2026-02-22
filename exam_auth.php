<?php
session_start();
header('Content-Type: application/json');

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
if ($username === 'marks_admin' && $password === 'ramamarks') {
    $_SESSION['examiner_logged_in'] = true;
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
}
