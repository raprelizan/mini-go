<?php

namespace App\Core;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'b',
        'strong',
        'i',
        'em',
        'u',
        'ul',
        'ol',
        'li',
        'h1',
        'h2',
        'h3',
        'h4',
        'blockquote',
        'a',
        'img',
        'span',
        'div',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'style'],
        'p' => ['style'],
        'span' => ['style'],
        'div' => ['style'],
        'h1' => ['style'],
        'h2' => ['style'],
        'h3' => ['style'],
        'h4' => ['style'],
        'blockquote' => ['style'],
    ];

    private const ALLOWED_STYLES = [
        'text-align',
        'float',
        'width',
        'max-width',
        'height',
    ];

    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        self::sanitizeNode($doc);

        $output = $doc->saveHTML() ?: '';
        return trim($output);
    }

    private static function sanitizeNode(\DOMNode $node): void
    {
        if ($node->hasChildNodes()) {
            foreach (iterator_to_array($node->childNodes) as $child) {
                self::sanitizeNode($child);
            }
        }

        if ($node instanceof \DOMElement) {
            $tag = strtolower($node->tagName);
            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                $text = $node->ownerDocument->createTextNode($node->textContent ?? '');
                $node->parentNode?->replaceChild($text, $node);
                return;
            }

            $allowedAttributes = self::ALLOWED_ATTRIBUTES[$tag] ?? [];
            if ($node->hasAttributes()) {
                foreach (iterator_to_array($node->attributes) as $attribute) {
                    $name = strtolower($attribute->name);
                    if (str_starts_with($name, 'on') || !in_array($name, $allowedAttributes, true)) {
                        $node->removeAttributeNode($attribute);
                        continue;
                    }

                    $value = trim($attribute->value);
                    if ($name === 'href' || $name === 'src') {
                        $clean = self::sanitizeUrl($value);
                        if ($clean === null) {
                            $node->removeAttributeNode($attribute);
                            continue;
                        }
                        $node->setAttribute($name, $clean);
                    }

                    if ($name === 'style') {
                        $clean = self::sanitizeStyle($value);
                        if ($clean === '') {
                            $node->removeAttribute('style');
                        } else {
                            $node->setAttribute('style', $clean);
                        }
                    }
                }
            }

            if ($tag === 'a') {
                if (!$node->hasAttribute('target')) {
                    $node->setAttribute('target', '_blank');
                }
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    private static function sanitizeUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    private static function sanitizeStyle(string $style): string
    {
        $clean = [];
        $styles = array_filter(array_map('trim', explode(';', $style)));
        foreach ($styles as $rule) {
            [$property, $value] = array_pad(array_map('trim', explode(':', $rule, 2)), 2, '');
            $property = strtolower($property);
            if ($property === '' || $value === '') {
                continue;
            }
            if (!in_array($property, self::ALLOWED_STYLES, true)) {
                continue;
            }
            $clean[] = $property . ': ' . $value;
        }

        return implode('; ', $clean);
    }
}
