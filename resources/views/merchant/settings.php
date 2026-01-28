<?php
$sidebar = '<div class="brand">Merchant Panel</div>'
    . '<nav class="nav flex-column">'
    . '<a class="nav-link" href="/merchant"><i class="bi bi-speedometer2"></i> Dashboard</a>'
    . '<a class="nav-link" href="/merchant/settings"><i class="bi bi-gear"></i> Settings</a>'
    . '<form method="post" action="/logout" class="mt-4">'
    . csrf_field()
    . '<button class="btn btn-outline-light w-100" type="submit">Sign out</button>'
    . '</form>'
    . '</nav>';
$title = 'Merchant Settings';
$subtitle = 'Manage your WhatsApp, Telegram, and order message template.';
ob_start();
?>
<div class="card app-card">
    <div class="card-body">
        <form method="post" action="/merchant/settings" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($merchant['whatsapp_number']) ?>">
            </div>
            <div>
                <label class="form-label">Telegram Chat ID</label>
                <input type="text" name="telegram_chat_id" class="form-control" value="<?= htmlspecialchars($merchant['telegram_chat_id']) ?>">
            </div>
            <div>
                <label class="form-label">Order Message Template</label>
                <textarea name="order_message_template" class="form-control" rows="6"><?= htmlspecialchars($merchant['order_message_template']) ?></textarea>
                <div class="form-text text-secondary">Use variables: {{page}}, {{name}}, {{phone}}, {{address}}, {{wilaya}}</div>
            </div>
            <button class="btn btn-accent" type="submit">Save settings</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
