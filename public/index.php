<?php

declare(strict_types=1);

use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

session_start();

function view(string $path, array $data = []): void
{
    extract($data);
    $viewPath = __DIR__ . '/../resources/views/' . $path . '.php';
    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo 'View not found.';
        exit;
    }
    require $viewPath;
}

function request_subdomain(): string
{
    $config = require __DIR__ . '/../config/app.php';
    $host = $_SERVER['HTTP_HOST'] ?? $config['base_domain'];
    $host = explode(':', $host)[0];
    if ($host === $config['base_domain']) {
        return '';
    }
    $suffix = '.' . $config['base_domain'];
    if (str_ends_with($host, $suffix)) {
        return str_replace($suffix, '', $host);
    }
    return '';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        echo 'Invalid CSRF token.';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$router = new Router();
require __DIR__ . '/../routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
