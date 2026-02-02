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
                <?php $fieldKey = $field['field_key']; ?>
                <?php $isRichText = in_array($fieldKey, ['subheadline', 'description'], true); ?>
                <?php if ((int) $field['is_editable_by_merchant'] !== 1) : ?>
                    <div class="locked-field">الحقل مقفل: <?= htmlspecialchars($field['label']) ?></div>
                    <?php continue; ?>
                <?php endif; ?>
                <div>
                    <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
                    <?php if ($field['field_type'] === 'textarea') : ?>
                        <?php if ($isRichText) : ?>
                            <?php $richId = 'rich_' . htmlspecialchars($fieldKey); ?>
                            <div class="btn-group btn-group-sm mb-2" role="group" aria-label="أدوات التنسيق" data-editor-toolbar="<?= $richId ?>">
                                <button class="btn btn-outline-light" type="button" data-command="bold"><i class="bi bi-type-bold"></i></button>
                                <button class="btn btn-outline-light" type="button" data-command="italic"><i class="bi bi-type-italic"></i></button>
                                <button class="btn btn-outline-light" type="button" data-command="underline"><i class="bi bi-type-underline"></i></button>
                                <button class="btn btn-outline-light" type="button" data-command="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                <button class="btn btn-outline-light" type="button" data-command="createLink"><i class="bi bi-link-45deg"></i></button>
                                <button class="btn btn-outline-light" type="button" data-command="insertImage"><i class="bi bi-image"></i></button>
                            </div>
                            <textarea name="field_<?= htmlspecialchars($fieldKey) ?>" id="<?= $richId ?>" class="form-control d-none rich-source" rows="6"><?= $pageData[$fieldKey] ?? '' ?></textarea>
                            <div class="form-control rich-editor" contenteditable="true" data-target="<?= $richId ?>"><?= $pageData[$fieldKey] ?? '' ?></div>
                            <div class="form-text text-secondary">يمكنك كتابة وصف غني وإضافة صور وروابط. سيتم حفظ المحتوى كما يظهر في المعاينة.</div>
                        <?php else : ?>
                            <textarea name="field_<?= htmlspecialchars($fieldKey) ?>" class="form-control" rows="4"><?= htmlspecialchars($pageData[$fieldKey] ?? '') ?></textarea>
                        <?php endif; ?>
                        <?php if ($fieldKey === 'gallery') : ?>
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
                        <input type="text" name="field_<?= htmlspecialchars($fieldKey) ?>" class="form-control" value="<?= htmlspecialchars($pageData[$fieldKey] ?? '') ?>">
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
<script>
    document.querySelectorAll('.rich-editor').forEach((editor) => {
        const targetId = editor.dataset.target;
        const targetInput = document.getElementById(targetId);
        const syncContent = () => {
            if (targetInput) {
                targetInput.value = editor.innerHTML.trim();
            }
        };
        editor.addEventListener('input', syncContent);
        syncContent();
    });

    document.querySelectorAll('[data-editor-toolbar]').forEach((toolbar) => {
        const targetId = toolbar.dataset.editorToolbar;
        const editor = document.querySelector(`.rich-editor[data-target="${targetId}"]`);
        if (!editor) return;
        toolbar.addEventListener('click', (event) => {
            const button = event.target.closest('[data-command]');
            if (!button) return;
            const command = button.dataset.command;
            if (command === 'createLink') {
                const url = prompt('أدخل رابط:', 'https://');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            } else if (command === 'insertImage') {
                const url = prompt('أدخل رابط الصورة:', 'https://');
                if (url) {
                    document.execCommand('insertImage', false, url);
                }
            } else {
                document.execCommand(command, false, null);
            }
            editor.focus();
        });
    });
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
