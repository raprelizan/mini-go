<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin"><i class="bi bi-speedometer2"></i> نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants"><i class="bi bi-people"></i> التجار</a>'
    . '<a class="nav-link" href="/admin/pages"><i class="bi bi-window"></i> الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates"><i class="bi bi-grid"></i> القوالب</a>'
    . '<a class="nav-link" href="/admin/orders"><i class="bi bi-inbox"></i> الطلبات</a>'
    . '<a class="nav-link" href="/admin/users"><i class="bi bi-shield-lock"></i> المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile"><i class="bi bi-person-gear"></i> حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'مركز القيادة';
$subtitle = 'إدارة سريعة للتجار والصفحات والطلبات.';
ob_start();
?>
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>عدد التجار</h6>
            <span><?= (int) $stats['merchants'] ?></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>الصفحات النشطة</h6>
            <span><?= (int) $stats['pages'] ?></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>الطلبات</h6>
            <span><?= (int) $stats['orders'] ?></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>القوالب</h6>
            <span><?= (int) $stats['templates'] ?></span>
        </div>
    </div>
</div>
<div class="card app-card mt-4">
    <div class="card-body">
        <h4>ابدأ بسرعة</h4>
        <p class="text-secondary">قم بإضافة تاجر جديد ثم أنشئ صفحة منتج وربطها بنطاق فرعي. يمكنك مراجعة الطلبات وإضافة مشرفين آخرين من القائمة الجانبية.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-accent" href="/admin/merchants">إضافة تاجر</a>
            <a class="btn btn-outline-light" href="/admin/pages">إنشاء صفحة</a>
            <a class="btn btn-outline-light" href="/admin/orders">عرض الطلبات</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
