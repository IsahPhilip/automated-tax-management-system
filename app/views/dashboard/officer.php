<?php $stats = $stats ?? []; $recent = $recentDeclarations ?? []; $scripts = ['/assets/js/dashboard.js']; ?>
<div class="dashboard-shell">
    <h1>Staff Dashboard</h1>
    <p class="muted">Operational overview for revenue officers and administrators.</p>

    <div class="dashboard-metrics">
        <div class="dashboard-metric"><span class="label">Registered taxpayers</span><strong><?= (int) ($stats['taxpayers'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Declarations awaiting review</span><strong><?= (int) ($stats['pending_declarations'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Assessments to issue</span><strong><?= (int) ($stats['pending_assessments'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Outstanding tax</span><strong>₦<?= number_format((float) ($stats['outstanding'] ?? 0), 2) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Revenue collected</span><strong>₦<?= number_format((float) ($stats['collected'] ?? 0), 2) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Payments pending verification</span><strong>₦<?= number_format((float) ($stats['payments_pending'] ?? 0), 2) ?></strong></div>
    </div>

    <div class="quick-actions">
        <a class="button" href="<?= e(app_url('/declarations?status=SUBMITTED')) ?>">Review declarations</a>
        <a class="button secondary" href="<?= e(app_url('/taxpayers/create')) ?>">Register taxpayer</a>
        <a class="button secondary" href="<?= e(app_url('/reports')) ?>">Open reports</a>
    </div>

    <div class="dashboard-section">
        <h2>Latest submitted declarations</h2>
        <table>
            <thead><tr><th>Taxpayer</th><th>Type</th><th>Period</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
            <tbody>
            <?php if ($recent === []): ?>
                <tr><td colspan="6">No declarations have been filed yet.</td></tr>
            <?php else: ?>
                <?php foreach ($recent as $d): ?>
                    <?php $name = trim(($d['business_name'] ?? '') !== '' ? $d['business_name'] : (($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? ''))); ?>
                    <tr>
                        <td><?= e($name ?: '—') ?><div class="muted"><?= e($d['taxpayer_number'] ?? '') ?></div></td>
                        <td><?= e($d['tax_type_code'] ?? '') ?></td>
                        <td><?= e($d['period_name'] ?? '') ?></td>
                        <td><span class="badge <?= ($d['status'] ?? '') === 'SUBMITTED' ? 'b-warn' : 'b-ok' ?>"><?= e($d['status'] ?? '—') ?></span></td>
                        <td><?= e($d['submitted_at'] ?? '—') ?></td>
                        <td><a class="button secondary" href="<?= e(app_url('/declarations/' . $d['declaration_id'])) ?>">Review</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
