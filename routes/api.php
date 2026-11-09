<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('admin/login', [AuthController::class, 'adminLogin']);
});

Route::prefix('storefront')->group(function () {
    Route::get('shops', [StorefrontController::class, 'shops']);
    Route::get('products', [StorefrontController::class, 'latest']);
    Route::get('shops/{shop:slug}', [StorefrontController::class, 'shop']);
    Route::get('shops/{shop:slug}/products', [StorefrontController::class, 'products']);
    Route::get('shops/{shop:slug}/products/{listing}', [StorefrontController::class, 'product']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('dashboard', Admin\DashboardController::class);
        Route::get('options/owners', [Admin\OptionController::class, 'owners']);
        Route::get('options/shops', [Admin\OptionController::class, 'shops']);
        Route::apiResource('shops', Admin\ShopController::class);
        Route::apiResource('products', Admin\ProductController::class);
        Route::apiResource('owners', Admin\OwnerController::class);
    });

    Route::prefix('owner')->name('owner.')->middleware('role:shop_owner')->group(function () {
        Route::get('dashboard', Owner\DashboardController::class);
        Route::get('catalog', [Owner\CatalogController::class, 'index']);
        Route::post('catalog', [Owner\CatalogController::class, 'store']);
        Route::put('catalog/{product}', [Owner\CatalogController::class, 'update']);
        Route::delete('catalog/{product}', [Owner\CatalogController::class, 'destroy']);
        Route::apiResource('listings', Owner\ListingController::class)->except('show');
    });
});
