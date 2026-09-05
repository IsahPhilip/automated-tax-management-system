<?php
$receipts = $receipts ?? [];
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
?>
<h1><?= function_exists('is_taxpayer') && is_taxpayer() ? 'My Receipts' : 'Receipts' ?></h1>
<p class="muted">Official receipts generated when payments are verified.</p>

<table>
    <thead><tr><th>Receipt number</th><th>Amount</th><th>Issued</th><th></th></tr></thead>
    <tbody>
    <?php if ($receipts === []): ?>
        <tr><td colspan="4">No receipts yet.</td></tr>
    <?php else: foreach ($receipts as $r): ?>
        <tr>
            <td><strong><?= e($r['receipt_number'] ?? '') ?></strong></td>
            <td><?= $money($r['amount']) ?></td>
            <td><?= e($r['issued_at'] ?? '') ?></td>
            <td><a class="button secondary" href="<?= e(app_url('/receipts/' . $r['receipt_id'])) ?>">View</a></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
