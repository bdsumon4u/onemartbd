<?php

use App\Http\Controllers\API\AbandonedCartForwardingController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderForwardingController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\LandingOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// dashboard
Route::get('/v1/dashboard', [AdminController::class, 'dashboard']);

// orders
Route::get('/v1/orders', [OrderController::class, 'index']);
Route::get('/v1/archive/orders', [OrderController::class, 'archiveOrders']);
Route::post('/v1/order/store', [OrderController::class, 'store']);
Route::get('/v1/order/edit', [OrderController::class, 'edit']);
Route::post('/v1/order/update', [OrderController::class, 'update']);
Route::get('/v1/order/delete', [OrderController::class, 'delete']);
Route::get('/v1/order/status', [OrderController::class, 'status']);
Route::get('/v1/order/print_invoice', [OrderController::class, 'printInvoice']);
Route::get('/v1/order/bulk_label_print', [OrderController::class, 'bulkLabelPrint']);
Route::get('/v1/order/assign', [OrderController::class, 'singleAssign']);
Route::get('/v1/order/bulk_assign', [OrderController::class, 'bulkAssign']);
Route::post('/v1/order/transaction_view', [OrderController::class, 'transactionView']);
Route::post('/v1/order/note_update', [OrderController::class, 'noteUpdate']);
Route::post('/v1/order/send_sms', [OrderController::class, 'sendSms']);

Route::get('/v1/order/bulk_status', [OrderController::class, 'bulkStatus']);
Route::get('/v1/order/courier_csv_export', [OrderController::class, 'courierCsvExport']);
Route::get('/v1/order/payment_status', [OrderController::class, 'paymentStatus']);

Route::get('/v1/order/product_courier', [OrderController::class, 'productCourier']);
Route::get('/v1/order/product_info', [OrderController::class, 'productInfo']);
Route::get('/v1/order/pathao/cities', [OrderController::class, 'pathaoCities']);
Route::get('/v1/order/pathao/zones', [OrderController::class, 'pathaoZones']);
Route::get('/v1/order/redx/cities', [OrderController::class, 'redxCities']);
Route::get('/v1/order/carrybee/cities', [OrderController::class, 'carrybeeCities']);
Route::get('/v1/order/carrybee/zones', [OrderController::class, 'carrybeeZones']);

// employee sync
Route::post('/v1/staff/sync', [StaffController::class, 'staffSync']);

// reports
Route::get('/v1/order/report/employee_orders', [ReportController::class, 'employeeOrders']);
Route::get('/v1/order/report/order_status_product', [ReportController::class, 'orderStatusProduct']);
Route::get('/v1/order/report/orders_product', [ReportController::class, 'ordersProduct']);

Route::post('/landing/order', [LandingOrderController::class, 'handle']);

// cross-site order forwarding
Route::post('/slave-orders', [OrderForwardingController::class, 'receiveFromSlave']);
Route::post('/slave-orders/status', [OrderForwardingController::class, 'updateStatusFromSlave']);
Route::post('/master-orders/status', [OrderForwardingController::class, 'updateStatusFromMaster']);

Route::post('/slave-abandoned-carts', [AbandonedCartForwardingController::class, 'receiveFromSlave']);
// product forwarding endpoints
use App\Http\Controllers\API\ProductForwardingController;

Route::post('/slave-products', [ProductForwardingController::class, 'receiveFromSlave']);
