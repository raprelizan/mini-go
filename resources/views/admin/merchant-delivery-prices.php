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
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'أسعار التوصيل للتاجر';
$subtitle = 'تعديل أسعار التوصيل وبادئة الطلبات.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/admin/merchants/delivery-prices" class="vstack gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="merchant_id" value="<?= (int) $merchant['id'] ?>">
            <div>
                <label class="form-label">اسم التاجر</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($merchant['name']) ?>" disabled>
            </div>
            <div>
                <label class="form-label">بادئة رقم الطلبية</label>
                <input type="text" name="order_prefix" class="form-control" value="<?= htmlspecialchars($merchant['order_prefix'] ?? 'GFM') ?>">
            </div>
            <div>
                <label class="form-label">أسعار التوصيل حسب الولاية</label>
                <div class="row g-3">
                    <?php foreach ($deliveryPrices as $wilaya => $price) : ?>
                        <div class="col-md-4">
                            <label class="form-label text-secondary"><?= htmlspecialchars($wilaya) ?></label>
                            <input type="number" name="delivery_prices[<?= htmlspecialchars($wilaya) ?>]" class="form-control" value="<?= htmlspecialchars((string) $price) ?>" min="0">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="btn btn-accent" type="submit">حفظ الأسعار</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
