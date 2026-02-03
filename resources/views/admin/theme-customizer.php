<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link active" href="/admin/customizer">مخصص الصفحات</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'مخصص الصفحات';
$subtitle = 'إدارة الأقسام والكتل الخاصة بكل صفحة مع معاينة مباشرة.';
ob_start();
?>

<?php if (!$page) : ?>
    <div class="card app-card">
        <div class="card-body">
            <h5 class="mb-3">اختر صفحة لبدء التخصيص</h5>
            <div class="list-group">
                <?php foreach ($pages as $item) : ?>
                    <a class="list-group-item list-group-item-action bg-transparent text-light" href="/admin/customizer?page_id=<?= (int) $item['id'] ?>">
                        <?= htmlspecialchars($item['title']) ?> - <?= htmlspecialchars($item['merchant_name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else : ?>
    <div
        class="customizer-shell"
        id="themeCustomizerApp"
        data-page-id="<?= (int) $page['id'] ?>"
        data-registry='<?= htmlspecialchars(json_encode($registry, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'
        data-customization='<?= htmlspecialchars(json_encode($customizationData, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'
    >
        <div class="customizer-panel">
            <h6>الصفحة المحددة</h6>
            <p class="text-secondary mb-4"><?= htmlspecialchars($page['title']) ?> - <?= htmlspecialchars($page['merchant_name']) ?></p>

            <form method="post" action="/admin/customizer/save" id="customizerForm" class="vstack gap-3">
                <?= csrf_field() ?>
                <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                <input type="hidden" name="customization_json" id="customizationJson">
                <input type="hidden" name="action" id="customizerAction" value="draft">

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-info" type="button" id="btnSaveDraft">حفظ المسودة</button>
                    <button class="btn btn-accent" type="button" id="btnPublish">نشر التخصيص</button>
                    <button class="btn btn-outline-light" type="button" id="btnPreviewRefresh">تحديث المعاينة</button>
                </div>
            </form>

            <form method="post" action="/admin/customizer/rollback" class="vstack gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                <label class="form-label text-secondary">استرجاع نسخة سابقة</label>
                <div class="d-flex gap-2">
                    <select class="form-select" name="version_id" required>
                        <option value="">اختر نسخة</option>
                        <?php foreach ($versions as $version) : ?>
                            <option value="<?= (int) $version['id'] ?>">#<?= (int) $version['id'] ?> - <?= htmlspecialchars($version['status']) ?> - <?= htmlspecialchars($version['created_at']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-warning" type="submit">استرجاع</button>
                </div>
            </form>

            <hr class="border-secondary my-4">

            <h6>الأقسام</h6>
            <div class="d-flex gap-2 mb-3">
                <select class="form-select" id="sectionType">
                    <?php foreach ($registry as $key => $schema) : ?>
                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($schema['label'] ?? $key) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-light" type="button" id="btnAddSection">إضافة</button>
            </div>
            <div class="vstack gap-2" id="sectionsList"></div>

            <hr class="border-secondary my-4">

            <h6>إعدادات القسم</h6>
            <div id="sectionSettings" class="settings-grid text-secondary">اختر قسمًا لعرض الإعدادات.</div>
        </div>

        <div>
            <iframe class="preview-frame" id="previewFrame" src="/admin/customizer/preview?page_id=<?= (int) $page['id'] ?>"></iframe>
        </div>
    </div>

    <script src="/assets/js/theme-customizer.js"></script>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
