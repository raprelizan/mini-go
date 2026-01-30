<?php
$appConfig = require __DIR__ . '/../../../config/app.php';
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link active" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'صفحات المنتجات';
$subtitle = 'إنشاء وتعديل روابط الصفحات للولوج السريع.';
ob_start();
?>
<div class="card app-card mb-4">
    <div class="card-body">
        <form method="post" action="/admin/pages" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-2">
                <select name="merchant_id" class="form-select" required>
                    <option value="">التاجر</option>
                    <?php foreach ($merchants as $merchant) : ?>
                        <option value="<?= (int) $merchant['id'] ?>"><?= htmlspecialchars($merchant['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="template_id" class="form-select" required>
                    <option value="">القالب</option>
                    <?php foreach ($templates as $template) : ?>
                        <option value="<?= (int) $template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="title" class="form-control" placeholder="عنوان الصفحة" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="slug" class="form-control" placeholder="Slug" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="price" class="form-control" placeholder="السعر">
            </div>
            <div class="col-md-2">
                <button class="btn btn-accent w-100" type="submit">إنشاء صفحة</button>
            </div>
            <div class="col-12">
                <input type="text" name="description" class="form-control" placeholder="وصف مختصر">
            </div>
        </form>
    </div>
</div>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>الصفحة</th>
                        <th>التاجر</th>
                        <th>الرابط</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page) : ?>
                        <tr>
                            <form method="post" action="/admin/pages/update">
                                <?= csrf_field() ?>
                                <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                                <td>
                                    <input class="form-control form-control-sm" name="title" value="<?= htmlspecialchars($page['title']) ?>">
                                    <input class="form-control form-control-sm mt-2" name="slug" value="<?= htmlspecialchars($page['slug']) ?>">
                                    <input class="form-control form-control-sm mt-2" name="description" value="<?= htmlspecialchars($page['description']) ?>">
                                </td>
                                <td><?= htmlspecialchars($page['merchant_name']) ?></td>
                                <td>
                                    <a class="link-accent" target="_blank" href="https://<?= htmlspecialchars($page['subdomain']) ?>.<?= htmlspecialchars($appConfig['base_domain']) ?>/p/<?= htmlspecialchars($page['slug']) ?>">
                                        <?= htmlspecialchars($page['subdomain']) ?>.<?= htmlspecialchars($appConfig['base_domain']) ?>/p/<?= htmlspecialchars($page['slug']) ?>
                                    </a>
                                </td>
                                <td><input class="form-control form-control-sm" name="price" value="<?= htmlspecialchars($page['price']) ?>"></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" <?= $page['is_active'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-light" type="submit">حفظ</button>
                            </form>
                                    <form method="post" action="/admin/pages/delete" onsubmit="return confirm('هل تريد حذف الصفحة؟');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">حذف</button>
                                    </form>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
