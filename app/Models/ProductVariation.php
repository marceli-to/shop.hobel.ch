<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductVariation extends Model
{
    protected $fillable = [
        'uuid',
        'product_id',
        'name',
        'label',
        'short_description',
        'sku',
        'price',
        'stock',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variation) {
            if (empty($variation->uuid)) {
                $variation->uuid = (string) Str::uuid();
            }
        });
    }
}
