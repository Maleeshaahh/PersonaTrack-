<?php
// ============================================================
// api/session.php
// Session status check - Frontend login redirect සඳහා
// ============================================================

require_once '../includes/functions.php';

startSession();

header('Content-Type: application/json');

// Session ඇත්නම් user info return, නැත්නම් not logged in
if (!empty($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user' => [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
