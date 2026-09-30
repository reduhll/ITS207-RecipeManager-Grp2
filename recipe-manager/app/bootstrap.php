<?php
// Shared setup: every PHP page loads this file before sending HTML.
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Show a useful message without exposing database credentials or SQL errors.
set_exception_handler(function (Throwable $error) {
    error_log((string) $error);
    http_response_code(500);
    exit('Something went wrong. Check the PHP server log and database settings.');
});

function db(): PDO
{
    static $connection = null;
    if ($connection === null) {
        $config = require __DIR__ . '/config.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $connection = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $connection;
}

function e($value): string
{
    // Display stored text as text, never as HTML or JavaScript.
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function input(array $source, string $key): string
{
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
}

function text_length(string $value): int
{
    // Count Unicode characters without requiring the mbstring extension.
    $length = preg_match_all('/./us', $value);
    return $length === false ? PHP_INT_MAX : $length;
}

function redirect(string $page): void
{
    header('Location: ' . $page, true, 303);
    exit;
}

function require_login(): int
{
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
    return (int) $_SESSION['user_id'];
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): void
{
    echo '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function check_csrf(): void
{
    // Reject form submissions that do not belong to the current session.
    if (!hash_equals(csrf_token(), input($_POST, 'csrf'))) {
        http_response_code(403);
        exit('This form has expired. Go back, refresh the page and try again.');
    }
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Please use the form to perform this action.');
    }
    check_csrf();
}

function flash(string $message): void
{
    $_SESSION['message'] = $message;
}
