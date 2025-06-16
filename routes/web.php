<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/staff', [StaffController::class, 'index'])->name('staff');

    // Management Routes
    Route::view('/branches', 'admin.branches')->name('branches');
    Route::view('/brands', 'admin.brands')->name('brands');
    Route::view('/categories', 'admin.categories')->name('categories');
    Route::view('/products', 'admin.products')->name('products');
    Route::view('/product-details', 'admin.product-details')->name('product.details');

    Route::view('/orders', 'admin.Orders.index')->name('orders');
    Route::view('/order-details', 'admin.order-details')->name('order.details');
    Route::view('/invoice', 'admin.invoice.index')->name('invoice');

    Route::view('/payments', 'admin.payments.index')->name('payments');
    Route::view('/payment-methods', 'admin.payment-methods')->name('payment.methods');

    Route::view('/customers', 'admin.customers')->name('customers');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/contactUs', 'admin.contactUs.index')->name('contactUs');
});
