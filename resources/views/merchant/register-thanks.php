<?php
$sidebar = null;
$title = 'تم إرسال الطلب';
$subtitle = 'شكرًا لتسجيلك، سيتم مراجعة الطلب قريبًا.';
ob_start();
$whatsappLink = '';
if (!empty($supportWhatsapp)) {
    $clean = preg_replace('/[^0-9]/', '', $supportWhatsapp);
    $message = urlencode('مرحباً، أود تفعيل حساب التاجر الخاص بي. شكراً لكم.');
    $whatsappLink = 'https://wa.me/' . $clean . '?text=' . $message;
}
?>
<div class="container">
    <div class="card app-card">
        <div class="card-body text-center">
            <h3 class="mb-3">تم استلام طلبك بنجاح</h3>
            <p class="text-secondary">سيقوم فريق الدعم بمراجعة بياناتك وتفعيل الحساب في أقرب وقت.</p>
            <?php if ($whatsappLink) : ?>
                <a class="btn btn-success mt-3" href="<?= htmlspecialchars($whatsappLink) ?>" target="_blank">تواصل عبر واتساب</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
