<h1>New tax type</h1>
<form method="post" action="<?= e(app_url('/tax-types')) ?>" style="max-width:560px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="code">Code * <span class="muted">(e.g. PIT, CIT)</span></label><input id="code" name="code" maxlength="50" required style="text-transform:uppercase">
    <label for="name">Name *</label><input id="name" name="name" maxlength="255" required>
    <label for="description">Description</label><textarea id="description" name="description" rows="3" maxlength="1000"></textarea>
    <div class="actions"><button class="button" type="submit">Create tax type</button>
        <a class="button secondary" href="<?= e(app_url('/tax-types')) ?>">Cancel</a></div>
</form>
