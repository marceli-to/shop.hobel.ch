<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
	protected $model = ShippingMethod::class;

	public function definition(): array
	{
		return [
			'uuid' => (string) Str::uuid(),
			'name' => fake()->unique()->words(2, true),
			'price' => fake()->randomFloat(2, 0, 50),
			'is_shipping' => true,
			'order' => 0,
		];
	}
}
