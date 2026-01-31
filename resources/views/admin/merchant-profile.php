<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'الملف التعريفي للتاجر';
$subtitle = 'تعديل بيانات العرض وروابط التواصل.';
ob_start();
?>
<div class="profile-admin-banner" style="background-image: url('<?= htmlspecialchars($merchant['cover_url'] ?: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=80') ?>');">
    <div class="overlay"></div>
    <div class="profile-admin-card">
        <img src="<?= htmlspecialchars($merchant['logo_url'] ?: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80') ?>" alt="<?= htmlspecialchars($merchant['name']) ?>">
        <div>
            <h4><?= htmlspecialchars($merchant['profile_name'] ?: $merchant['name']) ?></h4>
            <p><?= htmlspecialchars($merchant['profile_bio'] ?: 'ملف تعريفي للتاجر يظهر للعملاء في الصفحة العامة.') ?></p>
        </div>
    </div>
</div>
<div class="card app-card mt-4">
    <div class="card-body">
        <form method="post" action="/admin/merchants/profile" class="vstack gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="merchant_id" value="<?= (int) $merchant['id'] ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">اسم التاجر</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($merchant['name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">كود التاجر</label>
                    <input type="text" name="subdomain" class="form-control" value="<?= htmlspecialchars($merchant['subdomain']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" <?= $merchant['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label">تاجر نشط</label>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">واتساب</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($merchant['whatsapp_number']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">تيليجرام</label>
                    <input type="text" name="telegram_chat_id" class="form-control" value="<?= htmlspecialchars($merchant['telegram_chat_id']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">رابط الملف التعريفي</label>
                    <input type="text" class="form-control" value="/<?= htmlspecialchars($merchant['subdomain']) ?>" disabled>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">بادئة رقم الطلبية</label>
                    <input type="text" name="order_prefix" class="form-control" value="<?= htmlspecialchars($merchant['order_prefix'] ?? 'GFM') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">صفحة أسعار التوصيل</label>
                    <a class="btn btn-outline-info w-100" href="/admin/merchants/delivery-prices?merchant_id=<?= (int) $merchant['id'] ?>">إدارة الأسعار</a>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">الاسم الظاهر</label>
                    <input type="text" name="profile_name" class="form-control" value="<?= htmlspecialchars($merchant['profile_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">نبذة مختصرة</label>
                    <input type="text" name="profile_bio" class="form-control" value="<?= htmlspecialchars($merchant['profile_bio']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="profile_address" class="form-control" value="<?= htmlspecialchars($merchant['profile_address']) ?>">
                </div>
            </div>
            <div>
                <label class="form-label">الوصف الكامل</label>
                <textarea name="profile_about" class="form-control" rows="4"><?= htmlspecialchars($merchant['profile_about']) ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">هاتف إضافي</label>
                    <input type="text" name="profile_phone" class="form-control" value="<?= htmlspecialchars($merchant['profile_phone']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="profile_email" class="form-control" value="<?= htmlspecialchars($merchant['profile_email']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">رابط الشعار</label>
                    <input type="text" name="logo_url" class="form-control" value="<?= htmlspecialchars($merchant['logo_url']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">رابط الغلاف</label>
                    <input type="text" name="cover_url" class="form-control" value="<?= htmlspecialchars($merchant['cover_url']) ?>">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">إنستغرام</label>
                    <input type="text" name="instagram_url" class="form-control" value="<?= htmlspecialchars($merchant['instagram_url']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">فيسبوك</label>
                    <input type="text" name="facebook_url" class="form-control" value="<?= htmlspecialchars($merchant['facebook_url']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تيك توك</label>
                    <input type="text" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($merchant['tiktok_url']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الموقع</label>
                    <input type="text" name="website_url" class="form-control" value="<?= htmlspecialchars($merchant['website_url']) ?>">
                </div>
            </div>
            <button class="btn btn-accent" type="submit">حفظ الملف التعريفي</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
