<?php $t = $taxpayer ?? []; ?>
<h1>Edit taxpayer</h1>
<p class="muted"><?= e($t['taxpayer_number'] ?? '') ?> · registered <?= e($t['registration_date'] ?? '') ?></p>

<form method="post" action="<?= e(app_url('/taxpayers/' . ($t['taxpayer_id'] ?? 0) . '/update')) ?>" style="max-width:760px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="first_name">First name</label><input id="first_name" name="first_name" value="<?= e($t['first_name'] ?? '') ?>">
    <label for="last_name">Last name</label><input id="last_name" name="last_name" value="<?= e($t['last_name'] ?? '') ?>">
    <label for="business_name">Business name</label><input id="business_name" name="business_name" value="<?= e($t['business_name'] ?? '') ?>">
    <label for="email">Email *</label><input id="email" name="email" type="email" required value="<?= e($t['email'] ?? '') ?>">
    <label for="phone">Phone *</label><input id="phone" name="phone" required value="<?= e($t['phone'] ?? '') ?>">
    <label for="identification_type">Identification type</label><input id="identification_type" name="identification_type" value="<?= e($t['identification_type'] ?? '') ?>">
    <label for="identification_number">Identification number</label><input id="identification_number" name="identification_number" value="<?= e($t['identification_number'] ?? '') ?>">
    <label for="address">Address</label><textarea id="address" name="address" rows="2"><?= e($t['address'] ?? '') ?></textarea>
    <label for="city">City</label><input id="city" name="city" value="<?= e($t['city'] ?? '') ?>">
    <label for="state">State</label><input id="state" name="state" value="<?= e($t['state'] ?? '') ?>">
    <label for="annual_turnover">Annual turnover (₦)</label><input id="annual_turnover" name="annual_turnover" type="number" step="0.01" value="<?= e((string) ($t['annual_turnover'] ?? '0')) ?>">
    <label for="fixed_assets_value">Fixed assets value (₦)</label><input id="fixed_assets_value" name="fixed_assets_value" type="number" step="0.01" value="<?= e((string) ($t['fixed_assets_value'] ?? '0')) ?>">
    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="ACTIVE" <?= (($t['status'] ?? '') === 'ACTIVE' ? 'selected' : '') ?>>Active</option>
        <option value="INACTIVE" <?= (($t['status'] ?? '') === 'INACTIVE' ? 'selected' : '') ?>>Inactive</option>
    </select>
    <div class="actions"><button class="button" type="submit">Save changes</button>
        <a class="button secondary" href="<?= e(app_url('/taxpayers/' . ($t['taxpayer_id'] ?? 0))) ?>">Cancel</a></div>
</form>
