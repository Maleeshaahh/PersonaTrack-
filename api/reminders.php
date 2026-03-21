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
        'SELECT * FROM reminders WHERE user_id = ? ORDER BY rem_date ASC, rem_time ASC'
    );
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $input    = json_decode(file_get_contents('php://input'), true);
    $title    = clean($input['title']    ?? '');
    $type     = clean($input['type']     ?? 'Other');
    $priority = clean($input['priority'] ?? 'Normal');
    $remDate  = $input['rem_date']        ?: null;
    $remTime  = $input['rem_time']        ?: null;
    $notes    = clean($input['notes']    ?? '');

    if (empty($title) || empty($remDate)) jsonResponse(false, 'Title and date are required.');

    $stmt = $db->prepare(
        'INSERT INTO reminders (user_id, title, rem_type, priority, rem_date, rem_time, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $type, $priority, $remDate, $remTime, $notes]);

    echo json_encode(['success' => true, 'message' => 'Reminder set!', 'id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $input  = json_decode(file_get_contents('php://input'), true);
    $id     = (int)($input['id']      ?? 0);
    $isDone = (int)($input['is_done'] ?? 0);

    if (!$id) jsonResponse(false, 'Invalid ID.');

    $stmt = $db->prepare(
        'UPDATE reminders SET is_done = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$isDone, $id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Updated!']);
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    $stmt = $db->prepare('DELETE FROM reminders WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Deleted!']);
    exit;
}
