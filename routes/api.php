<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceItemsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//register api route
Route::post("auth/login", [UserController::class, "login"]);
Route::post("login", [CustomerController::class, "login"]);

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource("branches", BranchesController::class);
    Route::apiResource("category", CategoryController::class);
    Route::apiResource("product", ProductController::class);
    Route::apiResource("brand", BrandController::class);
    Route::apiResource("productDetails", ProductDetailsController::class);
    Route::apiResource("customer", CustomerController::class);
    Route::apiResource("paymentMethod", PaymentMethodController::class);
    Route::apiResource("contactUs", ContactUsController::class);
    Route::apiResource("staff", StaffController::class);
    Route::apiResource("orders", OrdersController::class);
    Route::apiResource("orderDetails", OrderDetailsController::class);
    Route::apiResource("invoices", InvoicesController::class);
    Route::apiResource("invoiceItems", InvoiceItemsController ::class);
    Route::apiResource("payments", PaymentsController::class);
    Route::apiResource("users", UserController::class);
});
//get, get1, post, put, delete
