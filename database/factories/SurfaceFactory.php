<?php

namespace Database\Factories;

use App\Models\Surface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Surface>
 */
class SurfaceFactory extends Factory
{
	protected $model = Surface::class;

	public function definition(): array
	{
		return [
			'uuid' => (string) Str::uuid(),
			'name' => fake()->unique()->words(2, true),
			'price' => fake()->randomFloat(2, 20, 150),
			'minimum_amount' => 0,
			'order' => 0,
		];
	}
}
