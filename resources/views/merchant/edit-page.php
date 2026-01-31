<?php
$sidebar = '<div class="brand">لوحة التاجر</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/merchant">لوحة التحكم</a>'
    . '<a class="nav-link" href="/merchant/settings">الإعدادات</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'تعديل صفحة الهبوط';
$subtitle = 'قم بتحديث المحتوى المسموح به.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/pages/update" class="vstack gap-3" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
            <?php foreach ($fields as $field) : ?>
                <?php if ((int) $field['is_editable_by_merchant'] !== 1) : ?>
                    <div class="locked-field">الحقل مقفل: <?= htmlspecialchars($field['label']) ?></div>
                    <?php continue; ?>
                <?php endif; ?>
                <div>
                    <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
                    <?php if ($field['field_type'] === 'textarea') : ?>
                        <textarea name="field_<?= htmlspecialchars($field['field_key']) ?>" class="form-control" rows="4"><?= htmlspecialchars($pageData[$field['field_key']] ?? '') ?></textarea>
                        <?php if ($field['field_key'] === 'gallery') : ?>
                            <?php $galleryItems = array_filter(array_map('trim', explode(',', $pageData['gallery'] ?? ''))); ?>
                            <?php if ($galleryItems) : ?>
                                <div class="row g-3 mt-3">
                                    <?php foreach ($galleryItems as $item) : ?>
                                        <div class="col-md-4">
                                            <div class="card app-card p-2">
                                                <img src="<?= htmlspecialchars($item) ?>" alt="gallery" class="img-fluid rounded">
                                                <input type="text" name="gallery_existing[]" class="form-control mt-2" value="<?= htmlspecialchars($item) ?>">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="gallery_remove[]" value="<?= htmlspecialchars($item) ?>" id="remove_<?= md5($item) ?>">
                                                    <label class="form-check-label" for="remove_<?= md5($item) ?>">حذف الصورة</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="gallery_files[]" class="form-control mt-3" multiple accept="image/*">
                            <div class="form-text text-secondary">يمكنك إضافة روابط أو رفع صور وسيتم حفظها تلقائياً. يمكنك تعديل الرابط أو تحديد الحذف لكل صورة.</div>
                        <?php endif; ?>
                    <?php else : ?>
                        <input type="text" name="field_<?= htmlspecialchars($field['field_key']) ?>" class="form-control" value="<?= htmlspecialchars($pageData[$field['field_key']] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $page['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">الصفحة نشطة</label>
            </div>
            <button class="btn btn-accent" type="submit">حفظ التغييرات</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
