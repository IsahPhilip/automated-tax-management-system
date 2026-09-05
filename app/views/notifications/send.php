<?php use App\Models\User; $users = $users ?? []; ?>
<h1>Send notification</h1>
<p class="muted">Deliver an in-app notice to any registered user (README Â§9 “Taxpayer Notifiedâ€).</p>
<form method="post" action="<?= e(app_url('/notifications/send')) ?>" style="max-width:560px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="user_id">Recipient *</label>
    <select id="user_id" name="user_id" required>
        <option value="">Select user…</option>
        <?php foreach ($users as $u): ?>
            <option value="<?= (int) $u['user_id'] ?>"><?= e('#' . $u['user_id'] . ' · ' . trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) . ' (' . ($u['email'] ?? '') . ')') ?></option>
        <?php endforeach; ?>
    </select>
    <label for="title">Title *</label><input id="title" name="title" maxlength="255" required>
    <label for="message">Message *</label><textarea id="message" name="message" rows="4" maxlength="2000" required></textarea>
    <label for="notification_type">Type *</label>
    <select id="notification_type" name="notification_type" required>
        <?php foreach (['INFO', 'ASSESSMENT', 'PAYMENT', 'RECEIPT', 'SYSTEM'] as $t): ?><option value="<?= $t ?>"><?= ucfirst(strtolower($t)) ?></option><?php endforeach; ?>
    </select>
    <div class="actions"><button class="button" type="submit">Send</button>
        <a class="button secondary" href="<?= e(app_url('/notifications')) ?>">Back</a></div>
</form>
