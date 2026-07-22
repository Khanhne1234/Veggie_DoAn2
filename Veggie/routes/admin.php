<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Middleware\DefaultAdminData;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DashboardController;


Route::prefix('admin')->group(function () {


    Route::middleware(['check.auth.admin'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'ShowLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'Login'])->name('admin.login.post');
    });

    Route::middleware(['auth.custom', DefaultAdminData::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/profile', [AccountController::class, 'index'])->name('admin.profile');
        Route::post('/profile/update', [AccountController::class, 'updateProfile'])->name('admin.profile.update');
        Route::get('/logout', [AdminAuthController::class, 'Logout'])->name('admin.logout');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('/notification/update', [NotificationController::class, 'updateNotification'])->name('admin.notification.update');

        Route::middleware(['check.permission:manage_users'])->group(function () {
            Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
            Route::post('/user/upgrade', [UsersController::class, 'upgrade'])->name('admin.user.upgrade');
            Route::post('/user/updateStatus', [UsersController::class, 'updateStatus'])->name('admin.user.updateStatus');
        });
        Route::middleware(['check.permission:manage_categories'])->group(function () {
            Route::get('/categories/add', [CategoryController::class, 'ShowFormAddCategory'])->name('admin.categories.add');
            Route::post('/categories/add', [CategoryController::class, 'AddCategory'])->name('admin.categories.store');

            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');

            Route::post('/categories/update', [CategoryController::class, 'UpdateCategory'])->name('admin.categories.update');
            Route::post('/categories/delete', [CategoryController::class, 'DeleteCategory'])->name('admin.categories.delete');
        });

        Route::middleware(['check.permission:manage_products'])->group(function () {
            Route::get('/product/add', [ProductController::class, 'ShowFormAddProduct'])->name('admin.product.add');
            Route::post('/product/add', [ProductController::class, 'AddProduct'])->name('admin.product.index');

            Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');

            Route::post('/product/update', [ProductController::class, 'UpdateProduct'])->name('admin.product.update');
            Route::post('/product/delete', [ProductController::class, 'DeleteProduct'])->name('admin.product.delete');
        });

        Route::middleware(['check.permission:manage_orders'])->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');

            Route::post('/order/confirm', [OrderController::class, 'confirm'])->name('admin.order.confirm');
            Route::get('/order-detail/{id}', [OrderController::class, 'showOrderDetail'])->name('admin.order_detail');
            Route::post('/order-detail/send-invoice', [OrderController::class, 'sendMailInvoice'])->name('admin.order.sendMail');
            Route::post('/order-detail/cancel-order', [OrderController::class, 'cancelOrder'])->name('admin.order.cancel');
        });

        Route::middleware(['check.permission:manage_contacts'])->group(function () {
            Route::get('/contacts', [ContactController::class, 'index'])->name('admin.contacts.index');

            Route::post('/contact/confirm', [ContactController::class, 'confirm'])->name('admin.contact.confirm');
            Route::post('/contact/reply', [ContactController::class, 'reply'])->name('admin.contact.reply');
        });
    });
});
