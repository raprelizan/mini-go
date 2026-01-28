<?php

namespace App\Models;

use App\Core\Database;

class Page
{
    public static function findBySlug(int $merchantId, string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM pages WHERE merchant_id = :merchant_id AND slug = :slug AND is_active = 1');
        $stmt->execute(['merchant_id' => $merchantId, 'slug' => $slug]);
        $page = $stmt->fetch();

        return $page ?: null;
    }

    public static function allForMerchant(int $merchantId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM pages WHERE merchant_id = :merchant_id ORDER BY created_at DESC');
        $stmt->execute(['merchant_id' => $merchantId]);
        return $stmt->fetchAll();
    }
}
