<?php

namespace Tests\Unit\Actions;

use App\Actions\Cart\Destroy;
use App\Actions\Cart\Get;
use App\Actions\Cart\Store;
use App\Actions\Cart\Update;
use Tests\TestCase;

class CartActionsTest extends TestCase
{
	public function test_get_returns_empty_cart_when_session_is_empty(): void
	{
		$cart = (new Get())->execute();

		$this->assertSame([
			'items' => [],
			'quantity' => 0,
			'total' => 0,
		], $cart);
	}

	public function test_get_returns_cart_from_session(): void
	{
		session()->put('cart', ['items' => [['name' => 'X']], 'quantity' => 1, 'total' => 50]);

		$cart = (new Get())->execute();

		$this->assertCount(1, $cart['items']);
		$this->assertSame(50, $cart['total']);
	}

	public function test_store_persists_cart_to_session(): void
	{
		(new Store())->execute(['items' => [['name' => 'Tisch']], 'quantity' => 1, 'total' => 999]);

		$this->assertSame(999, session('cart.total'));
		$this->assertSame('Tisch', session('cart.items.0.name'));
	}

	public function test_update_merges_into_existing_cart(): void
	{
		session()->put('cart', ['items' => [['name' => 'A']], 'quantity' => 1, 'total' => 100]);

		$cart = (new Update())->execute(['total' => 250, 'quantity' => 2]);

		$this->assertSame(250, $cart['total']);
		$this->assertSame(2, $cart['quantity']);
		// items preserved from existing cart
		$this->assertCount(1, $cart['items']);
		// and persisted
		$this->assertSame(250, session('cart.total'));
	}

	public function test_update_starts_from_default_when_no_cart_exists(): void
	{
		$cart = (new Update())->execute(['total' => 42]);

		$this->assertSame(42, $cart['total']);
		$this->assertSame([], $cart['items']);
	}

	public function test_destroy_clears_cart_and_checkout_session_keys(): void
	{
		session()->put('cart', ['items' => [['x']], 'total' => 1]);
		session()->put('invoice_address', ['firstname' => 'A']);
		session()->put('delivery_address', ['firstname' => 'B']);
		session()->put('payment_method', 'invoice');
		session()->put('order_step', 3);
		session()->put('completed_order_id', 99);

		(new Destroy())->execute();

		$this->assertNull(session('cart'));
		$this->assertNull(session('invoice_address'));
		$this->assertNull(session('delivery_address'));
		$this->assertNull(session('payment_method'));
		$this->assertNull(session('order_step'));

		// confirmation page still needs this — must NOT be cleared
		$this->assertSame(99, session('completed_order_id'));
	}
}
