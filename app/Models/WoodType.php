<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class WoodType extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'price',
        'sorting_factor',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sorting_factor' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($woodType) {
            if (empty($woodType->uuid)) {
                $woodType->uuid = (string) Str::uuid();
            }
        });
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
