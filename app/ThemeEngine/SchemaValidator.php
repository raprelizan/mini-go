<?php

namespace App\ThemeEngine;

class SchemaValidator
{
    private SectionRegistry $registry;

    public function __construct(SectionRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function sanitize(array $payload): array
    {
        $sections = $payload['sections'] ?? [];
        if (!is_array($sections)) {
            return ['sections' => []];
        }

        $cleanSections = [];
        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }
            $type = preg_replace('/[^a-z0-9_]/', '', (string) ($section['type'] ?? ''));
            $schema = $this->registry->get($type);
            if (!$schema) {
                continue;
            }

            $settings = $this->sanitizeSettings($section['settings'] ?? [], $schema['settings'] ?? []);
            $blocks = $this->sanitizeBlocks($section['blocks'] ?? [], $schema['blocks'] ?? []);

            $cleanSections[] = [
                'type' => $type,
                'settings' => $settings,
                'blocks' => $blocks,
            ];
        }

        return ['sections' => $cleanSections];
    }

    private function sanitizeSettings($settings, array $schema): array
    {
        if (!is_array($settings)) {
            return [];
        }
        $clean = [];
        foreach ($schema as $key => $type) {
            if (is_array($type)) {
                $type = $type['type'] ?? 'text';
            }
            $value = $settings[$key] ?? null;
            if (is_string($value)) {
                $clean[$key] = trim($value);
                continue;
            }
            if (is_numeric($value)) {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }

    private function sanitizeBlocks($blocks, array $schema): array
    {
        if (!is_array($blocks)) {
            return [];
        }
        $cleanBlocks = [];
        foreach ($blocks as $block) {
            if (!is_array($block)) {
                continue;
            }
            $type = preg_replace('/[^a-z0-9_]/', '', (string) ($block['type'] ?? ''));
            $blockSchema = $schema[$type] ?? null;
            if (!$blockSchema) {
                continue;
            }
            $settings = $this->sanitizeSettings($block['settings'] ?? [], $blockSchema);
            $cleanBlocks[] = [
                'type' => $type,
                'settings' => $settings,
            ];
        }
        return $cleanBlocks;
    }
}
