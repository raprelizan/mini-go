<?php
$sidebar = '<div class="brand">لوحة التحكم</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/admin">نظرة عامة</a>'
    . '<a class="nav-link" href="/admin/merchants">التجار</a>'
    . '<a class="nav-link" href="/admin/merchant-registrations">طلبات التجار</a>'
    . '<a class="nav-link active" href="/admin/pages">الصفحات</a>'
    . '<a class="nav-link" href="/admin/templates">القوالب</a>'
    . '<a class="nav-link" href="/admin/orders">الطلبات</a>'
    . '<a class="nav-link" href="/admin/users">المشرفون</a>'
    . '<a class="nav-link" href="/admin/profile">حسابي</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">تسجيل الخروج</button>'
    . '</form>'
    . '</nav>';
$title = 'صفحات المنتجات';
$subtitle = 'إنشاء وتعديل روابط الصفحات للولوج السريع.';
ob_start();
?>
<div class="card app-card mb-4">
    <div class="card-body">
        <h5 class="mb-3">التجار</h5>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm btn-outline-light" href="/admin/pages">الكل</a>
            <?php foreach ($merchants as $merchant) : ?>
                <a class="btn btn-sm btn-outline-info" href="/admin/pages?merchant_id=<?= (int) $merchant['id'] ?>">
                    <?= htmlspecialchars($merchant['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="card app-card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label">تصفية حسب التاجر</label>
                <select class="form-select" onchange="if (this.value) { window.location.href = '/admin/pages?merchant_id=' + this.value; } else { window.location.href = '/admin/pages'; }">
                    <option value="">جميع التجار</option>
                    <?php foreach ($merchants as $merchant) : ?>
                        <option value="<?= (int) $merchant['id'] ?>" <?= ($selectedMerchantId ?? 0) === (int) $merchant['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($merchant['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($selectedMerchantId)) : ?>
                <div class="col-md-8 text-end">
                    <span class="badge text-bg-info">إظهار صفحات التاجر المحدد</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="card app-card mb-4">
    <div class="card-body">
        <form method="post" action="/admin/pages" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-2">
                <select name="merchant_id" class="form-select" required>
                    <option value="">التاجر</option>
                    <?php foreach ($merchants as $merchant) : ?>
                        <option value="<?= (int) $merchant['id'] ?>" <?= ($selectedMerchantId ?? 0) === (int) $merchant['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($merchant['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="template_id" class="form-select" required>
                    <option value="">القالب</option>
                    <?php foreach ($templates as $template) : ?>
                        <option value="<?= (int) $template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="title" class="form-control" placeholder="عنوان الصفحة" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="slug" class="form-control" placeholder="Slug" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="price" class="form-control" placeholder="السعر">
            </div>
            <div class="col-md-2">
                <input type="text" name="delivery_price" class="form-control" placeholder="سعر التوصيل" value="500">
            </div>
            <div class="col-md-2">
                <button class="btn btn-accent w-100" type="submit">إنشاء صفحة</button>
            </div>
            <div class="col-12">
                <input type="text" name="description" class="form-control" placeholder="وصف مختصر">
            </div>
            <div class="col-12">
                <label class="form-label text-secondary">أسعار التوصيل لكل ولاية</label>
                <div class="row g-3">
                    <?php foreach ($defaultDeliveryPrices as $wilaya => $price) : ?>
                        <div class="col-md-3">
                            <label class="form-label text-secondary"><?= htmlspecialchars($wilaya) ?></label>
                            <input type="number" name="delivery_prices[<?= htmlspecialchars($wilaya) ?>]" class="form-control" value="<?= htmlspecialchars((string) $price) ?>" min="0">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card app-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>الصفحة</th>
                        <th>التاجر</th>
                        <th>الرابط</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page) : ?>
                        <tr>
                            <form method="post" action="/admin/pages/update">
                                <?= csrf_field() ?>
                                <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                                <td>
                                    <input class="form-control form-control-sm" name="title" value="<?= htmlspecialchars($page['title']) ?>">
                                    <input class="form-control form-control-sm mt-2" name="slug" value="<?= htmlspecialchars($page['slug']) ?>">
                                    <input class="form-control form-control-sm mt-2" name="description" value="<?= htmlspecialchars($page['description']) ?>">
                                </td>
                                <td><?= htmlspecialchars($page['merchant_name']) ?></td>
                                <td>
                                    <a class="link-accent" target="_blank" href="/<?= htmlspecialchars($page['subdomain']) ?>/<?= htmlspecialchars($page['slug']) ?>">
                                        /<?= htmlspecialchars($page['subdomain']) ?>/<?= htmlspecialchars($page['slug']) ?>
                                    </a>
                                </td>
                                <td>
                                    <input class="form-control form-control-sm" name="price" value="<?= htmlspecialchars($page['price']) ?>">
                                    <input class="form-control form-control-sm mt-2" name="delivery_price" value="<?= htmlspecialchars((string) ($page['delivery_price'] ?? '500')) ?>">
                                    <?php $pageDeliveryPrices = json_decode($page['delivery_prices'] ?? '', true) ?: $defaultDeliveryPrices; ?>
                                    <div class="row g-2 mt-2">
                                        <?php foreach ($pageDeliveryPrices as $wilaya => $price) : ?>
                                            <div class="col-md-6">
                                                <label class="form-label text-secondary"><?= htmlspecialchars($wilaya) ?></label>
                                                <input class="form-control form-control-sm" name="delivery_prices[<?= htmlspecialchars($wilaya) ?>]" value="<?= htmlspecialchars((string) $price) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" <?= $page['is_active'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-light" type="submit">حفظ</button>
                            </form>
                                    <form method="post" action="/admin/pages/delete" onsubmit="return confirm('هل تريد حذف الصفحة؟');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="page_id" value="<?= (int) $page['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">حذف</button>
                                    </form>
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
