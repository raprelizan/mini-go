<?php
    $appConfig = require __DIR__ . '/../../../config/app.php';
    $flashError = $_SESSION['flash_error'] ?? null;
    $flashSuccess = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appConfig['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-dark text-light">
    <div class="app-shell">
        <?php if (isset($sidebar)) : ?>
            <aside class="sidebar">
                <?= $sidebar ?>
            </aside>
        <?php endif; ?>
        <main class="main-content">
            <header class="app-header">
                <div>
                    <h1 class="app-title"><?= htmlspecialchars($title ?? $appConfig['name']) ?></h1>
                    <?php if (!empty($subtitle ?? '')) : ?>
                        <p class="app-subtitle"><?= htmlspecialchars($subtitle) ?></p>
                    <?php endif; ?>
                </div>
                <?php if (isset($headerAction)) : ?>
                    <div class="header-action"><?= $headerAction ?></div>
                <?php endif; ?>
            </header>
            <?php if ($flashError) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
            <?php endif; ?>
            <?php if ($flashSuccess) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
