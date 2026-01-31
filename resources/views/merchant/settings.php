<?php
$sidebar = '<div class="brand">لوحة التاجر</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/merchant">لوحة التحكم</a>'
    . '<a class="nav-link" href="/merchant/settings">الإعدادات</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'إعدادات التاجر';
$subtitle = 'تحديث وسائل التواصل ورسالة الطلبات.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/settings" class="vstack gap-3">
            <?= csrf_field() ?>
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
            <button class="btn btn-accent" type="submit">حفظ الإعدادات</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
