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
		'is_configurable',
		'configuration_schema',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'published_at' => 'datetime',
		'is_configurable' => 'boolean',
		'configuration_schema' => 'array',
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

	/**
	 * Check if product is configurable.
	 */
	public function isConfigurable(): bool
	{
		return $this->is_configurable;
	}

	/**
	 * Get configuration attributes.
	 */
	public function getConfigurationAttributes(): array
	{
		if (!$this->isConfigurable() || !$this->configuration_schema) {
			return [];
		}

		return $this->configuration_schema['attributes'] ?? [];
	}

	/**
	 * Calculate price with configuration.
	 *
	 * @param array $configuration Configuration data with selected options
	 * @return float
	 */
	public function calculateConfiguredPrice(array $configuration = []): float
	{
		$basePrice = (float) $this->price;

		if (!$this->isConfigurable() || empty($configuration)) {
			return $basePrice;
		}

		$totalModifier = 0;

		foreach ($this->getConfigurationAttributes() as $attribute) {
			$attributeKey = $attribute['key'];

			// Check if this attribute was configured
			if (isset($configuration[$attributeKey]['price'])) {
				$totalModifier += (float) $configuration[$attributeKey]['price'];
			}
		}

		return $basePrice + $totalModifier;
	}

	/**
	 * Validate configuration against schema.
	 *
	 * @param array $configuration
	 * @return bool
	 */
	public function isValidConfiguration(array $configuration): bool
	{
		if (!$this->isConfigurable()) {
			return empty($configuration);
		}

		foreach ($this->getConfigurationAttributes() as $attribute) {
			// Check required attributes
			if (($attribute['required'] ?? false) && !isset($configuration[$attribute['key']])) {
				return false;
			}

			// Validate option exists
			if (isset($configuration[$attribute['key']])) {
				$selectedOption = $configuration[$attribute['key']]['option'] ?? null;
				$validOptions = collect($attribute['options'])->pluck('value')->toArray();

				if (!in_array($selectedOption, $validOptions)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get the display price (base or "ab" for configurable).
	 */
	public function getDisplayPrice(): string
	{
		if ($this->isConfigurable()) {
			return 'ab CHF ' . number_format($this->price, 2, '.', '\'');
		}

		return 'CHF ' . number_format($this->price, 2, '.', '\'');
	}
}
