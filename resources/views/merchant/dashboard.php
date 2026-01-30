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
$title = 'لوحة التاجر';
$subtitle = 'إدارة صفحاتك وتتبع الطلبات الأخيرة.';
ob_start();
?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card app-card">
            <div class="card-header">
                <h3>صفحاتي</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-dark">
                    <?php foreach ($pages as $page) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($page['title']) ?></strong>
                                <div class="text-secondary">/p/<?= htmlspecialchars($page['slug']) ?></div>
                            </div>
                            <a class="btn btn-sm btn-outline-light" href="/merchant/pages/edit?page_id=<?= (int) $page['id'] ?>">تعديل</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card app-card">
            <div class="card-header">
                <h3>أحدث الطلبات</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الصفحة</th>
                                <th>العميل</th>
                                <th>الهاتف</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) : ?>
                                <tr>
                                    <td>#<?= (int) $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['page_title']) ?></td>
                                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                                    <td><?= htmlspecialchars($order['phone']) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
