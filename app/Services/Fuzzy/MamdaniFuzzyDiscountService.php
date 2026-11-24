<?php

namespace App\Services\Fuzzy;

class MamdaniFuzzyDiscountService implements FuzzyDiscountService
{
    public function calculate(float $stock, float $storageDuration, float $monthlySales): float
    {
        $result = MamdaniInference::calculateDiscount($stock, $storageDuration, $monthlySales);

        return (float) $result['discount'];
    }
}
