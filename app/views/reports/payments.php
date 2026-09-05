<?php
$report = $report ?? ['rows' => [], 'total' => 0, 'count' => 0];
$rows = $report['rows'] ?? [];
$total = (float) ($report['total'] ?? 0);
$count = (int) ($report['count'] ?? 0);
$from = $from ?? '';
$to = $to ?? '';
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$title = 'Payment Report';
?>
<h1>Payment Report</h1>
<p class="muted"><?= $count ?> payment(s) captured<?= ($from !== '' || $to !== '') ? ' between ' . e($from ?: 'start') . ' and ' . e($to ?: 'today') : '' ?>; excluded from total while pending verification.</p>

<form method="get" action="<?= e(app_url('/reports/payments')) ?>" style="max-width:520px">
    <label for="from">From date</label><input id="from" name="from" type="date" value="<?= e($from) ?>">
    <label for="to">To date</label><input id="to" name="to" type="date" value="<?= e($to) ?>">
    <div class="actions"><button class="button" type="submit">Apply filter</button></div>
</form>

<table style="margin-top:14px">
    <thead><tr><th>Reference</th><th>Assessment</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
    <tbody>
    <?php if ($rows === []): ?>
        <tr><td colspan="6">No payments match this filter.</td></tr>
    <?php else: foreach ($rows as $p): ?>
        <tr>
            <td><?= e($p['payment_reference'] ?? '') ?></td>
            <td>#<?= (int) ($p['assessment_id'] ?? 0) ?></td>
            <td><strong><?= $money($p['amount']) ?></strong></td>
            <td><?= e($p['payment_method'] ?? '') ?></td>
            <td><?= e($p['payment_date'] ?? '') ?></td>
            <td><span class="badge <?= match ($p['status'] ?? '') { 'VERIFIED','SUCCESS' => 'b-ok', 'PENDING' => 'b-warn', default => 'b-info' } ?>"><?= e($p['status'] ?? '') ?></span></td>
        </tr>
    <?php endforeach; endif; ?>
        <tr>
            <td colspan="2"><strong>Total (verified &amp; processed)</strong></td>
            <td colspan="4"><strong style="color:var(--brand-dark)"><?= $money($total) ?></strong> across <?= $count ?> row(s)</td>
        </tr>
    </tbody>
</table>

<div class="actions" style="margin-top:14px">
    <a class="button" href="<?= e(app_url('/reports/payments/export')) ?>">Export CSV</a>
    <button type="button" class="button secondary js-print">Print report</button>
    <a class="button secondary" href="<?= e(app_url('/payments')) ?>">Record / verify payments</a>
    <a class="button secondary" href="<?= e(app_url('/reports')) ?>">Back to reports</a>
</div>
