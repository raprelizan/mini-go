<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\User;

class SetupController
{
    public function show(): void
    {
        if (User::superAdminExists()) {
            header('Location: /login');
            return;
        }

        view('auth/setup');
    }

    public function store(): void
    {
        if (User::superAdminExists()) {
            header('Location: /login');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Please fill in all fields.';
            header('Location: /setup');
            return;
        }

        $stmt = Database::connection()->prepare('INSERT INTO users (name, email, password_hash, role, merchant_id, created_at) VALUES (:name, :email, :password_hash, :role, NULL, NOW())');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'super_admin',
        ]);

        $_SESSION['flash_success'] = 'Super admin created. You can now log in.';
        header('Location: /login');
    }
}
