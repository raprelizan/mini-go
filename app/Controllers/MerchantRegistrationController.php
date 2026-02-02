<?php

namespace App\Controllers;

use App\Core\Database;

class MerchantRegistrationController
{
    public function show(): void
    {
        view('merchant/register');
    }

    public function store(): void
    {
        $merchantName = trim($_POST['merchant_name'] ?? '');
        $tradeName = trim($_POST['trade_name'] ?? '');
        $businessType = trim($_POST['business_type'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $termsAccepted = isset($_POST['terms']);

        if ($merchantName === '' || $tradeName === '' || $businessType === '' || $phone === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'يرجى ملء جميع البيانات المطلوبة.';
            header('Location: /merchant/register');
            return;
        }

        if (!$termsAccepted) {
            $_SESSION['flash_error'] = 'يرجى الموافقة على شروط الاستخدام.';
            header('Location: /merchant/register');
            return;
        }

        $existingUser = Database::connection()->prepare('SELECT id FROM users WHERE email = :email');
        $existingUser->execute(['email' => $email]);
        if ($existingUser->fetch()) {
            $_SESSION['flash_error'] = 'هذا البريد الإلكتروني مستخدم بالفعل.';
            header('Location: /merchant/register');
            return;
        }

        $existingRequest = Database::connection()->prepare('SELECT id FROM merchant_registrations WHERE email = :email AND status = "pending"');
        $existingRequest->execute(['email' => $email]);
        if ($existingRequest->fetch()) {
            $_SESSION['flash_error'] = 'تم إرسال طلب سابق بهذا البريد الإلكتروني.';
            header('Location: /merchant/register');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO merchant_registrations (merchant_name, trade_name, business_type, phone, email, password_hash, terms_accepted, status, created_at) VALUES (:merchant_name, :trade_name, :business_type, :phone, :email, :password_hash, :terms_accepted, :status, NOW())');
        $stmt->execute([
            'merchant_name' => $merchantName,
            'trade_name' => $tradeName,
            'business_type' => $businessType,
            'phone' => $phone,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'terms_accepted' => 1,
            'status' => 'pending',
        ]);

        header('Location: /merchant/register/thanks');
    }

    public function thanks(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        view('merchant/register-thanks', [
            'supportWhatsapp' => $config['support_whatsapp'] ?? '',
        ]);
    }
}
