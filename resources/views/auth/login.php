<?php
$sidebar = null;
$title = 'تسجيل الدخول';
$subtitle = 'ادخل إلى لوحة التحكم الخاصة بك.';
ob_start();
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card app-card">
                <div class="card-body">
                    <form method="post" action="/login" class="vstack gap-3">
                        <?= csrf_field() ?>
                        <div>
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-accent" type="submit">دخول</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-secondary mt-3">لا يوجد تسجيل عام. يجب إنشاء الحساب من خلال مدير المنصة.</p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
