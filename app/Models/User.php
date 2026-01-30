<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function superAdminExists(): bool
    {
        $stmt = Database::connection()->query(\"SELECT COUNT(*) AS count FROM users WHERE role = 'super_admin'\");
        $result = $stmt->fetch();

        return (int) ($result['count'] ?? 0) > 0;
    }
}
