<?php

use App\Http\Controllers\Clients\AccountController;
use App\Http\Middleware\RedirectIfNotAuthenticated;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\ForgotPasswordController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\CartController;
use App\Http\Controllers\Clients\OrderController;
use App\Http\Controllers\Clients\CheckoutController;
use App\Http\Controllers\Clients\WishlistController;
use App\Http\Controllers\Clients\ContactController;
use App\Http\Controllers\Clients\ReviewController;
use App\Http\Controllers\Clients\SearchController;
use App\Http\Controllers\Clients\ResetPasswordController;
use App\Http\Controllers\Clients\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('clients.pages.about');
})->name('about');

Route::get('/service', function () {
    return view('clients.pages.service');
})->name('service');

Route::get('/team', function () {
    return view('clients.pages.team');
})->name('team');

Route::get('/faq', function () {
    return view('clients.pages.faq');
})->name('faq');

//guest
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'Showregister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('post.register');

    Route::get('/login', [AuthController::class, 'Showlogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('post.login');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'ShowforgotPassword'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('post.forgot-password');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'ShowresetPassword'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('post.reset-password');
});

Route::get('/activate/{token}', [AuthController::class, 'activate'])->name('activate');

Route::middleware(RedirectIfNotAuthenticated::class)->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // Các route khác dành cho người dùng đã đăng nhập
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('account');
        Route::put('/update', [AccountController::class, 'update'])->name('account.update');

        //change password
        Route::get('/change-password', [AccountController::class, 'showChangePasswordForm'])->name('account.change-password');
        Route::post('/change-password', [AccountController::class, 'changePassword'])->name('account.post-change-password');

        //address
        Route::post('/addresses', [AccountController::class, 'addAddress'])->name('account.address.add');
        Route::put('/addresses/{id}', [AccountController::class, 'updateAddress'])->name('account.address.update');
        Route::delete('/addresses/{id}', [AccountController::class, 'deleteAddress'])->name('account.address.delete');
    });
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/checkout/get-address', [CheckoutController::class, 'getAddress'])->name('checkout.get-address');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::post('/checkout/paypal', [CheckoutController::class, 'placeOrderPaypal'])->name('checkout.placeOrderPaypal');
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf.token');

    //Order
    Route::get('/order/{id}', [OrderController::class, 'ShowOrders'])->name('orders.show');
    Route::post('/order/{id}/received', [OrderController::class, 'receivedOrder'])->name('orders.received');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    //Rating
    Route::post('/review', [ReviewController::class, 'createReview'])->name('review.store');
    Route::get('/review/{product}', [ReviewController::class, 'index'])->name('review.index');
    //Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
//Detail product
Route::get('/product/{slug}', [ProductController::class, 'detail'])->name('product.detail');



//Handel cart
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/mini-cart', [CartController::class, 'loadMiniCart'])->name('cart.mini');

//Handle page cart
Route::get('/cart', [CartController::class, 'showCart'])->name('cart');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove-cart', [CartController::class, 'removeCart'])->name('cart.remove');

//Handle contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'sendContact'])->name('contact.send');

//Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

//Chat AI
Route::get('/chat/history', [ChatController::class, 'getHistory'])
    ->middleware('throttle:60,1')
    ->name('chat.history');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])
    ->middleware('throttle:20,1')
    ->name('chat.send');

require __DIR__ . '/admin.php';
