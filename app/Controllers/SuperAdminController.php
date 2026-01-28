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
        $merchants = Database::connection()->query('SELECT * FROM merchants ORDER BY created_at DESC')->fetchAll();
        $pages = Database::connection()->query('SELECT pages.*, merchants.name AS merchant_name FROM pages INNER JOIN merchants ON pages.merchant_id = merchants.id ORDER BY pages.created_at DESC')->fetchAll();
        $orders = Order::all();
        $templates = Database::connection()->query('SELECT * FROM templates ORDER BY created_at DESC')->fetchAll();

        view('admin/dashboard', [
            'merchants' => $merchants,
            'pages' => $pages,
            'orders' => $orders,
            'templates' => $templates,
        ]);
    }

    public function createMerchant(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $subdomain = trim($_POST['subdomain'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $subdomain === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Please fill in all merchant fields.';
            header('Location: /admin');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO merchants (name, subdomain, whatsapp_number, telegram_chat_id, order_message_template, is_active, created_at) VALUES (:name, :subdomain, :whatsapp, :telegram, :template, 1, NOW())');
        $stmt->execute([
            'name' => $name,
            'subdomain' => $subdomain,
            'whatsapp' => '',
            'telegram' => '',
            'template' => "New order for {{page}}\nName: {{name}}\nPhone: {{phone}}\nAddress: {{address}}\nWilaya: {{wilaya}}",
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

        $_SESSION['flash_success'] = 'Merchant created successfully.';
        header('Location: /admin');
    }

    public function toggleMerchant(): void
    {
        Auth::requireRole('super_admin');
        $merchantId = (int) ($_POST['merchant_id'] ?? 0);
        $stmt = Database::connection()->prepare('UPDATE merchants SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id');
        $stmt->execute(['id' => $merchantId]);
        $_SESSION['flash_success'] = 'Merchant status updated.';
        header('Location: /admin');
    }

    public function createTemplate(): void
    {
        Auth::requireRole('super_admin');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $_SESSION['flash_error'] = 'Template name is required.';
            header('Location: /admin');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO templates (name, description, created_at) VALUES (:name, :description, NOW())');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
        ]);
        $templateId = (int) Database::connection()->lastInsertId();

        $defaultFields = [
            ['field_key' => 'headline', 'label' => 'Headline', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'subheadline', 'label' => 'Subheadline', 'field_type' => 'text', 'is_editable_by_merchant' => 1],
            ['field_key' => 'features', 'label' => 'Key Features', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
            ['field_key' => 'gallery', 'label' => 'Image Gallery (comma separated URLs)', 'field_type' => 'textarea', 'is_editable_by_merchant' => 1],
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

        $_SESSION['flash_success'] = 'Template created.';
        header('Location: /admin');
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
            $_SESSION['flash_error'] = 'Please fill in all page fields.';
            header('Location: /admin');
            return;
        }

        $content = [
            'headline' => $title,
            'subheadline' => $description,
            'features' => "- Fast delivery\n- Cash on delivery\n- Verified product",
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

        $_SESSION['flash_success'] = 'Page created.';
        header('Location: /admin');
    }

    public function togglePage(): void
    {
        Auth::requireRole('super_admin');
        $pageId = (int) ($_POST['page_id'] ?? 0);
        $stmt = Database::connection()->prepare('UPDATE pages SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id');
        $stmt->execute(['id' => $pageId]);
        $_SESSION['flash_success'] = 'Page status updated.';
        header('Location: /admin');
    }
}
