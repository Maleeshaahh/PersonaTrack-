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

$name       = post('name');
$email      = post('email');
$university = post('university');
$password   = post('password');

if (empty($name) || empty($email) || empty($university) || empty($password)) {
    jsonResponse(false, 'Please fill in all fields.');
}

if (!isValidEmail($email)) {
    jsonResponse(false, 'Please enter a valid email address.');
}

if (strlen($password) < 6) {
    jsonResponse(false, 'Password must be at least 6 characters.');
}

$db = getDB();
if (!$db) {
    jsonResponse(false, 'Database connection failed. Check db.php settings.');
}

$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(false, 'This email is already registered.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare(
    'INSERT INTO users (username, email, password, university) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$name, $email, $hashedPassword, $university]);
$userId = $db->lastInsertId();

$_SESSION['user_id']   = $userId;
$_SESSION['user_name'] = $name;
$_SESSION['user_email']= $email;

jsonResponse(true, 'Account created! Welcome, ' . $name . '!', [
    'name'  => $name,
    'email' => $email,
]);
