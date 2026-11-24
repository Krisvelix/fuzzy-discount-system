<?php

namespace App\Providers;

use App\Services\Fuzzy\FuzzyDiscountService;
use App\Services\Fuzzy\MamdaniFuzzyDiscountService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FuzzyDiscountService::class, MamdaniFuzzyDiscountService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
