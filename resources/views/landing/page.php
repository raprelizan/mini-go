<?php
$sidebar = null;
$title = $page['title'];
$subtitle = $merchant['name'] . ' exclusive offer';
$gallery = array_filter(array_map('trim', explode(',', $pageData['gallery'] ?? '')));
ob_start();
?>
<section class="landing-hero">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="badge text-bg-success mb-3">Cash On Delivery</span>
                <h2 class="display-6 fw-bold mb-3"><?= htmlspecialchars($pageData['headline'] ?? $page['title']) ?></h2>
                <p class="lead mb-4"><?= htmlspecialchars($pageData['subheadline'] ?? $page['description']) ?></p>
                <div class="price-tag"><?= htmlspecialchars($page['price']) ?> DZD</div>
                <div class="features mt-4">
                    <h5>Why customers choose this</h5>
                    <pre><?= htmlspecialchars($pageData['features'] ?? '') ?></pre>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="gallery">
                    <?php if ($gallery) : ?>
                        <?php foreach ($gallery as $image) : ?>
                            <img src="<?= htmlspecialchars($image) ?>" alt="Product image" class="img-fluid">
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="gallery-placeholder">
                            <i class="bi bi-camera"></i>
                            <p>Add product images in the merchant panel.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="order-card">
                    <h4>Order now</h4>
                    <form method="post" action="/p/<?= htmlspecialchars($page['slug']) ?>/order" class="vstack gap-3">
                        <?= csrf_field() ?>
                        <input type="text" name="full_name" class="form-control" placeholder="Full name" required>
                        <input type="text" name="phone" class="form-control" placeholder="Phone number" required>
                        <input type="text" name="address" class="form-control" placeholder="Address" required>
                        <input type="text" name="wilaya" class="form-control" placeholder="Wilaya" required>
                        <button class="btn btn-accent" type="submit">Order Now</button>
                    </form>
                    <p class="text-secondary mt-2">Orders are confirmed by the merchant via WhatsApp or Telegram.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
