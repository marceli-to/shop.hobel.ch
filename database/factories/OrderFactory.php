<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
	protected $model = Order::class;

	public function definition(): array
	{
		return [
			'invoice_firstname' => fake()->firstName(),
			'invoice_lastname' => fake()->lastName(),
			'invoice_street' => fake()->streetName(),
			'invoice_street_number' => (string) fake()->numberBetween(1, 200),
			'invoice_zip' => (string) fake()->numberBetween(1000, 9999),
			'invoice_city' => fake()->city(),
			'invoice_country' => 'CH',
			'invoice_email' => fake()->safeEmail(),
			'use_invoice_address' => true,
			'subtotal' => 100.00,
			'shipping' => 10.00,
			'tax' => 8.10,
			'total' => 118.10,
			'payment_method' => 'invoice',
		];
	}
}
