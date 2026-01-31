<?php
$sidebar = null;
$title = $page['title'];
$subtitle = $merchant['name'] . ' - تشكيلة مميزة';
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
ob_start();
?>
<style>
    .aurora-template {
        --primary: #7c3aed;
        --accent: #22d3ee;
        --bg: #0f172a;
        --surface: #111827;
        --surface-soft: rgba(255, 255, 255, 0.04);
        --text: #f8fafc;
        --muted: #94a3b8;
        color: var(--text);
    }

    .aurora-template .hero-surface {
        background: radial-gradient(circle at 10% 20%, rgba(124, 58, 237, 0.35), transparent 45%),
            radial-gradient(circle at 80% 0%, rgba(34, 211, 238, 0.25), transparent 45%),
            var(--bg);
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 48px;
        box-shadow: 0 30px 60px rgba(2, 6, 23, 0.6);
    }

    .aurora-template .badge-pill {
        border-radius: 999px;
        padding: 8px 16px;
        background: rgba(124, 58, 237, 0.2);
        border: 1px solid rgba(124, 58, 237, 0.4);
        font-weight: 600;
    }

    .aurora-template .price-chip {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.35), rgba(34, 211, 238, 0.2));
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 12px 24px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .aurora-template .feature-card {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 20px;
        height: 100%;
    }

    .aurora-template .gallery-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    }

    .aurora-template .gallery-grid img {
        width: 100%;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .aurora-template .order-box {
        background: var(--surface);
        border-radius: 20px;
        padding: 28px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .aurora-template .order-summary {
        background: var(--surface-soft);
        border-radius: 14px;
        padding: 16px;
        border: 1px dashed rgba(255, 255, 255, 0.1);
    }

    .aurora-template .btn-accent {
        background: var(--primary);
        border: none;
        color: #fff;
        font-weight: 700;
    }

    .aurora-template .section-panel {
        background: var(--surface);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 36px;
    }
</style>
<div class="aurora-template">
    <section class="landing-hero">
        <div class="container">
            <div class="hero-surface">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge-pill">الدفع عند الاستلام</span>
                            <span class="badge-pill" style="border-color: rgba(34, 211, 238, 0.4); background: rgba(34, 211, 238, 0.15);">ضمان جودة</span>
                        </div>
                        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($pageData['headline'] ?? $page['title']) ?></h1>
                        <p class="lead text-secondary mb-4"><?= htmlspecialchars($pageData['subheadline'] ?? $page['description']) ?></p>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="price-chip"><?= htmlspecialchars($page['price']) ?> دج</div>
                            <span class="text-secondary">توصيل سريع لكل الولايات</span>
                        </div>
                        <div class="row g-3 mt-4">
                            <?php foreach ($features as $feature) : ?>
                                <div class="col-md-6">
                                    <div class="feature-card">
                                        <i class="bi bi-check2-circle text-info"></i>
                                        <p class="mb-0 mt-2"><?= htmlspecialchars($feature) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gallery-grid mb-4">
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
                        <div class="order-box">
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
                            <p class="text-secondary mt-2">سيتم التواصل معك لتأكيد الطلب وإتمام الشحن.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="feature-section alt">
        <div class="container">
            <div class="section-panel">
                <div class="section-title">
                    <h3>تجربة شراء مميزة</h3>
                    <p class="text-secondary">مزيج من الجودة، الشحن السريع، والدعم المميز لعملائنا.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="bi bi-lightning-charge"></i>
                            <h5 class="mt-3">جاهزية فورية</h5>
                            <p class="text-secondary mb-0">نضمن تجهيز الطلب خلال ساعات قليلة من التأكيد.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="bi bi-shield-lock"></i>
                            <h5 class="mt-3">دفع آمن</h5>
                            <p class="text-secondary mb-0">الدفع عند الاستلام لتجربة شراء موثوقة.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="bi bi-chat-dots"></i>
                            <h5 class="mt-3">دعم متواصل</h5>
                            <p class="text-secondary mb-0">فريق الدعم على تواصل مستمر حتى استلام المنتج.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    const orderForm = document.querySelector('.order-box form');
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
