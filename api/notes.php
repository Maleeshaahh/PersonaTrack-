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
    $stmt = $db->prepare('SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $input    = json_decode(file_get_contents('php://input'), true);
    $title    = clean($input['title']    ?? '');
    $body     = clean($input['body']     ?? '');
    $category = clean($input['category'] ?? 'Other');

    if (empty($title) || empty($body)) jsonResponse(false, 'Title and body are required.');

    $stmt = $db->prepare(
        'INSERT INTO notes (user_id, title, body, category) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $body, $category]);

    echo json_encode(['success' => true, 'message' => 'Note saved!', 'id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $input    = json_decode(file_get_contents('php://input'), true);
    $id       = (int)($input['id']       ?? 0);
    $title    = clean($input['title']    ?? '');
    $body     = clean($input['body']     ?? '');
    $category = clean($input['category'] ?? 'Other');

    if (!$id || empty($title) || empty($body)) jsonResponse(false, 'Invalid data.');

    $stmt = $db->prepare(
        'UPDATE notes SET title = ?, body = ?, category = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$title, $body, $category, $id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Note updated!']);
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    $stmt = $db->prepare('DELETE FROM notes WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Note deleted!']);
    exit;
}
