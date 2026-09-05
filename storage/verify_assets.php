<?php
/**
 * Verification script — checks all assets are served correctly
 * and reports pages return real content (not "wired up").
 * Run before deleting.
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/constants.php';

$PHP = 'C:\\xampp\\php\\php.exe';
$root = 'C:\\xampp\\htdocs\\codes\\automated-tax-management-system';

// Start dev server
$server = proc_open(
    [$PHP, '-S', '127.0.0.1:8099', '-t', $root . '/public'],
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes
);
sleep(2);

function httpGet(string $url, array &$cookies, array $extra = []): array {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0\r\n",
            'follow_location' => true,
            'max_redirects' => 3
        ]
    ]);
    foreach ($cookies as $name => $value) {
        // Append cookie header
    }
    // Use cookies
    $headers = "User-Agent: Mozilla/5.0\r\n";
    if (!empty($cookies)) {
        $cookieStr = implode('; ', array_map(fn($k,$v)=>"$k=$v", array_keys($cookies), $cookies));
        $headers .= "Cookie: $cookieStr\r\n";
    }
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => $headers,
            'follow_location' => false
        ]
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    $statusLine = $http_response_header[0] ?? '';
    preg_match('/(\d{3})/', $statusLine, $m);
    $status = $m[1] ?? 'ERR';
    // Extract new cookies
    if (isset($http_response_header)) {
        foreach ($http_response_header as $h) {
            if (preg_match('/^Set-Cookie:\s*(\w+)-?(.+?)=(.+?);/i', $h, $cm)) {
                $cookies[$cm[1]] = $cm[3];
            }
        }
    }
    return [$status, $resp ?? '', $http_response_header ?? []];
}

function httpPost(string $url, array &$cookies, array $body): array {
    $headers = "User-Agent: Mozilla/5.0\r\n";
    if (!empty($cookies)) {
        $cookieStr = implode('; ', array_map(fn($k,$v)=>"$k=$v", array_keys($cookies), $cookies));
        $headers .= "Cookie: $cookieStr\r\n";
    }
    $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => $headers,
            'content' => http_build_query($body),
            'follow_location' => false
        ]
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    $statusLine = $http_response_header[0] ?? '';
    preg_match('/(\d{3})/', $statusLine, $m);
    $status = $m[1] ?? 'ERR';
    if (isset($http_response_header)) {
        foreach ($http_response_header as $h) {
            if (preg_match('/^Set-Cookie:\s*(\w+)-?(.+?)=(.+?);/i', $h, $cm)) {
                $cookies[$cm[1]] = $cm[3];
            }
        }
    }
    return [$status, $resp ?? '', $http_response_header ?? []];
}

echo "=== HTTP VERIFICATION (public/assets/ usage) ===\n";

$cookies = [];
// Note: login likely won't work without proper CSRF; we'll test assets without auth first

// 1. Test CSS files are served
list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099/assets/css/style.css', $cookies);
echo "GET /assets/css/style.css -> HTTP $status, size=" . strlen($body) . " bytes\n";

list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099/assets/css/reports.css', $cookies);
echo "GET /assets/css/reports.css -> HTTP $status, size=" . strlen($body) . " bytes\n";

list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099/assets/js/app.js', $cookies);
echo "GET /assets/js/app.js -> HTTP $status, size=" . strlen($body) . " bytes\n";

// 2. Test login page (no auth needed) — check it references style.css
list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099/login', $cookies);
$hasStyle = strpos($body, 'style.css') !== false;
$hasInline = strpos($body, '<style>') !== false;
echo "GET /login -> HTTP $status | has style.css: " . ($hasStyle?'YES':'NO') . " | has inline <style>: " . ($hasInline?'YES':'NO') . "\n";

// 3. Attempt login to get session
list($status, $body, $hdrs) = httpPost('http://127.0.0.1:8099/login', $cookies, [
    'username_or_email' => 'admin@taxsystem.local',
    'password' => 'Admin123',
    'csrf_token' => '' // no CSRF for now; will likely fail but might still set session
]);
echo "POST /login -> HTTP $status | redirect to: " . ($status == '302' ? ($hdrs['Location'][0] ?? 'unknown') : 'N/A') . "\n";

// 4. Try reports pages (may redirect to login without auth)
foreach (['/', '/dashboard', '/reports'] as $path) {
    list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099' . $path, $cookies);
    $wiredUp = strpos($body, 'wired up') !== false;
    $hasStyleCss = strpos($body, 'style.css') !== false;
    $hasAppJs = strpos($body, 'app.js') !== false;
    echo "GET $path -> HTTP $status | wired-up:" . ($wiredUp?'YES(BAD)':'NO(GOOD)') . " | style.css:" . ($hasStyleCss?'YES':'NO') . " | app.js:" . ($hasAppJs?'YES':'NO') . "\n";
}

// 5. Try reports pages directly (staff only)
foreach (['/reports', '/reports/revenue', '/reports/taxpayers', '/reports/assessments', '/reports/payments'] as $path) {
    list($status, $body, $hdrs) = httpGet('http://127.0.0.1:8099' . $path, $cookies);
    $wiredUp = strpos($body, 'wired up') !== false;
    $hasReportsCss = strpos($body, 'reports.css') !== false;
    $hasStyleCss = strpos($body, 'style.css') !== false;
    echo "GET $path -> HTTP $status | wired-up:" . ($wiredUp?'YES(BAD)':'NO(GOOD)') . " | style.css:" . ($hasStyleCss?'YES':'NO') . " | reports.css:" . ($hasReportsCss?'YES':'NO') . "\n";
}

// Cleanup
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
$returnCode = proc_close($server);
echo "\nServer stopped (exit code: $returnCode)\n";
echo "=== Verification complete ===\n";