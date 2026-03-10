<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\BackEnd\ApiTokenController;
use App\Http\Controllers\BackEnd\CarryBeeApiSettingsController;
use App\Http\Controllers\BackEnd\CategoryController;
use App\Http\Controllers\BackEnd\DashboardController;
use App\Http\Controllers\BackEnd\FraudController;
use App\Http\Controllers\BackEnd\GoogleSheetSettingsController;
use App\Http\Controllers\BackEnd\IncompleteOrdersController;
use App\Http\Controllers\BackEnd\IpController;
use App\Http\Controllers\BackEnd\LandingPageController;
use App\Http\Controllers\BackEnd\MediaController;
use App\Http\Controllers\BackEnd\NoteSettingsController;
use App\Http\Controllers\BackEnd\OrderController;
use App\Http\Controllers\BackEnd\PageSettingsController;
use App\Http\Controllers\BackEnd\ParcelHandoverController;
use App\Http\Controllers\BackEnd\PasswordController;
use App\Http\Controllers\BackEnd\PathaoApiSettingsController;
use App\Http\Controllers\BackEnd\ProductController;
use App\Http\Controllers\BackEnd\PushSubscriptionController;
use App\Http\Controllers\BackEnd\RedxApiSettingsController;
use App\Http\Controllers\BackEnd\ReportController;
use App\Http\Controllers\BackEnd\ReturnOrderController;
use App\Http\Controllers\BackEnd\RoleController;
use App\Http\Controllers\BackEnd\SectionController;
use App\Http\Controllers\BackEnd\ShippingMethodController;
use App\Http\Controllers\BackEnd\SliderController;
use App\Http\Controllers\BackEnd\SmsController;
use App\Http\Controllers\BackEnd\SmsSettingsController;
use App\Http\Controllers\BackEnd\SteadFastApiSettingsController;
use App\Http\Controllers\BackEnd\StockController;
use App\Http\Controllers\BackEnd\TrashController;
use App\Http\Controllers\BackEnd\UserController;
use App\Http\Controllers\BackEnd\UserProductsController;
use App\Http\Controllers\BackEnd\WebSettingsController;
use App\Http\Controllers\CourierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to admin functionality.
|
*/

// Admin authentication routes
Route::group(['middleware' => 'admin.guest'], function (): void {
    Route::get('/admin-login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminLoginController::class, 'login']);
});
Route::post('/admin-logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Steadfast order sync
Route::get('/orders/steadfast_order_sync', [OrderController::class, 'steadFastOrderSync'])->name('orders.steadfast.order.sync');

// Admin protected routes (device restriction enforced)
Route::middleware('admin.auth')->group(function () {
    // Device approval/request routes (accessible to unapproved devices)
    Route::get('/admin-device-request', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'request'])->name('admin.device.request');
    Route::post('/admin-device-request', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'submit'])->name('admin.device.request.submit');
    Route::get('/admin-device-approvals', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'index'])->name('admin.device.approvals');
    Route::post('/admin-device-approve/{id}', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'approve'])->name('admin.device.approve');
    Route::post('/admin-device-reject/{id}', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'reject'])->name('admin.device.reject');
});

Route::group(['middleware' => ['admin.auth', 'ensure.trusted.device']], function (): void {
    Route::get('/admin', [DashboardController::class, 'dashboard'])->name('admin.home');
    Route::get('/admin/top-sell-filter', [DashboardController::class, 'topSellFilter'])->name('admin.dashboard.top_sell');
    Route::get('/admin/hourly-order-comparison', [DashboardController::class, 'hourlyOrderComparison'])->name('admin.dashboard.hourly_order_comparison');
    Route::get('/admin/traffic-source-stats', [DashboardController::class, 'trafficSourceStats'])->name('admin.dashboard.traffic_sources');
    Route::get('/admin/utm-medium-stats', [DashboardController::class, 'utmMediumStats'])->name('admin.dashboard.utm_medium');
    Route::get('/admin/utm-campaign-stats', [DashboardController::class, 'utmCampaignStats'])->name('admin.dashboard.utm_campaign');
    Route::get('/admin/top-cities-stats', [DashboardController::class, 'topCitiesStats'])->name('admin.dashboard.top_cities');

    // parcel handover
    Route::get('/admin-order-parcel-handover', [ParcelHandoverController::class, 'index'])->name('admin.orders.parcel.handover');
    Route::get('/admin-order-parcel-handover-clear', [ParcelHandoverController::class, 'clear'])->name('admin.orders.parcel.handover.clear');
    Route::get('/admin-order-parcel-handover-print', [ParcelHandoverController::class, 'print'])->name('admin.orders.parcel.handover.print');

    // incomplete orders
    Route::get('/admin-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('admin.incomplete.orders');
    Route::get('/admin-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('admin.incomplete.order.create');
    Route::get('/admin-incomplete-orders/{id}/delete', [IncompleteOrdersController::class, 'delete'])->name('admin.incomplete.order.delete');
    Route::post('/admin-incomplete-orders/{id}/cancel', [IncompleteOrdersController::class, 'cancel'])->name('admin.incomplete.order.cancel');
    Route::post('/admin-incomplete-orders/{id}/assign-employee', [IncompleteOrdersController::class, 'assignEmployee'])->name('admin.incomplete.order.assign-employee');
    Route::post('/admin-incomplete-orders/bulk-assign-employee', [IncompleteOrdersController::class, 'bulkAssignEmployee'])->name('admin.incomplete.order.bulk-assign-employee');
    Route::post('/admin-incomplete-orders/bulk-delete', [IncompleteOrdersController::class, 'bulkDelete'])->name('admin.incomplete.order.bulk-delete');
    Route::post('/admin-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('admin.incomplete.order.note.update');

    // order return
    Route::get('/admin-order-return-receive', [ReturnOrderController::class, 'index'])->name('admin.orders.return.receive');
    Route::get('/admin-order-return-receive-clear', [ReturnOrderController::class, 'sessionClear'])->name('admin.orders.return.receive.clear');
    Route::get('/admin-order-return-receive-print', [ReturnOrderController::class, 'print'])->name('admin.orders.return.receive.print');

    // customer activity
    Route::get('/admin-fraud-check/{id}', [FraudController::class, 'fraudCheck'])->name('admin.fraud.check');

    // note settings
    Route::get('/admin-settings-notes', [NoteSettingsController::class, 'index'])->name('admin.settings.notes');
    Route::post('/admin-settings-note/store', [NoteSettingsController::class, 'store'])->name('admin.settings.note.store');
    Route::post('/admin-settings-note/update', [NoteSettingsController::class, 'update'])->name('admin.settings.note.update');
    Route::get('/admin-settings-note/{id}/delete', [NoteSettingsController::class, 'delete'])->name('admin.settings.note.delete');

    // color settings
    Route::get('/admin-settings-color', [WebSettingsController::class, 'colorSettings'])->name('admin.settings.color');
    Route::post('/admin-settings-color', [WebSettingsController::class, 'colorSettingsUpdate'])->name('admin.settings.color.update');

    // Steadfast API settings
    Route::get('/admin-settings-stead-fast-api', [SteadFastApiSettingsController::class, 'index'])->name('admin.settings.stead_fast.api');
    Route::post('/admin-settings-stead-fast-api', [SteadFastApiSettingsController::class, 'update'])->name('admin.settings.stead_fast.api.update');

    // carrybee API settings
    Route::get('/admin-settings-carrybee-api', [CarryBeeApiSettingsController::class, 'index'])->name('admin.settings.carrybee.api');
    Route::post('/admin-settings-carrybee-api', [CarryBeeApiSettingsController::class, 'update'])->name('admin.settings.carrybee.api.update');
    Route::post('/admin-settings-carrybee-api-gen_access_token', [CarryBeeApiSettingsController::class, 'generateAccessToken'])->name('admin.settings.carrybee.api.gen_access_token');

    // Number sms settings
    Route::get('/admin-settings-sms', [SmsSettingsController::class, 'indexNumber'])->name('admin.settings.sms');
    Route::post('/admin-settings-sms', [SmsSettingsController::class, 'updateNumber'])->name('admin.settings.sms.update');

    // Whatsapp sms settings
    Route::get('/admin-settings-whatsapp', [SmsSettingsController::class, 'indexWhatsapp'])->name('admin.settings.whatsapp');
    Route::post('/admin-settings-whatsapp', [SmsSettingsController::class, 'updateWhatsapp'])->name('admin.settings.whatsapp.update');

    // fake customer remove
    Route::get('/admin/{id}/fake-remove', [FraudController::class, 'fakeRemove'])->name('admin.fake.remove');

    // generate api access token
    Route::get('/admin/generate_api_token', [ApiTokenController::class, 'generateAPIToken'])->name('admin.generate_api_token');

    // send sms from order edit
    Route::post('/admin/send-sms', [SmsController::class, 'sendSms'])->name('admin.send.sms');

    // ip
    Route::get('/admin/ip', [IpController::class, 'index'])->name('admin.ip');
    Route::get('/admin/ip/search', [IpController::class, 'search'])->name('admin.ip.search');
    Route::get('/admin/ip/{id}/{status}/status', [IpController::class, 'ipStatus'])->name('admin.ip.status');

    // stock
    Route::get('/admin/stock', [StockController::class, 'stock'])->name('admin.stock');

    // reports
    Route::get('/admin-reports/employee-orders', [ReportController::class, 'employeeOrders'])->name('admin.reports.employee_orders');
    Route::get('/admin-reports/order-status-p', [ReportController::class, 'orderStatusP'])->name('admin.reports.order_status_p');
    Route::get('/admin-reports/orders-product', [ReportController::class, 'ordersProduct'])->name('admin.reports.orders_product');
    Route::get('/admin-reports/sales', [ReportController::class, 'salesReport'])->name('admin.reports.sales');
    Route::get('/admin-reports/profit-loss', [ReportController::class, 'profitLoss'])->name('admin.reports.profit.loss');
    Route::post('/admin-reports/sales_print', [ReportController::class, 'salesReportPrint'])->name('admin.reports.sales.print');

    // user(employee) product assign
    Route::get('/admin-user-product', [UserProductsController::class, 'index'])->name('admin.user.product');
    Route::post('/admin-user-product/store', [UserProductsController::class, 'store'])->name('admin.user.product.store');
    Route::get('/admin-user-product/{id}/delete', [UserProductsController::class, 'delete'])->name('admin.user.product.delete');

    // Redx API settings
    Route::get('/admin-settings-redx-api', [RedxApiSettingsController::class, 'index'])->name('admin.settings.redx.api');
    Route::post('/admin-settings-redx-api', [RedxApiSettingsController::class, 'update'])->name('admin.settings.redx.api.update');
    Route::post('/admin-settings-redx-api-gen_access_token', [RedxApiSettingsController::class, 'generateAccessToken'])->name('admin.settings.redx.api.gen_access_token');

    // Pathao API settings
    Route::get('/admin-settings-pathao-api', [PathaoApiSettingsController::class, 'index'])->name('admin.settings.pathao.api');
    Route::post('/admin-settings-pathao-api', [PathaoApiSettingsController::class, 'update'])->name('admin.settings.pathao.api.update');
    Route::post('/admin-settings-pathao-api-gen_access_token', [PathaoApiSettingsController::class, 'generateAccessToken'])->name('admin.settings.pathao.api.gen_access_token');

    // page settings
    Route::get('/admin-settings-page', [PageSettingsController::class, 'index'])->name('admin.settings.page');
    Route::post('/admin-settings-page', [PageSettingsController::class, 'update'])->name('admin.settings.page.update');

    // web settings
    Route::get('/admin-settings-web', [WebSettingsController::class, 'index'])->name('admin.settings.web');
    Route::post('/admin-settings-web', [WebSettingsController::class, 'update'])->name('admin.settings.web.update');

    // google_sheet settings
    Route::get('/admin-settings-google_sheet', [GoogleSheetSettingsController::class, 'index'])->name('admin.settings.google_sheet');
    Route::post('/admin-settings-google_sheet', [GoogleSheetSettingsController::class, 'update'])->name('admin.settings.google_sheet.update');

    // attribute settings
    Route::get('/admin-settings-attribute', [WebSettingsController::class, 'attribute'])->name('admin.settings.attribute');
    Route::post('/admin-settings-attribute/store', [WebSettingsController::class, 'attributeStore'])->name('admin.settings.attribute.store');
    Route::post('/admin-settings-attribute/update', [WebSettingsController::class, 'attributeUpdate'])->name('admin.settings.attribute.update');
    Route::get('/admin-settings-attribute/{id}/delete', [WebSettingsController::class, 'attributeDelete'])->name('admin.settings.attribute.delete');
    // attribute item settings
    Route::post('/admin-settings-attribute_item/store', [WebSettingsController::class, 'attributeItemStore'])->name('admin.settings.attribute_item.store');
    Route::post('/admin-settings-attribute_item/update', [WebSettingsController::class, 'attributeItemUpdate'])->name('admin.settings.attribute_item.update');
    Route::get('/admin-settings-attribute_item/{id}/delete', [WebSettingsController::class, 'attributeItemDelete'])->name('admin.settings.attribute_item.delete');

    // change password
    Route::get('/admin-change_pass', [PasswordController::class, 'change_pass'])->name('admin.change_pass');
    Route::post('/admin-change_pass', [PasswordController::class, 'update_pass'])->name('admin.update_pass');

    // customers
    Route::get('/admin-customers', [UserController::class, 'index'])->name('admin.customers');
    Route::post('/admin-customers/customer_export', [UserController::class, 'customerExport'])->name('admin.customers.customer_export');
    Route::get('/admin-customer/{id}/{status}', [UserController::class, 'status'])->name('admin.customer.status');

    // media
    Route::get('/admin-media', [MediaController::class, 'index'])->name('admin.media');
    Route::post('/admin-media/store', [MediaController::class, 'store'])->name('admin.media.store');
    Route::post('/admin-media/update', [MediaController::class, 'update'])->name('admin.media.update');
    Route::get('/admin-media/delete/{id}', [MediaController::class, 'delete'])->name('admin.media.delete');

    // product
    Route::get('/admin-product', [ProductController::class, 'index'])->name('admin.product');
    Route::get('/admin-product/create', [ProductController::class, 'create'])->name('admin.product.create');
    Route::post('/admin-product/store', [ProductController::class, 'store'])->name('admin.product.store');
    Route::get('/admin-product/{id}/edit', [ProductController::class, 'edit'])->name('admin.product.edit');
    Route::post('/admin-product/{id}/update', [ProductController::class, 'update'])->name('admin.product.update');
    Route::get('/admin-product/{id}/delete', [ProductController::class, 'delete'])->name('admin.product.delete');
    Route::post('/admin-product/sku_check', [ProductController::class, 'skuCheck'])->name('admin.product.sku_check');
    Route::post('/admin-product/bulk-delete', [ProductController::class, 'bulkDelete'])->name('admin.product.bulk.delete');
    Route::post('/admin-product/bulk-status', [ProductController::class, 'bulkStatus'])->name('admin.product.bulk.status');
    Route::post('/admin-product/position_update', [ProductController::class, 'positionUpdate'])->name('admin.product.position_update');
    Route::get('/admin-product/{product}/duplicate', [ProductController::class, 'duplicate'])->name('admin.product.duplicate');

    // category
    Route::get('/admin-category', [CategoryController::class, 'index'])->name('admin.category');
    Route::post('/admin-category/store', [CategoryController::class, 'store'])->name('admin.category.store');
    Route::post('/admin-category/update', [CategoryController::class, 'update'])->name('admin.category.update');
    Route::get('/admin-category/delete/{id}', [CategoryController::class, 'delete'])->name('admin.category.delete');

    // sliders
    Route::get('/admin-sliders', [SliderController::class, 'index'])->name('admin.sliders');
    Route::post('/admin-sliders/store', [SliderController::class, 'store'])->name('admin.sliders.store');
    Route::post('/admin-sliders/update', [SliderController::class, 'update'])->name('admin.sliders.update');
    Route::get('/admin-sliders/delete/{id}', [SliderController::class, 'delete'])->name('admin.sliders.delete');

    // sections
    Route::get('/admin-sections', [SectionController::class, 'index'])->name('admin.sections');
    Route::get('/admin-sections/create', [SectionController::class, 'create'])->name('admin.sections.create');
    Route::post('/admin-sections/store', [SectionController::class, 'store'])->name('admin.sections.store');
    Route::get('/admin-sections/{id}/edit', [SectionController::class, 'edit'])->name('admin.sections.edit');
    Route::post('/admin-sections/{id}/update', [SectionController::class, 'update'])->name('admin.sections.update');
    Route::get('/admin-sections/{id}/delete', [SectionController::class, 'delete'])->name('admin.sections.delete');
    Route::post('/admin-sections/reorder', [SectionController::class, 'reorder'])->name('admin.sections.reorder');
    Route::get('/admin-sections/{id}/products', [SectionController::class, 'products'])->name('admin.sections.products');
    Route::post('/admin-sections/{id}/products/add', [SectionController::class, 'addProduct'])->name('admin.sections.products.add');
    Route::get('/admin-sections/{id}/products/{productId}/remove', [SectionController::class, 'removeProduct'])->name('admin.sections.products.remove');
    Route::post('/admin-sections/{id}/products/reorder', [SectionController::class, 'reorderProducts'])->name('admin.sections.products.reorder');
    Route::get('/admin-sections/products/search', [SectionController::class, 'searchProducts'])->name('admin.sections.products.search');

    // landing pages
    Route::get('/admin-landing-pages', [LandingPageController::class, 'index'])->name('landing-pages.index');
    Route::get('/admin-landing-pages/create', [LandingPageController::class, 'create'])->name('landing-pages.create');
    Route::post('/admin-landing-pages/store', [LandingPageController::class, 'store'])->name('landing-pages.store');
    Route::get('/admin-landing-pages/{landing_page}/edit', [LandingPageController::class, 'edit'])->name('landing-pages.edit');
    Route::post('/admin-landing-pages/{landing_page}/update', [LandingPageController::class, 'update'])->name('landing-pages.update');
    Route::get('/admin-landing-pages/{landing_page}/delete', [LandingPageController::class, 'destroy'])->name('landing-pages.destroy');
    Route::get('/admin-landing-pages/{landing_page}/duplicate', [LandingPageController::class, 'duplicate'])->name('landing-pages.duplicate');
    Route::get('/admin-landing-pages/products/search', [LandingPageController::class, 'searchProducts'])->name('landing-pages.products.search');

    // shipping_methods
    Route::get('/admin-shipping_methods', [ShippingMethodController::class, 'index'])->name('admin.shipping_methods');
    Route::post('/admin-shipping_methods/store', [ShippingMethodController::class, 'store'])->name('admin.shipping_methods.store');
    Route::post('/admin-shipping_methods/update', [ShippingMethodController::class, 'update'])->name('admin.shipping_methods.update');
    Route::get('/admin-shipping_methods/delete/{id}', [ShippingMethodController::class, 'delete'])->name('admin.shipping_methods.delete');

    // courier
    Route::get('/admin-courier', [CourierController::class, 'index'])->name('admin.courier');
    Route::post('/admin-courier/store', [CourierController::class, 'store'])->name('admin.courier.store');
    Route::post('/admin-courier/update', [CourierController::class, 'update'])->name('admin.courier.update');
    Route::get('/admin-courier/delete/{id}', [CourierController::class, 'delete'])->name('admin.courier.delete');
    Route::post('/admin-courier-ajax_get_c_charge', [CourierController::class, 'ajaxGetCCharge'])->name('admin.courier.ajax.get.c_charge');

    // courier city
    Route::get('/admin-courier-city', [CourierController::class, 'cityIndex'])->name('admin.courier.city');
    Route::post('/admin-courier-city/store', [CourierController::class, 'cityStore'])->name('admin.courier.city.store');
    Route::post('/admin-courier-city/update', [CourierController::class, 'cityUpdate'])->name('admin.courier.city.update');
    Route::get('/admin-courier-city/delete/{id}', [CourierController::class, 'cityDelete'])->name('admin.courier.city.delete');
    Route::post('/admin-courier-ajax_get_cities', [CourierController::class, 'ajaxGetCities'])->name('admin.courier.ajax.get.cities');
    Route::post('/admin-courier-pathao_ajax_get_cities', [CourierController::class, 'pathaoAjaxGetCities'])->name('admin.courier.pataho.ajax.get.cities');
    Route::post('/admin-courier-pathao_ajax_get_zones', [CourierController::class, 'pathaoAjaxGetZones'])->name('admin.courier.pataho.ajax.get.zones');
    Route::post('/admin-courier-redx_ajax_get_cities', [CourierController::class, 'redxAjaxGetCities'])->name('admin.courier.redx.ajax.get.cities');
    Route::post('/admin-courier-carrybee_ajax_get_cities', [CourierController::class, 'carrybeeAjaxGetCities'])->name('admin.courier.carrybee.ajax.get.cities');
    Route::post('/admin-courier-carrybee_ajax_get_zones', [CourierController::class, 'carrybeeAjaxGetZones'])->name('admin.courier.carrybee.ajax.get.zones');
    // courier zone
    Route::get('/admin-courier-zone', [CourierController::class, 'zoneIndex'])->name('admin.courier.zone');
    Route::post('/admin-courier-zone/store', [CourierController::class, 'zoneStore'])->name('admin.courier.zone.store');
    Route::post('/admin-courier-zone/update', [CourierController::class, 'zoneUpdate'])->name('admin.courier.zone.update');
    Route::get('/admin-courier-zone/delete/{id}', [CourierController::class, 'zoneDelete'])->name('admin.courier.zone.delete');
    Route::post('/admin-courier-ajax_get_zones', [CourierController::class, 'ajaxGetZones'])->name('admin.courier.ajax.get.zones');

    // orders
    Route::get('/admin-orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::get('/admin-orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/admin-orders/store', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/admin-orders/{id}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::post('/admin-orders/{id}/update', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::get('/admin-orders/delete/{id}', [OrderController::class, 'delete'])->name('admin.orders.delete');
    Route::get('/admin-orders/{id}/{status}/status', [OrderController::class, 'statusChange'])->name('admin.orders.status');
    Route::get('/admin-orders/{id}/{status}/payment_status', [OrderController::class, 'paymentStatusChange'])->name('admin.orders.payment_status');
    Route::post('/admin-orders/all-status', [OrderController::class, 'allStatusChange'])->name('admin.orders.all.status');
    Route::post('/admin-orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('admin.orders.bulk.delete');
    Route::post('/admin-orders/bulk-assign', [OrderController::class, 'bulkAssign'])->name('admin.orders.bulk.assign');
    Route::post('/admin-orders/bulk-equal-assign', [OrderController::class, 'bulkEqualAssign'])->name('admin.orders.bulk.equal.assign');
    Route::post('/admin-orders/single-assign', [OrderController::class, 'singleAssign'])->name('admin.orders.single.assign');
    Route::post('/admin-orders/send-to-courier', [OrderController::class, 'sendToCourier'])->name('admin.orders.send.to.courier');
    Route::post('/admin-orders/{id}/forwarding-retry', [OrderController::class, 'retryForwarding'])->name('admin.orders.forwarding.retry');

    // shipping
    Route::post('/admin-ajax-shipping', [OrderController::class, 'getShipping'])->name('admin.ajax.get.shipping');

    // trash orders
    Route::get('/admin-orders/trash', [TrashController::class, 'index'])->name('admin.orders.trash');
    Route::get('/admin-orders/trash/{id}/restore', [TrashController::class, 'restore'])->name('admin.orders.restore');
    Route::get('/admin-orders/trash/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('admin.orders.force.delete');

    // order ajax calls
    Route::post('/admin-ajax-get-products', [OrderController::class, 'ajaxGetProducts'])->name('admin.ajax.get.products');
    Route::post('/admin-orders/customer-old-orders', [OrderController::class, 'customerOldOrders'])->name('admin.orders.customer_old_orders');
    Route::post('/admin-orders/bulk-print', [OrderController::class, 'printBulkInvoice'])->name('admin.orders.bulk.print');
    Route::post('/admin-orders/bulk-label-print', [OrderController::class, 'printBulkLabelInvoice'])->name('admin.orders.bulk.label.print');
    Route::post('/admin-orders/print', [OrderController::class, 'printInvoice'])->name('admin.orders.print');
    // orders export
    Route::post('/admin-orders/courier_csv', [OrderController::class, 'courierCsv'])->name('admin.orders.courier_csv');
    // transaction
    Route::post('/admin-orders/transaction_view', [OrderController::class, 'transactionView'])->name('admin.orders.transaction_view');
    // note update
    Route::post('/admin-orders/note-update', [OrderController::class, 'noteUpdate'])->name('admin.orders.note_update');

    // roles
    Route::get('/admin-roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::post('/admin-roles/store', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::post('/admin-roles/update', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::get('/admin-roles/{id}/{role}/delete', [RoleController::class, 'delete'])->name('admin.roles.delete');

    // push notifications
    Route::post('/admin-push-subscription', [PushSubscriptionController::class, 'store'])->name('admin.push.subscribe');
    Route::delete('/admin-push-subscription', [PushSubscriptionController::class, 'destroy'])->name('admin.push.unsubscribe');
});
