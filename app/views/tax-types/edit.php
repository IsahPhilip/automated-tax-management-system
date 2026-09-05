<?php $t = $taxType ?? []; ?>
<h1>Edit tax type</h1>
<form method="post" action="<?= e(app_url('/tax-types/' . ($t['tax_type_id'] ?? 0) . '/update')) ?>" style="max-width:560px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="code">Code *</label><input id="code" name="code" maxlength="50" required value="<?= e($t['code'] ?? '') ?>">
    <label for="name">Name *</label><input id="name" name="name" maxlength="255" required value="<?= e($t['name'] ?? '') ?>">
    <label for="description">Description</label><textarea id="description" name="description" rows="3"><?= e($t['description'] ?? '') ?></textarea>
    <div class="actions"><button class="button" type="submit">Save changes</button>
        <a class="button secondary" href="<?= e(app_url('/tax-types')) ?>">Cancel</a></div>
</form>
