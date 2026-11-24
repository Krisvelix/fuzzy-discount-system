<?php

namespace App\Services\Fuzzy;

interface FuzzyDiscountService
{
    public function calculate(float $stock, float $storageDuration, float $monthlySales): float;
}
