<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
	use HasSlug;

	protected $fillable = [
		'uuid',
		'name',
		'slug',
		'description',
		'price',
		'stock',
		'image',
		'published_at',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'published_at' => 'datetime',
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
	 * Scope a query to only include published products.
	 */
	public function scopePublished($query)
	{
		return $query->whereNotNull('published_at');
	}

	/**
	 * Check if product is published.
	 */
	public function isPublished(): bool
	{
		return !is_null($this->published_at);
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
