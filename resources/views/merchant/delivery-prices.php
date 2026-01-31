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
$title = 'أسعار التوصيل حسب الولاية';
$subtitle = 'قم بتعديل الأسعار لكل ولاية وفق احتياجك.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/delivery-prices" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">بادئة رقم الطلبية</label>
                <input type="text" name="order_prefix" class="form-control" value="<?= htmlspecialchars($orderPrefix) ?>">
            </div>
            <div>
                <label class="form-label">أسعار التوصيل (JSON)</label>
                <textarea name="delivery_prices_json" class="form-control" rows="10"><?= htmlspecialchars($deliveryPrices) ?></textarea>
                <div class="form-text text-secondary">مثال: {"الجزائر":500,"وهران":700}</div>
            </div>
            <button class="btn btn-accent" type="submit">حفظ الأسعار</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
