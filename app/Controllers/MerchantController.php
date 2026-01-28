<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Page;
use App\Models\Template;
use App\Models\Order;

class MerchantController
{
    public function dashboard(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $pages = Page::allForMerchant($merchantId);
        $orders = Order::allForMerchant($merchantId);

        view('merchant/dashboard', [
            'pages' => $pages,
            'orders' => $orders,
        ]);
    }

    public function editPage(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $pageId = (int) ($_GET['page_id'] ?? 0);

        $stmt = Database::connection()->prepare('SELECT * FROM pages WHERE id = :id AND merchant_id = :merchant_id');
        $stmt->execute(['id' => $pageId, 'merchant_id' => $merchantId]);
        $page = $stmt->fetch();

        if (!$page) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $fields = Template::fields((int) $page['template_id']);
        $pageData = json_decode($page['content_json'], true) ?? [];

        view('merchant/edit-page', [
            'page' => $page,
            'fields' => $fields,
            'pageData' => $pageData,
        ]);
    }

    public function updatePage(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $pageId = (int) ($_POST['page_id'] ?? 0);

        $stmt = Database::connection()->prepare('SELECT * FROM pages WHERE id = :id AND merchant_id = :merchant_id');
        $stmt->execute(['id' => $pageId, 'merchant_id' => $merchantId]);
        $page = $stmt->fetch();

        if (!$page) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $fields = Template::fields((int) $page['template_id']);
        $content = json_decode($page['content_json'], true) ?? [];

        foreach ($fields as $field) {
            if ((int) $field['is_editable_by_merchant'] !== 1) {
                continue;
            }
            $value = trim($_POST['field_' . $field['field_key']] ?? '');
            if ($value !== '') {
                $content[$field['field_key']] = $value;
            }
        }

        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = Database::connection()->prepare('UPDATE pages SET content_json = :content_json, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'content_json' => json_encode($content),
            'is_active' => $isActive,
            'id' => $pageId,
        ]);

        $_SESSION['flash_success'] = 'Page updated successfully.';
        header('Location: /merchant');
    }

    public function settings(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];

        $stmt = Database::connection()->prepare('SELECT * FROM merchants WHERE id = :id');
        $stmt->execute(['id' => $merchantId]);
        $merchant = $stmt->fetch();

        view('merchant/settings', ['merchant' => $merchant]);
    }

    public function updateSettings(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];

        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $telegram = trim($_POST['telegram_chat_id'] ?? '');
        $template = trim($_POST['order_message_template'] ?? '');

        $stmt = Database::connection()->prepare('UPDATE merchants SET whatsapp_number = :whatsapp, telegram_chat_id = :telegram, order_message_template = :template WHERE id = :id');
        $stmt->execute([
            'whatsapp' => $whatsapp,
            'telegram' => $telegram,
            'template' => $template,
            'id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'Settings updated successfully.';
        header('Location: /merchant/settings');
    }
}
