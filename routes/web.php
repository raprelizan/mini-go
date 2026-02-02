<?php

use App\Controllers\LandingPageController;
use App\Controllers\MerchantController;
use App\Controllers\MerchantRegistrationController;
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
$router->get('/merchant/register', [new MerchantRegistrationController(), 'show']);
$router->post('/merchant/register', [new MerchantRegistrationController(), 'store']);
$router->get('/merchant/register/thanks', [new MerchantRegistrationController(), 'thanks']);

$router->get('/admin', [new SuperAdminController(), 'dashboard']);
$router->get('/admin/merchants', [new SuperAdminController(), 'merchantsIndex']);
$router->get('/admin/merchant-registrations', [new SuperAdminController(), 'merchantRegistrationsIndex']);
$router->post('/admin/merchant-registrations/approve', [new SuperAdminController(), 'approveMerchantRegistration']);
$router->post('/admin/merchant-registrations/reject', [new SuperAdminController(), 'rejectMerchantRegistration']);
$router->get('/admin/merchants/profile', [new SuperAdminController(), 'merchantProfile']);
$router->get('/admin/merchants/delivery-prices', [new SuperAdminController(), 'merchantDeliveryPrices']);
$router->post('/admin/merchants', [new SuperAdminController(), 'createMerchant']);
$router->post('/admin/merchants/update', [new SuperAdminController(), 'updateMerchant']);
$router->post('/admin/merchants/profile', [new SuperAdminController(), 'updateMerchantProfile']);
$router->post('/admin/merchants/delivery-prices', [new SuperAdminController(), 'updateMerchantDeliveryPrices']);
$router->post('/admin/merchants/delete', [new SuperAdminController(), 'deleteMerchant']);

$router->get('/admin/templates', [new SuperAdminController(), 'templatesIndex']);
$router->get('/admin/templates/builder', [new SuperAdminController(), 'templateBuilder']);
$router->get('/admin/templates/preview', [new SuperAdminController(), 'previewTemplate']);
$router->get('/admin/customizer', [new SuperAdminController(), 'themeCustomizer']);
$router->get('/admin/customizer/preview', [new SuperAdminController(), 'previewCustomizer']);
$router->post('/admin/templates', [new SuperAdminController(), 'createTemplate']);
$router->post('/admin/templates/update', [new SuperAdminController(), 'updateTemplate']);
$router->post('/admin/templates/delete', [new SuperAdminController(), 'deleteTemplate']);
$router->post('/admin/customizer/save', [new SuperAdminController(), 'saveCustomizer']);
$router->post('/admin/customizer/toggle', [new SuperAdminController(), 'toggleCustomizer']);

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
$router->post('/merchant/editor/upload', [new MerchantController(), 'uploadEditorImage']);
$router->get('/merchant/settings', [new MerchantController(), 'settings']);
$router->post('/merchant/settings', [new MerchantController(), 'updateSettings']);
$router->get('/merchant/orders', [new MerchantController(), 'orders']);
$router->get('/merchant/orders/view', [new MerchantController(), 'orderSheet']);
$router->post('/merchant/orders/update', [new MerchantController(), 'updateOrderStatus']);
$router->get('/merchant/delivery-prices', [new MerchantController(), 'deliveryPrices']);
$router->post('/merchant/delivery-prices', [new MerchantController(), 'updateDeliveryPrices']);

$router->get('/{merchant_code}', function (string $merchant_code) {
    (new LandingPageController())->profile($merchant_code);
});

$router->get('/{merchant_code}/{slug}', function (string $merchant_code, string $slug) {
    (new LandingPageController())->show($merchant_code, $slug);
});

$router->post('/{merchant_code}/{slug}/order', function (string $merchant_code, string $slug) {
    (new LandingPageController())->order($merchant_code, $slug);
});
