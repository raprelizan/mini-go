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
$title = 'إعدادات التاجر';
$subtitle = 'تحديث بيانات التواصل والملف التعريفي.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/settings" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">كود التاجر</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($merchant['subdomain']) ?>" disabled>
            </div>
            <div>
                <label class="form-label">رقم واتساب</label>
                <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($merchant['whatsapp_number']) ?>">
            </div>
            <div>
                <label class="form-label">معرف تيليجرام</label>
                <input type="text" name="telegram_chat_id" class="form-control" value="<?= htmlspecialchars($merchant['telegram_chat_id']) ?>">
            </div>
            <div>
                <label class="form-label">قالب رسالة الطلب</label>
                <textarea name="order_message_template" class="form-control" rows="6"><?= htmlspecialchars($merchant['order_message_template']) ?></textarea>
                <div class="form-text text-secondary">المتغيرات: {{page}} {{name}} {{phone}} {{address}} {{wilaya}} {{delivery}} {{total}}</div>
            </div>
            <div>
                <label class="form-label">بادئة رقم الطلبية</label>
                <input type="text" name="order_prefix" class="form-control" value="<?= htmlspecialchars($merchant['order_prefix'] ?? 'GFM') ?>">
            </div>
            <hr class="border-secondary">
            <h5>الملف التعريفي</h5>
            <div>
                <label class="form-label">الاسم الظاهر</label>
                <input type="text" name="profile_name" class="form-control" value="<?= htmlspecialchars($merchant['profile_name']) ?>">
            </div>
            <div>
                <label class="form-label">نبذة مختصرة</label>
                <input type="text" name="profile_bio" class="form-control" value="<?= htmlspecialchars($merchant['profile_bio']) ?>">
            </div>
            <div>
                <label class="form-label">الوصف الكامل</label>
                <textarea name="profile_about" class="form-control" rows="4"><?= htmlspecialchars($merchant['profile_about']) ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">هاتف إضافي</label>
                    <input type="text" name="profile_phone" class="form-control" value="<?= htmlspecialchars($merchant['profile_phone']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">بريد إلكتروني</label>
                    <input type="email" name="profile_email" class="form-control" value="<?= htmlspecialchars($merchant['profile_email']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="profile_address" class="form-control" value="<?= htmlspecialchars($merchant['profile_address']) ?>">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">رابط الشعار</label>
                    <input type="text" name="logo_url" class="form-control" value="<?= htmlspecialchars($merchant['logo_url']) ?>">
                </div>
                <div class="col-md-6">
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
            <button class="btn btn-accent" type="submit">حفظ الإعدادات</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
