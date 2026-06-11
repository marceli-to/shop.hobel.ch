<?php

namespace Database\Factories;

use App\Models\WoodType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WoodType>
 */
class WoodTypeFactory extends Factory
{
	protected $model = WoodType::class;

	public function definition(): array
	{
		return [
			'uuid' => (string) Str::uuid(),
			'name' => fake()->unique()->words(2, true),
			'price' => fake()->randomFloat(2, 500, 2000),
			'sorting_factor' => 1,
			'order' => 0,
		];
	}
}
