<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/customizer">مخصص الصفحات</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link active" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'إدارة المشرفين';
$subtitle = 'إضافة مشرفين جدد أو تعديل حساباتهم.';
ob_start();
?>
<div class="card app-card mb-4">
    <div class="card-body">
        <form method="post" action="/admin/users" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="الاسم" required>
            </div>
            <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="البريد" required>
            </div>
            <div class="col-md-3">
                <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
            </div>
            <div class="col-md-1">
                <button class="btn btn-accent w-100" type="submit">إضافة</button>
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
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>تحديث</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <form method="post" action="/admin/users/update" class="row g-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                <td><input class="form-control form-control-sm" name="name" value="<?= htmlspecialchars($user['name']) ?>"></td>
                                <td><input class="form-control form-control-sm" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                                <td><input class="form-control form-control-sm" name="password" placeholder="كلمة مرور جديدة"></td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-light" type="submit">حفظ</button>
                            </form>
                                    <form method="post" action="/admin/users/delete" onsubmit="return confirm('حذف المشرف؟');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
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
