<?php
$declarations = $declarations ?? [];
$isStaffView = isset($pendingReviewCount);
$statusFilter = $statusFilter ?? '';
?>
<h1><?= $isStaffView ? 'Declaration Review' : 'My Declarations' ?></h1>
<p class="muted">
    <?= $isStaffView
        ? 'Taxpayer filings queue. Approve a submitted declaration to generate its assessment automatically.'
        : 'Track your filings from draft through approval.' ?>
</p>

<?php if ($isStaffView): ?>
    <div class="filters">
        <a class="chip <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= e(app_url('/declarations')) ?>">All</a>
        <?php foreach (['SUBMITTED', 'DRAFT', 'APPROVED', 'REJECTED'] as $st): ?>
            <a class="chip <?= $statusFilter === $st ? 'active' : '' ?>" href="<?= e(app_url('/declarations?status=' . $st)) ?>"><?= e($st) ?></a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="actions" style="margin-bottom:14px"><a class="button" href="<?= e(app_url('/taxpayer/declarations/create')) ?>">New declaration</a></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <?php if ($isStaffView): ?><th>Taxpayer</th><?php endif; ?>
            <th>Type</th><th>Period</th><th>Gross / Turnover</th><th>Status</th><th>Updated</th><th></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($declarations === []): ?>
            <tr><td colspan="8">Nothing to show.</td></tr>
        <?php else: foreach ($declarations as $d): ?>
            <tr>
                <td><?= (int) $d['declaration_id'] ?></td>
                <?php if ($isStaffView): ?>
                    <td><?= e(trim(($d['business_name'] ?? '') !== '' ? $d['business_name'] : trim(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? '')))) ?></td>
                <?php endif; ?>
                <td><?= e($d['tax_type_code'] ?? ('#' . ($d['tax_type_id'] ?? ''))) ?></td>
                <td><?= e($d['period_name'] ?? ('#' . ($d['period_id'] ?? ''))) ?></td>
                <td>₦<?= number_format((float) (($d['gross_income'] ?? 0) ?: ($d['annual_turnover'] ?? 0)), 2) ?></td>
                <td><span class="badge <?= match ($d['status'] ?? '') { 'APPROVED','ISSUED','PAID' => 'b-ok', 'REJECTED' => 'b-bad', 'SUBMITTED' => 'b-warn', default => 'b-info' } ?>"><?= e($d['status'] ?? '') ?></span></td>
                <td><?= e(($d['submitted_at'] ?? $d['created_at']) ?? '') ?></td>
                <td><a class="button secondary" href="<?= e(app_url('/declarations/' . $d['declaration_id'])) ?>"><?= $isStaffView ? 'Review' : 'Open' ?></a></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>
