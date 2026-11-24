<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountHistory extends Model
{
    protected $fillable = [
        'product_id',
        'discount_percentage',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
        ];
    }
}
