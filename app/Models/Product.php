<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
	use HasSlug;
	use InteractsWithMedia;

	protected $fillable = [
		'uuid',
		'name',
		'slug',
		'description',
		'price',
		'stock',
	];

	protected $casts = [
		'price' => 'decimal:2',
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
	 * Register media collections.
	 */
	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('gallery')
			->useDisk('public');
	}

	/**
	 * Get the first image path from the gallery collection for use with Glide.
	 */
	public function getImagePath(?string $collection = 'gallery'): ?string
	{
		$media = $this->getFirstMedia($collection);

		if (!$media) {
			return null;
		}

		// Return path relative to storage/app/public for Glide
		return str_replace(storage_path('app/public/'), '', $media->getPath());
	}

	/**
	 * Get all image paths from the gallery collection for use with Glide.
	 */
	public function getImagePaths(?string $collection = 'gallery'): array
	{
		return $this->getMedia($collection)->map(function ($media) {
			return str_replace(storage_path('app/public/'), '', $media->getPath());
		})->toArray();
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
