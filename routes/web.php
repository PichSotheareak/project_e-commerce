<?php

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
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');

    // Management Routes
    Route::view('/branches', 'admin.branches')->name('branches');
    Route::view('/brands', 'admin.brands')->name('brands');
    Route::view('/categories', 'admin.categories')->name('categories');
    Route::view('/products', 'admin.products')->name('products');
    Route::view('/product-details', 'admin.product-details')->name('product.details');

    Route::view('/orders', 'admin.orders')->name('orders');
    Route::view('/order-details', 'admin.order-details')->name('order.details');
    Route::view('/invoices', 'admin.invoices')->name('invoices');

    Route::view('/payments', 'admin.payments')->name('payments');
    Route::view('/payment-methods', 'admin.payment-methods')->name('payment.methods');

    Route::view('/customers', 'admin.customers')->name('customers');
    Route::view('/staff', 'admin.staff')->name('staff');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/contact-us', 'admin.contact')->name('contact.us');
});
