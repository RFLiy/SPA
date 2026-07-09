<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RfqController;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('welcome');

Route::get('/debug-assets', function () {
    $path = public_path('css/filament/filament');
    if (!is_dir($path)) {
        return "Folder gak ada: " . $path;
    }
    return response()->json(scandir($path));
});

Auth::routes(['reset' => false, 'confirm' => false]);

Route::get('home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    //PROFILE / ACCOUNT
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('user-profile-information.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('user-password.update');
    Route::resource('products', ProductController::class)->only(['index', 'show']);

    // CART
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/increment/{cart}', [CartController::class, 'increment'])->name('cart.increment');
    Route::post('/cart/decrement/{cart}', [CartController::class, 'decrement'])->name('cart.decrement');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    // ORDER
    Route::get('/order/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/order/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');
    Route::post('/orders/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
    Route::get('/orders/{order}/download-sj', [OrderController::class, 'downloadSuratJalan'])->name('orders.download-sj');
    Route::post('/orders/{id}/finish', [OrderController::class, 'finish'])->name('orders.finish');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // CHECKOUT
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

Route::post('/midtrans/webhook', [OrderController::class, 'midtransWebhook'])->name('midtrans.webhook');
Route::post('/midtrans-callback', [OrderController::class, 'midtransWebhook']);




