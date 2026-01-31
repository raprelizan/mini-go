<?php
$sidebar = null;
$title = $page['title'];
$subtitle = $merchant['name'] . ' - عرض حصري';
$gallery = array_filter(array_map('trim', explode(',', $pageData['gallery'] ?? '')));
$deliveryPrice = (int) ($pageData['delivery_price'] ?? 500);
$productPrice = (int) preg_replace('/[^0-9]/', '', $page['price']);
$deliveryPricesJson = json_encode($deliveryPrices ?? [], JSON_UNESCAPED_UNICODE);
$wilayas = [
    'أدرار', 'الشلف', 'الأغواط', 'أم البواقي', 'باتنة', 'بجاية', 'بسكرة', 'بشار', 'البليدة', 'البويرة',
    'تمنراست', 'تبسة', 'تلمسان', 'تيارت', 'تيزي وزو', 'الجزائر', 'الجلفة', 'جيجل', 'سطيف', 'سعيدة',
    'سكيكدة', 'سيدي بلعباس', 'عنابة', 'قالمة', 'قسنطينة', 'المدية', 'مستغانم', 'المسيلة', 'معسكر', 'ورقلة',
    'وهران', 'البيض', 'إليزي', 'برج بوعريريج', 'بومرداس', 'الطارف', 'تندوف', 'تيسمسيلت', 'الوادي', 'خنشلة',
    'سوق أهراس', 'تيبازة', 'ميلة', 'عين الدفلى', 'النعامة', 'عين تموشنت', 'غرداية', 'غليزان', 'تيميمون', 'برج باجي مختار',
    'أولاد جلال', 'بني عباس', 'إن صالح', 'إن قزام', 'توقرت', 'جانت', 'المغير', 'المنيعة'
];
$featureLines = array_filter(array_map('trim', explode("\n", $pageData['features'] ?? '')));
ob_start();
?>
<section class="landing-hero">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge text-bg-success">الدفع عند الاستلام</span>
                    <span class="badge text-bg-primary">توصيل سريع</span>
                </div>
                <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($pageData['headline'] ?? $page['title']) ?></h1>
                <p class="lead text-secondary mb-4"><?= htmlspecialchars($pageData['subheadline'] ?? $page['description']) ?></p>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="price-tag"><?= htmlspecialchars($page['price']) ?> دج</div>
                    <span class="text-secondary">شامل ضمان الجودة والدفع الآمن</span>
                </div>
                <div class="row g-3 mt-4">
                    <?php foreach ($featureLines as $feature) : ?>
                        <div class="col-md-6">
                            <div class="feature-card h-100">
                                <i class="bi bi-check2-circle"></i>
                                <p class="mb-0 mt-2"><?= htmlspecialchars($feature) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card app-card mb-4">
                    <div class="card-body">
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
                    </div>
                </div>
                <div class="order-card">
                    <h4>اطلب الآن</h4>
                    <form method="post" action="/<?= htmlspecialchars($merchant['subdomain']) ?>/<?= htmlspecialchars($page['slug']) ?>/order" class="vstack gap-3" data-delivery="<?= $deliveryPrice ?>" data-product="<?= $productPrice ?>" data-delivery-map='<?= htmlspecialchars($deliveryPricesJson) ?>'>
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
                    <p class="text-secondary mt-2">سيتم التواصل معك مباشرة لتأكيد الطلب والشحن.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="feature-section alt">
    <div class="container">
        <div class="section-title">
            <h3>لماذا هذا المنتج؟</h3>
            <p class="text-secondary">تصميم عصري، جودة موثوقة، وخدمة عملاء تتابع طلبك حتى التسليم.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-stars"></i>
                    <h5 class="mt-3">جودة عالية</h5>
                    <p class="text-secondary mb-0">منتج مختار بعناية لضمان أفضل تجربة استخدام.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-truck"></i>
                    <h5 class="mt-3">شحن سريع</h5>
                    <p class="text-secondary mb-0">توصيل سريع لكل الولايات مع تحديث مستمر للحالة.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <i class="bi bi-shield-check"></i>
                    <h5 class="mt-3">دفع آمن</h5>
                    <p class="text-secondary mb-0">الدفع عند الاستلام لضمان راحة بالك.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const orderForm = document.querySelector('.order-card form');
    if (orderForm) {
        const baseDeliveryPrice = Number(orderForm.dataset.delivery || 500);
        const productPrice = Number(orderForm.dataset.product || 0);
        const deliveryMap = JSON.parse(orderForm.dataset.deliveryMap || '{}');
        const selectWilaya = orderForm.querySelector('select[name="wilaya"]');
        const deliveryEl = orderForm.querySelector('.delivery-price');
        const totalEl = orderForm.querySelector('.total-price');
        const resolveDelivery = () => {
            if (!selectWilaya) {
                return baseDeliveryPrice;
            }
            return Number(deliveryMap[selectWilaya.value]) || baseDeliveryPrice;
        };
        const updateTotals = () => {
            const currentDelivery = resolveDelivery();
            const total = productPrice + currentDelivery;
            if (deliveryEl) {
                deliveryEl.textContent = `${currentDelivery} دج`;
            }
            if (totalEl) {
                totalEl.textContent = `${total} دج`;
            }
        };
        if (selectWilaya) {
            selectWilaya.addEventListener('change', updateTotals);
        }
        updateTotals();
    }
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
