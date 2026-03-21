<?php

require_once '../includes/db.php';
require_once '../includes/functions.php';

startSession();
requireLogin(); // Login නැත්නම් 401 return

header('Content-Type: application/json');

$db     = getDB();
$userId = currentUserId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->prepare(
        'SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    $todos = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $todos]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $title    = clean($input['title']    ?? '');
    $category = clean($input['category'] ?? 'Other');
    $priority = clean($input['priority'] ?? 'Low');
    $dueDate  = $input['due_date'] ?: null;
    $dueTime  = $input['due_time'] ?: null;
    $notes    = clean($input['notes']    ?? '');

    if (empty($title)) {
        jsonResponse(false, 'Task title is required.');
    }

    $stmt = $db->prepare(
        'INSERT INTO todos (user_id, title, category, priority, due_date, due_time, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $category, $priority, $dueDate, $dueTime, $notes]);

    echo json_encode(['success' => true, 'message' => 'Task added!', 'id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    if (!$id) jsonResponse(false, 'Invalid task ID.');

    if (isset($input['is_done'])) {
        $stmt = $db->prepare(
            'UPDATE todos SET is_done = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([(int)$input['is_done'], $id, $userId]);
        echo json_encode(['success' => true, 'message' => 'Task updated!']);
        exit;
    }

    jsonResponse(false, 'Nothing to update.');
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);

    if (!$id) jsonResponse(false, 'Invalid task ID.');

    $stmt = $db->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);

    echo json_encode(['success' => true, 'message' => 'Task deleted!']);
    exit;
}
