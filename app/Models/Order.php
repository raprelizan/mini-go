<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO orders (merchant_id, page_id, full_name, phone, address, wilaya, status, message_payload, created_at) VALUES (:merchant_id, :page_id, :full_name, :phone, :address, :wilaya, :status, :message_payload, NOW())');
        $stmt->execute($data);
        return (int) Database::connection()->lastInsertId();
    }

    public static function allForMerchant(int $merchantId): array
    {
        $stmt = Database::connection()->prepare('SELECT orders.*, pages.title AS page_title FROM orders INNER JOIN pages ON orders.page_id = pages.id WHERE orders.merchant_id = :merchant_id ORDER BY orders.created_at DESC');
        $stmt->execute(['merchant_id' => $merchantId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT orders.*, pages.title AS page_title, merchants.name AS merchant_name FROM orders INNER JOIN pages ON orders.page_id = pages.id INNER JOIN merchants ON orders.merchant_id = merchants.id ORDER BY orders.created_at DESC');
        return $stmt->fetchAll();
    }
}
