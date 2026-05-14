<?php

namespace Tests\Feature\Order;

use App\Actions\Order\Create;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
	use RefreshDatabase;

	private function cart(Product $product, int $quantity = 2): array
	{
		return [
			'items' => [[
				'uuid' => $product->uuid,
				'name' => $product->name,
				'price' => 50.00,
				'quantity' => $quantity,
				'shipping_price' => 0,
			]],
			'subtotal' => 100.00,
			'shipping' => 10.00,
			'tax' => 8.10,
			'total' => 118.10,
		];
	}

	private function invoiceAddress(): array
	{
		return [
			'firstname' => 'Anna',
			'lastname' => 'Müller',
			'street' => 'Bahnhofstrasse',
			'street_number' => '12',
			'zip' => '8001',
			'city' => 'Zürich',
			'country' => 'CH',
			'email' => 'anna@example.ch',
		];
	}

	public function test_creates_order_with_items_and_decrements_stock(): void
	{
		$product = Product::factory()->create(['stock' => 10]);

		$order = (new Create())->execute(
			$this->cart($product, 2),
			$this->invoiceAddress(),
			[],
			'invoice'
		);

		$this->assertInstanceOf(Order::class, $order);
		$this->assertCount(1, $order->items);
		$this->assertSame(2, $order->items->first()->quantity);

		$this->assertSame(8, $product->fresh()->stock);
	}

	public function test_uses_invoice_address_when_delivery_address_is_empty(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[],
			'invoice'
		);

		$this->assertTrue($order->use_invoice_address);
		$this->assertNull($order->shipping_street);
	}

	public function test_stores_separate_shipping_address_when_provided(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[
				'firstname' => 'Hans',
				'lastname' => 'Meier',
				'street' => 'Seestrasse',
				'street_number' => '99',
				'zip' => '8800',
				'city' => 'Thalwil',
				'country' => 'CH',
			],
			'invoice'
		);

		$this->assertFalse($order->use_invoice_address);
		$this->assertSame('Seestrasse', $order->shipping_street);
		$this->assertSame('Thalwil', $order->shipping_city);
	}

	public function test_invoice_payment_leaves_paid_at_null(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[],
			'invoice'
		);

		$this->assertNull($order->paid_at);
		$this->assertFalse($order->isPaid());
	}

	public function test_creditcard_payment_marks_order_as_paid(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[],
			'creditcard',
			'pay_ref_abc'
		);

		$this->assertNotNull($order->paid_at);
		$this->assertTrue($order->isPaid());
		$this->assertSame('pay_ref_abc', $order->payment_reference);
	}

	public function test_stores_completed_order_id_in_session(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[],
			'invoice'
		);

		$this->assertSame($order->id, session('completed_order_id'));
	}

	public function test_generates_order_number_in_yynnnnn_format(): void
	{
		$product = Product::factory()->create();

		$order = (new Create())->execute(
			$this->cart($product),
			$this->invoiceAddress(),
			[],
			'invoice'
		);

		$this->assertMatchesRegularExpression('/^\d{7,}$/', $order->order_number);
		$this->assertSame($order->created_at->format('y'), substr($order->order_number, 0, 2));
	}
}
