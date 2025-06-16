<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BranchController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductDeatilController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/web-login-by-token', function (Request $request) {
    $user = User::find($request->user_id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    Auth::login($user);  // this creates Laravel session

    return response()->json(['message' => 'Laravel session created']);
});
/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Management Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands');
    Route::get('/product-details', [ProductDeatilController::class, 'index'])->name('productDetails');
    Route::get('/branches', [BranchController::class, 'index'])->name('branches');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/users', [AuthController::class, 'index'])->name('users');

    Route::view('/orders', 'admin.Orders.index')->name('orders');
    Route::view('/order-details', 'admin.order-details')->name('order.details');
    Route::view('/invoice', 'admin.invoice.index')->name('invoice');

    Route::view('/payments', 'admin.payments.index')->name('payments');
    Route::view('/payment-methods', 'admin.payment-methods')->name('payment.methods');

    Route::view('/profile', 'admin.profile')->name('profile');
    Route::view('/contactUs', 'admin.contactUs.index')->name('contactUs');
});
