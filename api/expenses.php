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
        'SELECT * FROM expenses WHERE user_id = ? ORDER BY exp_date DESC, created_at DESC'
    );
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $title    = clean($input['title']    ?? '');
    $amount   = (float)($input['amount'] ?? 0);
    $type     = clean($input['type']     ?? 'expense');
    $category = clean($input['category'] ?? 'Other');
    $expDate  = $input['exp_date']        ?: null;
    $notes    = clean($input['notes']    ?? '');

    if (empty($title) || $amount <= 0) {
        jsonResponse(false, 'Title and valid amount are required.');
    }

    $stmt = $db->prepare(
        'INSERT INTO expenses (user_id, title, amount, type, category, exp_date, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $amount, $type, $category, $expDate, $notes]);

    echo json_encode(['success' => true, 'message' => 'Saved!', 'id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    if (!$id) jsonResponse(false, 'Invalid ID.');

    $stmt = $db->prepare('DELETE FROM expenses WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Deleted!']);
    exit;
}
