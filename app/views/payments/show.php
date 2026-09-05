<?php
$payment = $payment ?? [];
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$receipt = null;
try { $receipt = (new \App\Models\Receipt())->forPayment((int) ($payment['payment_id'] ?? 0)); } catch (\Throwable $e) {}
?>
<h1>Payment <?= e($payment['payment_reference'] ?? '#' . ($payment['payment_id'] ?? '')) ?></h1>
<p class="muted">
    Recorded <?= e($payment['created_at'] ?? '') ?>
    <span class="badge <?= match ($payment['status'] ?? '') { 'VERIFIED','SUCCESS' => 'b-ok', 'PENDING' => 'b-warn', default => 'b-info' } ?>" style="margin-left:8px"><?= e($payment['status'] ?? '') ?></span>
</p>

<dl class="kv">
    <dt>Amount</dt><dd><strong style="font-size:19px"><?= $money($payment['amount']) ?></strong></dd>
    <dt>Method</dt><dd><?= e($payment['payment_method'] ?? '—') ?></dd>
    <dt>Payment date</dt><dd><?= e($payment['payment_date'] ?? '—') ?></dd>
    <dt>Transaction ref.</dt><dd><?= e($payment['transaction_reference'] ?? '—') ?></dd>
    <dt>Assessment ID</dt><dd><?= (int) ($payment['assessment_id'] ?? 0) ?></dd>
    <?php if (!empty($payment['notes'])): ?><dt>Notes</dt><dd><?= nl2br(e((string) $payment['notes'])) ?></dd><?php endif; ?>
    <?php if (($payment['verified_by'] ?? null)): ?><dt>Verified by user</dt><dd><?= e((string) $payment['verified_by']) ?> at <?= e($payment['verified_at'] ?? '') ?></dd><?php endif; ?>
</dl>

<?php if (function_exists('is_staff') && is_staff() && ($payment['status'] ?? '') === 'PENDING'): ?>
    <form method="post" action="<?= e(app_url('/payments/' . $payment['payment_id'] . '/verify')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="actions"><button class="button" type="submit">Verify payment &amp; issue receipt</button></div>
    </form>
<?php elseif ($receipt): ?>
    <div class="actions">
        <a class="button" href="<?= e(app_url('/receipts/' . $receipt['receipt_id'])) ?>">View receipt <?= e($receipt['receipt_number'] ?? '') ?></a>
    </div>
<?php endif; ?>

<div class="actions" style="margin-top:6px"><a class="button secondary" href="<?= e(app_url('/payments')) ?>">All payments</a></div>
