<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link active" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'إدارة التجار';
$subtitle = 'إدارة أكواد التجار والملفات التعريفية والروابط.';
ob_start();
?>
<div class="card app-card mb-4">
    <div class="card-body">
        <form method="post" action="/admin/merchants" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="اسم التاجر" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="subdomain" class="form-control" placeholder="كود التاجر" required>
            </div>
            <div class="col-md-3">
                <input type="email" name="email" class="form-control" placeholder="بريد المالك" required>
            </div>
            <div class="col-md-2">
                <input type="password" name="password" class="form-control" placeholder="كلمة مرور مؤقتة" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-accent w-100" type="submit">إنشاء تاجر</button>
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
                        <th>التاجر</th>
                        <th>كود التاجر</th>
                        <th>الرابط</th>
                        <th>واتساب</th>
                        <th>تيليجرام</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($merchants as $merchant) : ?>
                        <tr>
                            <form method="post" action="/admin/merchants/update">
                                <?= csrf_field() ?>
                                <input type="hidden" name="merchant_id" value="<?= (int) $merchant['id'] ?>">
                                <td><input class="form-control form-control-sm" name="name" value="<?= htmlspecialchars($merchant['name']) ?>"></td>
                                <td><input class="form-control form-control-sm" name="subdomain" value="<?= htmlspecialchars($merchant['subdomain']) ?>"></td>
                                <td>
                                    <a class="link-accent" href="/<?= htmlspecialchars($merchant['subdomain']) ?>" target="_blank">
                                        /<?= htmlspecialchars($merchant['subdomain']) ?>
                                    </a>
                                </td>
                                <td><input class="form-control form-control-sm" name="whatsapp_number" value="<?= htmlspecialchars($merchant['whatsapp_number']) ?>"></td>
                                <td><input class="form-control form-control-sm" name="telegram_chat_id" value="<?= htmlspecialchars($merchant['telegram_chat_id']) ?>"></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" <?= $merchant['is_active'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-light" type="submit">حفظ</button>
                                    <a class="btn btn-sm btn-outline-info" href="/admin/merchants/profile?merchant_id=<?= (int) $merchant['id'] ?>">الملف التعريفي</a>
                                    <a class="btn btn-sm btn-outline-info" href="/admin/merchants/delivery-prices?merchant_id=<?= (int) $merchant['id'] ?>">أسعار التوصيل</a>
                            </form>
                                    <form method="post" action="/admin/merchants/delete" onsubmit="return confirm('هل تريد حذف التاجر؟');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="merchant_id" value="<?= (int) $merchant['id'] ?>">
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
