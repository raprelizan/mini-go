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
            <div class="d-flex flex-wrap gap-3">
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
