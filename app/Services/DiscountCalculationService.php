<?php

namespace App\Services;

use App\Models\DiscountHistory;
use App\Models\Product;
use App\Services\Fuzzy\FuzzyDiscountService;
use UnexpectedValueException;

class DiscountCalculationService
{
    public function __construct(private FuzzyDiscountService $fuzzyDiscountService) {}

    public function calculate(Product $product): DiscountHistory
    {
        $discountPercentage = $this->fuzzyDiscountService->calculate(
            (float) $product->stock,
            (float) $product->storage_duration,
            (float) $product->monthly_sales,
        );

        if (! is_finite($discountPercentage) || $discountPercentage < 0 || $discountPercentage > 50) {
            throw new UnexpectedValueException('Fuzzy service returned a discount outside the 0-50 range.');
        }

        return $product->discountHistories()->create([
            'discount_percentage' => $discountPercentage,
        ]);
    }
}
