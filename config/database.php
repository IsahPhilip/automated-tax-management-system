<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/constants.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);
$dbName = getenv('DB_NAME') ?: 'automated_tax_management';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
    $conn->set_charset(DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    error_log('ATMS database connection failed: ' . $e->getMessage());
    if (APP_DEBUG) {
        http_response_code(500);
        exit('Database connection failed. Check your XAMPP/MySQL configuration.');
    }
    http_response_code(500);
    exit('Unable to connect to the application database.');
}

function db(): mysqli {
    global $conn;
    return $conn;
}