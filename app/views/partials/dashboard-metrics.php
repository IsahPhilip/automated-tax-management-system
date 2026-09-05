<?php
declare(strict_types=1);

$taxpayerCount = (new \App\Models\Taxpayer())->count();
$declarationCount = (new \App\Models\TaxDeclaration())->count();
$assessmentCount = (new \App\Models\TaxAssessment())->count();
$paymentCount = (new Payment())->count();
?>
<h1><?= e($title ?? 'Dashboard') ?></h1>
<p class="muted">Operational snapshot for the Automated Tax Management System.</p>
<div class="grid">
    <div class="metric">Taxpayers<strong><?= $taxpayerCount ?></strong></div>
    <div class="metric">Declarations<strong><?= $declarationCount ?></strong></div>
    <div class="metric">Assessments<strong><?= $assessmentCount ?></strong></div>
    <div class="metric">Payments<strong><?= $paymentCount ?></strong></div>
</div>
<div class="actions">
    <a class="button" href="<?= e(app_url('/taxpayers/create')) ?>">Register taxpayer</a>
    <a class="button secondary" href="<?= e(app_url('/assessments')) ?>">Review assessments</a>
    <a class="button secondary" href="<?= e(app_url('/reports/revenue')) ?>">Revenue report</a>
</div>
