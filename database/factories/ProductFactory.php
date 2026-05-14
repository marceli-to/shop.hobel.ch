<?php

namespace Database\Factories;

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
}
