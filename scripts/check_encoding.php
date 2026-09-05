<?php
$files = [
    'style.css', 'responsive.css', 'dashboard.css', 'reports.css'
];
foreach ($files as $f) {
    $path = __DIR__ . '/../public/assets/css/' . $f;
    $b = file_get_contents($path);
    $i = strpos($b, "\xe2");
    $isUtf8 = mb_check_encoding($b, 'UTF-8');
    echo $f . ' => bytes:' . strlen($b) . ' first_0xE2:' . ($i === false ? 'none' : $i);
    if ($i !== false) {
        echo ' next3_hex:' . bin2hex(substr($b, $i, 3));
    }
    echo ' mb_is_utf8:' . ($isUtf8 ? 'yes' : 'no') . PHP_EOL;
}