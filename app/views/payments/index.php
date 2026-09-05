<?php
$payments = $payments ?? [];
$isTaxpayerPortal = function_exists('is_taxpayer') && is_taxpayer();
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
?>
<h1><?= $isTaxpayerPortal ? 'My Payments' : 'Payments' ?></h1>
<p class="muted">Recorded payments; verification updates the linked assessment balance automatically.</p>

<table>
    <thead><tr><th>Reference</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if ($payments === []): ?>
        <tr><td colspan="6">No payments recorded.</td></tr>
    <?php else: foreach ($payments as $p): ?>
        <tr>
            <td><?= e($p['payment_reference'] ?? '') ?></td>
            <td><strong><?= $money($p['amount']) ?></strong></td>
            <td><?= e($p['payment_method'] ?? '') ?></td>
            <td><?= e($p['payment_date'] ?? '') ?></td>
            <td><span class="badge <?= match ($p['status'] ?? '') { 'VERIFIED','SUCCESS' => 'b-ok', 'PENDING' => 'b-warn', default => 'b-info' } ?>"><?= e($p['status'] ?? '') ?></span></td>
            <td style="display:flex;gap:8px">
                <a class="button secondary" href="<?= e(app_url('/payments/' . $p['payment_id'])) ?>">Open</a>
                <?php if (!$isTaxpayerPortal && ($p['status'] ?? '') === 'PENDING'): ?>
                    <form method="post" action="<?= e(app_url('/payments/' . $p['payment_id'] . '/verify')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button" type="submit">Verify</button></form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php if (!$isTaxpayerPortal): ?><div class="actions"><a class="button secondary" href="<?= e(app_url('/reports/payments')) ?>">Payments report</a></div><?php endif; ?>
