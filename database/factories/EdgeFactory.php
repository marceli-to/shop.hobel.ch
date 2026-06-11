<?php

namespace Database\Factories;

use App\Models\Edge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Edge>
 */
class EdgeFactory extends Factory
{
	protected $model = Edge::class;

	public function definition(): array
	{
		return [
			'uuid' => (string) Str::uuid(),
			'name' => fake()->unique()->words(2, true),
			'price' => fake()->randomFloat(2, 5, 80),
			'order' => 0,
		];
	}
}
