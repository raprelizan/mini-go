<?php
$heading = $settings['heading'] ?? 'المنتجات';
$columns = (int) ($settings['columns'] ?? 2);
$columns = in_array($columns, [2, 3, 4], true) ? $columns : 2;
$columnClass = $columns === 4 ? 'col-md-3' : ($columns === 3 ? 'col-md-4' : 'col-md-6');
?>
<section class="py-5">
    <div class="container">
        <h3 class="mb-4"><?= htmlspecialchars($heading) ?></h3>
        <div class="row g-4">
            <?php foreach ($blocks as $block) : ?>
                <?php
                    $blockSettings = is_array($block['settings'] ?? null) ? $block['settings'] : [];
                    $title = $blockSettings['title'] ?? '';
                    $price = $blockSettings['price'] ?? '';
                    $image = $blockSettings['image_url'] ?? '';
                ?>
                <div class="<?= $columnClass ?> col-12">
                    <div class="card app-card h-100">
                        <?php if ($image !== '') : ?>
                            <img src="<?= htmlspecialchars($image) ?>" class="card-img-top" alt="<?= htmlspecialchars($title) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($title) ?></h5>
                            <?php if ($price !== '') : ?>
                                <p class="text-success fw-bold mb-0"><?= htmlspecialchars($price) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
