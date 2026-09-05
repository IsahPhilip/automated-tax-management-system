<?php
declare(strict_types=1);
$base = rtrim($argv[1] ?? '', '/');
require 'C:/xampp/htdocs/codes/automated-tax-management-system/vendor/autoload.php';
require 'C:/xampp/htdocs/codes/automated-tax-management-system/config/app.php';
require 'C:/xampp/htdocs/codes/automated-tax-management-system/config/constants.php';
require_once 'C:/xampp/htdocs/codes/automated-tax-management-system/config/database.php';

$row = db()->query("SELECT user_id, role_id, status, LEFT(password_hash,7) ph FROM users WHERE email='tmp_repofficer@taxsystem.local'")->fetch_assoc();
echo 'seeded user: ', json_encode($row), PHP_EOL;
echo 'password verify vs stored hash: ';
$r = db()->query("SELECT password_hash FROM users WHERE email='tmp_repofficer@taxsystem.local'")->fetch_assoc();
var_export(password_verify('Probe#Test2026', (string) ($r['password_hash'] ?? '')));
echo PHP_EOL;

$jar = sys_get_temp_dir() . '/dbg.jar';
@unlink($jar);
$c = curl_init($base . '/login');
curl_setopt_array($c, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar]);
curl_exec($c);
curl_close($c);
echo 'GET /login done, cookies:', PHP_EOL, file_exists($jar) ? substr((string) file_get_contents($jar), -200) : '(none)', PHP_EOL;

$c = curl_init($base . '/login');
curl_setopt_array($c, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['email' => 'tmp_repofficer@taxsystem.local', 'password' => 'Probe#Test2026']),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_COOKIEJAR => $jar,
    CURLOPT_COOKIEFILE => $jar,
]);
$resp = (string) curl_exec($c);
$code = (int) curl_getinfo($c, CURLINFO_RESPONSE_CODE);
curl_close($c);
echo 'POST /login -> ', $code, PHP_EOL;
foreach (array_slice(explode("\r\n", $resp), 0, 10) as $h) {
    if (stripos($h, 'location:') === 0 || stripos($h, 'set-cookie:') === 0 || stripos($h, 'HTTP/') === 0) echo $h, PHP_EOL;
}
