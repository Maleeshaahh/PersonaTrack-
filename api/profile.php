<?php

require_once '../includes/db.php';
require_once '../includes/functions.php';

startSession();
requireLogin();

header('Content-Type: application/json');

$db     = getDB();
$userId = currentUserId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->prepare(
        'SELECT u.username, u.email, u.university,
                p.faculty, p.academic_yr, p.phone, p.dob
         FROM users u
         LEFT JOIN profiles p ON p.user_id = u.id
         WHERE u.id = ?'
    );
    $stmt->execute([$userId]);
    $data = $stmt->fetch() ?: [];
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($method === 'PUT') {
    $input      = json_decode(file_get_contents('php://input'), true);
    $name       = clean($input['name']        ?? '');
    $university = clean($input['university']  ?? '');
    $faculty    = clean($input['faculty']     ?? '');
    $year       = clean($input['academic_yr'] ?? '');
    $phone      = clean($input['phone']       ?? '');
    $dob        = $input['dob']                ?: null;

    if ($name) {
        $stmt = $db->prepare('UPDATE users SET username = ?, university = ? WHERE id = ?');
        $stmt->execute([$name, $university, $userId]);
    }

    $stmt = $db->prepare(
        'INSERT INTO profiles (user_id, faculty, academic_yr, phone, dob)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
           faculty = VALUES(faculty),
           academic_yr = VALUES(academic_yr),
           phone = VALUES(phone),
           dob = VALUES(dob)'
    );
    $stmt->execute([$userId, $faculty, $year, $phone, $dob]);

    if ($name) $_SESSION['user_name'] = $name;

    echo json_encode(['success' => true, 'message' => 'Profile updated!']);
    exit;
}
