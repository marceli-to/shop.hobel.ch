<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
	use HasSlug;
	use SoftDeletes;

	protected $fillable = [
		'uuid',
		'name',
		'slug',
		'featured',
	];

	protected $casts = [
		'featured' => 'boolean',
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
	 * Get all media items for this category.
	 */
	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('order');
	}

	/**
	 * Get the single image for this category.
	 */
	public function image(): MorphOne
	{
		return $this->morphOne(Media::class, 'mediable')->oldestOfMany('order');
	}

	/**
	 * Get the first/only image.
	 */
	public function getImage(): ?Media
	{
		return $this->image;
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
