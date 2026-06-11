<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
	protected $model = OrderItem::class;

	public function definition(): array
	{
		return [
			'order_id' => Order::factory(),
			'product_name' => fake()->words(3, true),
			'product_label' => null,
			'product_configuration' => null,
			'product_price' => fake()->randomFloat(2, 50, 1000),
			'quantity' => 1,
			'shipping_name' => 'Standard',
			'shipping_price' => fake()->randomFloat(2, 0, 50),
		];
	}
}
