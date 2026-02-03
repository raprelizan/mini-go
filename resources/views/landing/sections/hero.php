<?php
$heading = $settings['heading'] ?? $page['title'] ?? '';
$subheading = $settings['subheading'] ?? '';
$buttonText = $settings['button_text'] ?? 'اطلب الآن';
$buttonUrl = $settings['button_url'] ?? '';
$imageUrl = $settings['image_url'] ?? '';
$backgroundColor = $settings['background_color'] ?? '#0b1120';
$textColor = $settings['text_color'] ?? '#ffffff';
$padding = $settings['padding'] ?? '70px 0';
?>
<section class="landing-hero" style="background: <?= htmlspecialchars($backgroundColor) ?>; color: <?= htmlspecialchars($textColor) ?>; padding: <?= htmlspecialchars($padding) ?>;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($heading) ?></h1>
                <?php if ($subheading !== '') : ?>
                    <p class="lead"><?= htmlspecialchars($subheading) ?></p>
                <?php endif; ?>
                <?php if ($buttonUrl !== '') : ?>
                    <a class="btn btn-accent mt-3" href="<?= htmlspecialchars($buttonUrl) ?>"><?= htmlspecialchars($buttonText) ?></a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <?php if ($imageUrl !== '') : ?>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($heading) ?>" class="img-fluid rounded">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
