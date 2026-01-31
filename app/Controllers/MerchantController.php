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
        $merchantStmt = Database::connection()->prepare('SELECT * FROM merchants WHERE id = :id');
        $merchantStmt->execute(['id' => $merchantId]);
        $merchant = $merchantStmt->fetch();
        $pages = Page::allForMerchant($merchantId);
        $orders = Order::allForMerchant($merchantId);

        view('merchant/dashboard', [
            'pages' => $pages,
            'orders' => $orders,
            'merchant' => $merchant,
        ]);
    }

    public function orders(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $orders = Order::allForMerchant($merchantId);

        view('merchant/orders', ['orders' => $orders]);
    }

    public function orderSheet(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $orderId = (int) ($_GET['order_id'] ?? 0);

        $stmt = Database::connection()->prepare('SELECT orders.*, pages.title AS page_title FROM orders INNER JOIN pages ON orders.page_id = pages.id WHERE orders.id = :id AND orders.merchant_id = :merchant_id');
        $stmt->execute(['id' => $orderId, 'merchant_id' => $merchantId]);
        $order = $stmt->fetch();

        if (!$order) {
            http_response_code(404);
            echo 'Order not found.';
            return;
        }

        view('merchant/order-sheet', ['order' => $order]);
    }

    public function updateOrderStatus(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        $stmt = Database::connection()->prepare('UPDATE orders SET status = :status WHERE id = :id AND merchant_id = :merchant_id');
        $stmt->execute([
            'status' => $status,
            'id' => $orderId,
            'merchant_id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث حالة الطلب.';
        header('Location: /merchant/orders');
    }

    public function deliveryPrices(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $stmt = Database::connection()->prepare('SELECT delivery_prices_json, order_prefix FROM merchants WHERE id = :id');
        $stmt->execute(['id' => $merchantId]);
        $merchant = $stmt->fetch();

        $defaultPrices = $this->defaultDeliveryPrices();
        $deliveryPrices = $defaultPrices;
        if (!empty($merchant['delivery_prices_json'])) {
            $decoded = json_decode($merchant['delivery_prices_json'], true);
            if (is_array($decoded)) {
                $deliveryPrices = array_merge($deliveryPrices, $decoded);
            }
        }

        view('merchant/delivery-prices', [
            'deliveryPrices' => $deliveryPrices,
            'orderPrefix' => $merchant['order_prefix'] ?? 'GFM',
        ]);
    }

    public function updateDeliveryPrices(): void
    {
        Auth::requireRole('merchant');
        $merchantId = (int) Auth::user()['merchant_id'];
        $orderPrefix = trim($_POST['order_prefix'] ?? 'GFM');
        $deliveryPrices = $_POST['delivery_prices'] ?? [];
        if (!is_array($deliveryPrices)) {
            $deliveryPrices = [];
        }
        $sanitized = [];
        foreach ($this->defaultDeliveryPrices() as $wilaya => $defaultPrice) {
            $price = $deliveryPrices[$wilaya] ?? $defaultPrice;
            $sanitized[$wilaya] = is_numeric($price) ? (int) $price : $defaultPrice;
        }

        $stmt = Database::connection()->prepare('UPDATE merchants SET delivery_prices_json = :delivery_prices_json, order_prefix = :order_prefix WHERE id = :id');
        $stmt->execute([
            'delivery_prices_json' => json_encode($sanitized, JSON_UNESCAPED_UNICODE),
            'order_prefix' => $orderPrefix,
            'id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث أسعار التوصيل.';
        header('Location: /merchant/delivery-prices');
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

        $galleryUrls = [];
        $existingGallery = $_POST['gallery_existing'] ?? [];
        $removedGallery = $_POST['gallery_remove'] ?? [];
        if (is_array($existingGallery)) {
            foreach ($existingGallery as $url) {
                $url = trim((string) $url);
                if ($url === '') {
                    continue;
                }
                if (is_array($removedGallery) && in_array($url, $removedGallery, true)) {
                    continue;
                }
                $galleryUrls[] = $url;
            }
        }

        if (!empty($content['gallery'])) {
            $typedUrls = array_filter(array_map('trim', explode(',', (string) $content['gallery'])));
            $galleryUrls = array_merge($galleryUrls, $typedUrls);
        }

        if (!empty($_FILES['gallery_files']['name'][0])) {
            $uploadDir = __DIR__ . '/../../public/uploads/merchant_' . $merchantId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['gallery_files']['name'] as $index => $name) {
                if ($_FILES['gallery_files']['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }
                $tmpName = $_FILES['gallery_files']['tmp_name'][$index];
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $safeName = uniqid('img_', true) . '.' . $extension;
                $destination = $uploadDir . '/' . $safeName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $galleryUrls[] = '/uploads/merchant_' . $merchantId . '/' . $safeName;
                }
            }
        }
        if ($galleryUrls) {
            $content['gallery'] = implode(',', array_values(array_unique($galleryUrls)));
        } elseif (array_key_exists('gallery', $content)) {
            $content['gallery'] = '';
        }

        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = Database::connection()->prepare('UPDATE pages SET content_json = :content_json, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'content_json' => json_encode($content),
            'is_active' => $isActive,
            'id' => $pageId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث الصفحة بنجاح.';
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
        $orderPrefix = trim($_POST['order_prefix'] ?? '');
        $profileName = trim($_POST['profile_name'] ?? '');
        $profileBio = trim($_POST['profile_bio'] ?? '');
        $profileAbout = trim($_POST['profile_about'] ?? '');
        $profilePhone = trim($_POST['profile_phone'] ?? '');
        $profileEmail = trim($_POST['profile_email'] ?? '');
        $profileAddress = trim($_POST['profile_address'] ?? '');
        $logoUrl = trim($_POST['logo_url'] ?? '');
        $coverUrl = trim($_POST['cover_url'] ?? '');
        $instagramUrl = trim($_POST['instagram_url'] ?? '');
        $facebookUrl = trim($_POST['facebook_url'] ?? '');
        $tiktokUrl = trim($_POST['tiktok_url'] ?? '');
        $websiteUrl = trim($_POST['website_url'] ?? '');

        $stmt = Database::connection()->prepare('UPDATE merchants SET whatsapp_number = :whatsapp, telegram_chat_id = :telegram, order_message_template = :template, order_prefix = :order_prefix, profile_name = :profile_name, profile_bio = :profile_bio, profile_about = :profile_about, profile_phone = :profile_phone, profile_email = :profile_email, profile_address = :profile_address, logo_url = :logo_url, cover_url = :cover_url, instagram_url = :instagram_url, facebook_url = :facebook_url, tiktok_url = :tiktok_url, website_url = :website_url WHERE id = :id');
        $stmt->execute([
            'whatsapp' => $whatsapp,
            'telegram' => $telegram,
            'template' => $template,
            'order_prefix' => $orderPrefix,
            'profile_name' => $profileName,
            'profile_bio' => $profileBio,
            'profile_about' => $profileAbout,
            'profile_phone' => $profilePhone,
            'profile_email' => $profileEmail,
            'profile_address' => $profileAddress,
            'logo_url' => $logoUrl,
            'cover_url' => $coverUrl,
            'instagram_url' => $instagramUrl,
            'facebook_url' => $facebookUrl,
            'tiktok_url' => $tiktokUrl,
            'website_url' => $websiteUrl,
            'id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث الإعدادات بنجاح.';
        header('Location: /merchant/settings');
    }

    private function defaultDeliveryPrices(): array
    {
        return [
            'أدرار' => 500,
            'الشلف' => 500,
            'الأغواط' => 500,
            'أم البواقي' => 500,
            'باتنة' => 500,
            'بجاية' => 500,
            'بسكرة' => 500,
            'بشار' => 500,
            'البليدة' => 500,
            'البويرة' => 500,
            'تمنراست' => 500,
            'تبسة' => 500,
            'تلمسان' => 500,
            'تيارت' => 500,
            'تيزي وزو' => 500,
            'الجزائر' => 500,
            'الجلفة' => 500,
            'جيجل' => 500,
            'سطيف' => 500,
            'سعيدة' => 500,
            'سكيكدة' => 500,
            'سيدي بلعباس' => 500,
            'عنابة' => 500,
            'قالمة' => 500,
            'قسنطينة' => 500,
            'المدية' => 500,
            'مستغانم' => 500,
            'المسيلة' => 500,
            'معسكر' => 500,
            'ورقلة' => 500,
            'وهران' => 500,
            'البيض' => 500,
            'إليزي' => 500,
            'برج بوعريريج' => 500,
            'بومرداس' => 500,
            'الطارف' => 500,
            'تندوف' => 500,
            'تيسمسيلت' => 500,
            'الوادي' => 500,
            'خنشلة' => 500,
            'سوق أهراس' => 500,
            'تيبازة' => 500,
            'ميلة' => 500,
            'عين الدفلى' => 500,
            'النعامة' => 500,
            'عين تموشنت' => 500,
            'غرداية' => 500,
            'غليزان' => 500,
            'تيميمون' => 500,
            'برج باجي مختار' => 500,
            'أولاد جلال' => 500,
            'بني عباس' => 500,
            'إن صالح' => 500,
            'إن قزام' => 500,
            'توقرت' => 500,
            'جانت' => 500,
            'المغير' => 500,
            'المنيعة' => 500,
        ];
    }
}
