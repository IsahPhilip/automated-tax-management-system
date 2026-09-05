<?php
declare(strict_types=1);

$title = $title ?? 'Error';
$message = $message ?? 'An unexpected error occurred.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - <?= e(APP_SHORT_NAME) ?></title>
    <!-- Core stylesheet -->
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/style.css')) ?>">
    <!-- Responsive styles -->
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/responsive.css')) ?>">
    <style>
        body {
            align-items: center;
            background: linear-gradient(135deg, #f7fbf3, #edf7ef 52%, #fff8e7);
            display: flex;
            font-family: Georgia, "Times New Roman", serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
        }
        .error-box {
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 22px 60px rgba(24, 64, 40, .12);
            max-width: 520px;
            padding: 34px;
            text-align: center;
            width: 100%;
        }
        .error-box h1 {
            color: #b32638;
            margin: 0 0 12px;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1><?= e($title) ?></h1>
        <p class="muted"><?= e($message) ?></p>
        <div class="actions" style="justify-content: center;">
            <a class="button" href="<?= e(app_url('/dashboard')) ?>">Go to dashboard</a>
            <a class="button secondary" href="<?= e(app_url('/')) ?>">Home</a>
        </div>
    </div>
</body>
</html>
