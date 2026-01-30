<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link active" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'الطلبات';
$subtitle = 'متابعة الطلبات وتحديث حالتها.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>التاجر</th>
                        <th>الصفحة</th>
                        <th>العميل</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['merchant_name']) ?></td>
                            <td><?= htmlspecialchars($order['page_title']) ?></td>
                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                            <td><?= htmlspecialchars($order['phone']) ?></td>
                            <td>
                                <form method="post" action="/admin/orders/update" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['new' => 'جديد', 'confirmed' => 'مؤكد', 'shipped' => 'تم الشحن', 'cancelled' => 'ملغي'] as $key => $label) : ?>
                                            <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-light" type="submit">تحديث</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="/admin/orders/delete" onsubmit="return confirm('حذف الطلب؟');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
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
