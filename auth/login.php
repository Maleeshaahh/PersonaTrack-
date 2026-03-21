<?php

require_once '../includes/db.php';
require_once '../includes/functions.php';

startSession();

if (!empty($_SESSION['user_id'])) {
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}

$email    = post('email');
$password = post('password');

if (empty($email) || empty($password)) {
    jsonResponse(false, 'Please fill in all fields.');
}

if (!isValidEmail($email)) {
    jsonResponse(false, 'Please enter a valid email address.');
}

$db = getDB();
if (!$db) {
    jsonResponse(false, 'Database connection failed. Check db.php settings.');
}

$stmt = $db->prepare('SELECT id, username, email, password FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(false, 'Invalid email or password.');
}

$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['username'];
$_SESSION['user_email'] = $user['email'];

jsonResponse(true, 'Welcome back, ' . $user['username'] . '!', [
    'name'  => $user['username'],
    'email' => $user['email'],
]);
