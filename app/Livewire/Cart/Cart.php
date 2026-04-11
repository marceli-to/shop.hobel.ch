<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Actions\Cart\Destroy as DestroyCartAction;

class Cart extends Component
{
	public array $cart;

	public function mount(): void
	{
		$this->cart = (new GetCartAction())->execute();
		$this->calculateTotals();
	}

	#[On('cart-updated')]
	public function updateCart(): void
	{
		$this->cart = (new GetCartAction())->execute();
		$this->calculateTotals();
	}

	private function calculateTotals(): void
	{
		$taxRate = config('invoice.tax_rate') / 100;
		$flatRate = config('invoice.shipping_flat_rate');
		$freeThreshold = config('invoice.shipping_free_threshold');
		$items = collect($this->cart['items'] ?? []);

		$subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);

		// Flat rate shipping: CHF 20 if any item uses "Versand", free if subtotal >= threshold
		$hasShipping = $items->contains(fn($item) => str_contains($item['shipping_name'] ?? '', 'Versand'));
		$shipping = ($hasShipping && $subtotal < $freeThreshold) ? $flatRate : 0;

		$this->cart['subtotal'] = $subtotal;
		$this->cart['shipping'] = $shipping;
		$this->cart['tax'] = ($subtotal + $shipping) * $taxRate;
		$this->cart['total'] = $subtotal + $shipping + $this->cart['tax'];
	}

	public function removeItem(string $cartKey): void
	{
		$this->cart = (new GetCartAction())->execute();

		$this->cart['items'] = collect($this->cart['items'])
			->reject(fn($item) => ($item['cart_key'] ?? $item['uuid']) === $cartKey)
			->values()
			->toArray();

		$this->cart['quantity'] = collect($this->cart['items'])->sum('quantity');

		if ($this->cart['quantity'] <= 0) {
			(new DestroyCartAction())->execute();
			$this->dispatch('cart-updated');
			$this->redirect(route('page.checkout.basket'));
			return;
		}

		$this->updateTotal();
		$this->dispatch('cart-updated');
	}

	public function updateQuantity(string $cartKey, int $quantity): void
	{
		if ($quantity <= 0) {
			$this->removeItem($cartKey);
			return;
		}

		$this->cart = (new GetCartAction())->execute();

		$this->cart['items'] = collect($this->cart['items'])
			->map(function ($item) use ($cartKey, $quantity) {
				if (($item['cart_key'] ?? $item['uuid']) === $cartKey) {
					$item['quantity'] = $quantity;
				}
				return $item;
			})
			->toArray();

		$this->updateTotal();
		$this->dispatch('cart-updated');
	}

	public function updateShipping(string $cartKey, int $shippingMethodId): void
	{
		$this->cart = (new GetCartAction())->execute();

		$this->cart['items'] = collect($this->cart['items'])
			->map(function ($item) use ($cartKey, $shippingMethodId) {
				if (($item['cart_key'] ?? $item['uuid']) === $cartKey) {
					$item['selected_shipping'] = $shippingMethodId;
					$method = collect($item['shipping_methods'] ?? [])->firstWhere('id', $shippingMethodId);
					$item['shipping_name'] = $method['name'] ?? 'Versand';
					$item['shipping_price'] = 0;
				}
				return $item;
			})
			->toArray();

		$this->updateTotal();
		$this->dispatch('cart-updated');
	}

	private function updateTotal(): void
	{
		$taxRate = config('invoice.tax_rate') / 100;
		$flatRate = config('invoice.shipping_flat_rate');
		$freeThreshold = config('invoice.shipping_free_threshold');
		$items = collect($this->cart['items']);

		$subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);

		$hasShipping = $items->contains(fn($item) => str_contains($item['shipping_name'] ?? '', 'Versand'));
		$shipping = ($hasShipping && $subtotal < $freeThreshold) ? $flatRate : 0;

		$this->cart['subtotal'] = $subtotal;
		$this->cart['shipping'] = $shipping;
		$this->cart['tax'] = ($subtotal + $shipping) * $taxRate;
		$this->cart['total'] = $subtotal + $shipping + $this->cart['tax'];
		$this->cart['quantity'] = $items->sum('quantity');

		(new UpdateCartAction())->execute($this->cart);
	}

	public function render()
	{
		return view('livewire.cart.cart');
	}
}
