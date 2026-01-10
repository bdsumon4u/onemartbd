<?php

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

//dashboard
Route::get('/v1/dashboard', 'API\AdminController@dashboard')/*->where('url','(.*)')*/;

//orders
Route::get('/v1/orders', 'API\OrderController@index')/*->where('url','(.*)')*/;
Route::get('/v1/archive/orders', 'API\OrderController@archiveOrders')/*->where('url','(.*)')*/;
Route::post('/v1/order/store', 'API\OrderController@store');
Route::get('/v1/order/edit', 'API\OrderController@edit');
Route::post('/v1/order/update', 'API\OrderController@update');
Route::get('/v1/order/delete', 'API\OrderController@delete');
Route::get('/v1/order/status', 'API\OrderController@status');
Route::get('/v1/order/print_invoice', 'API\OrderController@printInvoice');
Route::get('/v1/order/bulk_label_print', 'API\OrderController@bulkLabelPrint');
Route::get('/v1/order/assign', 'API\OrderController@singleAssign');
Route::get('/v1/order/bulk_assign', 'API\OrderController@bulkAssign');
Route::post('/v1/order/transaction_view', 'API\OrderController@transactionView');
Route::post('/v1/order/note_update', 'API\OrderController@noteUpdate');
Route::post('/v1/order/send_sms', 'API\OrderController@sendSms');

Route::get('/v1/order/bulk_status', 'API\OrderController@bulkStatus');
Route::get('/v1/order/courier_csv_export', 'API\OrderController@courierCsvExport');
Route::get('/v1/order/payment_status', 'API\OrderController@paymentStatus');

Route::get('/v1/order/product_courier', 'API\OrderController@productCourier');
Route::get('/v1/order/product_info', 'API\OrderController@productInfo');
Route::get('/v1/order/pathao/cities', 'API\OrderController@pathaoCities');
Route::get('/v1/order/pathao/zones', 'API\OrderController@pathaoZones');
Route::get('/v1/order/redx/cities', 'API\OrderController@redxCities');
Route::get('/v1/order/carrybee/cities', 'API\OrderController@carrybeeCities');
Route::get('/v1/order/carrybee/zones', 'API\OrderController@carrybeeZones');

//employee sync
Route::post('/v1/staff/sync', 'API\StaffController@staffSync');

//reports
Route::get('/v1/order/report/employee_orders', 'API\ReportController@employeeOrders');
Route::get('/v1/order/report/order_status_product', 'API\ReportController@orderStatusProduct');
Route::get('/v1/order/report/orders_product', 'API\ReportController@ordersProduct');


Route::post('/landing/order', 'LandingOrderController');
