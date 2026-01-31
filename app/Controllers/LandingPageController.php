<?php

namespace App\Controllers;

use App\Models\Merchant;
use App\Models\Page;
use App\Models\Template;
use App\Models\Order;

class LandingPageController
{
    public function show(string $subdomain, string $slug): void
    {
        $merchant = Merchant::findBySubdomain($subdomain);
        if (!$merchant) {
            http_response_code(404);
            echo 'Merchant not found.';
            return;
        }

        $page = Page::findBySlug((int) $merchant['id'], $slug);
        if (!$page) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $template = Template::find((int) $page['template_id']);
        $fields = Template::fields((int) $page['template_id']);
        $pageData = json_decode($page['content_json'], true) ?? [];

        $viewKey = $template['view_key'] ?? 'default';
        $viewName = $viewKey === 'default' ? 'landing/page' : 'landing/templates/' . $viewKey;
        $viewFile = __DIR__ . '/../../resources/views/' . $viewName . '.php';
        if (!file_exists($viewFile)) {
            $viewName = 'landing/page';
        }

        view($viewName, [
            'merchant' => $merchant,
            'page' => $page,
            'template' => $template,
            'fields' => $fields,
            'pageData' => $pageData,
        ]);
    }

    public function order(string $subdomain, string $slug): void
    {
        $merchant = Merchant::findBySubdomain($subdomain);
        if (!$merchant) {
            http_response_code(404);
            echo 'Merchant not found.';
            return;
        }

        $page = Page::findBySlug((int) $merchant['id'], $slug);
        if (!$page) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $pageData = json_decode($page['content_json'], true) ?? [];
        $deliveryPrice = (int) ($pageData['delivery_price'] ?? 500);
        $productPrice = (int) preg_replace('/[^0-9]/', '', $page['price']);
        $totalPrice = $productPrice + $deliveryPrice;

        $data = [
            'merchant_id' => (int) $merchant['id'],
            'page_id' => (int) $page['id'],
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'wilaya' => trim($_POST['wilaya'] ?? ''),
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'status' => 'new',
        ];

        foreach (['full_name', 'phone', 'address', 'wilaya'] as $field) {
            if ($data[$field] === '') {
                $_SESSION['flash_error'] = 'Please fill in all fields.';
                header('Location: /p/' . $slug);
                return;
            }
        }

        $messageTemplate = $merchant['order_message_template'] ?: "طلب جديد للصفحة {{page}}\nالاسم: {{name}}\nالهاتف: {{phone}}\nالعنوان: {{address}}\nالولاية: {{wilaya}}\nسعر التوصيل: {{delivery}}\nالإجمالي: {{total}}";
        $message = str_replace(
            ['{{page}}', '{{name}}', '{{phone}}', '{{address}}', '{{wilaya}}', '{{delivery}}', '{{total}}'],
            [$page['title'], $data['full_name'], $data['phone'], $data['address'], $data['wilaya'], $deliveryPrice . ' دج', $totalPrice . ' دج'],
            $messageTemplate
        );

        $data['message_payload'] = $message;
        $orderId = Order::create($data);

        $whatsappLink = $this->whatsAppLink($merchant['whatsapp_number'], $message);
        $telegramLink = $this->telegramLink($merchant['telegram_chat_id'], $message);
        $this->sendTelegram($merchant['telegram_chat_id'], $message);

        view('landing/thank-you', [
            'merchant' => $merchant,
            'page' => $page,
            'order' => $data,
            'orderId' => $orderId,
            'whatsappLink' => $whatsappLink,
            'telegramLink' => $telegramLink,
        ]);
    }

    private function whatsAppLink(string $number, string $message): string
    {
        $clean = preg_replace('/[^0-9]/', '', $number);
        return 'https://wa.me/' . $clean . '?text=' . urlencode($message);
    }

    private function telegramLink(string $chatId, string $message): string
    {
        if ($chatId === '') {
            return '';
        }

        $config = require __DIR__ . '/../../config/app.php';
        if ($config['telegram_bot_token']) {
            return '';
        }

        $encoded = urlencode($message);
        return "https://t.me/share/url?url=&text={$encoded}";
    }

    private function sendTelegram(string $chatId, string $message): void
    {
        if ($chatId === '') {
            return;
        }

        $config = require __DIR__ . '/../../config/app.php';
        $token = $config['telegram_bot_token'];
        if (!$token) {
            return;
        }

        $payload = json_encode([
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        $ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 3,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}
