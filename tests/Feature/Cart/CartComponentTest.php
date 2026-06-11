<?php

namespace Tests\Feature\Cart;

use App\Livewire\Cart\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartComponentTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A cart item with every key the cart view touches.
	 */
	private function item(string $cartKey, float $price, int $quantity, bool $isShipping = true): array
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
			'is_shipping' => $isShipping,
		];
	}

	private function seedCart(array $items): void
	{
		session()->put('cart', [
			'items' => $items,
			'quantity' => collect($items)->sum('quantity'),
			'order_step' => 1,
		]);
	}

	public function test_update_quantity_changes_quantity_and_recalculates_totals(): void
	{
		$this->seedCart([$this->item('a', 100.0, 1)]);

		Livewire::test(Cart::class)
			->call('updateQuantity', 'a', 3)
			->assertSet('cart.items.0.quantity', 3);

		$cart = session('cart');
		$this->assertSame(3, $cart['items'][0]['quantity']);
		$this->assertEqualsWithDelta(300.0, $cart['subtotal'], 0.0001);
		// 300 subtotal is over the free-shipping threshold → no shipping.
		$this->assertEqualsWithDelta(0.0, $cart['shipping'], 0.0001);
		$this->assertEqualsWithDelta(300.0 * 0.081, $cart['tax'], 0.0001);
	}

	public function test_remove_item_drops_only_that_item(): void
	{
		$this->seedCart([
			$this->item('a', 100.0, 1),
			$this->item('b', 50.0, 2),
		]);

		Livewire::test(Cart::class)
			->call('removeItem', 'a')
			->assertSet('cart.items', fn ($items) => count($items) === 1 && $items[0]['cart_key'] === 'b');

		$cart = session('cart');
		$this->assertCount(1, $cart['items']);
		$this->assertSame('b', $cart['items'][0]['cart_key']);
		$this->assertSame(2, $cart['quantity']);
	}

	public function test_removing_the_last_item_destroys_the_cart_and_redirects_to_basket(): void
	{
		$this->seedCart([$this->item('a', 100.0, 1)]);

		Livewire::test(Cart::class)
			->call('removeItem', 'a')
			->assertRedirect(route('page.checkout.basket'));

		$this->assertNull(session('cart'));
	}

	public function test_updating_quantity_to_zero_removes_the_item(): void
	{
		$this->seedCart([$this->item('a', 100.0, 1)]);

		Livewire::test(Cart::class)
			->call('updateQuantity', 'a', 0)
			->assertRedirect(route('page.checkout.basket'));

		$this->assertNull(session('cart'));
	}
}
