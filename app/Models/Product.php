<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
	use HasSlug;
	use SoftDeletes;

	protected $fillable = [
		'uuid',
		'name',
		'slug',
		'description',
		'price',
		'stock',
		'published',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'published' => 'boolean',
	];

	/**
	 * Get the options for generating the slug.
	 */
	public function getSlugOptions(): SlugOptions
	{
		return SlugOptions::create()
			->generateSlugsFrom('name')
			->saveSlugsTo('slug');
	}

	/**
	 * Get the route key for the model.
	 */
	public function getRouteKeyName(): string
	{
		return 'slug';
	}

	/**
	 * Categories that belong to this product.
	 */
	public function categories(): BelongsToMany
	{
		return $this->belongsToMany(Category::class);
	}

	/**
	 * Get all images for this product.
	 */
	public function images(): MorphMany
	{
		return $this->morphMany(Image::class, 'imageable')->orderBy('order');
	}

	/**
	 * Get the preview image (for category views).
	 */
  public function previewImage()
  {
    return $this->morphOne(Image::class, 'imageable')->ofMany([
      'preview' => 'MAX', // 1 (true) will come before 0 (false)
      'id'      => 'MIN', // Tie-breaker: if no preview, pick the oldest/first uploaded
    ]);
  }

	/**
	 * Boot the model.
	 */
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($product) {
			if (empty($product->uuid)) {
				$product->uuid = (string) Str::uuid();
			}
		});
	}
}
