<?php
$assessments = $assessments ?? [];
$isTaxpayerPortal = function_exists('is_taxpayer') && is_taxpayer();
?>
<h1><?= $isTaxpayerPortal ? 'My Assessments' : 'Assessments' ?></h1>
<p class="muted">Automated assessment records with payable balances and workflow status.</p>

<table>
    <thead><tr><th>Number</th><th>Taxable income</th><th>Penalties</th><th>Interest</th><th>Total liability</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if ($assessments === []): ?>
        <tr><td colspan="9">No assessments found.</td></tr>
    <?php else: foreach ($assessments as $a): ?>
        <tr>
            <td><?= e($a['assessment_number'] ?? '') ?></td>
            <td>₦<?= number_format((float) ($a['taxable_income'] ?? 0), 2) ?></td>
            <td>₦<?= number_format((float) ($a['penalties'] ?? 0), 2) ?></td>
            <td>₦<?= number_format((float) ($a['interest'] ?? 0), 2) ?></td>
            <td><strong>₦<?= number_format((float) ($a['total_liability'] ?? 0), 2) ?></strong></td>
            <td>₦<?= number_format((float) ($a['amount_paid'] ?? 0), 2) ?></td>
            <td><strong style="color:var(--brand-dark)">₦<?= number_format((float) ($a['balance_due'] ?? 0), 2) ?></strong></td>
            <td><span class="badge <?= match ($a['status'] ?? '') { 'PAID' => 'b-ok', 'ISSUED','APPROVED' => 'b-info', 'PARTIALLY_PAID' => 'b-warn', 'CANCELLED','REJECTED' => 'b-bad', default => 'b-info' } ?>"><?= e($a['status'] ?? '') ?></span></td>
            <td><a class="button secondary" href="<?= e(app_url(($isTaxpayerPortal ? '/taxpayer/assessments/' : '/assessments/') . $a['assessment_id'])) ?>">Open</a></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php if (!$isTaxpayerPortal): ?>
<div class="actions"><a class="button" href="<?= e(app_url('/assessments/create')) ?>">Generate from declaration</a><a class="button secondary" href="<?= e(app_url('/reports/assessments')) ?>">Summary report</a></div>
<?php endif; ?>
