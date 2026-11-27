<?php

use App\Http\Controllers\Web\DiscountUiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiscountUiController::class, 'dashboard'])
    ->name('dashboard');

Route::post('/simulate', [DiscountUiController::class, 'simulate'])
    ->name('fuzzy.simulate');

Route::get('/products', [DiscountUiController::class, 'products'])
    ->name('ui.products.index');

Route::get('/products/{product}/fuzzy-detail', [DiscountUiController::class, 'productFuzzyDetail'])
    ->name('ui.products.fuzzy-detail');

Route::get('/histories', [DiscountUiController::class, 'histories'])
    ->name('ui.histories.index');
