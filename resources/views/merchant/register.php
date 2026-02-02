<?php
$sidebar = null;
$title = 'تسجيل التاجر';
$subtitle = 'أرسل طلب التسجيل وسيتم تفعيل الحساب بعد المراجعة.';
ob_start();
?>
<div class="container">
    <div class="card app-card">
        <div class="card-body">
            <form method="post" action="/merchant/register" class="vstack gap-3">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم التاجر</label>
                        <input type="text" name="merchant_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الاسم التجاري</label>
                        <input type="text" name="trade_name" class="form-control" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">المجال الذي تعمل به</label>
                    <input type="text" name="business_type" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">كلمة السر</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="card app-card bg-transparent border border-secondary">
                    <div class="card-body">
                        <h6>شروط استخدام المنصة</h6>
                        <ul class="text-secondary">
                            <li>المنصة مخصصة للتجار المعتمدين فقط.</li>
                            <li>يجب الالتزام بسياسات الشحن والدفع عند الاستلام.</li>
                            <li>تفعيل الحساب يتم يدويًا بعد مراجعة البيانات.</li>
                        </ul>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                            <label class="form-check-label" for="terms">أوافق على الشروط والأحكام</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-accent" type="submit">إرسال طلب التسجيل</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
