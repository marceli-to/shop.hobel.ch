<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Surface extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'price',
        'minimum_amount',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($surface) {
            if (empty($surface->uuid)) {
                $surface->uuid = (string) Str::uuid();
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
