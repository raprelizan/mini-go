<?php
$sidebar = '<div class="brand">لوحة التاجر</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/merchant">لوحة التحكم</a>'
    . '<a class="nav-link" href="/merchant/orders">الطلبيات</a>'
    . '<a class="nav-link" href="/merchant/delivery-prices">أسعار التوصيل</a>'
    . '<a class="nav-link" href="/merchant/settings">الإعدادات</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'الطلبيات الواردة';
$subtitle = 'قائمة مختصرة للطلبات مع إمكانية عرض التفاصيل وتحديث الحالة.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الصفحة</th>
                        <th>العميل</th>
                        <th>الهاتف</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_code'] ?? ('#' . $order['id'])) ?></td>
                            <td><?= htmlspecialchars($order['page_title']) ?></td>
                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                            <td><?= htmlspecialchars($order['phone']) ?></td>
                            <td><?= htmlspecialchars((string) $order['total_price']) ?> دج</td>
                            <td>
                                <form method="post" action="/merchant/orders/update" class="d-flex gap-2">
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
                                <a class="btn btn-sm btn-outline-info" href="/merchant/orders/view?order_id=<?= (int) $order['id'] ?>">عرض التفاصيل</a>
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
