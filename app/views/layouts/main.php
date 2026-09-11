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
                <a class="<?= $navIsActive('/dashboard') ? 'active' : '' ?>" href="<?= e(app_url('/dashboard')) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7A1 1 0 0 0 3 11h1v6a1 1 0 0 0 1 1h4v-4h2v4h4a1 1 0 0 0 1-1v-6h1a1 1 0 0 0 .707-1.707l-7-7z"/></svg>
                    Dashboard
                </a>
                <?php if ($isStaffNav): ?>
                    <a class="<?= $navIsActive('/taxpayers') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayers')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm-7 2a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm14 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0zM2 17v-1a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v1H2zm-2 0v-1a2 2 0 0 1 2-2H1a5.97 5.97 0 0 0-1 3.33V17H0zm18 0v-.67A5.97 5.97 0 0 0 17 13h-.5a2 2 0 0 1 2 2v1h-1z"/></svg>
                        Taxpayers
                    </a>
                    <a class="<?= $navIsActive('/declarations') ? 'active' : '' ?>" href="<?= e(app_url('/declarations')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 0 1 2-2h4.586A2 2 0 0 1 12 2.586L15.414 6A2 2 0 0 1 16 7.414V16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4zm2 6a1 1 0 0 1 1-1h6a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1zm1 3a1 1 0 0 0 0 2h6a1 1 0 0 0 0-2H7z" clip-rule="evenodd"/></svg>
                        Declarations
                    </a>
                    <a class="<?= $navIsActive('/assessments') ? 'active' : '' ?>" href="<?= e(app_url('/assessments')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2H6zm2 10a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2H8zm0-3a1 1 0 0 0 0 2h4a1 1 0 0 0 0-2H8zm0-3a1 1 0 0 0 0 2h1a1 1 0 0 0 0-2H8z" clip-rule="evenodd"/></svg>
                        Assessments
                    </a>
                    <a class="<?= $navIsActive('/payments') ? 'active' : '' ?>" href="<?= e(app_url('/payments')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 0 0-2 2v1h16V6a2 2 0 0 0-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9zM4 13a1 1 0 0 1 1-1h1a1 1 0 1 1 0 2H5a1 1 0 0 1-1-1zm5-1a1 1 0 1 0 0 2h1a1 1 0 1 0 0-2H9z" clip-rule="evenodd"/></svg>
                        Payments
                    </a>
                    <a class="<?= $navIsActive('/receipts') ? 'active' : '' ?>" href="<?= e(app_url('/receipts')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 2a2 2 0 0 0-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 0 0-2-2H5zm4.707 5.707a1 1 0 0 0-1.414-1.414l-3 3a1 1 0 0 0 0 1.414l3 3a1 1 0 0 0 1.414-1.414L8.414 11H13a1 1 0 1 0 0-2H8.414l1.293-1.293z" clip-rule="evenodd"/></svg>
                        Receipts
                    </a>
                    <a class="<?= $navIsActive('/reports') ? 'active' : '' ?>" href="<?= e(app_url('/reports')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3zm3 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1zm0 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1zm0 4a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1z" clip-rule="evenodd"/></svg>
                        Reports
                    </a>
                    <a class="<?= $navIsActive('/notifications/send') ? 'active' : '' ?>" href="<?= e(app_url('/notifications/send')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 0 0-1.788 0l-7 14a1 1 0 0 0 1.169 1.409l.003-.001.014-.003.058-.014a14.3 14.3 0 0 1 1.041-.2 20 20 0 0 1 2.609-.2c1.034 0 2.093.07 3.04.2a14.3 14.3 0 0 1 1.1.214l.014.003.003.001a1 1 0 0 0 1.169-1.409l-7-14z"/></svg>
                        Send notice
                    </a>
                <?php else: ?>
                    <a class="<?= $navIsActive('/taxpayer/declarations') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/declarations')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 0 1 2-2h4.586A2 2 0 0 1 12 2.586L15.414 6A2 2 0 0 1 16 7.414V16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4zm2 6a1 1 0 0 1 1-1h6a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1zm1 3a1 1 0 0 0 0 2h6a1 1 0 0 0 0-2H7z" clip-rule="evenodd"/></svg>
                        My declarations
                    </a>
                    <a class="<?= $navIsActive('/taxpayer/assessments') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/assessments')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2H6zm2 10a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2H8zm0-3a1 1 0 0 0 0 2h4a1 1 0 0 0 0-2H8zm0-3a1 1 0 0 0 0 2h1a1 1 0 0 0 0-2H8z" clip-rule="evenodd"/></svg>
                        My assessments
                    </a>
                    <a class="<?= $navIsActive('/taxpayer/payments') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/payments')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 0 0-2 2v1h16V6a2 2 0 0 0-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9zM4 13a1 1 0 0 1 1-1h1a1 1 1 0 1 0 2H5a1 1 0 0 1-1-1zm5-1a1 1 0 1 0 0 2h1a1 1 0 1 0 0-2H9z" clip-rule="evenodd"/></svg>
                        My payments
                    </a>
                    <a class="<?= $navIsActive('/taxpayer/profile') ? 'active' : '' ?>" href="<?= e(app_url('/taxpayer/profile')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-7 9a7 7 0 1 1 14 0H3z" clip-rule="evenodd"/></svg>
                        My profile
                    </a>
                <?php endif; ?>
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <div class="nav-section">Administration</div>
                    <a class="<?= $navIsActive('/admin/users') ? 'active' : '' ?>" href="<?= e(app_url('/admin/users')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM14 15a4 4 0 0 0-8 0v1h8v-1zM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM2 15v-1a4 4 0 0 1 4-4h.5A5.5 5.5 0 0 0 5 15v1H2zm14 1v-1a5.5 5.5 0 0 0-1.5-3.793H15a4 4 0 0 1 4 4v1h-3z"/></svg>
                        Users
                    </a>
                    <a class="<?= $navIsActive('/admin/tax-rules') ? 'active' : '' ?>" href="<?= e(app_url('/admin/tax-rules')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 0 1-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 0 1 .947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 0 1 2.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 0 1 2.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 0 1 .947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 0 1-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 0 1-2.287-.947zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" clip-rule="evenodd"/></svg>
                        Tax rules
                    </a>
                    <a class="<?= $navIsActive('/admin/settings') ? 'active' : '' ?>" href="<?= e(app_url('/admin/settings')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 0 1-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 0 1 .947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 0 1 2.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 0 1 2.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 0 1 .947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 0 1-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 0 1-2.287-.947zM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" clip-rule="evenodd"/></svg>
                        Settings
                    </a>
                    <a class="<?= $navIsActive('/admin/audit-logs') ? 'active' : '' ?>" href="<?= e(app_url('/admin/audit-logs')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1zm3 8V5.5a3 3 0 1 0-6 0V9h6z" clip-rule="evenodd"/></svg>
                        Audit logs
                    </a>
                <?php endif; ?>
                <?php if ($user): ?>
                    <a class="<?= $navIsActive('/notifications') ? 'active' : '' ?>" href="<?= e(app_url('/notifications')) ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 4 14h12a1 1 0 0 0 .707-1.707L16 11.586V8a6 6 0 0 0-6-6zM10 18a3 3 0 0 1-3-3h6a3 3 0 0 1-3 3z"/></svg>
                        Notifications
                    </a>
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