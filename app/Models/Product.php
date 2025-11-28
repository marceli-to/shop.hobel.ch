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
	 * Get all media items for this product.
	 */
	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('order');
	}

	/**
	 * Get the first image.
	 */
	public function getFirstImage(): ?Media
	{
		return $this->media()->first();
	}

	/**
	 * Get all images.
	 */
	public function getImages()
	{
		return $this->media;
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
