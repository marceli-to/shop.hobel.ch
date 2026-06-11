<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasGermanSlug;

class Category extends Model
{
	use HasSlug;
	use HasGermanSlug;
	use SoftDeletes;

	protected $fillable = [
		'uuid',
		'name',
		'meta_description',
		'slug',
		'featured',
		'order',
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
	 * Scope a query to only include featured categories.
	 */
	public function scopeFeatured($query)
	{
		return $query->where('featured', true);
	}

	/**
	 * Scope a query to only include categories with published products.
	 */
	public function scopeWithPublishedProducts($query)
	{
		return $query->whereHas('products', function ($query) {
			$query->where('published', true);
		});
	}

	/**
	 * Products that belong to this category.
	 */
	public function products(): BelongsToMany
	{
		return $this->belongsToMany(Product::class);
	}

	/**
	 * Get the single image for this category.
	 */
	public function image(): MorphOne
	{
		return $this->morphOne(Image::class, 'imageable');
	}

	/**
	 * Resolved meta description for SEO, with fallback to the site default.
	 */
	protected function seoDescription(): Attribute
	{
		return Attribute::get(fn () => filled($this->meta_description)
			? $this->meta_description
			: config('shop.meta.description'));
	}

	/**
	 * Absolute OpenGraph image URL (the category image as a 1200×630 social
	 * card), or null to fall back to the site default in the layout.
	 */
	protected function ogImageUrl(): Attribute
	{
		return Attribute::get(fn () => $this->image
			? Image::ogCardUrl($this->image->file_path)
			: null);
	}

	/**
	 * Tags that belong to this category.
	 */
	public function tags(): HasMany
	{
		return $this->hasMany(Tag::class);
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
