# MiniGo Multi-Tenant Landing Page Order Platform

MiniGo is a production-ready, multi-tenant landing page platform designed for Cash On Delivery markets. The platform hosts multiple merchant landing pages on subdomains, saves orders to the database, and routes order notifications to WhatsApp and Telegram.

## Requirements
- PHP 8.1+
- MySQL 8+ or MariaDB 10.5+
- Apache/Nginx (shared hosting friendly)

## Installation
1. Create a database and import the migration SQL.
2. Update environment variables for DB credentials and base domain.
3. Point your web server document root to `public/`.
4. Create the first Super Admin user manually in the `users` table.

## Environment Variables
```
APP_BASE_DOMAIN=yourplatform.com
APP_URL=https://yourplatform.com
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_go
DB_USERNAME=root
DB_PASSWORD=
```

## Deployment Notes
- Configure a wildcard DNS record (`*.yourplatform.com`) that points to the server.
- Ensure the web server accepts all subdomains and forwards them to `public/index.php`.
- Use HTTPS with a wildcard certificate for subdomains.

## Database
Run the SQL file in `database/migrations/2024_01_01_000001_create_core_tables.sql` to create required tables.

## Subdomain Routing
The platform reads `HTTP_HOST` and extracts the subdomain relative to `APP_BASE_DOMAIN`. Merchant landing pages live at:

```
merchant.yourplatform.com/p/product-slug
```

## WhatsApp & Telegram
- WhatsApp uses `wa.me` links with prefilled message text.
- Set `TELEGRAM_BOT_TOKEN` to enable automatic bot delivery to the merchant chat ID; otherwise the UI provides a share link.

## Security
- Sessions are used for authentication.
- Role-based access control is enforced in controllers.
- CSRF protection is enforced for all POST requests.
