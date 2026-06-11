<?php

namespace Tests\Feature\Cart;

use App\Livewire\Cart\MiniCart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MiniCartComponentTest extends TestCase
{
	use RefreshDatabase;

	private function item(string $cartKey, float $price, int $quantity): array
	{
		return [
			'cart_key' => $cartKey,
			'uuid' => 'uuid-' . $cartKey,
			'name' => 'Product ' . $cartKey,
			'label' => null,
			'price' => $price,
			'base_price' => $price,
			'quantity' => $quantity,
			'image' => null,
			'configuration' => null,
			'shipping_methods' => [],
			'selected_shipping' => null,
			'shipping_name' => null,
			'shipping_price' => 0,
			'is_shipping' => true,
		];
	}

	/**
	 * The mini cart renders straight from the session without recalculating,
	 * so totals must already be present.
	 */
	private function seedCart(array $items): void
	{
		$subtotal = collect($items)->sum(fn ($i) => $i['price'] * $i['quantity']);

		session()->put('cart', [
			'items' => $items,
			'quantity' => collect($items)->sum('quantity'),
			'subtotal' => $subtotal,
			'shipping' => 0.0,
			'tax' => $subtotal * 0.081,
			'total' => $subtotal * 1.081,
			'order_step' => 1,
		]);
	}

	public function test_mount_reflects_the_session_cart(): void
	{
		$this->seedCart([$this->item('a', 100.0, 2)]);

		Livewire::test(MiniCart::class)
			->assertSet('cart.quantity', 2)
			->assertSet('cart.items.0.cart_key', 'a');
	}

	public function test_cart_updated_event_refreshes_from_session(): void
	{
		$this->seedCart([$this->item('a', 100.0, 1)]);

		$component = Livewire::test(MiniCart::class)
			->assertSet('cart.quantity', 1);

		// Simulate another component mutating the session cart.
		$this->seedCart([
			$this->item('a', 100.0, 1),
			$this->item('b', 40.0, 3),
		]);

		$component->dispatch('cart-updated')
			->assertSet('cart.quantity', 4)
			->assertSet('cart.items.1.cart_key', 'b');
	}

	public function test_remove_item_persists_and_recalculates(): void
	{
		$this->seedCart([
			$this->item('a', 100.0, 1),
			$this->item('b', 40.0, 2),
		]);

		Livewire::test(MiniCart::class)
			->call('removeItem', 'a');

		$cart = session('cart');
		$this->assertCount(1, $cart['items']);
		$this->assertSame('b', $cart['items'][0]['cart_key']);
		$this->assertEqualsWithDelta(80.0, $cart['subtotal'], 0.0001);
	}

	public function test_removing_the_last_item_destroys_the_cart(): void
	{
		$this->seedCart([$this->item('a', 100.0, 1)]);

		Livewire::test(MiniCart::class)
			->call('removeItem', 'a')
			->assertSet('show', false);

		$this->assertNull(session('cart'));
	}
}
