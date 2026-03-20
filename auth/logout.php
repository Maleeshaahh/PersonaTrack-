<?php
// ============================================================
// auth/logout.php
// User Logout - Session destroy කරයි
// ============================================================

require_once '../includes/functions.php';

startSession();

// ---- Session ඔක්කොම clear කරයි ----
$_SESSION = [];

// ---- Session cookie delete කරයි ----
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// ---- Session destroy කරයි ----
session_destroy();

// ---- Login page redirect ----
redirect('../index.php');
