<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link active" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'الطلبات';
$subtitle = 'ملخص الطلبات حسب التاجر مع إمكانية استعراض التفاصيل.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>التاجر</th>
                        <th>إجمالي الطلبات</th>
                        <th>جديد</th>
                        <th>مؤكد</th>
                        <th>تم الشحن</th>
                        <th>ملغي</th>
                        <th>تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= (int) $row['total_orders'] ?></td>
                            <td><?= (int) $row['new_orders'] ?></td>
                            <td><?= (int) $row['confirmed_orders'] ?></td>
                            <td><?= (int) $row['shipped_orders'] ?></td>
                            <td><?= (int) $row['cancelled_orders'] ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-info" href="/admin/orders?merchant_id=<?= (int) $row['id'] ?>">عرض الطلبات</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (!empty($selectedMerchant)) : ?>
    <div class="card app-card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">طلبات التاجر: <?= htmlspecialchars($selectedMerchant['name']) ?></h5>
                <a class="btn btn-sm btn-outline-light" href="/admin/orders">رجوع للملخص</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الصفحة</th>
                            <th>العميل</th>
                            <th>الهاتف</th>
                            <th>التوصيل</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_code'] ?? ('#' . (int) $order['id'])) ?></td>
                                <td><?= htmlspecialchars($order['page_title']) ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td><?= htmlspecialchars($order['phone']) ?></td>
                                <td><?= htmlspecialchars((string) $order['delivery_price']) ?> دج</td>
                                <td><?= htmlspecialchars((string) $order['total_price']) ?> دج</td>
                                <td>
                                    <form method="post" action="/admin/orders/update" class="d-flex gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                        <input type="hidden" name="merchant_id" value="<?= (int) $selectedMerchant['id'] ?>">
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
                                        <input type="hidden" name="merchant_id" value="<?= (int) $selectedMerchant['id'] ?>">
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
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
