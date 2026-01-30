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
            'template' => "طلب جديد للصفحة {{page}}\nالاسم: {{name}}\nالهاتف: {{phone}}\nالعنوان: {{address}}\nالولاية: {{wilaya}}",
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

    public function createTemplate(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $_SESSION['flash_error'] = 'اسم القالب مطلوب.';
            header('Location: /admin/templates');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO templates (name, description, created_at) VALUES (:name, :description, NOW())');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
        ]);
        $templateId = (int) Database::connection()->lastInsertId();

        $defaultFields = [
            ['field_key' => 'headline', 'label' => 'العنوان الرئيسي', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'subheadline', 'label' => 'الوصف القصير', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'features', 'label' => 'مزايا المنتج', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
            ['field_key' => 'gallery', 'label' => 'معرض الصور (روابط مفصولة بفواصل)', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
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

        $stmt = Database::connection()->prepare('UPDATE templates SET name = :name, description = :description WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
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

        view('admin/pages', [
            'merchants' => $merchants,
            'templates' => $templates,
            'pages' => $pages,
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
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = Database::connection()->prepare('UPDATE pages SET title = :title, slug = :slug, price = :price, description = :description, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'price' => $price,
            'description' => $description,
            'is_active' => $isActive,
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
}
