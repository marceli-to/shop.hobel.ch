<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Enums\TableShape;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
	protected $model = Product::class;

	public function definition(): array
	{
		$name = fake()->unique()->words(3, true);

		return [
			'uuid' => (string) Str::uuid(),
			'type' => 'simple',
			'name' => $name,
			'slug' => Str::slug($name) . '-' . Str::random(5),
			'price' => fake()->randomFloat(2, 10, 500),
			'stock' => 100,
			'published' => true,
		];
	}

	/**
	 * A configurable product with a complete, neutral set of pricing
	 * parameters. Surcharge thresholds are set high enough that they don't
	 * trigger by default, so tests can opt into each surcharge in isolation.
	 */
	public function configurable(): static
	{
		return $this->state(fn () => [
			'type' => ProductType::Configurable->value,
			'shape' => TableShape::Rectangular->value,
			'min_length' => 50,
			'max_length' => 300,
			'min_width' => 50,
			'max_width' => 150,
			'base_price' => 100,
			'material_factor' => 2,
			'surface_processing_price' => 10,
			'large_format_threshold' => 1000,
			'large_format_surcharge' => 0,
			'oversize_threshold' => 1000,
			'oversize_surcharge' => 0,
			'minimum_price' => 0,
			'form_surcharge' => 0,
		]);
	}
}
