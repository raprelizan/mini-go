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

        view('landing/page', [
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

        $data = [
            'merchant_id' => (int) $merchant['id'],
            'page_id' => (int) $page['id'],
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'wilaya' => trim($_POST['wilaya'] ?? ''),
            'status' => 'new',
        ];

        foreach (['full_name', 'phone', 'address', 'wilaya'] as $field) {
            if ($data[$field] === '') {
                $_SESSION['flash_error'] = 'Please fill in all fields.';
                header('Location: /p/' . $slug);
                return;
            }
        }

        $messageTemplate = $merchant['order_message_template'] ?: "New order for {{page}}\nName: {{name}}\nPhone: {{phone}}\nAddress: {{address}}\nWilaya: {{wilaya}}";
        $message = str_replace(
            ['{{page}}', '{{name}}', '{{phone}}', '{{address}}', '{{wilaya}}'],
            [$page['title'], $data['full_name'], $data['phone'], $data['address'], $data['wilaya']],
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
