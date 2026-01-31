<?php

namespace App\Controllers;

use App\Models\Merchant;
use App\Models\Page;
use App\Models\Template;
use App\Models\Order;

class LandingPageController
{
    public function profile(string $merchantCode): void
    {
        $merchant = Merchant::findByCode($merchantCode);
        if (!$merchant) {
            http_response_code(404);
            echo 'Merchant not found.';
            return;
        }

        view('landing/profile', [
            'merchant' => $merchant,
        ]);
    }

    public function show(string $merchantCode, string $slug): void
    {
        $merchant = Merchant::findByCode($merchantCode);
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
        $deliveryPrices = $this->deliveryPriceMap($pageData, $merchant);

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
            'deliveryPrices' => $deliveryPrices,
        ]);
    }

    public function order(string $merchantCode, string $slug): void
    {
        $merchant = Merchant::findByCode($merchantCode);
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
        $deliveryPrices = $this->deliveryPriceMap($pageData, $merchant);
        $productPrice = (int) preg_replace('/[^0-9]/', '', $page['price']);
        $wilaya = trim($_POST['wilaya'] ?? '');
        $deliveryPrice = (int) ($deliveryPrices[$wilaya] ?? 500);
        $totalPrice = $productPrice + $deliveryPrice;
        $orderCode = $this->nextOrderCode((int) $merchant['id'], $merchant['order_prefix'] ?? 'GFM');

        $data = [
            'merchant_id' => (int) $merchant['id'],
            'page_id' => (int) $page['id'],
            'order_code' => $orderCode,
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'wilaya' => $wilaya,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'status' => 'new',
        ];

        foreach (['full_name', 'phone', 'address', 'wilaya'] as $field) {
            if ($data[$field] === '') {
                $_SESSION['flash_error'] = 'يرجى ملء جميع الحقول.';
                header('Location: /' . $merchantCode . '/' . $slug);
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
            'orderCode' => $orderCode,
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

    private function deliveryPriceMap(array $pageData, ?array $merchant = null): array
    {
        $default = 500;
        $wilayas = [
            'أدرار', 'الشلف', 'الأغواط', 'أم البواقي', 'باتنة', 'بجاية', 'بسكرة', 'بشار', 'البليدة', 'البويرة',
            'تمنراست', 'تبسة', 'تلمسان', 'تيارت', 'تيزي وزو', 'الجزائر', 'الجلفة', 'جيجل', 'سطيف', 'سعيدة',
            'سكيكدة', 'سيدي بلعباس', 'عنابة', 'قالمة', 'قسنطينة', 'المدية', 'مستغانم', 'المسيلة', 'معسكر', 'ورقلة',
            'وهران', 'البيض', 'إليزي', 'برج بوعريريج', 'بومرداس', 'الطارف', 'تندوف', 'تيسمسيلت', 'الوادي', 'خنشلة',
            'سوق أهراس', 'تيبازة', 'ميلة', 'عين الدفلى', 'النعامة', 'عين تموشنت', 'غرداية', 'غليزان', 'تيميمون', 'برج باجي مختار',
            'أولاد جلال', 'بني عباس', 'إن صالح', 'إن قزام', 'توقرت', 'جانت', 'المغير', 'المنيعة'
        ];

        $prices = [];
        foreach ($wilayas as $wilaya) {
            $prices[$wilaya] = $default;
        }

        $custom = $pageData['delivery_prices'] ?? null;
        if (is_string($custom)) {
            $decoded = json_decode($custom, true);
            if (is_array($decoded)) {
                $custom = $decoded;
            }
        }

        if (!is_array($custom) && $merchant) {
            $merchantPrices = $merchant['delivery_prices_json'] ?? null;
            if (is_string($merchantPrices)) {
                $decoded = json_decode($merchantPrices, true);
                if (is_array($decoded)) {
                    $custom = $decoded;
                }
            }
        }

        if (is_array($custom)) {
            foreach ($custom as $wilaya => $price) {
                if (isset($prices[$wilaya]) && is_numeric($price)) {
                    $prices[$wilaya] = (int) $price;
                }
            }
        }

        return $prices;
    }

    private function nextOrderCode(int $merchantId, string $prefix): string
    {
        $prefix = $prefix ?: 'GFM';
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS count FROM orders WHERE merchant_id = :merchant_id');
        $stmt->execute(['merchant_id' => $merchantId]);
        $count = (int) ($stmt->fetch()['count'] ?? 0) + 1;

        return strtoupper($prefix) . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}
