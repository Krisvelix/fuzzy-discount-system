<?php

use App\Http\Controllers\CalculateDiscountController;
use App\Http\Controllers\DiscountHistoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

Route::get('products/{product}/discount-histories', [DiscountHistoryController::class, 'index'])
    ->name('products.discount-histories.index');

Route::post('products/{product}/discount-calculations', CalculateDiscountController::class)
    ->name('products.discount-calculations.store');
