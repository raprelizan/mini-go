<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Auth;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        if (!User::superAdminExists()) {
            header('Location: /setup');
            return;
        }
        view('auth/login');
    }

    public function login(): void
    {
        if (!User::superAdminExists()) {
            $_SESSION['flash_error'] = 'Complete the initial setup first.';
            header('Location: /setup');
            return;
        }
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = Database::connection()->prepare('SELECT users.*, merchants.name AS merchant_name FROM users LEFT JOIN merchants ON users.merchant_id = merchants.id WHERE users.email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Invalid credentials.';
            header('Location: /login');
            return;
        }

        Auth::login([
            'id' => $user['id'],
            'role' => $user['role'],
            'merchant_id' => $user['merchant_id'],
            'name' => $user['name'],
            'merchant_name' => $user['merchant_name'],
        ]);

        if ($user['role'] === 'super_admin') {
            header('Location: /admin');
            return;
        }

        header('Location: /merchant');
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
    }
}
