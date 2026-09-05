<?php
declare(strict_types=1);

$title = $title ?? APP_NAME;
$user = function_exists('auth_user') ? auth_user() : null;
$content = $content ?? null;
$styles = $styles ?? [];
if (!is_array($styles)) {
    $styles = [$styles];
}
$styles = array_values(array_unique(array_filter($styles, static fn ($style) => is_string($style) && $style !== '')));
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$currentPath = '/' . trim($currentPath, '/');
if ($currentPath === '') {
    $currentPath = '/';
}
$navIsActive = static function (string $target) use ($currentPath): bool {
    $target = '/' . trim($target, '/');
    if ($target === '/') {
        return $currentPath === '/';
    }

    if ($target === '/dashboard') {
        return in_array($currentPath, ['/dashboard', '/staff/dashboard', '/taxpayer/dashboard', '/admin'], true);
    }

    if ($target === '/taxpayers') {
        return $currentPath === '/taxpayers' || str_starts_with($currentPath, '/taxpayers/');
    }

    if ($target === '/declarations') {
        return $currentPath === '/declarations' || str_starts_with($currentPath, '/declarations/');
    }

    if ($target === '/assessments') {
        return $currentPath === '/assessments' || str_starts_with($currentPath, '/assessments/');
    }

    if ($target === '/payments') {
        return $currentPath === '/payments' || str_starts_with($currentPath, '/payments/');
    }

    if ($target === '/receipts') {
        return $currentPath === '/receipts' || str_starts_with($currentPath, '/receipts/');
    }

    if ($target === '/reports') {
        return $currentPath === '/reports' || str_starts_with($currentPath, '/reports/');
    }

    if ($target === '/notifications') {
        return $currentPath === '/notifications' || str_starts_with($currentPath, '/notifications/');
    }

    if ($target === '/admin/users') {
        return $currentPath === '/admin/users' || str_starts_with($currentPath, '/admin/users/');
    }

    if ($target === '/admin/tax-rules') {
        return $currentPath === '/admin/tax-rules' || str_starts_with($currentPath, '/admin/tax-rules/');
    }

    if ($target === '/admin/settings') {
        return $currentPath === '/admin/settings' || str_starts_with($currentPath, '/admin/settings/');
    }

    if ($target === '/admin/audit-logs') {
        return $currentPath === '/admin/audit-logs' || str_starts_with($currentPath, '/admin/audit-logs/');
    }

    return $currentPath === $target || str_starts_with($currentPath, $target . '/');
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> - <?= e(APP_SHORT_NAME) ?></title>
    <!-- Core stylesheet -->
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/style.css')) ?>">
    <?php foreach ($styles as $style): ?>
        <?php if ($style === '/assets/css/style.css') continue; ?>
        <link rel="stylesheet" href="<?= e(app_url($style)) ?>">
    <?php endforeach; ?>
    <!-- Responsive styles -->
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/responsive.css')) ?>">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand"><?= e(APP_SHORT_NAME) ?><span><?= e(APP_NAME) ?></span></div>
            <?php $isStaffNav = function_exists('is_staff') && is_staff(); ?>
            <?php $isTaxpayerNav = function_exists('is_taxpayer') && is_taxpayer(); ?>
            <nav class="nav">
                <a class="<?= $navIsActive('/dashboard') ? 'active' : '' ?>" href="<?= e(app_url('/dashboard')) ?>">Dashboard</a>
                <?php if ($isStaffNav): ?>
                    <a class="<?= $navIsActive('/taxpayers') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayers')) ?>">Taxpayers</a>
                    <a class="<?= $navIsActive('/declarations') ? 'active' : '' ?>" href="<?= e(app_url('/declarations')) ?>">Declarations</a>
                    <a class="<?= $navIsActive('/assessments') ? 'active' : '' ?>" href="<?= e(app_url('/assessments')) ?>">Assessments</a>
                    <a class="<?= $navIsActive('/payments') ? 'active' : '' ?>" href="<?= e(app_url('/payments')) ?>">Payments</a>
                    <a class="<?= $navIsActive('/receipts') ? 'active' : '' ?>" href="<?= e(app_url('/receipts')) ?>">Receipts</a>
                    <a class="<?= $navIsActive('/reports') ? 'active' : '' ?>" href="<?= e(app_url('/reports')) ?>">Reports</a>
                    <a class="<?= $navIsActive('/notifications/send') ? 'active' : '' ?>" href="<?= e(app_url('/notifications/send')) ?>">Send notice</a>
                <?php else: ?>
                    <a class="<?= $navIsActive('/taxpayer/declarations') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/declarations')) ?>">My declarations</a>
                    <a class="<?= $navIsActive('/taxpayer/assessments') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/assessments')) ?>">My assessments</a>
                    <a class="<?= $navIsActive('/taxpayer/payments') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/payments')) ?>">My payments</a>
                    <a class="<?= $navIsActive('/taxpayer/profile') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/profile')) ?>">My profile</a>
                <?php endif; ?>
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <a class="<?= $navIsActive('/admin/users') ? 'active' : '' ?>" href="<?= e(app_url('/admin/users')) ?>">Admin &middot; Users</a>
                    <a class="<?= $navIsActive('/admin/tax-rules') ? 'active' : '' ?>" href="<?= e(app_url('/admin/tax-rules')) ?>">Admin &middot; Tax rules</a>
                    <a class="<?= $navIsActive('/admin/settings') ? 'active' : '' ?>" href="<?= e(app_url('/admin/settings')) ?>">Admin &middot; Settings</a>
                    <a class="<?= $navIsActive('/admin/audit-logs') ? 'active' : '' ?>" href="<?= e(app_url('/admin/audit-logs')) ?>">Admin &middot; Audit logs</a>
                <?php endif; ?>
                <?php if ($user): ?>
                    <a class="<?= $navIsActive('/notifications') ? 'active' : '' ?>" href="<?= e(app_url('/notifications')) ?>">Notifications</a>
                <?php endif; ?>
            </nav>
            <?php if ($user): ?>
                <form class="logout" action="<?= e(app_url('/logout')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit">Sign out</button>
                </form>
            <?php endif; ?>
        </aside>
        <main class="main">
            <div class="topbar">
                <div>
                    <strong><?= e($title) ?></strong>
                    <?php if ($user): ?>
                        <div class="muted"><?= e(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?> &middot; <?= e($user['role_name'] ?? '') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <section class="panel">
                <?php if (function_exists('get_flash') && ($__flash = get_flash())): ?>
                    <div class="flash flash-<?= e($__flash['type'] ?? 'info') ?>"><?= e($__flash['message'] ?? '') ?></div>
                <?php endif; ?>
                <?php if (is_string($content)): ?>
                    <h1><?= e($title) ?></h1>
                    <?= $content ?>
                <?php elseif (isset($viewFile) && is_file($viewFile)): ?>
                    <?php require $viewFile; ?>
                <?php else: ?>
                    <p class="muted">No content is available for this screen yet.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <!-- Main JS bundle from public/assets/ -->
    <script src="<?= e(app_url('/assets/js/app.js')) ?>"></script>
    <!-- Page-specific scripts: a view may set `$scripts` to an array of asset paths -->
    <?php foreach (($scripts ?? []) as $script): ?>
        <script src="<?= e(app_url($script)) ?>"></script>
    <?php endforeach; ?>
</body>
</html>