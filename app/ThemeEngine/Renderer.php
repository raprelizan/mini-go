<?php

namespace App\ThemeEngine;

class Renderer
{
    private string $sectionRoot;

    public function __construct(string $sectionRoot)
    {
        $this->sectionRoot = rtrim($sectionRoot, '/');
    }

    public function render(array $payload, array $context = []): string
    {
        $sections = $payload['sections'] ?? [];
        if (!is_array($sections)) {
            return '';
        }

        $output = '';
        foreach ($sections as $section) {
            $type = $section['type'] ?? '';
            if ($type === '') {
                continue;
            }
            $view = $this->sectionRoot . '/' . basename((string) $type) . '.php';
            if (!file_exists($view)) {
                continue;
            }
            $settings = is_array($section['settings'] ?? null) ? $section['settings'] : [];
            $blocks = is_array($section['blocks'] ?? null) ? $section['blocks'] : [];
            $output .= $this->renderSection($view, $settings, $blocks, $context);
        }

        return $output;
    }

    private function renderSection(string $view, array $settings, array $blocks, array $context): string
    {
        $context = $context;
        ob_start();
        require $view;
        return ob_get_clean() ?: '';
    }
}
