<?php
$sidebar = null;
$title = 'تم استلام الطلب';
$subtitle = 'تم إرسال الطلب إلى التاجر بنجاح.';
ob_start();
?>
<div class="container">
    <div class="card app-card">
        <div class="card-body">
            <h3 class="mb-3">شكراً لطلبك!</h3>
            <p>رقم الطلب <strong>#<?= htmlspecialchars((string) $orderId) ?></strong>. سيتم التواصل معك قريباً.</p>
            <div class="order-sheet">
                <h5>ملخص الطلب</h5>
                <div class="sheet-row"><span>الاسم</span><strong><?= htmlspecialchars($order['full_name']) ?></strong></div>
                <div class="sheet-row"><span>الهاتف</span><strong><?= htmlspecialchars($order['phone']) ?></strong></div>
                <div class="sheet-row"><span>العنوان</span><strong><?= htmlspecialchars($order['address']) ?></strong></div>
                <div class="sheet-row"><span>الولاية</span><strong><?= htmlspecialchars($order['wilaya']) ?></strong></div>
                <div class="sheet-row"><span>سعر التوصيل</span><strong><?= htmlspecialchars((string) $order['delivery_price']) ?> دج</strong></div>
                <div class="sheet-row total"><span>الإجمالي</span><strong><?= htmlspecialchars((string) $order['total_price']) ?> دج</strong></div>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <?php if ($whatsappLink) : ?>
                    <a class="btn btn-success" href="<?= htmlspecialchars($whatsappLink) ?>" target="_blank">فتح واتساب</a>
                <?php endif; ?>
                <?php if ($telegramLink) : ?>
                    <a class="btn btn-info" href="<?= htmlspecialchars($telegramLink) ?>" target="_blank">مشاركة عبر تيليجرام</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
