<?php
$sidebar = null;
$title = $page['title'] ?? '';
$subtitle = $merchant['name'] ?? '';
ob_start();
?>
<div class="theme-engine-render">
    <?= $renderedSections ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
