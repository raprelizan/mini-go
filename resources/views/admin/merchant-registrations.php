<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
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
$title = 'طلبات تسجيل التجار';
$subtitle = 'اعتماد أو رفض طلبات التسجيل الجديدة.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>اسم التاجر</th>
                        <th>الاسم التجاري</th>
                        <th>المجال</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration) : ?>
                        <tr>
                            <td><?= htmlspecialchars($registration['merchant_name']) ?></td>
                            <td><?= htmlspecialchars($registration['trade_name']) ?></td>
                            <td><?= htmlspecialchars($registration['business_type']) ?></td>
                            <td><?= htmlspecialchars($registration['phone']) ?></td>
                            <td><?= htmlspecialchars($registration['email']) ?></td>
                            <td>
                                <span class="badge text-bg-<?= $registration['status'] === 'pending' ? 'warning' : ($registration['status'] === 'approved' ? 'success' : 'secondary') ?>">
                                    <?= htmlspecialchars($registration['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($registration['status'] === 'pending') : ?>
                                    <form method="post" action="/admin/merchant-registrations/approve" class="vstack gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="registration_id" value="<?= (int) $registration['id'] ?>">
                                        <input type="text" name="subdomain" class="form-control form-control-sm" placeholder="كود التاجر" required>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">تفعيل الحساب</label>
                                        </div>
                                        <button class="btn btn-sm btn-outline-light" type="submit">اعتماد</button>
                                        <button class="btn btn-sm btn-outline-danger" type="submit" formaction="/admin/merchant-registrations/reject">رفض</button>
                                    </form>
                                <?php else : ?>
                                    <span class="text-secondary">تمت المعالجة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
