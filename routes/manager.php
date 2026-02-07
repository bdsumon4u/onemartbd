<?php

use App\Http\Controllers\Auth\ManagerLoginController;
use App\Http\Controllers\BackEnd\DashboardController;
use App\Http\Controllers\BackEnd\FraudController;
use App\Http\Controllers\BackEnd\IncompleteOrdersController;
use App\Http\Controllers\BackEnd\IpController;
use App\Http\Controllers\BackEnd\OrderController;
use App\Http\Controllers\BackEnd\PasswordController;
use App\Http\Controllers\BackEnd\ProductController;
use App\Http\Controllers\BackEnd\ReportController;
use App\Http\Controllers\BackEnd\RoleController;
use App\Http\Controllers\BackEnd\SmsController;
use App\Http\Controllers\BackEnd\StockController;
use App\Http\Controllers\BackEnd\UserController;
use App\Http\Controllers\CourierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to manager functionality.
|
*/

// Manager authentication routes
Route::group(['middleware' => 'manager.guest'], function (): void {
    Route::get('/manager-login', [ManagerLoginController::class, 'showLoginForm'])->name('manager.login');
    Route::post('/manager-login', [ManagerLoginController::class, 'login']);
});
Route::post('/manager-logout', [ManagerLoginController::class, 'logout'])->name('manager.logout');

// Manager protected routes
Route::group(['middleware' => 'manager.auth'], function (): void {
    Route::get('/manager', [DashboardController::class, 'dashboard'])->name('manager.home');
    Route::get('/manager/top-sell-filter', [DashboardController::class, 'topSellFilter'])->name('manager.dashboard.top_sell');

    // incomplete orders
    Route::get('/manager-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('manager.incomplete.orders');
    Route::get('/manager-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('manager.incomplete.order.create');
    Route::post('/manager-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('manager.incomplete.order.note.update');

    // customer activity
    Route::get('/manager-fraud-check/{id}', [FraudController::class, 'fraudCheck'])->name('manager.fraud.check');

    Route::get('/manager-customers', [UserController::class, 'index'])->name('manager.customers');

    // send sms from order edit
    Route::post('/manager/send-sms', [SmsController::class, 'sendSms'])->name('manager.send.sms');

    // ip
    Route::get('/manager/ip', [IpController::class, 'index'])->name('manager.ip');
    Route::get('/manager/ip/search', [IpController::class, 'search'])->name('manager.ip.search');
    Route::get('/manager/ip/{id}/{status}/status', [IpController::class, 'ipStatus'])->name('manager.ip.status');

    // stock
    Route::get('/manager/stock', [StockController::class, 'stock'])->name('manager.stock');

    // reports
    Route::get('/manager-reports/employee-orders', [ReportController::class, 'employeeOrders'])->name('manager.reports.employee_orders');
    Route::get('/manager-reports/order-status-p', [ReportController::class, 'orderStatusP'])->name('manager.reports.order_status_p');
    Route::get('/manager-reports/orders-product', [ReportController::class, 'ordersProduct'])->name('manager.reports.orders_product');

    // change password
    Route::get('/manager-change_pass', [PasswordController::class, 'change_pass'])->name('manager.change_pass');
    Route::post('/manager-change_pass', [PasswordController::class, 'update_pass'])->name('manager.update_pass');

    // orders
    Route::get('/manager-p_orders', [OrderController::class, 'indexP'])->name('manager.orders.p');
    Route::get('/manager-orders', [OrderController::class, 'index'])->name('manager.orders');
    Route::get('/manager-orders/create', [OrderController::class, 'create'])->name('manager.orders.create');
    Route::post('/manager-orders/store', [OrderController::class, 'store'])->name('manager.orders.store');
    Route::get('/manager-orders/{id}/edit', [OrderController::class, 'edit'])->name('manager.orders.edit');
    Route::post('/manager-orders/{id}/update', [OrderController::class, 'update'])->name('manager.orders.update');
    Route::get('/manager-orders/{id}/{status}/status', [OrderController::class, 'statusChange'])->name('manager.orders.status');
    Route::get('/manager-orders/{id}/{status}/payment_status', [OrderController::class, 'paymentStatusChange'])->name('manager.orders.payment_status');
    Route::post('/manager-orders/all-status', [OrderController::class, 'allStatusChange'])->name('manager.orders.all.status');
    Route::post('/manager-orders/bulk-assign', [OrderController::class, 'bulkAssign'])->name('manager.orders.bulk.assign');

    // order ajax calls
    Route::post('/manager-ajax-get-products', [OrderController::class, 'ajaxGetProducts'])->name('manager.ajax.get.products');
    Route::post('/manager-orders/print', [OrderController::class, 'printInvoice'])->name('manager.orders.print');
    Route::post('/manager-orders/bulk-print', [OrderController::class, 'printBulkInvoice'])->name('manager.orders.bulk.print');
    Route::post('/manager-orders/bulk-label-print', [OrderController::class, 'printBulkLabelInvoice'])->name('manager.orders.bulk.label.print');
    // shipping
    Route::post('/manager-ajax-shipping', [OrderController::class, 'getShipping'])->name('manager.ajax.get.shipping');

    // orders export
    Route::post('/manager-orders/courier_csv', [OrderController::class, 'courierCsv'])->name('manager.orders.courier_csv');
    // note update
    Route::post('/manager-orders/note-update', [OrderController::class, 'noteUpdate'])->name('manager.orders.note_update');
    // transaction
    Route::post('/manager-orders/transaction_view', [OrderController::class, 'transactionView'])->name('manager.orders.transaction_view');

    // product
    Route::get('/manager-product', [ProductController::class, 'index'])->name('manager.product');
    Route::get('/manager-product/create', [ProductController::class, 'create'])->name('manager.product.create');
    Route::post('/manager-product/store', [ProductController::class, 'store'])->name('manager.product.store');
    Route::get('/manager-product/{id}/edit', [ProductController::class, 'edit'])->name('manager.product.edit');
    Route::post('/manager-product/{id}/update', [ProductController::class, 'update'])->name('manager.product.update');
    Route::get('/manager-product/{id}/delete', [ProductController::class, 'delete'])->name('manager.product.delete');

    // courier
    Route::get('/manager-courier', [CourierController::class, 'index'])->name('manager.courier');
    Route::post('/manager-courier/store', [CourierController::class, 'store'])->name('manager.courier.store');
    Route::post('/manager-courier/update', [CourierController::class, 'update'])->name('manager.courier.update');
    Route::get('/manager-courier/delete/{id}', [CourierController::class, 'delete'])->name('manager.courier.delete');
    Route::post('/manager-courier-ajax_get_c_charge', [CourierController::class, 'ajaxGetCCharge'])->name('manager.courier.ajax.get.c_charge');

    // courier city
    Route::get('/manager-courier-city', [CourierController::class, 'cityIndex'])->name('manager.courier.city');
    Route::post('/manager-courier-city/store', [CourierController::class, 'cityStore'])->name('manager.courier.city.store');
    Route::post('/manager-courier-city/update', [CourierController::class, 'cityUpdate'])->name('manager.courier.city.update');
    Route::get('/manager-courier-city/delete/{id}', [CourierController::class, 'cityDelete'])->name('manager.courier.city.delete');
    Route::post('/manager-courier-ajax_get_cities', [CourierController::class, 'ajaxGetCities'])->name('manager.courier.ajax.get.cities');
    Route::post('/manager-courier-pathao_ajax_get_cities', [CourierController::class, 'pathaoAjaxGetCities'])->name('manager.courier.pataho.ajax.get.cities');
    Route::post('/manager-courier-pathao_ajax_get_zones', [CourierController::class, 'pathaoAjaxGetZones'])->name('manager.courier.pataho.ajax.get.zones');
    Route::post('/manager-courier-redx_ajax_get_cities', [CourierController::class, 'redxAjaxGetCities'])->name('manager.courier.redx.ajax.get.cities');
    Route::post('/manager-courier-carrybee_ajax_get_cities', [CourierController::class, 'carrybeeAjaxGetCities'])->name('manager.courier.carrybee.ajax.get.cities');
    Route::post('/manager-courier-carrybee_ajax_get_zones', [CourierController::class, 'carrybeeAjaxGetZones'])->name('manager.courier.carrybee.ajax.get.zones');

    // courier zone
    Route::get('/manager-courier-zone', [CourierController::class, 'zoneIndex'])->name('manager.courier.zone');
    Route::post('/manager-courier-zone/store', [CourierController::class, 'zoneStore'])->name('manager.courier.zone.store');
    Route::post('/manager-courier-zone/update', [CourierController::class, 'zoneUpdate'])->name('manager.courier.zone.update');
    Route::get('/manager-courier-zone/delete/{id}', [CourierController::class, 'zoneDelete'])->name('manager.courier.zone.delete');
    Route::post('/manager-courier-ajax_get_zones', [CourierController::class, 'ajaxGetZones'])->name('manager.courier.ajax.get.zones');

    // roles
    Route::get('/manager-roles', [RoleController::class, 'index'])->name('manager.roles');
    Route::post('/manager-roles/store', [RoleController::class, 'store'])->name('manager.roles.store');
    Route::post('/manager-roles/update', [RoleController::class, 'update'])->name('manager.roles.update');
    Route::get('/manager-roles/{id}/{role}/delete', [RoleController::class, 'delete'])->name('manager.roles.delete');
});
