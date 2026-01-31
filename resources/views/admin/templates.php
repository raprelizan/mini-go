<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link active" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'قوالب الصفحات';
$subtitle = 'إدارة القوالب والحقول الافتراضية.';
ob_start();
?>
<div class="card app-card mb-4">
    <div class="card-body">
        <form method="post" action="/admin/templates" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="اسم القالب" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="description" class="form-control" placeholder="وصف مختصر">
            </div>
            <div class="col-md-2">
                <input type="text" name="view_key" class="form-control" placeholder="template-key" value="default">
            </div>
            <div class="col-md-2">
                <button class="btn btn-accent w-100" type="submit">إضافة قالب</button>
            </div>
        </form>
    </div>
</div>
<div class="row g-4">
    <?php foreach ($templates as $template) : ?>
        <div class="col-md-4">
            <div class="template-card h-100">
                <form method="post" action="/admin/templates/update" class="vstack gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="template_id" value="<?= (int) $template['id'] ?>">
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($template['name']) ?>">
                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($template['description']) ?></textarea>
                    <input type="text" class="form-control" name="view_key" value="<?= htmlspecialchars($template['view_key'] ?? 'default') ?>">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-light" type="submit">حفظ</button>
                        <a class="btn btn-sm btn-outline-info" href="/admin/templates/preview?template_id=<?= (int) $template['id'] ?>" target="_blank">معاينة</a>
                        <button class="btn btn-sm btn-outline-danger" type="submit" formaction="/admin/templates/delete" onclick="return confirm('هل تريد حذف القالب؟');">حذف القالب</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
