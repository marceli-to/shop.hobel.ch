<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WoodType extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'price',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
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
}
