<?php
$receipt = $receipt ?? [];
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);

$payment = null; $assessment = null; $taxpayer = null;
try { $payment = (new Payment())->find((int) ($receipt['payment_id'] ?? 0)); } catch (\Throwable $e) {}
try { $assessment = $payment ? (new \App\Models\TaxAssessment())->find((int) $payment['assessment_id']) : null; } catch (\Throwable $e) {}
try { $taxpayer = (new \App\Models\Taxpayer())->find((int) ($receipt['taxpayer_id'] ?? 0)); } catch (\Throwable $e) {}
$name = $taxpayer ? (($taxpayer['business_name'] ?? '') ?: trim(($taxpayer['first_name'] ?? '') . ' ' . ($taxpayer['last_name'] ?? ''))) : '—';
?>
<h1>Official Receipt</h1>
<p class="muted"><?= e(APP_NAME) ?> · Issued <?= e($receipt['issued_at'] ?? '') ?></p>

<div class="card" style="border-top:6px solid var(--brand)">
    <dl class="kv">
        <dt>Receipt number</dt><dd style="font-size:20px;color:var(--brand-dark)"><?= e($receipt['receipt_number'] ?? '—') ?></dd>
        <dt>Taxpayer</dt><dd><?= e($name) ?> <?= $taxpayer ? '· TIN ' . e($taxpayer['taxpayer_number'] ?? '') : '' ?></dd>
        <dt>Payment reference</dt><dd><?= e($payment['payment_reference'] ?? '—') ?></dd>
        <dt>Assessment number</dt><dd><?= e($assessment['assessment_number'] ?? '—') ?></dd>
        <dt>Amount paid</dt><dd><strong style="font-size:19px"><?= $money($receipt['amount']) ?></strong></dd>
        <dt>Payment method</dt><dd><?= e($payment['payment_method'] ?? '—') ?></dd>
        <dt>Payment date</dt><dd><?= e($payment['payment_date'] ?? '—') ?></dd>
        <?php if ($assessment): ?><dt>Total liability</dt><dd><?= $money($assessment['total_liability']) ?></dd><?php endif; ?>
        <?php if ($assessment): ?><dt>Remaining balance</dt><dd><strong><?= $money($assessment['balance_due']) ?></strong></dd><?php endif; ?>
    </dl>
</div>

<div class="actions">
    <button type="button" class="button js-print">Print</button>
    <a class="button secondary" href="<?= e(app_url('/payments/' . ($payment['payment_id'] ?? 0))) ?>">View payment</a>
    <a class="button secondary" href="<?= e(app_url('/receipts')) ?>">All receipts</a>
</div>
