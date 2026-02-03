<?php

namespace App\ThemeEngine;

class PageResolver
{
    private VersionManager $versions;

    public function __construct(VersionManager $versions)
    {
        $this->versions = $versions;
    }

    public function resolve(int $pageId): ?array
    {
        $status = $this->versions->currentStatus($pageId);
        if (!$status || (int) ($status['is_enabled'] ?? 0) !== 1) {
            return null;
        }

        return $this->versions->findPublishedPayload($pageId);
    }
}
