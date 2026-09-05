<?php
$report = $report ?? ['payment_count' => 0, 'total_revenue' => 0, 'pending_amount' => 0];
$from = $from ?? '';
$to = $to ?? '';
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$title = 'Revenue Report';
?>
<h1>Revenue Report</h1>
<p class="muted">Collected revenue for <?= e($from !== '' ? $from : 'the beginning') ?> → <?= e($to !== '' ? $to : date('Y-m-d')) ?>.</p>

<form method="get" action="<?= e(app_url('/reports/revenue')) ?>" style="max-width:520px">
    <label for="from">From date</label><input id="from" name="from" type="date" value="<?= e($from) ?>">
    <label for="to">To date</label><input id="to" name="to" type="date" value="<?= e($to) ?>">
    <div class="actions"><button class="button" type="submit">Generate report</button></div>
</form>

<h2>Results</h2>
<div class="grid">
    <div class="metric">Payments recorded<strong><?= (int) $report['payment_count'] ?></strong></div>
    <div class="metric">Total revenue<strong><?= $money($report['total_revenue']) ?></strong></div>
    <div class="metric">Pending verification<strong><?= $money($report['pending_amount']) ?></strong></div>
</div>

<table style="max-width:640px;margin-top:10px">
    <tbody>
        <tr><th scope="row">Verified / successful payments</th><td><?= $money($report['total_revenue']) ?></td></tr>
        <tr><th scope="row">Awaiting officer verification</th><td><?= $money($report['pending_amount']) ?></td></tr>
        <tr><th scope="row">Gross receipts captured</th><td><?= $money((float) $report['total_revenue'] + (float) $report['pending_amount']) ?></td></tr>
    </tbody>
</table>

<div class="actions" style="margin-top:14px">
    <button type="button" class="button js-print">Print report</button>
    <a class="button secondary" href="<?= e(app_url('/reports')) ?>">Back to reports</a>
</div>
