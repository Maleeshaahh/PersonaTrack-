<?php
// ============================================================
// auth/login.php
// User Login - Frontend index.html login form handle කරයි
// ============================================================

require_once '../includes/db.php';
require_once '../includes/functions.php';

startSession();

// Already logged in නම් dashboard redirect
if (!empty($_SESSION['user_id'])) {
    redirect('../dashboard.php');
}

// POST request පමණක් handle කරයි
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}

// ---- Form data get කරයි ----
$email    = post('email');
$password = post('password');

// ---- Validation ----
if (empty($email) || empty($password)) {
    jsonResponse(false, 'Please fill in all fields.');
}

if (!isValidEmail($email)) {
    jsonResponse(false, 'Please enter a valid email address.');
}

// ---- Database connection ----
$db = getDB();
if (!$db) {
    jsonResponse(false, 'Database connection failed. Check db.php settings.');
}

// ---- User email by lookup ----
$stmt = $db->prepare('SELECT id, username, email, password FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

// ---- Password verify කරයි ----
if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(false, 'Invalid email or password.');
}

// ---- Session set කරයි ----
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['username'];
$_SESSION['user_email'] = $user['email'];

// ---- Success response ----
jsonResponse(true, 'Welcome back, ' . $user['username'] . '!', [
    'name'  => $user['username'],
    'email' => $user['email'],
]);
