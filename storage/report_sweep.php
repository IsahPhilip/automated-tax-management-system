<?php
declare(strict_types=1);
// Report-page verifier: php report_sweep.php <base>
require 'C:/xampp/htdocs/codes/automated-tax-management-system/vendor/autoload.php';
require 'C:/xampp/htdocs/codes/automated-tax-management-system/config/app.php';
require 'C:/xampp/htdocs/codes/automated-tax-management-system/config/constants.php';
require_once 'C:/xampp/htdocs/codes/automated-tax-management-system/config/database.php';

$base = rtrim($argv[1] ?? '', '/');
$jar = sys_get_temp_dir() . '/rs.jar';
@unlink($jar);

// Seed one officer
db()->execute_query("DELETE FROM users WHERE email='tmp_repofficer@taxsystem.local'");
db()->execute_query(
    'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status) VALUES (2,"Rep","Officer","tmp_repofficer@taxsystem.local","+2347777777",?,"ACTIVE")',
    [password_hash('Probe#Test2026', PASSWORD_BCRYPT)]
);

function fetch(string $url, ?string $jar = null): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true]);
    if ($jar !== null) { curl_setopt($ch, CURLOPT_COOKIEJAR, $jar); curl_setopt($ch, CURLOPT_COOKIEFILE, $jar); }
    $body = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return [$code, $body];
}

fetch($base . '/login', $jar);
$ch = curl_init($base . '/login');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['email' => 'tmp_repofficer@taxsystem.local', 'password' => 'Probe#Test2026']),
    CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
]);
curl_exec($ch);
curl_close($ch);

$pages = [
    ['reports hub', '/reports', ['Available reports']],
    ['revenue', '/reports/revenue', ['Revenue Report', 'Total revenue']],
    ['revenue filtered', '/reports/revenue?from=2026-01-01&to=2026-12-31', ['Generate report']],
    ['taxpayers', '/reports/taxpayers', ['Taxpayer Report', 'Share of register']],
    ['assessments', '/reports/assessments', ['Assessment Report', 'Collection rate']],
    ['payments', '/reports/payments', ['Payment Report', 'Export CSV']],
    ['payments filtered', '/reports/payments?from=2020-01-01&to=2030-01-01', ['Apply filter']],
];
$failures = 0;
foreach ($pages as [$name, $route, $markers]) {
    [$code, $body] = fetch($base . $route, $jar);
    $placeholder = str_contains($body, 'This screen is wired up');
    $missingMarkers = [];
    foreach ($markers as $m) {
        if (!str_contains($body, $m)) { $missingMarkers[] = $m; }
    }
    $ok = $code < 400 && !$placeholder && $missingMarkers === [];
    if (!$ok) { $failures++; }
    printf("[%s] %-18s %s -> code=%d placeholder=%s missing=%s\n",
        $ok ? 'OK ' : 'BAD',
        $name,
        $route,
        $code,
        $placeholder ? 'YES' : 'no',
        $missingMarkers === [] ? '-' : implode('|', $missingMarkers)
    );
}
echo 'FAILURES=', $failures, PHP_EOL;
exit($failures === 0 ? 0 : 1);
