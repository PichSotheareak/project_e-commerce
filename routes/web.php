<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admin.master');
});
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
Route::view('/login', 'admin.login')->name('login');
