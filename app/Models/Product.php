<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\HasGermanSlug;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
	use HasSlug;
	use HasGermanSlug;
	use SoftDeletes;

	protected $fillable = [
		'uuid',
		'parent_id',
		'type',
		'name',
		'slug',
		'short_description',
		'label',
		'description',
		'sku',
		'delivery_time',
		'price',
		'stock',
		'order',
		'published',
	];

	protected $casts = [
		'type' => ProductType::class,
		'price' => 'decimal:2',
		'published' => 'boolean',
	];

	/**
	 * Check if product is of type simple.
	 */
	protected function isSimple(): Attribute
	{
		return Attribute::get(fn () => $this->type === ProductType::Simple);
	}

	/**
	 * Check if product is of type configurable.
	 */
	protected function isConfigurable(): Attribute
	{
		return Attribute::get(fn () => $this->type === ProductType::Configurable);
	}

	/**
	 * Check if product has children (is a parent with variations).
	 */
	protected function isVariations(): Attribute
	{
		return Attribute::get(fn () => $this->children()->exists());
	}

	/**
	 * Check if product is a child (variation).
	 */
	protected function isChild(): Attribute
	{
		return Attribute::get(fn () => $this->parent_id !== null);
	}

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
	 * Tags that belong to this product.
	 */
	public function tags(): BelongsToMany
	{
		return $this->belongsToMany(Tag::class);
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
	 * Shipping methods available for this product.
	 */
	public function shippingMethods(): BelongsToMany
	{
		return $this->belongsToMany(ShippingMethod::class, 'product_shipping_method')
			->withPivot('price')
			->withTimestamps()
			->orderBy('order');
	}

	/**
	 * Attributes for this product.
	 */
	public function attributes(): HasMany
	{
		return $this->hasMany(ProductAttribute::class)->orderBy('order');
	}

	/**
	 * Parent product (for child products).
	 */
	public function parent(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'parent_id');
	}

	/**
	 * Child products (variations).
	 */
	public function children(): HasMany
	{
		return $this->hasMany(Product::class, 'parent_id')->orderBy('order');
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
