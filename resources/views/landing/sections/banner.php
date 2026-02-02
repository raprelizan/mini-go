<?php
$text = $settings['text'] ?? '';
$backgroundColor = $settings['background_color'] ?? '#22c55e';
$textColor = $settings['text_color'] ?? '#ffffff';
?>
<?php if ($text !== '') : ?>
    <section style="background: <?= htmlspecialchars($backgroundColor) ?>; color: <?= htmlspecialchars($textColor) ?>;">
        <div class="container py-3 text-center">
            <strong><?= htmlspecialchars($text) ?></strong>
        </div>
    </section>
<?php endif; ?>
