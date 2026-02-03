<?php
$sidebar = null;
$title = $page['title'] ?? '';
$subtitle = $merchant['name'] ?? '';
$sections = $customization['sections'] ?? [];
ob_start();
if (!is_array($sections)) {
    $sections = [];
}
?>
<div class="customized-landing">
    <?php if (!$sections) : ?>
        <section class="landing-hero">
            <div class="container">
                <h2 class="mb-3"><?= htmlspecialchars($page['title']) ?></h2>
                <p class="text-secondary">لم يتم إعداد أقسام مخصصة لهذه الصفحة بعد.</p>
            </div>
        </section>
    <?php else : ?>
        <?php foreach ($sections as $section) : ?>
            <?php
                $type = $section['type'] ?? '';
                $settings = is_array($section['settings'] ?? null) ? $section['settings'] : [];
                $blocks = is_array($section['blocks'] ?? null) ? $section['blocks'] : [];
                $sectionPath = __DIR__ . '/sections/' . basename((string) $type) . '.php';
            ?>
            <?php if ($type && file_exists($sectionPath)) : ?>
                <?php require $sectionPath; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
