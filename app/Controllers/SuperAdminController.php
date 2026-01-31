<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Order;

class SuperAdminController
{
    public function dashboard(): void
    {
        Auth::requireRole('super_admin');
        $stats = [
            'merchants' => (int) Database::connection()->query('SELECT COUNT(*) AS count FROM merchants')->fetch()['count'],
            'pages' => (int) Database::connection()->query('SELECT COUNT(*) AS count FROM pages')->fetch()['count'],
            'orders' => (int) Database::connection()->query('SELECT COUNT(*) AS count FROM orders')->fetch()['count'],
            'templates' => (int) Database::connection()->query('SELECT COUNT(*) AS count FROM templates')->fetch()['count'],
        ];

        view('admin/dashboard', [
            'stats' => $stats,
        ]);
    }

    public function merchantsIndex(): void
    {
        Auth::requireRole('super_admin');
        $merchants = Database::connection()->query('SELECT * FROM merchants ORDER BY created_at DESC')->fetchAll();
        view('admin/merchants', ['merchants' => $merchants]);
    }

    public function merchantProfile(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_GET['merchant_id'] ?? 0);
        $stmt = Database::connection()->prepare('SELECT * FROM merchants WHERE id = :id');
        $stmt->execute(['id' => $merchantId]);
        $merchant = $stmt->fetch();

        if (!$merchant) {
            http_response_code(404);
            echo 'Merchant not found.';
            return;
        }

        view('admin/merchant-profile', ['merchant' => $merchant]);
    }

    public function updateMerchantProfile(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_POST['merchant_id'] ?? 0);

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'subdomain' => trim($_POST['subdomain'] ?? ''),
            'profile_name' => trim($_POST['profile_name'] ?? ''),
            'profile_bio' => trim($_POST['profile_bio'] ?? ''),
            'profile_about' => trim($_POST['profile_about'] ?? ''),
            'profile_phone' => trim($_POST['profile_phone'] ?? ''),
            'profile_email' => trim($_POST['profile_email'] ?? ''),
            'profile_address' => trim($_POST['profile_address'] ?? ''),
            'logo_url' => trim($_POST['logo_url'] ?? ''),
            'cover_url' => trim($_POST['cover_url'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? ''),
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'tiktok_url' => trim($_POST['tiktok_url'] ?? ''),
            'website_url' => trim($_POST['website_url'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'telegram_chat_id' => trim($_POST['telegram_chat_id'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'id' => $merchantId,
        ];

        $stmt = Database::connection()->prepare('UPDATE merchants SET name = :name, subdomain = :subdomain, profile_name = :profile_name, profile_bio = :profile_bio, profile_about = :profile_about, profile_phone = :profile_phone, profile_email = :profile_email, profile_address = :profile_address, logo_url = :logo_url, cover_url = :cover_url, instagram_url = :instagram_url, facebook_url = :facebook_url, tiktok_url = :tiktok_url, website_url = :website_url, whatsapp_number = :whatsapp_number, telegram_chat_id = :telegram_chat_id, is_active = :is_active WHERE id = :id');
        $stmt->execute($data);

        $_SESSION['flash_success'] = 'تم تحديث الملف التعريفي للتاجر.';
        header('Location: /admin/merchants/profile?merchant_id=' . $merchantId);
    }

    public function createMerchant(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $subdomain = trim($_POST['subdomain'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $subdomain === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'يرجى ملء جميع بيانات التاجر.';
            header('Location: /admin/merchants');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO merchants (name, subdomain, whatsapp_number, telegram_chat_id, order_message_template, is_active, created_at) VALUES (:name, :subdomain, :whatsapp, :telegram, :template, 1, NOW())');
        $stmt->execute([
            'name' => $name,
            'subdomain' => $subdomain,
            'whatsapp' => '',
            'telegram' => '',
            'template' => "طلب جديد للصفحة {{page}}\nالاسم: {{name}}\nالهاتف: {{phone}}\nالعنوان: {{address}}\nالولاية: {{wilaya}}\nسعر التوصيل: {{delivery}}\nالإجمالي: {{total}}",
        ]);
        $merchantId = (int) Database::connection()->lastInsertId();

        $stmt = Database::connection()->prepare('INSERT INTO users (name, email, password_hash, role, merchant_id, created_at) VALUES (:name, :email, :password_hash, :role, :merchant_id, NOW())');
        $stmt->execute([
            'name' => $name . ' Owner',
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'merchant',
            'merchant_id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'تم إنشاء التاجر بنجاح.';
        header('Location: /admin/merchants');
    }

    public function updateMerchant(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_POST['merchant_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $subdomain = trim($_POST['subdomain'] ?? '');
        $whatsapp = trim($_POST['whatsapp_number'] ?? '');
        $telegram = trim($_POST['telegram_chat_id'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = Database::connection()->prepare('UPDATE merchants SET name = :name, subdomain = :subdomain, whatsapp_number = :whatsapp, telegram_chat_id = :telegram, is_active = :is_active WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'subdomain' => $subdomain,
            'whatsapp' => $whatsapp,
            'telegram' => $telegram,
            'is_active' => $isActive,
            'id' => $merchantId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث بيانات التاجر.';
        header('Location: /admin/merchants');
    }

    public function deleteMerchant(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_POST['merchant_id'] ?? 0);
        $stmt = Database::connection()->prepare('DELETE FROM merchants WHERE id = :id');
        $stmt->execute(['id' => $merchantId]);
        $_SESSION['flash_success'] = 'تم حذف التاجر.';
        header('Location: /admin/merchants');
    }

    public function templatesIndex(): void
    {
        Auth::requireRole('super_admin');
        $templates = Database::connection()->query('SELECT * FROM templates ORDER BY created_at DESC')->fetchAll();
        view('admin/templates', ['templates' => $templates]);
    }

    public function previewTemplate(): void
    {
        Auth::requireRole('super_admin');
        $templateId = (int) ($_GET['template_id'] ?? 0);
        $stmt = Database::connection()->prepare('SELECT * FROM templates WHERE id = :id');
        $stmt->execute(['id' => $templateId]);
        $template = $stmt->fetch();

        if (!$template) {
            http_response_code(404);
            echo 'Template not found.';
            return;
        }

        $fields = Database::connection()->prepare('SELECT * FROM template_fields WHERE template_id = :template_id');
        $fields->execute(['template_id' => $templateId]);
        $fieldRows = $fields->fetchAll();

        $pageData = [];
        foreach ($fieldRows as $field) {
            $pageData[$field['field_key']] = match ($field['field_key']) {
                'headline' => 'عرض حصري لتجربة القالب',
                'subheadline' => 'هذا مثال توضيحي لواجهة صفحة الهبوط قبل اعتمادها.',
                'features' => "- تصميم متجاوب\n- نموذج طلب سريع\n- دعم واتساب وتيليجرام",
                'gallery' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80,https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
                'delivery_price' => '500',
                default => '...',
            };
        }

        $merchant = ['name' => 'اسم التاجر'];
        $page = [
            'title' => 'منتج تجريبي',
            'description' => 'وصف مختصر يظهر في القالب كنموذج.',
            'price' => '4500',
            'slug' => 'preview',
        ];

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
            'fields' => $fieldRows,
            'pageData' => $pageData,
        ]);
    }

    public function createTemplate(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $viewKey = trim($_POST['view_key'] ?? 'default');

        if ($name === '') {
            $_SESSION['flash_error'] = 'اسم القالب مطلوب.';
            header('Location: /admin/templates');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO templates (name, description, view_key, created_at) VALUES (:name, :description, :view_key, NOW())');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'view_key' => $viewKey ?: 'default',
        ]);
        $templateId = (int) Database::connection()->lastInsertId();

        $defaultFields = [
            ['field_key' => 'headline', 'label' => 'العنوان الرئيسي', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'subheadline', 'label' => 'الوصف القصير', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'features', 'label' => 'مزايا المنتج', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
            ['field_key' => 'gallery', 'label' => 'معرض الصور (روابط مفصولة بفواصل)', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
            ['field_key' => 'delivery_price', 'label' => 'سعر التوصيل (دج)', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'delivery_prices', 'label' => 'أسعار التوصيل لكل ولاية (JSON)', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
        ];

        $stmt = Database::connection()->prepare('INSERT INTO template_fields (template_id, field_key, label, field_type, is_editable_by_merchant, display_order) VALUES (:template_id, :field_key, :label, :field_type, :editable, :display_order)');
        foreach ($defaultFields as $index => $field) {
            $stmt->execute([
                'template_id' => $templateId,
                'field_key' => $field['field_key'],
                'label' => $field['label'],
                'field_type' => $field['field_type'],
                'editable' => $field['is_editable_by_merchant'],
                'display_order' => $index + 1,
            ]);
        }

        $_SESSION['flash_success'] = 'تم إنشاء القالب.';
        header('Location: /admin/templates');
    }

    public function updateTemplate(): void
    {
        Auth::requireRole('super_admin');
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $viewKey = trim($_POST['view_key'] ?? 'default');

        $stmt = Database::connection()->prepare('UPDATE templates SET name = :name, description = :description, view_key = :view_key WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'view_key' => $viewKey ?: 'default',
            'id' => $templateId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث القالب.';
        header('Location: /admin/templates');
    }

    public function deleteTemplate(): void
    {
        Auth::requireRole('super_admin');
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $stmt = Database::connection()->prepare('DELETE FROM templates WHERE id = :id');
        $stmt->execute(['id' => $templateId]);
        $_SESSION['flash_success'] = 'تم حذف القالب.';
        header('Location: /admin/templates');
    }

    public function pagesIndex(): void
    {
        Auth::requireRole('super_admin');
        $merchants = Database::connection()->query('SELECT * FROM merchants ORDER BY name')->fetchAll();
        $templates = Database::connection()->query('SELECT * FROM templates ORDER BY name')->fetchAll();
        $pages = Database::connection()->query('SELECT pages.*, merchants.name AS merchant_name, merchants.subdomain FROM pages INNER JOIN merchants ON pages.merchant_id = merchants.id ORDER BY pages.created_at DESC')->fetchAll();
        foreach ($pages as &$page) {
            $content = json_decode($page['content_json'] ?? '{}', true) ?? [];
            $page['delivery_price'] = $content['delivery_price'] ?? '500';
            $page['delivery_prices'] = $content['delivery_prices'] ?? '';
        }

        $defaultDeliveryPrices = $this->defaultDeliveryPrices();

        view('admin/pages', [
            'merchants' => $merchants,
            'templates' => $templates,
            'pages' => $pages,
            'defaultDeliveryPrices' => $defaultDeliveryPrices,
        ]);
    }

    public function createPage(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_POST['merchant_id'] ?? 0);
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deliveryPrice = trim($_POST['delivery_price'] ?? '500');
        $deliveryPrices = trim($_POST['delivery_prices'] ?? '');

        if ($merchantId === 0 || $templateId === 0 || $title === '' || $slug === '') {
            $_SESSION['flash_error'] = 'يرجى ملء جميع بيانات الصفحة.';
            header('Location: /admin/pages');
            return;
        }

        $content = [
            'headline' => $title,
            'subheadline' => $description,
            'features' => "- توصيل سريع\n- الدفع عند الاستلام\n- منتج موثوق",
            'gallery' => '',
            'delivery_price' => $deliveryPrice,
            'delivery_prices' => $deliveryPrices !== '' ? $deliveryPrices : json_encode($this->defaultDeliveryPrices(), JSON_UNESCAPED_UNICODE),
        ];

        $stmt = Database::connection()->prepare('INSERT INTO pages (merchant_id, template_id, title, slug, price, description, content_json, is_active, created_at) VALUES (:merchant_id, :template_id, :title, :slug, :price, :description, :content_json, 1, NOW())');
        $stmt->execute([
            'merchant_id' => $merchantId,
            'template_id' => $templateId,
            'title' => $title,
            'slug' => $slug,
            'price' => $price,
            'description' => $description,
            'content_json' => json_encode($content),
        ]);

        $_SESSION['flash_success'] = 'تم إنشاء الصفحة.';
        header('Location: /admin/pages');
    }

    public function updatePage(): void
    {
        Auth::requireRole('super_admin');
        $pageId = (int) ($_POST['page_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deliveryPrice = trim($_POST['delivery_price'] ?? '500');
        $deliveryPrices = trim($_POST['delivery_prices'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $pageStmt = Database::connection()->prepare('SELECT content_json FROM pages WHERE id = :id');
        $pageStmt->execute(['id' => $pageId]);
        $page = $pageStmt->fetch();
        $content = json_decode($page['content_json'] ?? '{}', true) ?? [];
        $content['delivery_price'] = $deliveryPrice;
        if ($deliveryPrices !== '') {
            $content['delivery_prices'] = $deliveryPrices;
        }

        $stmt = Database::connection()->prepare('UPDATE pages SET title = :title, slug = :slug, price = :price, description = :description, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'price' => $price,
            'description' => $description,
            'is_active' => $isActive,
            'id' => $pageId,
        ]);

        $contentStmt = Database::connection()->prepare('UPDATE pages SET content_json = :content_json WHERE id = :id');
        $contentStmt->execute([
            'content_json' => json_encode($content, JSON_UNESCAPED_UNICODE),
            'id' => $pageId,
        ]);

        $_SESSION['flash_success'] = 'تم تحديث الصفحة.';
        header('Location: /admin/pages');
    }

    public function deletePage(): void
    {
        Auth::requireRole('super_admin');
        $pageId = (int) ($_POST['page_id'] ?? 0);
        $stmt = Database::connection()->prepare('DELETE FROM pages WHERE id = :id');
        $stmt->execute(['id' => $pageId]);
        $_SESSION['flash_success'] = 'تم حذف الصفحة.';
        header('Location: /admin/pages');
    }

    public function ordersIndex(): void
    {
        Auth::requireRole('super_admin');
        $orders = Order::all();
        view('admin/orders', ['orders' => $orders]);
    }

    public function updateOrder(): void
    {
        Auth::requireRole('super_admin');
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $stmt = Database::connection()->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $orderId]);
        $_SESSION['flash_success'] = 'تم تحديث الطلب.';
        header('Location: /admin/orders');
    }

    public function deleteOrder(): void
    {
        Auth::requireRole('super_admin');
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $stmt = Database::connection()->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
        $_SESSION['flash_success'] = 'تم حذف الطلب.';
        header('Location: /admin/orders');
    }

    public function usersIndex(): void
    {
        Auth::requireRole('super_admin');
        $users = Database::connection()->query("SELECT * FROM users WHERE role = 'super_admin' ORDER BY created_at DESC")->fetchAll();
        view('admin/users', ['users' => $users]);
    }

    public function createUser(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'يرجى ملء جميع بيانات المشرف.';
            header('Location: /admin/users');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:name, :email, :password_hash, :role, NOW())');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'super_admin',
        ]);

        $_SESSION['flash_success'] = 'تم إضافة مشرف جديد.';
        header('Location: /admin/users');
    }

    public function updateUser(): void
    {
        Auth::requireRole('super_admin');
        $userId = (int) ($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $params = [
            'name' => $name,
            'email' => $email,
            'id' => $userId,
        ];

        $sql = 'UPDATE users SET name = :name, email = :email';
        if ($password !== '') {
            $sql .= ', password_hash = :password_hash';
            $params['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }
        $sql .= ' WHERE id = :id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        $_SESSION['flash_success'] = 'تم تحديث بيانات المشرف.';
        header('Location: /admin/users');
    }

    public function deleteUser(): void
    {
        Auth::requireRole('super_admin');
        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId === (int) Auth::user()['id']) {
            $_SESSION['flash_error'] = 'لا يمكن حذف الحساب الحالي.';
            header('Location: /admin/users');
            return;
        }

        $stmt = Database::connection()->prepare('DELETE FROM users WHERE id = :id AND role = "super_admin"');
        $stmt->execute(['id' => $userId]);
        $_SESSION['flash_success'] = 'تم حذف المشرف.';
        header('Location: /admin/users');
    }

    public function profile(): void
    {
        Auth::requireRole('super_admin');
        $user = Database::connection()->prepare('SELECT * FROM users WHERE id = :id');
        $user->execute(['id' => Auth::user()['id']]);
        $profile = $user->fetch();

        view('admin/profile', ['profile' => $profile]);
    }

    public function updateProfile(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $userId = (int) Auth::user()['id'];

        $params = [
            'name' => $name,
            'email' => $email,
            'id' => $userId,
        ];

        $sql = 'UPDATE users SET name = :name, email = :email';
        if ($password !== '') {
            $sql .= ', password_hash = :password_hash';
            $params['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }
        $sql .= ' WHERE id = :id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        $_SESSION['flash_success'] = 'تم تحديث بيانات الحساب.';
        header('Location: /admin/profile');
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
