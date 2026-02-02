<?php

return [
    'name' => 'MiniGo Landing Platform',
    'env' => 'production',
    'base_domain' => getenv('APP_BASE_DOMAIN') ?: 'yourplatform.com',
    'url' => getenv('APP_URL') ?: 'https://yourplatform.com',
    'timezone' => 'Africa/Algiers',
    'support_email' => 'support@yourplatform.com',
    'support_whatsapp' => getenv('SUPPORT_WHATSAPP') ?: '',
    'whatsapp_default_country_code' => '+213',
    'telegram_bot_token' => getenv('TELEGRAM_BOT_TOKEN') ?: '',
];
