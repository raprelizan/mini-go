<?php

namespace App\Models;

use App\Core\Database;

class Template
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM templates WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $template = $stmt->fetch();

        return $template ?: null;
    }

    public static function fields(int $templateId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM template_fields WHERE template_id = :template_id ORDER BY display_order');
        $stmt->execute(['template_id' => $templateId]);
        return $stmt->fetchAll();
    }
}
