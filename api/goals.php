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
    $stmt = $db->prepare('SELECT * FROM goals WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $title      = clean($input['title']       ?? '');
    $desc       = clean($input['description'] ?? '');
    $category   = clean($input['category']    ?? 'Other');
    $targetDate = $input['target_date']        ?: null;
    $progress   = (int)($input['progress']    ?? 0);

    if (empty($title)) jsonResponse(false, 'Goal title is required.');

    $stmt = $db->prepare(
        'INSERT INTO goals (user_id, title, description, category, target_date, progress)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $desc, $category, $targetDate, $progress]);

    echo json_encode(['success' => true, 'message' => 'Goal added!', 'id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $input    = json_decode(file_get_contents('php://input'), true);
    $id       = (int)($input['id']       ?? 0);
    $progress = (int)($input['progress'] ?? 0);

    if (!$id) jsonResponse(false, 'Invalid ID.');

    $isAchieved = ($progress >= 100) ? 1 : 0;
    $stmt = $db->prepare(
        'UPDATE goals SET progress = ?, is_achieved = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$progress, $isAchieved, $id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Progress updated!']);
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    $stmt = $db->prepare('DELETE FROM goals WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Goal deleted!']);
    exit;
}
