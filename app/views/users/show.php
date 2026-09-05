<?php
$u = $user ?? [];
?>
<h1><?= e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'User #' . ($u['user_id'] ?? '')) ?></h1>
<p class="muted"><span class="badge <?= ($u['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>"><?= e($u['status'] ?? '') ?></span></p>
<dl class="kv">
    <dt>User ID</dt><dd><?= (int) ($u['user_id'] ?? 0) ?></dd>
    <dt>Email</dt><dd><?= e($u['email'] ?? '—') ?></dd>
    <dt>Role ID</dt><dd><?= (int) ($u['role_id'] ?? 0) ?></dd>
    <dt>Phone</dt><dd><?= e($u['phone'] ?? '—') ?></dd>
    <dt>Last login</dt><dd><?= e(($u['last_login'] ?? 'never') ?: 'never') ?></dd>
    <dt>Created</dt><dd><?= e($u['created_at'] ?? '—') ?></dd>
</dl>
<div class="actions">
    <a class="button secondary" href="<?= e(app_url('/users/' . ($u['user_id'] ?? 0) . '/edit')) ?>">Edit</a>
    <a class="button secondary" href="<?= e(app_url('/users')) ?>">All users</a>
</div>
