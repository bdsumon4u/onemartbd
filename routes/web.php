<?php

use App\Http\Controllers\CourierController;
use App\Http\Controllers\HomeController;
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
