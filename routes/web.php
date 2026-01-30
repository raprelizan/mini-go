<?php

use App\Controllers\LandingPageController;
use App\Controllers\MerchantController;
use App\Controllers\SuperAdminController;
use App\Controllers\AuthController;
use App\Controllers\SetupController;

$router->get('/', function () {
    view('marketing/home');
});

$router->get('/login', [new AuthController(), 'showLogin']);
$router->post('/login', [new AuthController(), 'login']);
$router->post('/logout', [new AuthController(), 'logout']);
$router->get('/setup', [new SetupController(), 'show']);
$router->post('/setup', [new SetupController(), 'store']);

$router->get('/admin', [new SuperAdminController(), 'dashboard']);
$router->post('/admin/merchants', [new SuperAdminController(), 'createMerchant']);
$router->post('/admin/merchants/toggle', [new SuperAdminController(), 'toggleMerchant']);
$router->post('/admin/templates', [new SuperAdminController(), 'createTemplate']);
$router->post('/admin/pages', [new SuperAdminController(), 'createPage']);
$router->post('/admin/pages/toggle', [new SuperAdminController(), 'togglePage']);

$router->get('/merchant', [new MerchantController(), 'dashboard']);
$router->get('/merchant/pages/edit', [new MerchantController(), 'editPage']);
$router->post('/merchant/pages/update', [new MerchantController(), 'updatePage']);
$router->get('/merchant/settings', [new MerchantController(), 'settings']);
$router->post('/merchant/settings', [new MerchantController(), 'updateSettings']);

$router->get('/p/{slug}', function (string $slug) {
    $subdomain = request_subdomain();
    (new LandingPageController())->show($subdomain, $slug);
});

$router->post('/p/{slug}/order', function (string $slug) {
    $subdomain = request_subdomain();
    (new LandingPageController())->order($subdomain, $slug);
});
