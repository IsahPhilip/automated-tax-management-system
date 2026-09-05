<?php
declare(strict_types=1);

// Composer autoloader for PSR-4 and classmap
require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/config/database.php';
// Load all helper files (auth, session, csrf, validation, response, security, flash, URL helpers)
require_once dirname(__DIR__) . '/app/helpers/audit.php';
require_once dirname(__DIR__) . '/app/helpers/session.php';
require_once dirname(__DIR__) . '/app/helpers/auth.php';
require_once dirname(__DIR__) . '/app/helpers/csrf.php';
require_once dirname(__DIR__) . '/app/helpers/response.php';
require_once dirname(__DIR__) . '/app/helpers/security.php';
require_once dirname(__DIR__) . '/app/helpers/validation.php';
require_once dirname(__DIR__) . '/app/helpers/flash.php';
require_once dirname(__DIR__) . '/app/helpers/input.php';
require_once dirname(__DIR__) . '/app/helpers/url.php';

set_exception_handler(function (Throwable $exception): void {
    error_log(sprintf(
        '[%s] Unhandled exception: %s in %s:%d',
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    http_response_code(500);

    if (APP_DEBUG && isDevelopment()) {
        echo '<h1>Application Error</h1><pre>'
            . e($exception->getMessage())
            . "\n\n"
            . e($exception->getFile())
            . ':' . $exception->getLine()
            . "\n\n"
            . e($exception->getTraceAsString())
            . '</pre>';
        return;
    }

    echo 'An internal server error occurred.';
});

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

$allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD'];
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if (!in_array($requestMethod, $allowedMethods, true)) {
    http_response_code(405);
    header('Allow: ' . implode(', ', $allowedMethods));
    exit('Method Not Allowed');
}

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = is_string($requestUri) && $requestUri !== '' ? $requestUri : '/';
$basePath = parse_url(APP_BASE_URL, PHP_URL_PATH) ?: '';

if ($basePath !== '' && $basePath !== '/' && str_starts_with($requestUri, $basePath)) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$requestUri = '/' . trim($requestUri, '/');

if ($requestUri === '/index.php' || $requestUri === '/public/index.php') {
    $requestUri = '/';
}

if (PHP_SAPI !== 'cli') {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $_SESSION['last_activity'] = time();
}

$publicAssetPath = null;
if (preg_match('#^/public/assets/(.+)$#', $requestUri, $matches)) {
    $publicAssetPath = PUBLIC_PATH . '/assets/' . $matches[1];
} elseif (preg_match('#^/assets/(.+)$#', $requestUri, $matches)) {
    $publicAssetPath = PUBLIC_PATH . '/assets/' . $matches[1];
}

if ($publicAssetPath !== null && is_file($publicAssetPath)) {
    $extension = strtolower(pathinfo($publicAssetPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];

    http_response_code(200);
    header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=31536000, immutable');
    header('X-Content-Type-Options: nosniff');
    readfile($publicAssetPath);
    exit;
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self';");
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Helper functions are loaded in helpers/*.php above; only define fallbacks
// if they were not already provided by the helper files.
if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token(?string $token): bool
    {
        return is_string($token) && $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}

require_once PROJECT_ROOT . '/routes/web.php';

dispatch($requestMethod, $requestUri);
