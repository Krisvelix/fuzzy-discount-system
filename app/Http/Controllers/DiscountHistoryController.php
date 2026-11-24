<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountHistoryResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiscountHistoryController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        return DiscountHistoryResource::collection(
            $product->discountHistories()
                ->latest('id')
                ->paginate(15)
        );
    }
}
