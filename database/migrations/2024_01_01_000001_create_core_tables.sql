CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'merchant') NOT NULL DEFAULT 'merchant',
    merchant_id INT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_users_merchant (merchant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE merchants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    subdomain VARCHAR(120) NOT NULL UNIQUE,
    profile_name VARCHAR(150) NOT NULL DEFAULT '',
    profile_bio VARCHAR(255) NOT NULL DEFAULT '',
    profile_about TEXT NULL,
    profile_phone VARCHAR(60) NOT NULL DEFAULT '',
    profile_email VARCHAR(190) NOT NULL DEFAULT '',
    profile_address VARCHAR(190) NOT NULL DEFAULT '',
    logo_url TEXT NULL,
    cover_url TEXT NULL,
    instagram_url VARCHAR(190) NOT NULL DEFAULT '',
    facebook_url VARCHAR(190) NOT NULL DEFAULT '',
    tiktok_url VARCHAR(190) NOT NULL DEFAULT '',
    website_url VARCHAR(190) NOT NULL DEFAULT '',
    delivery_prices_json TEXT NULL,
    order_prefix VARCHAR(20) NOT NULL DEFAULT 'GFM',
    whatsapp_number VARCHAR(60) NOT NULL,
    telegram_chat_id VARCHAR(120) NOT NULL,
    order_message_template TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL,
    view_key VARCHAR(120) NOT NULL DEFAULT 'default',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE template_fields (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_id INT UNSIGNED NOT NULL,
    field_key VARCHAR(120) NOT NULL,
    label VARCHAR(150) NOT NULL,
    field_type VARCHAR(40) NOT NULL,
    is_editable_by_merchant TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
    INDEX idx_template_fields_template (template_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT UNSIGNED NOT NULL,
    template_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    price VARCHAR(60) NOT NULL,
    description VARCHAR(255) NOT NULL,
    content_json JSON NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uniq_pages_slug (merchant_id, slug),
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE RESTRICT,
    INDEX idx_pages_merchant (merchant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    merchant_id INT UNSIGNED NOT NULL,
    page_id INT UNSIGNED NOT NULL,
    order_code VARCHAR(40) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(80) NOT NULL,
    address VARCHAR(255) NOT NULL,
    wilaya VARCHAR(120) NOT NULL,
    delivery_price INT NOT NULL DEFAULT 500,
    total_price INT NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'new',
    message_payload TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    INDEX idx_orders_merchant (merchant_id),
    INDEX idx_orders_page (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price VARCHAR(60) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_items_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(180) NOT NULL,
    payload TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
