<?php
$sidebar = null;
$title = $merchant['profile_name'] ?: $merchant['name'];
$subtitle = $merchant['profile_bio'] ?: 'ملف تعريفي احترافي للتاجر.';
ob_start();
?>
<section class="profile-hero" style="background-image: url('<?= htmlspecialchars($merchant['cover_url'] ?: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=80') ?>');">
    <div class="overlay"></div>
    <div class="container">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="<?= htmlspecialchars($merchant['logo_url'] ?: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80') ?>" alt="<?= htmlspecialchars($merchant['name']) ?>">
            </div>
            <div class="profile-details">
                <h2><?= htmlspecialchars($merchant['profile_name'] ?: $merchant['name']) ?></h2>
                <p><?= htmlspecialchars($merchant['profile_bio'] ?: 'نحن نوفر تجربة تسوق موثوقة مع خدمة عملاء مميزة وتسليم سريع.') ?></p>
                <div class="profile-tags">
                    <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($merchant['profile_address'] ?: 'الجزائر') ?></span>
                    <span><i class="bi bi-telephone"></i> <?= htmlspecialchars($merchant['profile_phone'] ?: $merchant['whatsapp_number']) ?></span>
                    <span><i class="bi bi-envelope"></i> <?= htmlspecialchars($merchant['profile_email'] ?: 'support@example.com') ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="profile-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card app-card">
                    <div class="card-body">
                        <h4>نبذة عن المتجر</h4>
                        <p class="text-secondary"><?= htmlspecialchars($merchant['profile_about'] ?: 'صفحة تعريفية كاملة يمكنك تعديلها من لوحة التاجر لتقديم قصتك، خدماتك، ومميزاتك للعملاء بشكل احترافي.') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card app-card">
                    <div class="card-body">
                        <h5>روابط التواصل</h5>
                        <div class="social-links">
                            <?php if (!empty($merchant['instagram_url'])) : ?>
                                <a href="<?= htmlspecialchars($merchant['instagram_url']) ?>" target="_blank"><i class="bi bi-instagram"></i> انستغرام</a>
                            <?php endif; ?>
                            <?php if (!empty($merchant['facebook_url'])) : ?>
                                <a href="<?= htmlspecialchars($merchant['facebook_url']) ?>" target="_blank"><i class="bi bi-facebook"></i> فيسبوك</a>
                            <?php endif; ?>
                            <?php if (!empty($merchant['tiktok_url'])) : ?>
                                <a href="<?= htmlspecialchars($merchant['tiktok_url']) ?>" target="_blank"><i class="bi bi-tiktok"></i> تيك توك</a>
                            <?php endif; ?>
                            <?php if (!empty($merchant['website_url'])) : ?>
                                <a href="<?= htmlspecialchars($merchant['website_url']) ?>" target="_blank"><i class="bi bi-globe"></i> الموقع الرسمي</a>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <a class="btn btn-accent w-100" href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', $merchant['whatsapp_number'] ?? '')) ?>" target="_blank">تواصل عبر واتساب</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
