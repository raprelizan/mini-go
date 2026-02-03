<?php

namespace App\ThemeEngine;

class SectionRegistry
{
    private array $sections = [
        'hero' => [
            'label' => 'Hero',
            'settings' => [
                'heading' => ['type' => 'text', 'label' => 'العنوان'],
                'subheading' => ['type' => 'text', 'label' => 'الوصف'],
                'button_text' => ['type' => 'text', 'label' => 'نص الزر'],
                'button_url' => ['type' => 'url', 'label' => 'رابط الزر'],
                'image_url' => ['type' => 'url', 'label' => 'رابط الصورة'],
                'background_color' => ['type' => 'color', 'label' => 'لون الخلفية'],
                'text_color' => ['type' => 'color', 'label' => 'لون النص'],
                'padding' => ['type' => 'text', 'label' => 'Padding'],
            ],
            'blocks' => [],
            'view' => 'hero',
        ],
        'banner' => [
            'label' => 'Banner',
            'settings' => [
                'text' => ['type' => 'text', 'label' => 'النص'],
                'background_color' => ['type' => 'color', 'label' => 'لون الخلفية'],
                'text_color' => ['type' => 'color', 'label' => 'لون النص'],
            ],
            'blocks' => [],
            'view' => 'banner',
        ],
        'product_grid' => [
            'label' => 'Product Grid',
            'settings' => [
                'heading' => ['type' => 'text', 'label' => 'العنوان'],
                'columns' => ['type' => 'number', 'label' => 'عدد الأعمدة'],
            ],
            'blocks' => [
                'product' => [
                    'title' => ['type' => 'text', 'label' => 'اسم المنتج'],
                    'price' => ['type' => 'text', 'label' => 'السعر'],
                    'image_url' => ['type' => 'url', 'label' => 'رابط الصورة'],
                ],
            ],
            'view' => 'product_grid',
        ],
    ];

    public function all(): array
    {
        return $this->sections;
    }

    public function get(string $type): ?array
    {
        return $this->sections[$type] ?? null;
    }
}
