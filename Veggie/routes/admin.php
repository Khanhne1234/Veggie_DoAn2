<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;


Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard');
    })->name('admin.dashboard');

    Route::get('/login', [AdminAuthController::class, 'ShowLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'Login'])->name('admin.login.post');
});
