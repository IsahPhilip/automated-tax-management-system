<?php
$taxpayer = $taxpayer ?? [];
$tid = (int) ($taxpayer['taxpayer_id'] ?? 0);
$count = static function (callable $fn, int $fallback = 0) use ($tid): int { try { return count($fn()); } catch (\Throwable) { return $fallback; } };
?>
<h1><?= e(($taxpayer['business_name'] ?? '') ?: trim(($taxpayer['first_name'] ?? '') . ' ' . ($taxpayer['last_name'] ?? '')) ?: 'Taxpayer profile') ?></h1>
<p class="muted">TIN <?= e($taxpayer['taxpayer_number'] ?? '—') ?> ·
   <span class="badge <?= ($taxpayer['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>"><?= e($taxpayer['status'] ?? '') ?></span>
</p>

<dl class="kv">
    <dt>Type</dt><dd><?= e($taxpayer['taxpayer_type'] ?? '—') ?></dd>
    <dt>Email</dt><dd><?= e($taxpayer['email'] ?? '—') ?></dd>
    <dt>Phone</dt><dd><?= e($taxpayer['phone'] ?? '—') ?></dd>
    <dt>Address</dt><dd><?= e(trim(($taxpayer['address'] ?? '') . ', ' . ($taxpayer['city'] ?? '') . ', ' . ($taxpayer['state'] ?? ''), ', ') ?: '—') ?></dd>
    <dt>Identification</dt><dd><?= e(($taxpayer['identification_type'] ?? '—') . ' ' . ($taxpayer['identification_number'] ?? '')) ?></dd>
    <dt>Registered</dt><dd><?= e($taxpayer['registration_date'] ?? '') ?></dd>
    <dt>Annual turnover</dt><dd>₦<?= number_format((float) ($taxpayer['annual_turnover'] ?? 0), 2) ?></dd>
</dl>

<h2>Tax position snapshot</h2>
<?php
$assessments = []; try { $assessments = (new \App\Models\TaxAssessment())->findByTaxpayer($tid); } catch (\Throwable) {}
$outstanding = 0; foreach ($assessments as $a) { if (!in_array($a['status'] ?? '', ['CANCELLED','DRAFT'], true)) $outstanding += (float) ($a['balance_due'] ?? 0); }
?>
<div class="grid">
    <div class="metric">Declarations<strong><?= $count(fn () => (new \App\Models\TaxDeclaration())->forTaxpayer($tid)) ?></strong></div>
    <div class="metric">Assessments<strong><?= count($assessments) ?></strong></div>
    <div class="metric">Payments<strong><?= $count(fn () => (new Payment())->forTaxpayer($tid)) ?></strong></div>
    <div class="metric">Outstanding<strong>₦<?= number_format($outstanding, 2) ?></strong></div>
</div>

<h2>Actions</h2>
<div class="actions">
    <a class="button secondary" href="<?= e(app_url('/taxpayers/search?search=' . urlencode((string) ($taxpayer['taxpayer_number'] ?? '')))) ?>">Related declarations</a>
    <a class="button secondary" href="<?= e(app_url('/payments/create')) ?>">Record a payment</a>
    <a class="button secondary" href="<?= e(app_url('/taxpayers/' . $tid . '/edit')) ?>">Edit taxpayer</a>
</div>
