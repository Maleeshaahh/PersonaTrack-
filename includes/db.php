<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'personatrack');
define('DB_USER', 'root');        // XAMPP default user
define('DB_PASS', '');            // XAMPP default password (empty)
define('DB_CHARSET', 'utf8mb4');

function getDB(): ?PDO {
    static $pdo = null; // Singleton - connection එකක් ම use කරයි

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errors throw කරයි
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Array ලෙස return
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Real prepared statements
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("DB Connection Error: " . $e->getMessage());
            return null;
        }
    }
    return $pdo;
}
