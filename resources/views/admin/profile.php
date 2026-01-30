<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link active" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'إعدادات الحساب';
$subtitle = 'تحديث بيانات المشرف وكلمة المرور.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/admin/profile" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
            </div>
            <div>
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
            </div>
            <div>
                <label class="form-label">كلمة مرور جديدة</label>
                <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لا تريد التغيير">
            </div>
            <button class="btn btn-accent" type="submit">حفظ التغييرات</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
