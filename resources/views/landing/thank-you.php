<?php
$sidebar = null;
$title = 'Order received';
$subtitle = 'We have sent your order to the merchant.';
ob_start();
?>
<div class="container">
    <div class="card app-card">
        <div class="card-body">
            <h3 class="mb-3">Thank you for your order!</h3>
            <p>Your order reference is <strong>#<?= htmlspecialchars((string) $orderId) ?></strong>. The merchant will contact you shortly.</p>
            <div class="d-flex flex-wrap gap-3">
                <?php if ($whatsappLink) : ?>
                    <a class="btn btn-success" href="<?= htmlspecialchars($whatsappLink) ?>" target="_blank">Open WhatsApp</a>
                <?php endif; ?>
                <?php if ($telegramLink) : ?>
                    <a class="btn btn-info" href="<?= htmlspecialchars($telegramLink) ?>" target="_blank">Share on Telegram</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
