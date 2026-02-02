<?php
$sidebar = null;
$title = $page['title'];
$subtitle = $merchant['name'] . ' - عرض رمضان';
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
$features = array_filter(array_map('trim', explode("\n", $pageData['features'] ?? '')));
$descriptionImages = array_slice($gallery, 0, 2);
ob_start();
?>
<style>
    .ramadan-template {
        --bg: #0b1120;
        --surface: #111827;
        --accent: #16a34a;
        --accent-strong: #22c55e;
        --text: #f8fafc;
        --muted: #cbd5f5;
        color: var(--text);
    }

    .ramadan-template .ticker {
        background: #000;
        color: #fff;
        padding: 10px 0;
        font-weight: 600;
        overflow: hidden;
        white-space: nowrap;
    }

    .ramadan-template .ticker span {
        display: inline-block;
        padding-left: 100%;
        animation: ticker 18s linear infinite;
    }

    @keyframes ticker {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
    }

    .ramadan-template .hero {
        background: radial-gradient(circle at top right, rgba(34, 197, 94, 0.25), transparent 50%),
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.2), transparent 50%),
            var(--bg);
        padding: 60px 0;
    }

    .ramadan-template .hero-card {
        background: var(--surface);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 36px;
        box-shadow: 0 30px 60px rgba(2, 6, 23, 0.6);
    }

    .ramadan-template .price-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(22, 163, 74, 0.15);
        color: #fff;
        border: 1px solid rgba(22, 163, 74, 0.5);
        padding: 10px 20px;
        border-radius: 999px;
        font-weight: 700;
    }

    .ramadan-template .discount-tag {
        background: var(--accent);
        color: #fff;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.9rem;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 rgba(22, 163, 74, 0.6); }
        50% { transform: scale(1.08); box-shadow: 0 0 18px rgba(22, 163, 74, 0.6); }
    }

    .ramadan-template .gallery-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .ramadan-template .gallery-grid img {
        width: 100%;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .ramadan-template .description-section {
        padding: 50px 0;
    }

    .ramadan-template .description-card {
        background: var(--surface);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .ramadan-template .order-card {
        background: var(--surface);
        border-radius: 20px;
        padding: 28px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .ramadan-template .order-summary {
        background: rgba(15, 23, 42, 0.7);
        border-radius: 14px;
        padding: 16px;
        border: 1px dashed rgba(255, 255, 255, 0.12);
    }

    .ramadan-template .btn-accent {
        background: var(--accent);
        border: none;
        color: #fff;
        font-weight: 700;
    }

    .ramadan-template footer {
        padding: 30px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--muted);
    }
</style>
<div class="ramadan-template">
    <div class="ticker"><span>تخفيضات رمضان لفترة محدودة ✨ الكمية محدودة - اطلب الآن واستفد من العرض الخاص</span></div>
    <section class="hero">
        <div class="container">
            <div class="hero-card">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="price-badge mb-3">
                            <span class="discount-tag">تخفيض</span>
                            <span><?= htmlspecialchars($page['price']) ?> دج</span>
                        </div>
                        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($pageData['headline'] ?? $page['title']) ?></h1>
                        <?php if (!empty($pageData['subheadline'])) : ?>
                            <div class="lead text-secondary mb-4 rich-content"><?= $pageData['subheadline'] ?></div>
                        <?php else : ?>
                            <p class="lead text-secondary mb-4"><?= htmlspecialchars($page['description']) ?></p>
                        <?php endif; ?>
                        <div class="row g-3">
                            <?php foreach ($features as $feature) : ?>
                                <div class="col-md-6">
                                    <div class="feature-card">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <p class="mb-0 mt-2"><?= htmlspecialchars($feature) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gallery-grid">
                            <?php if ($gallery) : ?>
                                <?php foreach ($gallery as $image) : ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="صورة المنتج">
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
            </div>
        </div>
    </section>
    <section class="description-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="description-card">
                        <h3 class="mb-3">وصف المنتج</h3>
                        <p class="text-secondary mb-4"><?= nl2br(htmlspecialchars($pageData['features'] ?? '')) ?></p>
                        <div class="row g-3">
                            <?php foreach ($descriptionImages as $image) : ?>
                                <div class="col-md-6">
                                    <img src="<?= htmlspecialchars($image) ?>" class="img-fluid rounded" alt="تفاصيل المنتج">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
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
                        <p class="text-secondary mt-2">العرض محدود خلال رمضان - سارع بالطلب.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container d-flex flex-wrap justify-content-between gap-3">
            <div><?= htmlspecialchars($merchant['name']) ?> © جميع الحقوق محفوظة</div>
            <div>توصيل سريع - دفع عند الاستلام</div>
        </div>
    </footer>
</div>
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
