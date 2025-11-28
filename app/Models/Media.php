<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'width',
        'height',
        'caption',
        'order',
    ];

    /**
     * Get the parent mediable model (Product, Category, etc.).
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL to the media file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Delete the file when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($media) {
            if (Storage::exists($media->file_path)) {
                Storage::delete($media->file_path);
            }
        });
    }
}
