<?php
declare(strict_types=1);

$result = $taxpayers ?? ['data' => [], 'page' => 1, 'last_page' => 1, 'total' => 0];
$rows = $result['data'] ?? [];
$page = (int) ($result['page'] ?? 1);
$lastPage = (int) ($result['last_page'] ?? 1);
$total = (int) ($result['total'] ?? 0);
?>
<div class="page-head">
    <div>
        <h1>Taxpayers</h1>
        <p class="muted">Search, review, and register taxpayer records.</p>
    </div>
    <a class="button" href="<?= e(app_url('/taxpayers/create')) ?>">Register taxpayer</a>
</div>

<form class="form-filters" action="<?= e(app_url('/taxpayers')) ?>" method="get">
    <label>
        <span>Search</span>
        <input name="search" type="search" value="<?= e($search ?? '') ?>" placeholder="TIN, name, email or phone">
    </label>
    <label>
        <span>Type</span>
        <select name="type">
            <option value="">All taxpayers</option>
            <?php foreach (['INDIVIDUAL' => 'Individual', 'BUSINESS' => 'Business', 'CORPORATE' => 'Corporate'] as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= ($type ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button class="button secondary" type="submit">Apply filters</button>
</form>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <h2>No taxpayers found</h2>
        <p class="muted">Register the first taxpayer or adjust the current filters.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>TIN</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $taxpayer): ?>
                    <?php
                    $displayName = $taxpayer['taxpayer_type'] === 'INDIVIDUAL'
                        ? trim((string) ($taxpayer['first_name'] ?? '') . ' ' . (string) ($taxpayer['last_name'] ?? ''))
                        : (string) ($taxpayer['business_name'] ?? '');
                    ?>
                    <tr>
                        <td><strong><?= e($taxpayer['taxpayer_number'] ?? '') ?></strong></td>
                        <td><?= e($displayName !== '' ? $displayName : 'Unnamed taxpayer') ?></td>
                        <td><?= e(ucfirst(strtolower((string) ($taxpayer['taxpayer_type'] ?? '')))) ?></td>
                        <td>
                            <?= e($taxpayer['email'] ?? '') ?><br>
                            <span class="muted"><?= e($taxpayer['phone'] ?? '') ?></span>
                        </td>
                        <td><span class="badge"><?= e($taxpayer['status'] ?? '') ?></span></td>
                        <td><?= e($taxpayer['registration_date'] ?? '') ?></td>
                        <td><a href="<?= e(app_url('/taxpayers/' . (int) $taxpayer['taxpayer_id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <span class="muted">Showing <?= count($rows) ?> of <?= $total ?> taxpayers</span>
        <div>
            <?php if ($page > 1): ?>
                <a class="button secondary" href="<?= e(app_url('/taxpayers?' . http_build_query(['search' => $search ?? '', 'type' => $type ?? '', 'page' => $page - 1]))) ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $lastPage): ?>
                <a class="button secondary" href="<?= e(app_url('/taxpayers?' . http_build_query(['search' => $search ?? '', 'type' => $type ?? '', 'page' => $page + 1]))) ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
