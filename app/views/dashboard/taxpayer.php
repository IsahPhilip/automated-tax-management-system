<?php
$taxpayer = $taxpayer ?? null;
$declarations = $declarations ?? [];
$assessments = $assessments ?? [];
$payments = $payments ?? [];
$receipts = $receipts ?? [];
$outstanding = (float) ($outstanding ?? 0);
$currentPeriod = $currentPeriod ?? null;
$scripts = ['/assets/js/dashboard.js'];
?>
<div class="dashboard-shell">
    <h1>Taxpayer Dashboard</h1>
    <p class="muted">
        Welcome back. Here is your current tax position for
        <?= e($currentPeriod['period_name'] ?? 'the current period') ?>.
    </p>

    <?php if (!$taxpayer): ?>
        <div class="flash flash-warning">No taxpayer profile is linked to your account yet. Please contact a revenue officer to complete your registration.</div>
    <?php endif; ?>

    <div class="dashboard-metrics">
        <div class="dashboard-metric"><span class="label">Outstanding balance</span><strong>₦<?= number_format($outstanding, 2) ?></strong></div>
        <div class="dashboard-metric"><span class="label">My declarations</span><strong><?= count($declarations) ?></strong></div>
        <div class="dashboard-metric"><span class="label">My assessments</span><strong><?= count($assessments) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Receipts issued</span><strong><?= count($receipts) ?></strong></div>
    </div>

    <div class="dashboard-section">
        <h2>Recent declarations</h2>
        <table>
            <thead><tr><th>Type</th><th>Status</th><th>Created</th><th></th></tr></thead>
            <tbody>
                <?php if ($declarations === []): ?><tr><td colspan="4">You have not submitted any declaration yet.</td></tr>
                <?php else: foreach ($declarations as $d): ?>
                    <tr>
                        <td><?= e($d['tax_type_id'] == 1 ? 'Personal Income Tax' : 'Corporate Income Tax') ?></td>
                        <td><span class="badge <?= in_array($d['status'], ['APPROVED', 'ISSUED', 'PAID'], true) ? 'b-ok' : (($d['status'] ?? '') === 'REJECTED' ? 'b-bad' : 'b-info') ?>"><?= e($d['status'] ?? '—') ?></span></td>
                        <td><?= e($d['created_at'] ?? '') ?></td>
                        <td><a class="button secondary" href="<?= e(app_url('/taxpayer/declarations/' . $d['declaration_id'])) ?>">Open</a></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-section">
        <h2>Assessments &amp; balances</h2>
        <table>
            <thead><tr><th>Assessment</th><th>Total liability</th><th>Paid</th><th>Balance due</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php if ($assessments === []): ?><tr><td colspan="6">No assessments yet.</td></tr>
                <?php else: foreach ($assessments as $a): ?>
                    <tr>
                        <td><?= e($a['assessment_number'] ?? '') ?></td>
                        <td>₦<?= number_format((float) ($a['total_liability'] ?? 0), 2) ?></td>
                        <td>₦<?= number_format((float) ($a['amount_paid'] ?? 0), 2) ?></td>
                        <td><strong>₦<?= number_format((float) ($a['balance_due'] ?? 0), 2) ?></strong></td>
                        <td><span class="badge <?= in_array($a['status'], ['PAID'], true) ? 'b-ok' : 'b-warn' ?>"><?= e($a['status'] ?? '') ?></span></td>
                        <td><?php if (in_array($a['status'] ?? '', ['ISSUED', 'PARTIALLY_PAID'], true)): ?><a class="button" href="<?= e(app_url('/payments/create?assessment=' . $a['assessment_id'])) ?>">Pay now</a><?php else: ?><a class="button secondary" href="<?= e(app_url('/taxpayer/assessments/' . $a['assessment_id'])) ?>">View</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="quick-actions">
        <a class="button" href="<?= e(app_url('/taxpayer/declarations/create')) ?>">Submit new declaration</a>
        <a class="button secondary" href="<?= e(app_url('/taxpayer/payments')) ?>">Payment history</a>
    </div>
</div>
