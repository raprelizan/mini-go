<?php

namespace App\Models;

use App\Core\Database;

class PageCustomization
{
    public static function findByPageId(int $pageId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM page_customizations WHERE page_id = :page_id');
        $stmt->execute(['page_id' => $pageId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function ensure(int $pageId): array
    {
        $existing = self::findByPageId($pageId);
        if ($existing) {
            return $existing;
        }

        $stmt = Database::connection()->prepare('INSERT INTO page_customizations (page_id, is_enabled, created_at) VALUES (:page_id, 0, NOW())');
        $stmt->execute(['page_id' => $pageId]);
        return self::findByPageId($pageId) ?? [];
    }

    public static function saveDraft(int $pageId, array $payload): void
    {
        $stmt = Database::connection()->prepare('UPDATE page_customizations SET draft_json = :draft_json, updated_at = NOW() WHERE page_id = :page_id');
        $stmt->execute([
            'draft_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'page_id' => $pageId,
        ]);
    }

    public static function publish(int $pageId, array $payload): void
    {
        $stmt = Database::connection()->prepare('UPDATE page_customizations SET published_json = :published_json, published_at = NOW(), is_enabled = 1, updated_at = NOW() WHERE page_id = :page_id');
        $stmt->execute([
            'published_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'page_id' => $pageId,
        ]);
    }

    public static function toggle(int $pageId, int $enabled): void
    {
        $stmt = Database::connection()->prepare('UPDATE page_customizations SET is_enabled = :enabled, updated_at = NOW() WHERE page_id = :page_id');
        $stmt->execute([
            'enabled' => $enabled,
            'page_id' => $pageId,
        ]);
    }
}
