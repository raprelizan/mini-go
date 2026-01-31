<?php
$sidebar = null;
$title = $page['title'];
$subtitle = $merchant['name'] . ' - عرض خاص';
$gallery = array_filter(array_map('trim', explode(',', $pageData['gallery'] ?? '')));
$deliveryPrice = (int) ($pageData['delivery_price'] ?? 500);
$productPrice = (int) preg_replace('/[^0-9]/', '', $page['price']);
$wilayas = [
    'أدرار', 'الشلف', 'الأغواط', 'أم البواقي', 'باتنة', 'بجاية', 'بسكرة', 'بشار', 'البليدة', 'البويرة',
    'تمنراست', 'تبسة', 'تلمسان', 'تيارت', 'تيزي وزو', 'الجزائر', 'الجلفة', 'جيجل', 'سطيف', 'سعيدة',
    'سكيكدة', 'سيدي بلعباس', 'عنابة', 'قالمة', 'قسنطينة', 'المدية', 'مستغانم', 'المسيلة', 'معسكر', 'ورقلة',
    'وهران', 'البيض', 'إليزي', 'برج بوعريريج', 'بومرداس', 'الطارف', 'تندوف', 'تيسمسيلت', 'الوادي', 'خنشلة',
    'سوق أهراس', 'تيبازة', 'ميلة', 'عين الدفلى', 'النعامة', 'عين تموشنت', 'غرداية', 'غليزان', 'تيميمون', 'برج باجي مختار',
    'أولاد جلال', 'بني عباس', 'إن صالح', 'إن قزام', 'توقرت', 'جانت', 'المغير', 'المنيعة'
];
ob_start();
?>
<section class="landing-hero">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="badge text-bg-success mb-3">الدفع عند الاستلام</span>
                <h2 class="display-6 fw-bold mb-3"><?= htmlspecialchars($pageData['headline'] ?? $page['title']) ?></h2>
                <p class="lead mb-4"><?= htmlspecialchars($pageData['subheadline'] ?? $page['description']) ?></p>
                <div class="price-tag"><?= htmlspecialchars($page['price']) ?> دج</div>
                <div class="features mt-4">
                    <h5>مزايا المنتج</h5>
                    <pre><?= htmlspecialchars($pageData['features'] ?? '') ?></pre>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="gallery">
                    <?php if ($gallery) : ?>
                        <?php foreach ($gallery as $image) : ?>
                            <img src="<?= htmlspecialchars($image) ?>" alt="صورة المنتج" class="img-fluid">
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="gallery-placeholder">
                            <i class="bi bi-camera"></i>
                            <p>أضف صور المنتج من لوحة التاجر.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="order-card">
                    <h4>اطلب الآن</h4>
                    <form method="post" action="/p/<?= htmlspecialchars($page['slug']) ?>/order" class="vstack gap-3" data-delivery="<?= $deliveryPrice ?>" data-product="<?= $productPrice ?>">
                        <?= csrf_field() ?>
                        <input type="text" name="full_name" class="form-control" placeholder="الاسم الكامل" required>
                        <input type="text" name="phone" class="form-control" placeholder="رقم الهاتف" required>
                        <input type="text" name="address" class="form-control" placeholder="العنوان" required>
                        <select name="wilaya" class="form-select" required>
                            <option value="">اختر الولاية</option>
                            <?php foreach ($wilayas as $wilaya) : ?>
                                <option value="<?= htmlspecialchars($wilaya) ?>"><?= htmlspecialchars($wilaya) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>سعر التوصيل</span>
                                <strong class="delivery-price"><?= $deliveryPrice ?> دج</strong>
                            </div>
                            <div class="summary-row total">
                                <span>الإجمالي</span>
                                <strong class="total-price"><?= $productPrice + $deliveryPrice ?> دج</strong>
                            </div>
                        </div>
                        <button class="btn btn-accent" type="submit">تأكيد الطلب</button>
                    </form>
                    <p class="text-secondary mt-2">سيتم تأكيد الطلب مباشرة عبر التاجر.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const orderForm = document.querySelector('.order-card form');
    if (orderForm) {
        const deliveryPrice = Number(orderForm.dataset.delivery || 500);
        const productPrice = Number(orderForm.dataset.product || 0);
        const deliveryEl = orderForm.querySelector('.delivery-price');
        const totalEl = orderForm.querySelector('.total-price');
        const updateTotals = () => {
            const total = productPrice + deliveryPrice;
            if (deliveryEl) {
                deliveryEl.textContent = `${deliveryPrice} دج`;
            }
            if (totalEl) {
                totalEl.textContent = `${total} دج`;
            }
        };
        updateTotals();
    }
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
