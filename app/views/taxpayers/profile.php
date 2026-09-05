<?php $t = $taxpayer ?? null; $money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2); ?>
<h1>My Profile</h1>
<?php if (!$t): ?>
    <div class="flash flash-warning">Your taxpayer profile has not been created yet — please contact a revenue officer.</div>
<?php else: ?>
<p class="muted">TIN <strong><?= e($t['taxpayer_number']) ?></strong> · keep your contact details current.</p>
<form method="post" action="<?= e(app_url('/taxpayer/profile')) ?>" style="max-width:640px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="first_name">First name</label><input id="first_name" name="first_name" value="<?= e($t['first_name'] ?? '') ?>">
    <label for="last_name">Last name</label><input id="last_name" name="last_name" value="<?= e($t['last_name'] ?? '') ?>">
    <?php if (($t['business_name'] ?? null) !== null && $t['business_name'] !== ''): ?>
        <label for="business_name">Business name</label><input id="business_name" name="business_name" value="<?= e($t['business_name']) ?>">
    <?php endif; ?>
    <label for="email">Email *</label><input id="email" name="email" type="email" required value="<?= e($t['email'] ?? '') ?>">
    <label for="phone">Phone *</label><input id="phone" name="phone" required value="<?= e($t['phone'] ?? '') ?>">
    <label for="address">Address</label><textarea id="address" name="address" rows="2"><?= e($t['address'] ?? '') ?></textarea>
    <label for="city">City</label><input id="city" name="city" value="<?= e($t['city'] ?? '') ?>">
    <label for="state">State</label><input id="state" name="state" value="<?= e($t['state'] ?? '') ?>">
    <div class="actions"><button class="button" type="submit">Save profile</button></div>
</form>
<?php endif; ?>
