<?php $u = $user ?? []; $roles = $roles ?? []; $scripts = ['/assets/js/validation.js']; ?>
<h1>Edit user #<?= (int) ($u['user_id'] ?? 0) ?></h1>
<form method="post" action="<?= e(app_url('/users/' . ($u['user_id'] ?? 0) . '/update')) ?>" style="max-width:560px" data-validate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="first_name">First name *</label><input id="first_name" name="first_name" maxlength="100" required value="<?= e($u['first_name'] ?? '') ?>">
    <label for="last_name">Last name *</label><input id="last_name" name="last_name" maxlength="100" required value="<?= e($u['last_name'] ?? '') ?>">
    <label for="email">Email *</label><input id="email" name="email" type="email" required value="<?= e($u['email'] ?? '') ?>">
    <label for="phone">Phone</label><input id="phone" name="phone" value="<?= e($u['phone'] ?? '') ?>">
    <label for="role_id">Role *</label>
    <select id="role_id" name="role_id" required>
        <?php foreach ($roles as $r): ?><option value="<?= (int) $r['role_id'] ?>" <?= ((int) ($u['role_id'] ?? 0) === (int) $r['role_id'] ? 'selected' : '') ?>><?= e($r['role_name']) ?></option><?php endforeach; ?>
    </select>
    <label for="password">New password <span class="muted">(leave blank to keep current)</span></label><input id="password" name="password" type="password">
    <div class="actions"><button class="button" type="submit">Save changes</button>
        <a class="button secondary" href="<?= e(app_url('/users/' . ($u['user_id'] ?? 0))) ?>">Cancel</a></div>
</form>
