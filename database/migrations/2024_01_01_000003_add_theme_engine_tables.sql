CREATE TABLE theme_pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    is_enabled TINYINT(1) NOT NULL DEFAULT 0,
    published_version_id INT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uniq_theme_pages_page (page_id),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme_versions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    theme_page_id INT UNSIGNED NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    payload_json JSON NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_page_id) REFERENCES theme_pages(id) ON DELETE CASCADE,
    INDEX idx_theme_versions_page (theme_page_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme_sections (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    theme_version_id INT UNSIGNED NOT NULL,
    section_order INT NOT NULL DEFAULT 0,
    type VARCHAR(80) NOT NULL,
    settings_json JSON NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_version_id) REFERENCES theme_versions(id) ON DELETE CASCADE,
    INDEX idx_theme_sections_version (theme_version_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme_blocks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    theme_section_id INT UNSIGNED NOT NULL,
    block_order INT NOT NULL DEFAULT 0,
    type VARCHAR(80) NOT NULL,
    settings_json JSON NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_section_id) REFERENCES theme_sections(id) ON DELETE CASCADE,
    INDEX idx_theme_blocks_section (theme_section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
