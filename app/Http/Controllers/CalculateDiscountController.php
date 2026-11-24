<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountHistoryResource;
use App\Models\Product;
use App\Services\DiscountCalculationService;

class CalculateDiscountController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Product $product, DiscountCalculationService $calculationService): DiscountHistoryResource
    {
        return new DiscountHistoryResource($calculationService->calculate($product));
    }
}
