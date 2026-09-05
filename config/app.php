<?php
declare(strict_types=1);

function load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key === '' || getenv($key) !== false) {
            continue;
        }

        $value = trim($value, "\"'");
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

load_env_file(dirname(__DIR__) . '/.env');

// Environment-aware application bootstrap.
define('APP_ENV', strtolower(getenv('APP_ENV') ?: 'development'));
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: (APP_ENV === 'development' ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN));

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Africa/Lagos');

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
}

define('APP_NAME', 'Automated Tax Management System');
define('APP_SHORT_NAME', 'ATMS');
define('APP_VERSION', '1.0.0');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');
define('APP_BASE_URL', rtrim(getenv('APP_BASE_URL') ?: '/automated-tax-management-system', '/'));
define('APP_URL', APP_BASE_URL);

define('PROJECT_ROOT', dirname(__DIR__));
define('CONFIG_PATH', PROJECT_ROOT . '/config');
define('APP_PATH', PROJECT_ROOT . '/app');
define('DATABASE_PATH', PROJECT_ROOT . '/database');
define('PUBLIC_PATH', PROJECT_ROOT . '/public');
define('STORAGE_PATH', PROJECT_ROOT . '/storage');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
define('DOCUMENT_PATH', UPLOAD_PATH . '/documents');

define('SESSION_NAME', getenv('SESSION_NAME') ?: 'ATMS_SESSION');
define('SESSION_TIMEOUT', (int)(getenv('SESSION_TIMEOUT') ?: 1800));

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function app_url(string $path = ''): string {
    return APP_BASE_URL . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isProduction(): bool { return APP_ENV === 'production'; }
function isDevelopment(): bool { return APP_ENV === 'development'; }
