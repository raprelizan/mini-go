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
$router->get('/admin/merchants', [new SuperAdminController(), 'merchantsIndex']);
$router->post('/admin/merchants', [new SuperAdminController(), 'createMerchant']);
$router->post('/admin/merchants/update', [new SuperAdminController(), 'updateMerchant']);
$router->post('/admin/merchants/delete', [new SuperAdminController(), 'deleteMerchant']);

$router->get('/admin/templates', [new SuperAdminController(), 'templatesIndex']);
$router->get('/admin/templates/preview', [new SuperAdminController(), 'previewTemplate']);
$router->post('/admin/templates', [new SuperAdminController(), 'createTemplate']);
$router->post('/admin/templates/update', [new SuperAdminController(), 'updateTemplate']);
$router->post('/admin/templates/delete', [new SuperAdminController(), 'deleteTemplate']);

$router->get('/admin/pages', [new SuperAdminController(), 'pagesIndex']);
$router->post('/admin/pages', [new SuperAdminController(), 'createPage']);
$router->post('/admin/pages/update', [new SuperAdminController(), 'updatePage']);
$router->post('/admin/pages/delete', [new SuperAdminController(), 'deletePage']);

$router->get('/admin/orders', [new SuperAdminController(), 'ordersIndex']);
$router->post('/admin/orders/update', [new SuperAdminController(), 'updateOrder']);
$router->post('/admin/orders/delete', [new SuperAdminController(), 'deleteOrder']);

$router->get('/admin/users', [new SuperAdminController(), 'usersIndex']);
$router->post('/admin/users', [new SuperAdminController(), 'createUser']);
$router->post('/admin/users/update', [new SuperAdminController(), 'updateUser']);
$router->post('/admin/users/delete', [new SuperAdminController(), 'deleteUser']);

$router->get('/admin/profile', [new SuperAdminController(), 'profile']);
$router->post('/admin/profile', [new SuperAdminController(), 'updateProfile']);

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
