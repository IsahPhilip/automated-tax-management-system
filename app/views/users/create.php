<?php $roles = $roles ?? []; $scripts = ['/assets/js/validation.js']; ?>
<h1>Create system user</h1>
<form method="post" action="<?= e(app_url('/users')) ?>" style="max-width:560px" data-validate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="first_name">First name *</label><input id="first_name" name="first_name" maxlength="100" required>
    <label for="last_name">Last name *</label><input id="last_name" name="last_name" maxlength="100" required>
    <label for="email">Email *</label><input id="email" name="email" type="email" maxlength="255" required>
    <label for="phone">Phone</label><input id="phone" name="phone">
    <label for="role_id">Role *</label>
    <select id="role_id" name="role_id" required>
        <option value="">Select…</option>
        <?php foreach ($roles as $r): ?><option value="<?= (int) $r['role_id'] ?>"><?= e($r['role_name']) ?></option><?php endforeach; ?>
    </select>
    <label for="password">Password * <span class="muted">(8+ chars incl. upper/lower/number)</span></label><input id="password" name="password" type="password" minlength="8" required>
    <label for="password_confirmation">Confirm password *</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
    <div class="actions"><button class="button" type="submit">Create user</button>
        <a class="button secondary" href="<?= e(app_url('/users')) ?>">Cancel</a></div>
</form>
