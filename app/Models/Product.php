<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'storage_duration',
        'monthly_sales',
    ];

    /**
     * @return HasMany<DiscountHistory, $this>
     */
    public function discountHistories(): HasMany
    {
        return $this->hasMany(DiscountHistory::class);
    }
}
