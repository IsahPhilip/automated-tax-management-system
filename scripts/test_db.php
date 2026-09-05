<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

try {
    $conn = db();
    $res = $conn->query("SELECT COUNT(*) as cnt FROM users");
    $row = $res->fetch_assoc();
    echo "DB_OK: connected. users=" . ($row['cnt'] ?? 0) . PHP_EOL;
} catch (Throwable $e) {
    echo "DB_ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
