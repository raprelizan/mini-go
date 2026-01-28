<?php

namespace App\Models;

use App\Core\Database;

class Merchant
{
    public static function findBySubdomain(string $subdomain): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM merchants WHERE subdomain = :subdomain AND is_active = 1');
        $stmt->execute(['subdomain' => $subdomain]);
        $merchant = $stmt->fetch();

        return $merchant ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM merchants WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $merchant = $stmt->fetch();

        return $merchant ?: null;
    }
}
