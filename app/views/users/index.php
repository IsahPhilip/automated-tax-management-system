<?php $users = $users ?? []; ?>
<h1>System Users</h1>
<p class="muted">Staff accounts with role-based access to portals and admin functions.</p>
<div class="actions" style="margin-bottom:14px"><a class="button" href="<?= e(app_url('/users/create')) ?>">Create user</a></div>

<table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <?php if (($u['role_id'] ?? 0) === null) continue; ?>
        <tr>
            <td><?= (int) $u['user_id'] ?></td>
            <td><?= e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?></td>
            <td><?= e($u['email'] ?? '') ?></td>
            <td><?= e($u['role_name'] ?? ('#' . ($u['role_id'] ?? ''))) ?></td>
            <td><span class="badge <?= ($u['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>"><?= e($u['status'] ?? '') ?></span></td>
            <td><?= e(($u['last_login'] ?? 'never') ?: 'never') ?></td>
            <td style="display:flex;gap:8px">
                <a class="button secondary" href="<?= e(app_url('/users/' . $u['user_id'])) ?>">View</a>
                <a class="button secondary" href="<?= e(app_url('/users/' . $u['user_id'] . '/edit')) ?>">Edit</a>
                <form method="post" action="<?= e(app_url('/users/' . $u['user_id'] . '/' . ((($u['status'] ?? '') === 'ACTIVE') ? 'deactivate' : 'activate'))) ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button class="button secondary" type="submit"><?= (($u['status'] ?? '') === 'ACTIVE' ? 'Deactivate' : 'Activate') ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
