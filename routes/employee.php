<?php

use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\BackEnd\DashboardController;
use App\Http\Controllers\BackEnd\FraudController;
use App\Http\Controllers\BackEnd\IncompleteOrdersController;
use App\Http\Controllers\BackEnd\IpController;
use App\Http\Controllers\BackEnd\OrderController;
use App\Http\Controllers\BackEnd\PasswordController;
use App\Http\Controllers\BackEnd\PushSubscriptionController;
use App\Http\Controllers\BackEnd\SmsController;
use App\Http\Controllers\BackEnd\StockController;
use App\Http\Controllers\CourierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Employee Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to employee functionality.
|
*/

// Employee authentication routes
Route::group(['middleware' => 'employee.guest'], function (): void {
    Route::get('/employee-login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/employee-login', [EmployeeLoginController::class, 'login']);
});
Route::post('/employee-logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');

Route::middleware('employee.auth')->group(function (): void {
    // Device approval/request routes (accessible to unapproved devices)
    Route::get('/employee-device-request', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'request'])->name('employee.device.request');
    Route::post('/employee-device-request', [\App\Http\Controllers\BackEnd\AuthDeviceApprovalController::class, 'submit'])->name('employee.device.request.submit');
});

// Employee protected routes
Route::group(['middleware' => ['employee.auth', 'ensure.trusted.device']], function (): void {
    Route::get('/employee', [DashboardController::class, 'dashboard'])->name('employee.home');
    Route::get('/employee/top-sell-filter', [DashboardController::class, 'topSellFilter'])->name('employee.dashboard.top_sell');
    Route::get('/employee/traffic-source-stats', [DashboardController::class, 'trafficSourceStats'])->name('employee.dashboard.traffic_sources');
    Route::get('/employee/utm-medium-stats', [DashboardController::class, 'utmMediumStats'])->name('employee.dashboard.utm_medium');
    Route::get('/employee/utm-campaign-stats', [DashboardController::class, 'utmCampaignStats'])->name('employee.dashboard.utm_campaign');
    Route::get('/employee/top-cities-stats', [DashboardController::class, 'topCitiesStats'])->name('employee.dashboard.top_cities');

    // incomplete orders
    Route::get('/employee-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('employee.incomplete.orders');
    Route::get('/employee-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('employee.incomplete.order.create');
    Route::post('/employee-incomplete-orders/{id}/cancel', [IncompleteOrdersController::class, 'cancel'])->name('employee.incomplete.order.cancel');
    Route::post('/employee-incomplete-orders/{id}/assign-employee', [IncompleteOrdersController::class, 'assignEmployee'])->name('employee.incomplete.order.assign-employee');
    Route::post('/employee-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('employee.incomplete.order.note.update');

    // customer activity
    Route::get('/employee-fraud-check/{id}', [FraudController::class, 'fraudCheck'])->name('employee.fraud.check');

    // send sms from order edit
    Route::post('/employee/send-sms', [SmsController::class, 'sendSms'])->name('employee.send.sms');

    // ip
    Route::get('/employee/ip', [IpController::class, 'index'])->name('employee.ip');
    Route::get('/employee/ip/search', [IpController::class, 'search'])->name('employee.ip.search');

    // stock
    Route::get('/employee/stock', [StockController::class, 'stock'])->name('employee.stock');

    // change password
    Route::get('/employee-change_pass', [PasswordController::class, 'change_pass'])->name('employee.change_pass');
    Route::post('/employee-change_pass', [PasswordController::class, 'update_pass'])->name('employee.update_pass');

    // orders
    Route::get('/employee-p_orders', [OrderController::class, 'indexP'])->name('employee.orders.p');
    Route::get('/employee-orders', [OrderController::class, 'index'])->name('employee.orders');
    Route::get('/employee-orders/create', [OrderController::class, 'create'])->name('employee.orders.create');
    Route::post('/employee-orders/store', [OrderController::class, 'store'])->name('employee.orders.store');
    Route::get('/employee-orders/{id}/edit', [OrderController::class, 'edit'])->name('employee.orders.edit');
    Route::post('/employee-orders/{id}/update', [OrderController::class, 'update'])->name('employee.orders.update');
    Route::get('/employee-orders/{id}/{status}/status', [OrderController::class, 'statusChange'])->name('employee.orders.status');
    Route::get('/employee-orders/{id}/{status}/payment_status', [OrderController::class, 'paymentStatusChange'])->name('employee.orders.payment_status');
    Route::post('/employee-orders/all-status', [OrderController::class, 'allStatusChange'])->name('employee.orders.all.status');

    // order ajax calls
    Route::post('/employee-ajax-get-products', [OrderController::class, 'ajaxGetProducts'])->name('employee.ajax.get.products');
    Route::post('/employee-orders/print', [OrderController::class, 'printInvoice'])->name('employee.orders.print');
    Route::post('/employee-orders/bulk-print', [OrderController::class, 'printBulkInvoice'])->name('employee.orders.bulk.print');
    // shipping
    Route::post('/employee-ajax-shipping', [OrderController::class, 'getShipping'])->name('employee.ajax.get.shipping');

    // orders export
    Route::post('/employee-orders/courier_csv', [OrderController::class, 'courierCsv'])->name('employee.orders.courier_csv');
    // note update
    Route::post('/employee-orders/note-update', [OrderController::class, 'noteUpdate'])->name('employee.orders.note_update');

    // courier
    Route::post('/employee-courier-ajax_get_c_charge', [CourierController::class, 'ajaxGetCCharge'])->name('employee.courier.ajax.get.c_charge');
    Route::post('/employee-courier-ajax_get_cities', [CourierController::class, 'ajaxGetCities'])->name('employee.courier.ajax.get.cities');
    Route::post('/employee-courier-ajax_get_zones', [CourierController::class, 'ajaxGetZones'])->name('employee.courier.ajax.get.zones');
    Route::post('/employee-courier-pathao_ajax_get_cities', [CourierController::class, 'pathaoAjaxGetCities'])->name('employee.courier.pataho.ajax.get.cities');
    Route::post('/employee-courier-pathao_ajax_get_zones', [CourierController::class, 'pathaoAjaxGetZones'])->name('employee.courier.pataho.ajax.get.zones');
    Route::post('/employee-courier-redx_ajax_get_cities', [CourierController::class, 'redxAjaxGetCities'])->name('employee.courier.redx.ajax.get.cities');
    Route::post('/employee-courier-carrybee_ajax_get_cities', [CourierController::class, 'carrybeeAjaxGetCities'])->name('employee.courier.carrybee.ajax.get.cities');
    Route::post('/employee-courier-carrybee_ajax_get_zones', [CourierController::class, 'carrybeeAjaxGetZones'])->name('employee.courier.carrybee.ajax.get.zones');

    // push notifications
    Route::post('/employee-push-subscription', [PushSubscriptionController::class, 'store'])->name('employee.push.subscribe');
    Route::delete('/employee-push-subscription', [PushSubscriptionController::class, 'destroy'])->name('employee.push.unsubscribe');
});
