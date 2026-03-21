<?php

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function requireLogin(): void {
    startSession();
    if (empty($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit;
    }
}

function currentUserId(): int {
    startSession();
    return (int)($_SESSION['user_id'] ?? 0);
}

function clean(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function post(string $key, string $default = ''): string {
    return clean($_POST[$key] ?? $default);
}
