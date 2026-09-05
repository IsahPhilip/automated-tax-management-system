<?php
$report = $report ?? [
    'total_assessments' => 0,
    'assessed_amount' => 0,
    'collected_amount' => 0,
    'outstanding_amount' => 0,
];
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$collected = (float) ($report['collected_amount'] ?? 0);
$outstanding = (float) ($report['outstanding_amount'] ?? 0);
$collectionRate = ($collected + $outstanding) > 0 ? number_format($collected * 100 / ($collected + $outstanding), 1) : '0';
$title = 'Assessment Report';
?>
<h1>Assessment Report</h1>
<p class="muted">Aggregate assessment position as at <?= date('d M Y') ?>.</p>

<div class="grid">
    <div class="metric">Live assessments<strong><?= (int) ($report['total_assessments'] ?? 0) ?></strong></div>
    <div class="metric">Amount assessed<strong><?= $money($report['assessed_amount']) ?></strong></div>
    <div class="metric">Collected<strong><?= $money($collected) ?></strong></div>
    <div class="metric">Outstanding<strong><?= $money($outstanding) ?></strong></div>
</div>

<table style="max-width:720px;margin-top:10px">
    <thead><tr><th>Metric</th><th>Value</th></tr></thead>
    <tbody>
        <tr><th scope="row">Assessments raised (excl. drafts/cancelled)</th><td><?= (int) ($report['total_assessments'] ?? 0) ?></td></tr>
        <tr><th scope="row">Total tax assessed</th><td><?= $money($report['assessed_amount']) ?></td></tr>
        <tr><th scope="row">Payments applied</th><td><?= $money($collected) ?></td></tr>
        <tr><th scope="row">Balance outstanding</th><td><?= $money($outstanding) ?></td></tr>
        <tr><th scope="row">Collection rate</th><td><strong><?= $collectionRate ?>%</strong></td></tr>
    </tbody>
</table>

<div class="actions" style="margin-top:14px">
    <button type="button" class="button js-print">Print report</button>
    <a class="button secondary" href="<?= e(app_url('/assessments')) ?>">Browse assessments</a>
    <a class="button secondary" href="<?= e(app_url('/reports')) ?>">Back to reports</a>
</div>
