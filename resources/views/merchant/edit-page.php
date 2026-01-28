<?php
$sidebar = '<div class="brand">Merchant Panel</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/merchant"><i class="bi bi-speedometer2"></i> Dashboard</a>'
    . '<a class="nav-link" href="/merchant/settings"><i class="bi bi-gear"></i> Settings</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">Sign out</button>'
    . '</form>'
    . '</nav>';
$title = 'Edit Landing Page';
$subtitle = 'Update content that is editable for merchants.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/pages/update" class="vstack gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
            <?php foreach ($fields as $field) : ?>
                <?php if ((int) $field['is_editable_by_merchant'] !== 1) : ?>
                    <div class="locked-field">Locked: <?= htmlspecialchars($field['label']) ?></div>
                    <?php continue; ?>
                <?php endif; ?>
                <div>
                    <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
                    <?php if ($field['field_type'] === 'textarea') : ?>
                        <textarea name="field_<?= htmlspecialchars($field['field_key']) ?>" class="form-control" rows="4"><?= htmlspecialchars($pageData[$field['field_key']] ?? '') ?></textarea>
                    <?php else : ?>
                        <input type="text" name="field_<?= htmlspecialchars($field['field_key']) ?>" class="form-control" value="<?= htmlspecialchars($pageData[$field['field_key']] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $page['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Page is active</label>
            </div>
            <button class="btn btn-accent" type="submit">Save changes</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
