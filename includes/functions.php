<?php
// ============================================================
// includes/functions.php
// Helper functions - validation, session, sanitize etc.
// ============================================================

/**
 * Session start කරයි (already started නැත්නම් පමණක්)
 */
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * User logged in ද බලයි. නැත්නම් login page redirect කරයි.
 */
function requireLogin(): void {
    startSession();
    if (empty($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Currently logged in user id return කරයි
 */
function currentUserId(): int {
    startSession();
    return (int)($_SESSION['user_id'] ?? 0);
}

/**
 * String sanitize කරයි (XSS prevent)
 */
function clean(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Email valid ද validate කරයි
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * JSON response return කරයි (AJAX calls සඳහා)
 * @param bool   $success  Success ද fail ද
 * @param string $message  Message to show
 * @param array  $data     Extra data (optional)
 */
function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

/**
 * Redirect කරයි
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * POST data safely get කරයි
 */
function post(string $key, string $default = ''): string {
    return clean($_POST[$key] ?? $default);
}
