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
$title = 'تفاصيل الطلب';
$subtitle = 'عرض كامل لمعلومات العميل والطلب.';
ob_start();
?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card app-card order-sheet">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><?= htmlspecialchars($order['page_title']) ?></h5>
                    <span class="badge text-bg-primary"><?= htmlspecialchars($order['order_code'] ?? ('#' . $order['id'])) ?></span>
                </div>
                <div class="sheet-row"><span>الاسم</span><strong><?= htmlspecialchars($order['full_name']) ?></strong></div>
                <div class="sheet-row"><span>الهاتف</span><strong><?= htmlspecialchars($order['phone']) ?></strong></div>
                <div class="sheet-row"><span>العنوان</span><strong><?= htmlspecialchars($order['address']) ?></strong></div>
                <div class="sheet-row"><span>الولاية</span><strong><?= htmlspecialchars($order['wilaya']) ?></strong></div>
                <div class="sheet-row"><span>سعر التوصيل</span><strong><?= htmlspecialchars((string) $order['delivery_price']) ?> دج</strong></div>
                <div class="sheet-row total"><span>الإجمالي</span><strong><?= htmlspecialchars((string) $order['total_price']) ?> دج</strong></div>
                <div class="mt-3 text-secondary">الحالة: <?= htmlspecialchars($order['status']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card app-card">
            <div class="card-body">
                <h6 class="mb-3">إجراءات سريعة</h6>
                <a class="btn btn-outline-light w-100 mb-2" href="/merchant/orders">العودة للطلبات</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
