<?php $stats = $stats ?? []; $scripts = ['/assets/js/dashboard.js']; ?>
<div class="dashboard-shell">
    <h1>Administrator Dashboard</h1>
    <p class="muted">System-wide control centre for users, tax configuration and compliance oversight.</p>

    <div class="dashboard-metrics">
        <div class="dashboard-metric"><span class="label">System users</span><strong><?= (int) ($stats['users'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Taxpayers</span><strong><?= (int) ($stats['taxpayers'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Declarations pending review</span><strong><?= (int) ($stats['declarations_pending'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Total assessed</span><strong>₦<?= number_format((float) ($stats['assessed_amount'] ?? 0), 2) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Revenue collected</span><strong>₦<?= number_format((float) ($stats['collected_amount'] ?? 0), 2) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Outstanding balance</span><strong>₦<?= number_format((float) ($stats['outstanding_amount'] ?? 0), 2) ?></strong></div>
    </div>

    <div class="dashboard-section">
        <h2>Administration</h2>
        <div class="dashboard-cards">
            <div class="dashboard-card"><h3>Users</h3><p class="muted">Create staff accounts and manage access.</p><a class="button" href="<?= e(app_url('/users')) ?>">Manage users</a></div>
            <div class="dashboard-card"><h3>Tax types</h3><p class="muted">PIT, CIT and future tax categories.</p><a class="button" href="<?= e(app_url('/tax-types')) ?>">Manage types</a></div>
            <div class="dashboard-card"><h3>Tax rules</h3><p class="muted">Bands, rates and effective periods.</p><a class="button" href="<?= e(app_url('/tax-rules')) ?>">Manage rules</a></div>
            <div class="dashboard-card"><h3>Penalties</h3><p class="muted">Late-filing and late-payment rules.</p><a class="button" href="<?= e(app_url('/admin/penalties')) ?>">Configure</a></div>
            <div class="dashboard-card"><h3>Interest rates</h3><p class="muted">Annual interest on unpaid tax.</p><a class="button" href="<?= e(app_url('/admin/interest-rates')) ?>">Configure</a></div>
            <div class="dashboard-card"><h3>Audit logs</h3><p class="muted">Every important operation recorded.</p><a class="button" href="<?= e(app_url('/admin/audit-logs')) ?>">View trail</a></div>
            <div class="dashboard-card"><h3>System settings</h3><p class="muted">Deployment-level configuration.</p><a class="button" href="<?= e(app_url('/admin/settings')) ?>">Open settings</a></div>
        </div>
    </div>
</div>
