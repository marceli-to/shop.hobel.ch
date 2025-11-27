<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model implements HasMedia
{
	use HasSlug;
	use InteractsWithMedia;

	protected $fillable = [
		'uuid',
		'name',
		'slug',
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
	 * Products that belong to this category.
	 */
	public function products(): BelongsToMany
	{
		return $this->belongsToMany(Product::class);
	}

	/**
	 * Register media collections.
	 */
	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('image')
			->singleFile()
			->useDisk('public');
	}

	/**
	 * Get the image path for use with Glide.
	 */
	public function getImagePath(?string $collection = 'image'): ?string
	{
		$media = $this->getFirstMedia($collection);

		if (!$media) {
			return null;
		}

		// Return path relative to storage/app/public for Glide
		return str_replace(storage_path('app/public/'), '', $media->getPath());
	}

	/**
	 * Boot the model.
	 */
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($category) {
			if (empty($category->uuid)) {
				$category->uuid = (string) Str::uuid();
			}
		});
	}
}
