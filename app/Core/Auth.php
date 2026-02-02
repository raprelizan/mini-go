<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function login(array $user): void
    {
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    public static function requireRole(string $role): void
    {
        if (!self::check() || ($_SESSION['user']['role'] ?? null) !== $role) {
            http_response_code(403);
            echo 'Unauthorized.';
            exit;
        }
    }
}
