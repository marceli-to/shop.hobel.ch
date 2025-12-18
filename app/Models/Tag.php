<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasGermanSlug;

class Tag extends Model
{
	use HasSlug;
	use HasGermanSlug;
	use SoftDeletes;

	protected $fillable = [
		'uuid',
		'category_id',
		'name',
		'slug',
		'order',
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
	 * Category that this tag belongs to.
	 */
	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}

	/**
	 * Products that have this tag.
	 */
	public function products(): BelongsToMany
	{
		return $this->belongsToMany(Product::class);
	}

	/**
	 * Boot the model.
	 */
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($tag) {
			if (empty($tag->uuid)) {
				$tag->uuid = (string) Str::uuid();
			}
		});
	}
}
