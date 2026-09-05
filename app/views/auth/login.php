<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?= e(APP_SHORT_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/style.css')) ?>">
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
        .login {
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 22px 60px rgba(24, 64, 40, .12);
            max-width: 430px;
            padding: 34px;
            width: 100%;
        }
        .login h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }
        .login p.muted {
            margin-top: 0;
        }
        .login button[type="submit"] {
            background: var(--brand);
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font: inherit;
            padding: 10px 14px;
            width: 100%;
        }
        .login button[type="submit"]:hover {
            background: var(--brand-dark);
        }
    </style>
</head>
<body>
    <form class="login" action="<?= e(app_url('/login')) ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <h1><?= e(APP_SHORT_NAME) ?></h1>
        <p class="muted">Sign in to manage taxpayers, declarations, assessments, payments and receipts.</p>
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="email" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Sign in</button>
    </form>
</body>
</html>
