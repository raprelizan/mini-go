<?php

namespace App\ThemeEngine;

use App\Core\Database;

class VersionManager
{
    public function ensureThemePage(int $pageId): int
    {
        $stmt = Database::connection()->prepare('SELECT id FROM theme_pages WHERE page_id = :page_id');
        $stmt->execute(['page_id' => $pageId]);
        $row = $stmt->fetch();
        if ($row) {
            return (int) $row['id'];
        }

        $stmt = Database::connection()->prepare('INSERT INTO theme_pages (page_id, is_enabled, created_at) VALUES (:page_id, 0, NOW())');
        $stmt->execute(['page_id' => $pageId]);
        return (int) Database::connection()->lastInsertId();
    }

    public function saveDraft(int $pageId, array $payload): void
    {
        $themePageId = $this->ensureThemePage($pageId);
        $versionId = $this->createVersion($themePageId, 'draft', $payload);
        $this->storeSections($versionId, $payload);
    }

    public function publish(int $pageId, array $payload): void
    {
        $themePageId = $this->ensureThemePage($pageId);
        $versionId = $this->createVersion($themePageId, 'published', $payload);
        $this->storeSections($versionId, $payload);

        $stmt = Database::connection()->prepare('UPDATE theme_pages SET published_version_id = :version_id, is_enabled = 1, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['version_id' => $versionId, 'id' => $themePageId]);
    }

    public function findPublishedPayload(int $pageId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT theme_versions.payload_json
             FROM theme_pages
             INNER JOIN theme_versions ON theme_pages.published_version_id = theme_versions.id
             WHERE theme_pages.page_id = :page_id AND theme_pages.is_enabled = 1'
        );
        $stmt->execute(['page_id' => $pageId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return json_decode($row['payload_json'] ?? '', true);
    }

    public function findDraftPayload(int $pageId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT payload_json FROM theme_versions WHERE theme_page_id = (SELECT id FROM theme_pages WHERE page_id = :page_id) AND status = :status ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute(['page_id' => $pageId, 'status' => 'draft']);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return json_decode($row['payload_json'] ?? '', true);
    }

    public function toggle(int $pageId, int $enabled): void
    {
        $themePageId = $this->ensureThemePage($pageId);
        $stmt = Database::connection()->prepare('UPDATE theme_pages SET is_enabled = :enabled, updated_at = NOW() WHERE id = :id');
        $stmt->execute(['enabled' => $enabled, 'id' => $themePageId]);
    }

    public function listVersions(int $pageId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT theme_versions.id, theme_versions.status, theme_versions.created_at
             FROM theme_versions
             INNER JOIN theme_pages ON theme_versions.theme_page_id = theme_pages.id
             WHERE theme_pages.page_id = :page_id
             ORDER BY theme_versions.id DESC'
        );
        $stmt->execute(['page_id' => $pageId]);
        return $stmt->fetchAll() ?: [];
    }

    public function rollbackToVersion(int $pageId, int $versionId): void
    {
        $stmt = Database::connection()->prepare(
            'SELECT theme_versions.id, theme_versions.theme_page_id
             FROM theme_versions
             INNER JOIN theme_pages ON theme_versions.theme_page_id = theme_pages.id
             WHERE theme_pages.page_id = :page_id AND theme_versions.id = :version_id'
        );
        $stmt->execute(['page_id' => $pageId, 'version_id' => $versionId]);
        $row = $stmt->fetch();
        if (!$row) {
            return;
        }

        $update = Database::connection()->prepare('UPDATE theme_pages SET published_version_id = :version_id, is_enabled = 1, updated_at = NOW() WHERE id = :theme_page_id');
        $update->execute([
            'version_id' => $versionId,
            'theme_page_id' => $row['theme_page_id'],
        ]);
    }

    public function currentStatus(int $pageId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM theme_pages WHERE page_id = :page_id');
        $stmt->execute(['page_id' => $pageId]);
        $row = $stmt->fetch();
        return $row ?: [];
    }

    private function createVersion(int $themePageId, string $status, array $payload): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO theme_versions (theme_page_id, status, payload_json, created_at) VALUES (:theme_page_id, :status, :payload_json, NOW())');
        $stmt->execute([
            'theme_page_id' => $themePageId,
            'status' => $status,
            'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    private function storeSections(int $versionId, array $payload): void
    {
        $sections = $payload['sections'] ?? [];
        if (!is_array($sections)) {
            return;
        }

        $sectionStmt = Database::connection()->prepare('INSERT INTO theme_sections (theme_version_id, section_order, type, settings_json) VALUES (:version_id, :section_order, :type, :settings_json)');
        $blockStmt = Database::connection()->prepare('INSERT INTO theme_blocks (theme_section_id, block_order, type, settings_json) VALUES (:section_id, :block_order, :type, :settings_json)');

        foreach ($sections as $index => $section) {
            $sectionStmt->execute([
                'version_id' => $versionId,
                'section_order' => $index + 1,
                'type' => $section['type'] ?? '',
                'settings_json' => json_encode($section['settings'] ?? [], JSON_UNESCAPED_UNICODE),
            ]);
            $sectionId = (int) Database::connection()->lastInsertId();
            $blocks = $section['blocks'] ?? [];
            if (!is_array($blocks)) {
                continue;
            }
            foreach ($blocks as $blockIndex => $block) {
                $blockStmt->execute([
                    'section_id' => $sectionId,
                    'block_order' => $blockIndex + 1,
                    'type' => $block['type'] ?? '',
                    'settings_json' => json_encode($block['settings'] ?? [], JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }
}
